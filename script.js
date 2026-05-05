// ==========================================
// HEADER SCROLL EFFECT
// ==========================================
const header = document.getElementById('header');
let lastScroll = 0;
let isScrollingToTarget = false;
let scrollTimeout;

// ==========================================
// THEME & LANGUAGE TOGGLES
// ==========================================
const themeToggle = document.getElementById('themeToggle');
const htmlEl = document.documentElement;

// Initialize theme from local storage or system preference
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    htmlEl.classList.add('theme-dark');
} else if (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    // optional: auto-dark mode based on system, uncomment if needed
    // htmlEl.classList.add('theme-dark');
}

if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        htmlEl.classList.toggle('theme-dark');
        const isDark = htmlEl.classList.contains('theme-dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
}

const langDropdownToggle = document.querySelector('.lang-dropdown-toggle');
const langDropdownMenu = document.querySelector('.lang-dropdown-menu');
if (langDropdownToggle && langDropdownMenu) {
    langDropdownToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        langDropdownMenu.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
        if (!langDropdownToggle.contains(e.target) && !langDropdownMenu.contains(e.target)) {
            langDropdownMenu.classList.remove('show');
        }
    });

    const langOptions = document.querySelectorAll('.lang-option');
    langOptions.forEach(opt => {
        opt.addEventListener('click', (e) => {
            e.preventDefault();
            const lang = opt.getAttribute('data-lang');
            localStorage.setItem('lang', lang);

            // Reload page with new lang parameter while keeping other query parameters or hash
            const url = new URL(window.location.href);
            url.searchParams.set('lang', lang);
            window.location.href = url.toString();
        });
    });
}

// ==========================================
// BACKGROUND LAZY TRANSLATION (SPEED OPTIMIZATION)
// ==========================================
(function () {
    const translateElements = document.querySelectorAll('.js-translate');
    if (translateElements.length === 0) return;

    const urlParams = new URLSearchParams(window.location.search);
    const lang = urlParams.get('lang') || document.cookie.match(/lang=([^;]+)/)?.[1] || 'ru';
    if (lang === 'ru') return;

    translateElements.forEach(async (el) => {
        const key = el.getAttribute('data-key');
        const source = el.getAttribute('data-source');

        try {
            const response = await fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=ru&tl=${lang}&dt=t&q=${encodeURIComponent(source)}`);
            const data = await response.json();
            if (data && data[0]) {
                let translatedText = "";
                data[0].forEach(chunk => { if (chunk[0]) translatedText += chunk[0]; });

                // Update UI instantly
                el.innerHTML = translatedText.trim();
                el.classList.remove('js-translate'); // Marker removed

                // 🧪 PROACTIVE: Save to server cache for next users (atomicity handled by i18n.php)
                const formData = new FormData();
                formData.append('action', 'save_translate');
                formData.append('key', key);
                formData.append('value', translatedText.trim());
                formData.append('lang', lang);
                fetch('/i18n.php', { method: 'POST', body: formData }).catch(() => { });
            }
        } catch (e) {
            console.warn('Translation failed for:', key);
        }
    });
})();
window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    // Сбрасываем флаг программного скролла к цели после остановки скроллинга
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        isScrollingToTarget = false;
    }, 150);

    // Скрытие/Отображение хедера и футера
    if (currentScroll > 5) {
        header.classList.add('scrolled');

        // Логика скрытия при прокрутке вниз и появления при прокрутке вверх
        if (currentScroll > lastScroll && !isScrollingToTarget) {
            // Скролл вниз
            // Не скрываем хедер, если открыто мобильное меню
            if (!header.classList.contains('hidden') && (typeof navMenu === 'undefined' || !navMenu || !navMenu.classList.contains('active'))) {
                header.classList.add('hidden');
            }
        } else if (currentScroll < lastScroll || isScrollingToTarget) {
            // Скролл вверх или программный скролл
            if (header.classList.contains('hidden')) header.classList.remove('hidden');
        }
    } else {
        header.classList.remove('scrolled');
        header.classList.remove('hidden');
    }

    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        if (currentScroll > 500) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    }

    lastScroll = currentScroll;
});

// ==========================================
// BACK TO TOP
// ==========================================
const backToTopBtn = document.getElementById('backToTop');
if (backToTopBtn) {
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ==========================================
// MOBILE MENU TOGGLE
// ==========================================
const navToggle = document.getElementById('navToggle');
const navMenu = document.getElementById('navMenu');

navToggle.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    navToggle.classList.toggle('active');
});

// Закрытие меню при клике на ссылку
const navLinks = document.querySelectorAll('.nav-link');
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 1024) {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
        }
    });
});

// Закрытие меню при клике вне его
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 1024 && !navMenu.contains(e.target) && !navToggle.contains(e.target)) {
        navMenu.classList.remove('active');
        navToggle.classList.remove('active');
    }
});

// ==========================================
// SMOOTH SCROLL WITH OFFSET
// ==========================================
document.querySelectorAll('a[href^="#"], a[href^="/#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        let href = this.getAttribute('href');

        // Handle cross-page hash links (e.g. /#services)
        if (href.startsWith('/#')) {
            if (window.location.pathname === '/' || window.location.pathname === '/index.php') {
                href = href.substring(1); // turn /#services into #services
            } else {
                return; // Let default navigation happen
            }
        }

        if (href.startsWith('#')) {
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                isScrollingToTarget = true;

                const headerHeight = header.offsetHeight;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        }
    });
});

// ==========================================
// INTERSECTION OBSERVER FOR ANIMATIONS
// ==========================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Анимация для секций
document.querySelectorAll('.section').forEach(section => {
    section.style.opacity = '0';
    section.style.transform = 'translateY(30px)';
    section.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
    observer.observe(section);
});

// ==========================================
// FORM HANDLING WITH ANTI-SPAM
// ==========================================
const contactForm = document.getElementById('contactForm');
let formData = {
    startTime: Date.now(),
    interactions: 0,
    mouseMovements: 0,
    touchEvents: 0
};

// Отслеживание взаимодействий пользователя
document.addEventListener('mousemove', () => {
    formData.mouseMovements++;
});

document.addEventListener('touchstart', () => {
    formData.touchEvents++;
});

// Отслеживание взаимодействий с полями формы
const formInputs = contactForm ? contactForm.querySelectorAll('input, textarea, select') : [];
formInputs.forEach(input => {
    input.addEventListener('focus', () => {
        formData.interactions++;
    });

    input.addEventListener('input', () => {
        formData.interactions++;
    });
});

// Обработка отправки формы
if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitButton = contactForm.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;

        // Собираем данные формы
        const data = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            service: document.getElementById('service').value,
            message: document.getElementById('message').value,

            // Анти-спам данные
            formTime: Date.now() - formData.startTime,
            interactions: formData.interactions,
            mouseMovements: formData.mouseMovements,
            touchEvents: formData.touchEvents,
            timestamp: Date.now(),
            userAgent: navigator.userAgent,
            screenResolution: `${window.screen.width}x${window.screen.height}`,
            referrer: document.referrer,
            formSource: 'Главная страница - Форма обратной связи'
        };

        // Базовая валидация анти-спам
        const isLikelySpam = validateFormData(data);

        if (isLikelySpam) {
            data.spamFlag = '[POSSIBLE SPAM]';
        }

        // Показываем индикатор загрузки
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" style="animation: spin 1s linear infinite;">
                <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="2" fill="none" opacity="0.25"/>
                <path d="M10 2a8 8 0 0 1 8 8" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            <span>Отправка...</span>
        `;

        try {
            // Отправка данных на сервер
            const response = await fetch('send-email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // Успешная отправка
                showNotification('Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в ближайшее время.', 'success');
                contactForm.reset();

                // Сброс анти-спам данных
                formData = {
                    startTime: Date.now(),
                    interactions: 0,
                    mouseMovements: 0,
                    touchEvents: 0
                };
            } else {
                throw new Error(result.message || 'Ошибка отправки');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Произошла ошибка при отправке. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.', 'error');
        } finally {
            // Восстанавливаем кнопку
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    });
}

// Валидация данных формы для обнаружения спама
function validateFormData(data) {
    let spamScore = 0;

    // Проверка времени заполнения формы (слишком быстро = подозрительно)
    if (data.formTime < 3000) {
        spamScore += 3;
    }

    // Проверка взаимодействий
    if (data.interactions < 3) {
        spamScore += 2;
    }

    // Проверка движений мыши (боты обычно не двигают мышью)
    if (data.mouseMovements === 0 && data.touchEvents === 0) {
        spamScore += 3;
    }

    // Проверка на подозрительные паттерны в тексте
    const suspiciousPatterns = [
        /\b(viagra|casino|poker|loan|credit)\b/i,
        /https?:\/\//gi, // Множество ссылок
        /(.)\1{10,}/, // Повторяющиеся символы
    ];

    const allText = `${data.name} ${data.email} ${data.message}`.toLowerCase();
    suspiciousPatterns.forEach(pattern => {
        if (pattern.test(allText)) {
            spamScore += 2;
        }
    });

    // Проверка email
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(data.email)) {
        spamScore += 2;
    }

    // Если набрано 5+ баллов, помечаем как возможный спам
    return spamScore >= 5;
}

// Показ уведомлений
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        max-width: 400px;
        padding: 1.5rem;
        background: ${type === 'success' ? 'hsl(120, 60%, 50%)' : 'hsl(0, 60%, 50%)'};
        color: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        z-index: 10000;
        animation: slideInRight 0.5s ease-out, fadeOut 0.5s ease-out 4.5s;
        font-weight: 500;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Добавляем стили для анимаций
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
`;
document.head.appendChild(styleSheet);

// ==========================================
// LAZY LOADING FOR IMAGES
// ==========================================
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// ==========================================
// CONSOLE MESSAGE
// ==========================================
console.log('%c👋 Привет, разработчик!', 'font-size: 20px; font-weight: bold; color: #8b5cf6;');
console.log('%cЕсли вы ищете такой же крутой сайт для своего бизнеса - свяжитесь с нами!', 'font-size: 14px; color: #60a5fa;');
console.log('%c📧 mdn77@yandex.ru | 📱 +7 (923) 406-44-41', 'font-size: 12px; color: #94a3b8;');

// ==========================================
// INTERACTIVE PORTFOLIO MODAL
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    const portfolioModal = document.getElementById('portfolioModal');
    if (!portfolioModal) {
        console.warn('[Portfolio] Modal element not found');
        return;
    }

    const modalOverlay = document.getElementById('portfolioModalOverlay');
    const modalClose = document.getElementById('portfolioModalClose');
    const modalTitle = document.getElementById('portfolioModalTitle');
    const modalLink = document.getElementById('portfolioModalLink');
    const modalIframe = document.getElementById('portfolioIframe');
    const iframeWrapper = document.getElementById('iframeWrapper');
    const deviceBtns = document.querySelectorAll('.device-btn');

    console.log('[Portfolio] Modal initialized, cards:', document.querySelectorAll('.open-interactive-modal').length);

    // Open Modal via EVENT DELEGATION for reliability
    document.addEventListener('click', (e) => {
        const card = e.target.closest('.open-interactive-modal');
        if (!card) return;

        e.preventDefault();
        const url = card.getAttribute('data-url');
        const title = card.getAttribute('data-title');
        const desc = card.getAttribute('data-description') || 'Описание проекта готовится...';
        const tagsRaw = card.getAttribute('data-tags') || '';

        console.log('[Portfolio] Opening modal for:', url);

        if (modalTitle) modalTitle.textContent = title;
        if (modalLink) modalLink.href = url;

        const sidebarTitle = document.getElementById('portfolioSidebarTitle');
        if (sidebarTitle) sidebarTitle.textContent = title;

        const sidebarDesc = document.getElementById('portfolioSidebarDesc');
        if (sidebarDesc) sidebarDesc.textContent = desc;

        const sidebarTags = document.getElementById('portfolioSidebarTags');
        if (sidebarTags) {
            sidebarTags.innerHTML = '';
            if (tagsRaw) {
                const tags = tagsRaw.split(',').map(t => t.trim()).filter(t => t);
                tags.forEach(tag => {
                    const li = document.createElement('li');
                    li.textContent = tag;
                    sidebarTags.appendChild(li);
                });
            }
        }

        // Show loader
        if (iframeWrapper) iframeWrapper.classList.add('loader-active');

        // Set iframe src
        if (modalIframe) modalIframe.src = url;

        // Show modal
        portfolioModal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    });

    // Handle Iframe Load
    modalIframe.addEventListener('load', () => {
        if (modalIframe.src && modalIframe.src !== window.location.href) {
            iframeWrapper.classList.remove('loader-active');
        }
    });

    // Close Modal
    const closeModal = () => {
        portfolioModal.classList.remove('is-open');
        document.body.style.overflow = '';

        // Wait for transition to finish before clearing
        setTimeout(() => {
            modalIframe.src = '';
            iframeWrapper.classList.remove('mobile-view');
            deviceBtns.forEach(b => b.classList.remove('active'));
            const desktopBtn = document.querySelector('.device-btn[data-device="desktop"]');
            if (desktopBtn) desktopBtn.classList.add('active');
        }, 400);
    };

    modalClose.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', closeModal);

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && portfolioModal.classList.contains('is-open')) {
            closeModal();
        }
    });

    // Device Switcher
    deviceBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            deviceBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const device = btn.getAttribute('data-device');
            if (device === 'mobile') {
                iframeWrapper.classList.add('mobile-view');
            } else {
                iframeWrapper.classList.remove('mobile-view');
            }
        });
    });
});

// ==========================================
// CALCULATOR API
// ==========================================
document.addEventListener("DOMContentLoaded", function () {
    const calcTypeRadios = document.querySelectorAll('input[name="calc_type"]');
    const calcAddonCheckboxes = document.querySelectorAll('.calc-addon');
    const calcSlider = document.getElementById('calcSlider');
    const calcTotalEl = document.getElementById('calcTotal');

    const timelineModifiers = {
        "1": 1.5,   // Urgent (+50%)
        "2": 1.0,   // Standard
        "3": 1.0,   // standard
        "4": 0.9    // slow (-10%)
    };

    function updateCalculator() {
        if (!calcTotalEl) return;

        let basePrice = 0;
        calcTypeRadios.forEach(radio => {
            if (radio.checked) basePrice = parseInt(radio.value);
        });

        let addonsPrice = 0;
        calcAddonCheckboxes.forEach(cb => {
            if (cb.checked) addonsPrice += parseInt(cb.value);
        });

        const sliderVal = calcSlider ? calcSlider.value : "2";
        const modifier = timelineModifiers[sliderVal] || 1;

        let total = (basePrice + addonsPrice) * modifier;

        animateValue(calcTotalEl, parseInt(calcTotalEl.innerText.replace(/\D/g, '')) || 0, total, 500);
    }

    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start).toLocaleString('ru-RU');
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    calcTypeRadios.forEach(r => r.addEventListener('change', updateCalculator));
    calcAddonCheckboxes.forEach(cb => cb.addEventListener('change', updateCalculator));
    if (calcSlider) calcSlider.addEventListener('input', updateCalculator);
    if (calcSlider) calcSlider.addEventListener('change', updateCalculator);

    updateCalculator();
});

// ==========================================
// INTERACTIVE PREMIUM EFFECTS
// ==========================================
(function () {
    const cursorGlow = document.getElementById("cursorGlow");
    const orbs = document.querySelectorAll(".gradient-orb");
    // const heroContent = document.querySelector(".hero-content");
    // const magneticBtns = document.querySelectorAll(".btn-primary, .btn-secondary");

    document.addEventListener("mousemove", (e) => {
        const { clientX, clientY } = e;

        // 1. Move Cursor Glow
        if (cursorGlow) {
            cursorGlow.style.left = clientX + "px";
            cursorGlow.style.top = clientY + "px";
        }

        // 2. Parallax Orbs (SAFE)
        const xPos = (clientX / window.innerWidth - 0.5);
        const yPos = (clientY / window.innerHeight - 0.5);

        orbs.forEach((orb, index) => {
            const speed = (index + 1) * 20;
            orb.style.transform = `translate(${xPos * speed}px, ${yPos * speed}px)`;
        });
    });
})();

// ==========================================
// HERO CANVAS: Плавающие геометрические частицы
// ==========================================
(function () {
    const canvas = document.getElementById('heroParticles');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    // Адаптивное количество частиц
    let particles = [];
    const isMobile = window.innerWidth < 768;
    const PARTICLE_COUNT = isMobile ? 25 : 60;
    const CONNECTION_DIST = isMobile ? 100 : 150;
    let mouse = { x: -9999, y: -9999 };
    let animId;

    // Ресайз canvas
    function resize() {
        const hero = canvas.parentElement;
        canvas.width = hero.offsetWidth;
        canvas.height = hero.offsetHeight;
    }

    // Создание частицы
    function createParticle() {
        const shapes = ['circle', 'triangle', 'square', 'diamond'];
        return {
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            vx: (Math.random() - 0.5) * 0.6,
            vy: (Math.random() - 0.5) * 0.6,
            size: Math.random() * 3 + 1.5,
            shape: shapes[Math.floor(Math.random() * shapes.length)],
            opacity: Math.random() * 0.4 + 0.15,
            rotation: Math.random() * Math.PI * 2,
            rotSpeed: (Math.random() - 0.5) * 0.02,
            // Цвет — индиго/пурпур/розовый
            hue: 240 + Math.random() * 80
        };
    }

    // Инициализация
    function init() {
        resize();
        particles = [];
        for (let i = 0; i < PARTICLE_COUNT; i++) {
            particles.push(createParticle());
        }
    }

    // Рисование фигуры
    function drawShape(p) {
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate(p.rotation);
        ctx.fillStyle = `hsla(${p.hue}, 80%, 65%, ${p.opacity})`;
        ctx.beginPath();

        switch (p.shape) {
            case 'circle':
                ctx.arc(0, 0, p.size, 0, Math.PI * 2);
                break;
            case 'triangle':
                ctx.moveTo(0, -p.size);
                ctx.lineTo(p.size, p.size);
                ctx.lineTo(-p.size, p.size);
                ctx.closePath();
                break;
            case 'square':
                ctx.rect(-p.size, -p.size, p.size * 2, p.size * 2);
                break;
            case 'diamond':
                ctx.moveTo(0, -p.size * 1.4);
                ctx.lineTo(p.size, 0);
                ctx.lineTo(0, p.size * 1.4);
                ctx.lineTo(-p.size, 0);
                ctx.closePath();
                break;
        }
        ctx.fill();
        ctx.restore();
    }

    // Анимация
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Обновление и отрисовка частиц
        particles.forEach(p => {
            p.x += p.vx;
            p.y += p.vy;
            p.rotation += p.rotSpeed;

            // Границы — оборот
            if (p.x < -10) p.x = canvas.width + 10;
            if (p.x > canvas.width + 10) p.x = -10;
            if (p.y < -10) p.y = canvas.height + 10;
            if (p.y > canvas.height + 10) p.y = -10;

            // Притяжение к курсору
            const dx = mouse.x - p.x;
            const dy = mouse.y - p.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < 200) {
                p.vx += dx * 0.00008;
                p.vy += dy * 0.00008;
            }

            drawShape(p);
        });

        // Соединяющие линии между ближайшими частицами
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < CONNECTION_DIST) {
                    const alpha = (1 - dist / CONNECTION_DIST) * 0.12;
                    ctx.strokeStyle = `hsla(260, 70%, 65%, ${alpha})`;
                    ctx.lineWidth = 0.5;
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                }
            }
        }

        animId = requestAnimationFrame(animate);
    }

    // Отслеживание мыши
    document.addEventListener('mousemove', e => {
        const rect = canvas.getBoundingClientRect();
        mouse.x = e.clientX - rect.left;
        mouse.y = e.clientY - rect.top;
    });

    // Остановка анимации вне viewport
    const heroSection = canvas.closest('.hero');
    const heroObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (!animId) animate();
            } else {
                cancelAnimationFrame(animId);
                animId = null;
            }
        });
    }, { threshold: 0.1 });

    if (heroSection) heroObserver.observe(heroSection);

    window.addEventListener('resize', () => {
        resize();
    });

    init();
    animate();
})();

// ==========================================
// SCROLL-REVEAL: Появление секций при скролле
// ==========================================
(function () {
    // Все элементы для reveal-анимации
    const revealElements = document.querySelectorAll(
        '.section, .section-header, .cases-grid, .services-grid, ' +
        '.process-grid, .pricing-cards, .ecosystem-grid, .contact-section'
    );

    if (!revealElements.length) return;

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                // Перестаём наблюдать — анимация один раз
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));
})();

// ==========================================
// ANIMATED COUNTERS: Счёт от 0 до числа
// ==========================================
(function () {
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-count'));
                const suffix = el.getAttribute('data-suffix') || '';
                const duration = 2000; // 2 секунды
                const startTime = performance.now();

                function updateCounter(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    // Кубическая easing для плавности
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const current = Math.floor(eased * target);
                    el.textContent = current + suffix;

                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        el.textContent = target + suffix;
                    }
                }

                requestAnimationFrame(updateCounter);
                counterObserver.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(c => counterObserver.observe(c));
})();


// ==========================================
// CANVAS PARTICLES — живые частицы на hero
// ==========================================
(function () {
    const canvas = document.getElementById('heroParticles');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const hero = canvas.closest('.hero');
    if (!hero) return;

    let W, H, particles = [], mouseX = -999, mouseY = -999;
    const PARTICLE_COUNT = 55;
    const CONNECT_DIST = 120;
    const MOUSE_RADIUS = 150;

    function resize() {
        W = canvas.width = hero.offsetWidth;
        H = canvas.height = hero.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    // Создаём частицы
    for (let i = 0; i < PARTICLE_COUNT; i++) {
        particles.push({
            x: Math.random() * W,
            y: Math.random() * H,
            vx: (Math.random() - 0.5) * 0.5,
            vy: (Math.random() - 0.5) * 0.5,
            r: Math.random() * 2 + 1,
            alpha: Math.random() * 0.5 + 0.3,
            // Случайный цвет: фиолетовый или голубой
            color: Math.random() > 0.5 ? '99, 102, 241' : '14, 165, 233'
        });
    }

    // Следим за мышью
    hero.addEventListener('mousemove', e => {
        const rect = hero.getBoundingClientRect();
        mouseX = e.clientX - rect.left;
        mouseY = e.clientY - rect.top;
    });

    hero.addEventListener('mouseleave', () => {
        mouseX = -999;
        mouseY = -999;
    });

    function draw() {
        ctx.clearRect(0, 0, W, H);

        // Обновляем и рисуем частицы
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];

            // Реакция на мышь — мягкое отталкивание
            const dx = p.x - mouseX;
            const dy = p.y - mouseY;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < MOUSE_RADIUS && dist > 0) {
                const force = (MOUSE_RADIUS - dist) / MOUSE_RADIUS * 0.015;
                p.vx += (dx / dist) * force;
                p.vy += (dy / dist) * force;
            }

            // Движение
            p.x += p.vx;
            p.y += p.vy;

            // Торможение
            p.vx *= 0.998;
            p.vy *= 0.998;

            // Границы — мягкий отскок
            if (p.x < 0 || p.x > W) p.vx *= -1;
            if (p.y < 0 || p.y > H) p.vy *= -1;
            p.x = Math.max(0, Math.min(W, p.x));
            p.y = Math.max(0, Math.min(H, p.y));

            // Рисуем частицу
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(${p.color}, ${p.alpha})`;
            ctx.fill();
        }

        // Соединяем близкие частицы линиями
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const a = particles[i], b = particles[j];
                const dx = a.x - b.x;
                const dy = a.y - b.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < CONNECT_DIST) {
                    const opacity = (1 - dist / CONNECT_DIST) * 0.15;
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.strokeStyle = `rgba(99, 102, 241, ${opacity})`;
                    ctx.lineWidth = 0.5;
                    ctx.stroke();
                }
            }
        }

        // Линии от мыши к ближайшим частицам
        if (mouseX > 0) {
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                const dx = p.x - mouseX;
                const dy = p.y - mouseY;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < MOUSE_RADIUS) {
                    const opacity = (1 - dist / MOUSE_RADIUS) * 0.25;
                    ctx.beginPath();
                    ctx.moveTo(mouseX, mouseY);
                    ctx.lineTo(p.x, p.y);
                    ctx.strokeStyle = `rgba(14, 165, 233, ${opacity})`;
                    ctx.lineWidth = 0.8;
                    ctx.stroke();
                }
            }
        }

        requestAnimationFrame(draw);
    }

    draw();
})();

// ==========================================
// ИНТЕРАКТИВНЫЙ GLOW НА HERO (следует за мышью)
// ==========================================
(function () {
    const hero = document.querySelector('.hero');
    const glow = document.getElementById('heroGlow');
    if (!hero || !glow) return;

    hero.addEventListener('mousemove', e => {
        const rect = hero.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        glow.style.left = x + 'px';
        glow.style.top = y + 'px';
    });
})();

// ==========================================
// МАГНИТНЫЕ КНОПКИ (притяжение к курсору)
// ==========================================
(function () {
    if (window.innerWidth < 769) return;

    const magneticBtns = document.querySelectorAll('.magnetic-btn');
    magneticBtns.forEach(btn => {
        btn.addEventListener('mousemove', e => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            // Сила притяжения — 30%
            btn.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
        });

        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'translate(0, 0)';
            btn.style.transition = 'transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
        });

        btn.addEventListener('mouseenter', () => {
            btn.style.transition = 'transform 0.1s ease';
        });
    });
})();

// ==========================================
// SMOOTH TEXT REVEAL (слова появляются по очереди)
// ==========================================
(function () {
    const title = document.getElementById('heroTitle');
    if (!title) return;

    // Анимация только текстовых узлов
    title.style.opacity = '0';
    title.style.transform = 'translateY(30px)';
    title.style.transition = 'opacity 0.8s ease, transform 0.8s ease';

    setTimeout(() => {
        title.style.opacity = '1';
        title.style.transform = 'translateY(0)';
    }, 300);
})();

// ==========================================
// АНИМИРОВАННЫЕ СЧЁТЧИКИ В HERO (набег цифр)
// ==========================================
(function () {
    const cards = document.querySelectorAll('.hero-float-card strong');
    if (!cards.length) return;

    // Парсим число из текста (250+, 12, 98%)
    function parseNum(text) {
        const match = text.match(/[\d,.]+/);
        return match ? parseFloat(match[0].replace(',', '.')) : 0;
    }

    let animated = false;

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !animated) {
                animated = true;
                cards.forEach(el => {
                    const originalText = el.textContent;
                    const target = parseNum(originalText);
                    const suffix = originalText.replace(/[\d,.]+/, '');
                    const isFloat = originalText.includes('.');
                    const duration = 2000;
                    const start = performance.now();

                    function tick(now) {
                        const elapsed = now - start;
                        const progress = Math.min(elapsed / duration, 1);
                        // Ease-out cubic
                        const eased = 1 - Math.pow(1 - progress, 3);
                        const current = isFloat
                            ? (target * eased).toFixed(1)
                            : Math.floor(target * eased);
                        el.textContent = current + suffix;
                        if (progress < 1) requestAnimationFrame(tick);
                        else el.textContent = originalText;
                    }

                    el.textContent = '0' + suffix;
                    requestAnimationFrame(tick);
                });
            }
        });
    }, { threshold: 0.3 });

    const heroVisual = document.querySelector('.hero-visual');
    if (heroVisual) counterObserver.observe(heroVisual);
})();

// ==========================================
// CASCADING REVEAL — каскадное появление секций
// ==========================================
(function () {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -60px 0px' });

    // Все секции + внутренние контейнеры
    document.querySelectorAll(
        '.section-header, .cases-grid, .services-grid, .process-timeline, .pricing-grid, .contact-section, .eco-grid'
    ).forEach(el => revealObserver.observe(el));
})();

// ==========================================
// 3D TILT-ЭФФЕКТ НА КАРТОЧКАХ (как у Stripe)
// ==========================================
(function () {
    if (window.innerWidth < 769) return;

    document.querySelectorAll('.case-card, .eco-card-new').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            // Максимальный наклон 6 градусов
            const rotateX = ((y - centerY) / centerY) * -6;
            const rotateY = ((x - centerX) / centerX) * 6;

            card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
            card.style.transition = 'transform 0.1s ease';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(800px) rotateX(0) rotateY(0) translateY(0)';
            card.style.transition = 'transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)';
        });
    });
})();

// ==========================================
// СВЕЧЕНИЕ НА КАРТОЧКАХ УСЛУГ (glow следует за мышью)
// ==========================================
(function () {
    if (window.innerWidth < 769) return;

    document.querySelectorAll('.service-card').forEach(card => {
        card.style.position = 'relative';
        card.style.overflow = 'hidden';

        // Создаём glow-элемент
        const glow = document.createElement('div');
        glow.style.cssText = `
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, hsla(260, 100%, 65%, 0.12), transparent 70%);
            pointer-events: none;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        `;
        card.appendChild(glow);

        // Контент поверх glow
        Array.from(card.children).forEach(child => {
            if (child !== glow) child.style.position = 'relative';
        });

        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            glow.style.left = (e.clientX - rect.left) + 'px';
            glow.style.top = (e.clientY - rect.top) + 'px';
            glow.style.opacity = '1';
        });

        card.addEventListener('mouseleave', () => {
            glow.style.opacity = '0';
        });
    });
})();

// ==========================================
// ПЛАВНЫЙ ПРОГРЕСС-БАР В ШАПКЕ ПРИ СКРОЛЛЕ
// ==========================================
(function () {
    const bar = document.createElement('div');
    bar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        width: 0%;
        background: var(--gradient-primary, linear-gradient(90deg, #6366f1, #0ea5e9));
        z-index: 10001;
        transition: width 0.1s linear;
        pointer-events: none;
    `;
    document.body.appendChild(bar);

    window.addEventListener('scroll', () => {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
        bar.style.width = progress + '%';
    }, { passive: true });
})();

// ==========================================
// 3D INTERACTIVE HERO ELEMENT (THREE.JS) - ЛЕСЕНКА УСПЕХА
// ==========================================
(function () {
    const container = document.getElementById('hero3DElement');
    if (!container) return;

    // Проверка поддержки WebGL
    function isWebGLAvailable() {
        try {
            const canvas = document.createElement('canvas');
            return !!(window.WebGLRenderingContext &&
                (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
        } catch (e) {
            return false;
        }
    }

    // Если WebGL не поддерживается или THREE.js не загрузился — показываем интерактивный CSS-фоллбэк
    if (typeof THREE === 'undefined' || !isWebGLAvailable()) {
        console.log('[Hero3D] WebGL недоступен, показан интерактивный CSS-фоллбэк');
        container.style.position = 'relative';

        // Данные ступенек — те же, что и в 3D версии
        const stepsInfo = [
            { color: '#475569', title: 'Шаблонные сайты (Конкуренты)', desc: 'Здесь топчутся 90% Студий. Однотипный дизайн, низкая конверсия.', icon: '🛡️', bold: 'NDA', sub: '& безопасность' },
            { color: '#3b82f6', title: '1. Аналитика и CJM', desc: 'Строим прочный фундамент. Изучаем нишу и проектируем путь клиента к покупке.', icon: '🇷🇺', bold: 'Студия', sub: 'с России' },
            { color: '#8b5cf6', title: '2. Премиум UI/UX Дизайн', desc: 'Отрисовываем уникальный интерфейс, который сразу транслирует экспертность.', icon: '⭐', bold: '12 лет', sub: 'опыта' },
            { color: '#ec4899', title: '3. Fullstack Разработка', desc: 'Пишем надежный код, интегрируем сложные калькуляторы и CRM системы.', icon: '💎', bold: 'Премиум', sub: 'качество' },
            { color: '#f59e0b', title: '4. Топ-1 и Рост продаж', desc: 'Сайт становится машиной по генерации заявок. Вы на вершине!', icon: '📈', bold: '98%', sub: 'довольных' }
        ];

        // Генерируем HTML ступеньки
        const stepsHTML = stepsInfo.map((s, i) => {
            const bottom = i * 50;
            const leftStep = i * 40;
            const leftLabel = leftStep + 160;
            return `
                <div class="fb-step" data-index="${i}" style="
                    position: absolute; bottom: ${bottom}px; left: ${leftStep}px;
                    width: 140px; height: 30px;
                    background: ${s.color};
                    border-radius: 4px;
                    box-shadow: 0 4px 15px ${s.color}44;
                    cursor: pointer;
                    transition: transform 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
                    opacity: 0;
                    animation: fadeInStep 0.5s ease ${0.3 + i * 0.15}s forwards;
                "></div>
                <div class="fb-label" data-index="${i}" style="
                    position: absolute; bottom: ${bottom + 5}px; left: ${leftLabel}px;
                    color: #fff; font-size: 0.9rem; white-space: nowrap;
                    font-family: 'Inter', sans-serif;
                    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
                    cursor: pointer;
                    transition: transform 0.3s ease;
                    opacity: 0;
                    animation: fadeInStep 0.5s ease ${0.5 + i * 0.15}s forwards;
                ">
                    <b>${s.icon} ${s.bold}</b> <span style="color:#cbd5e1; margin-left: 4px;">${s.sub}</span>
                </div>
            `;
        }).join('');

        container.innerHTML = `
            <!-- Тултип с заголовком и описанием ступеньки -->
            <div class="fb-tooltip" style="
                position: absolute; top: 20px; left: 10px;
                width: 300px; z-index: 100;
                pointer-events: none;
                transition: opacity 0.3s ease, transform 0.3s ease;
            ">
                <h4 class="fb-tooltip-title" style="
                    margin: 0 0 8px 0; font-size: 1.1rem; font-weight: 700;
                    color: #f59e0b; font-family: 'Inter', sans-serif;
                ">4. Топ-1 и Рост продаж</h4>
                <p class="fb-tooltip-desc" style="
                    margin: 0; font-size: 0.85rem; color: #cbd5e1;
                    line-height: 1.5; font-family: 'Inter', sans-serif;
                ">Сайт становится машиной по генерации заявок. Вы на вершине!</p>
            </div>

            <div class="hero-fallback" style="
                width: 100%; height: 100%;
                display: flex; align-items: center; justify-content: center;
                position: relative;
            ">
                <div class="fb-stairs" style="position: relative; width: 320px; height: 300px; transform: perspective(600px) rotateX(15deg) rotateY(-15deg);">
                    ${stepsHTML}
                    <!-- Прыгающая стрелочка над первой ступенью -->
                    <div class="fb-arrow" style="
                        position: absolute; bottom: 35px; left: 55px;
                        font-size: 2rem; color: #ef4444;
                        filter: drop-shadow(0 4px 12px rgba(239, 68, 68, 0.6));
                        animation: bounceArrowFB 2s infinite ease-in-out, fadeInStep 0.5s ease 1s forwards;
                        opacity: 0;
                    ">↓</div>
                    <!-- Звезда на верхушке -->
                    <div style="
                        position: absolute; bottom: 235px; left: 185px;
                        font-size: 2rem;
                        animation: floatStar 3s ease-in-out infinite, fadeInStep 0.5s ease 1.2s forwards;
                        opacity: 0;
                    ">⭐</div>
                </div>
            </div>

            <style>
                @keyframes fadeInStep {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes floatStar {
                    0%, 100% { transform: translateY(0) rotate(0deg); }
                    50% { transform: translateY(-12px) rotate(15deg); }
                }
                @keyframes bounceArrowFB {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-18px); }
                }
                /* Hover на ступеньках */
                .fb-step:hover, .fb-step.active {
                    transform: scale(1.05) translateY(-3px) !important;
                    box-shadow: 0 8px 30px rgba(255,255,255,0.15) !important;
                    filter: brightness(1.3);
                }
                .fb-step.active {
                    filter: brightness(1.2);
                }
                .fb-label:hover {
                    transform: translateX(5px);
                }
            </style>
        `;

        // Интерактивность — hover и клик по ступенькам
        const tooltipTitle = container.querySelector('.fb-tooltip-title');
        const tooltipDesc = container.querySelector('.fb-tooltip-desc');
        const fbSteps = container.querySelectorAll('.fb-step');
        const fbLabels = container.querySelectorAll('.fb-label');
        const fbArrow = container.querySelector('.fb-arrow');
        let activeIndex = 4; // По умолчанию — верхняя ступень

        // Установим активную ступень
        function setActive(index) {
            activeIndex = index;
            const data = stepsInfo[index];
            tooltipTitle.textContent = data.title;
            tooltipTitle.style.color = data.color;
            tooltipDesc.textContent = data.desc;

            // Обновляем подсветку
            fbSteps.forEach((el, i) => {
                el.classList.toggle('active', i === index);
            });
        }

        // По умолчанию верхняя ступень
        setActive(4);

        // Hover на ступенях и надписях
        fbSteps.forEach(el => {
            el.addEventListener('mouseenter', () => {
                setActive(parseInt(el.dataset.index));
                if (fbArrow) fbArrow.style.opacity = '0';
            });
            el.addEventListener('mouseleave', () => {
                if (fbArrow) fbArrow.style.opacity = '1';
            });
            el.addEventListener('click', () => setActive(parseInt(el.dataset.index)));
        });
        fbLabels.forEach(el => {
            el.addEventListener('mouseenter', () => {
                setActive(parseInt(el.dataset.index));
                if (fbArrow) fbArrow.style.opacity = '0';
            });
            el.addEventListener('mouseleave', () => {
                if (fbArrow) fbArrow.style.opacity = '1';
            });
            el.addEventListener('click', () => setActive(parseInt(el.dataset.index)));
        });

        return; // Выходим, не запускаем THREE.js
    }

    const scene = new THREE.Scene();

    // Камера выставлена изометрически (чуть дальше и шире, чтобы не обрезалось)
    const camera = new THREE.PerspectiveCamera(42, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.set(14, 15, 19);
    camera.lookAt(0, 1.5, 0);

    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    } catch (e) {
        console.warn('[Hero3D] Ошибка создания WebGL рендерера:', e.message);
        return;
    }

    // Очищаем canvas
    Array.from(container.children).forEach(c => {
        if (c.tagName === 'CANVAS' || c.classList.contains('hero-float-card') || c.classList.contains('hero-step-label')) c.remove();
    });
    container.style.position = 'relative';
    container.appendChild(renderer.domElement);

    // Функция создания текста со стрелками/линиями
    const createStepLabel = (icon, boldText, smallText, className, lineColor) => {
        const div = document.createElement('div');
        div.className = `hero-step-label ${className}`;
        // Адаптивные размеры шрифтов
        const isMobile = window.innerWidth < 768;
        const iconSize = isMobile ? '1rem' : '1.4rem';
        const boldSize = isMobile ? '0.85rem' : '1.25rem';
        const smallSize = isMobile ? '0.7rem' : '1rem';
        const gap = isMobile ? '4px' : '8px';
        div.style.cssText = `
            position: absolute;
            top: 0; left: 0;
            pointer-events: none;
            z-index: 10;
            color: #fff;
            display: flex;
            align-items: center;
            opacity: 0;
            transition: opacity 1s ease 0.5s;
            will-change: transform;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            font-family: 'Inter', sans-serif;
        `;
        div.innerHTML = `
            <span style="font-size: ${iconSize}; margin-right: ${gap};">${icon}</span>
            <span style="white-space: nowrap;">
                <b style="font-size: ${boldSize}; font-weight: 700;">${boldText}</b>
                <span style="font-size: ${smallSize}; color: #cbd5e1; margin-left: 4px;${isMobile ? ' display:none;' : ''}">${smallText}</span>
            </span>
        `;
        container.appendChild(div);
        setTimeout(() => { div.style.opacity = '1'; }, 100);
        return div;
    };

    // Создаем тексты, передаем цвета, соответствующие ступенькам
    const labelNDA = createStepLabel('🛡️', 'NDA', '& безопасность', 'label-0', '#94a3b8'); // Gray
    const labelRus = createStepLabel('🇷🇺', 'Студия', 'с России', 'label-1', '#3b82f6'); // Blue
    const label12 = createStepLabel('⭐', '12 лет', 'опыта', 'label-2', '#8b5cf6'); // Purple
    const labelPremium = createStepLabel('💎', 'Премиум', 'качество', 'label-4', '#ec4899'); // Pink
    const label98 = createStepLabel('📈', '98%', 'довольных', 'label-5', '#f59e0b'); // Gold

    // Стрелочка убрана — вместо неё активная ступенька увеличивается

    // Освещение
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);

    const dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
    dirLight.position.set(10, 20, 10);
    scene.add(dirLight);

    const dirLight2 = new THREE.DirectionalLight(0xec4899, 0.5); // Розовая подсветка сзади
    dirLight2.position.set(-10, 10, -10);
    scene.add(dirLight2);

    // Данные для ступенек
    const stepsData = [
        {
            title: "Шаблонные сайты (Конкуренты)",
            desc: "Здесь топчутся 90% Студий. Однотипный дизайн, низкая конверсия. Мы оставляем их внизу.",
            color: 0x475569, // slate-600
            emissive: 0x000000,
            yOffset: 0
        },
        {
            title: "1. Аналитика и CJM",
            desc: "Строим прочный фундамент. Изучаем вашу нишу и проектируем путь клиента к покупке.",
            color: 0x3b82f6, // blue-500
            emissive: 0x1e3a8a,
            yOffset: 1.2
        },
        {
            title: "2. Премиум UI/UX Дизайн",
            desc: "Отрисовываем уникальный интерфейс, который сразу транслирует экспертность.",
            color: 0x8b5cf6, // violet-500
            emissive: 0x4c1d95,
            yOffset: 2.4
        },
        {
            title: "3. Fullstack Разработка",
            desc: "Пишем надежный код, интегрируем сложные калькуляторы и CRM системы.",
            color: 0xec4899, // pink-500
            emissive: 0x831843,
            yOffset: 3.6
        },
        {
            title: "4. Топ-1 и Рост продаж",
            desc: "Сайт становится машиной по генерации заявок. Вы на вершине!",
            color: 0xf59e0b, // amber-500
            emissive: 0x78350f,
            yOffset: 4.8
        }
    ];

    const group = new THREE.Group();
    // Сдвигаем группу вниз и поворачиваем, чтобы все части помещались в "экран" без косяков
    group.position.set(-1.0, -2.5, 0);
    group.rotation.y = 0.2; // поворот по оси для красивого ракурса
    scene.add(group);
    const meshes = [];

    // Создаем ступеньки
    stepsData.forEach((data, i) => {
        // Каждая ступень 4 x 0.8 x 4
        const geo = new THREE.BoxGeometry(4, 0.8, 4);
        const mat = new THREE.MeshStandardMaterial({
            color: data.color,
            emissive: data.emissive,
            emissiveIntensity: 0.5,
            roughness: 0.2,
            metalness: 0.2,
            transparent: true,
            opacity: 0.95
        });
        const mesh = new THREE.Mesh(geo, mat);

        // Размещаем лесенкой (по диагонали вверх)
        mesh.position.set(-4 + i * 2, data.yOffset, 4 - i * 2);

        mesh.userData = {
            index: i,
            baseY: data.yOffset,
            data: data
        };
        group.add(mesh);
        meshes.push(mesh);
    });

    // Добавляем "Конкурентов" на самую нижнюю ступень
    const compGroup = new THREE.Group();
    meshes[0].add(compGroup);
    compGroup.position.y = 0.4; // на поверхности ступени

    for (let j = 0; j < 5; j++) {
        const compGeo = new THREE.BoxGeometry(0.5, 0.5, 0.5);
        const compMat = new THREE.MeshStandardMaterial({ color: 0xef4444, roughness: 0.8 });
        const comp = new THREE.Mesh(compGeo, compMat);
        comp.position.set(Math.random() * 3 - 1.5, 0.25, Math.random() * 3 - 1.5);

        // Вращаем их хаотично
        comp.rotation.y = Math.random() * Math.PI;
        compGroup.add(comp);
    }

    // Добавляем "Звезду/Кубок" на самую верхнюю ступень
    const starGeo = new THREE.OctahedronGeometry(0.6);
    const starMat = new THREE.MeshStandardMaterial({ color: 0xffd700, emissive: 0xb8860b, emissiveIntensity: 0.8, metalness: 1, roughness: 0.1 });
    const starMesh = new THREE.Mesh(starGeo, starMat);
    starMesh.position.set(0, 1.2, 0);
    meshes[4].add(starMesh);

    // Добавляем HTML элемент для показа текста (Tooltip) — АДАПТИВНЫЙ
    const tooltip = document.createElement('div');
    const isMobileView = window.innerWidth < 768;
    tooltip.style.cssText = `
        position: absolute;
        top: ${isMobileView ? '5px' : '20px'};
        left: ${isMobileView ? '10px' : '20px'};
        width: ${isMobileView ? '260px' : '340px'};
        color: #fff;
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.4s, transform 0.4s;
        pointer-events: none;
        z-index: 100;
    `;

    const titleSize = isMobileView ? '1rem' : '1.4rem';
    const descSize = isMobileView ? '0.8rem' : '1.05rem';
    tooltip.innerHTML = `
        <h4 id="stairTitle" style="margin: 0 0 ${isMobileView ? '6px' : '12px'} 0; font-size: ${titleSize}; font-weight: 700; color: #a5b4fc;">Название ступени</h4>
        <p id="stairDesc" style="margin: 0; font-size: ${descSize}; color: #cbd5e1; line-height: 1.5;">Описание процесса.</p>
    `;
    container.appendChild(tooltip);

    // Взаимодействие
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    let hoveredIndex = -1;
    let selectedIndex = 0; // Начинаем с нижней ступеньки (было 4)
    let isUserControlling = false; // Флаг: пользователь управляет мышкой
    let autoSwitchTimer = null; // Таймер автопереключения
    let resumeAutoTimer = null; // Таймер возврата к авто

    function updateTooltip(index) {
        if (index < 0 || index >= stepsData.length) return;
        const data = stepsData[index];
        const titleEl = document.getElementById('stairTitle');
        const descEl = document.getElementById('stairDesc');
        if (!titleEl || !descEl) return; // Защита от null
        titleEl.innerText = data.title;
        titleEl.style.color = '#' + data.color.toString(16).padStart(6, '0');
        descEl.innerText = data.desc;

        tooltip.style.opacity = '1';
        tooltip.style.transform = 'translateY(0)';
    }

    // === АВТОПЕРЕКЛЮЧЕНИЕ СТУПЕНЕК ===
    function startAutoSwitch() {
        stopAutoSwitch(); // Очищаем предыдущий таймер
        autoSwitchTimer = setInterval(() => {
            if (isUserControlling) return; // Не мешаем пользователю
            selectedIndex = (selectedIndex + 1) % stepsData.length; // Следующая ступенька (по кругу)
            updateTooltip(selectedIndex);
        }, 2500); // Каждые 2.5 секунды
    }

    function stopAutoSwitch() {
        if (autoSwitchTimer) {
            clearInterval(autoSwitchTimer);
            autoSwitchTimer = null;
        }
    }

    // Запускаем автопереключение с небольшой задержкой
    setTimeout(() => {
        updateTooltip(selectedIndex);
        startAutoSwitch();
    }, 800);

    container.addEventListener('mousemove', (event) => {
        const rect = container.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        // Вращаем всю группу легонько от движения мыши (эффект параллакса)
        group.rotation.y = 0.2 + mouse.x * 0.15;
        group.rotation.x = -mouse.y * 0.15;

        // Рэйкаст
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(meshes);

        if (intersects.length > 0) {
            const currentHover = intersects[0].object.userData.index;
            if (hoveredIndex !== currentHover) {
                hoveredIndex = currentHover;
                selectedIndex = currentHover; // Авто-выбор при наведении
                isUserControlling = true; // Пользователь управляет
                stopAutoSwitch(); // Останавливаем авто
                updateTooltip(selectedIndex);
                container.style.cursor = 'pointer';

                // Очищаем таймер возврата
                if (resumeAutoTimer) { clearTimeout(resumeAutoTimer); resumeAutoTimer = null; }
            }
        } else {
            if (hoveredIndex !== -1) {
                hoveredIndex = -1;
                container.style.cursor = 'default';
            }
        }
    });

    container.addEventListener('mouseleave', () => {
        hoveredIndex = -1;
        container.style.cursor = 'default';
        group.rotation.y = 0.2; // Возврат к базовому повороту
        group.rotation.x = 0;

        // Через 2 секунды после ухода мыши — возобновляем автопереключение
        if (resumeAutoTimer) clearTimeout(resumeAutoTimer);
        resumeAutoTimer = setTimeout(() => {
            isUserControlling = false;
            startAutoSwitch();
        }, 2000);
    });

    // Убираем подсветку при тапе на мобильных
    container.style.webkitTapHighlightColor = 'transparent';
    container.style.webkitTouchCallout = 'none';
    container.style.userSelect = 'none';

    // Звук шага через Web Audio API (без внешних файлов)
    let audioCtx = null;
    function playStepSound() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            // Создаём короткий "thud" звук — имитация шага по ступеньке
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.frequency.setValueAtTime(120, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(60, audioCtx.currentTime + 0.1);
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.15);
        } catch (e) { /* Браузер не поддерживает Web Audio */ }
    }

    // На клик — выбираем ступень + звук
    container.addEventListener('click', () => {
        if (hoveredIndex !== -1) {
            selectedIndex = hoveredIndex;
            updateTooltip(selectedIndex);
            playStepSound(); // Звук только при ручном нажатии
        }
    });

    // Тач-события для мобильных
    container.addEventListener('touchstart', (event) => {
        const touch = event.touches[0];
        const rect = container.getBoundingClientRect();
        mouse.x = ((touch.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((touch.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(meshes);

        if (intersects.length > 0) {
            const tapped = intersects[0].object.userData.index;
            selectedIndex = tapped;
            hoveredIndex = tapped;
            isUserControlling = true;
            stopAutoSwitch();
            updateTooltip(selectedIndex);
            playStepSound(); // Звук при тапе

            // Возобновляем авто через 3 секунды
            if (resumeAutoTimer) clearTimeout(resumeAutoTimer);
            resumeAutoTimer = setTimeout(() => {
                isUserControlling = false;
                startAutoSwitch();
            }, 3000);
        }
    }, { passive: true });

    // Resize
    window.addEventListener('resize', () => {
        if (!container || container.clientWidth === 0) return;
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    });

    const clock = new THREE.Clock();

    function animate() {
        requestAnimationFrame(animate);

        const delta = clock.getDelta();
        const time = clock.getElapsedTime();

        // СИНХРОНИЗАЦИЯ ЛЕЙБЛОВ С 3D
        const syncLabelPos = (label, meshIndex, yOffset, xOffset, zOffset) => {
            if (!label || meshes.length <= meshIndex) return;
            const mesh = meshes[meshIndex];
            const vector = new THREE.Vector3();
            vector.setFromMatrixPosition(mesh.matrixWorld);
            vector.y += yOffset;
            vector.x += xOffset;
            vector.z += zOffset;
            vector.project(camera);
            // Адаптивные отступы для десктопа и мобильных
            const isMob = window.matchMedia('(max-width: 768px)').matches;
            const xShift = isMob ? 0 : -25; // На мобильных — без сдвига, на десктопе -25px
            const yShift = isMob ? -30 : -70; // На мобильных меньше подъём
            const x = (vector.x * 0.5 + 0.5) * container.clientWidth + xShift;
            const y = (vector.y * -0.5 + 0.5) * container.clientHeight + yShift;
            label.style.transform = `translate(0%, -50%) translate(${x}px, ${y}px)`;
        };

        // xOffset=6.5 — отодвинули от ступенек
        syncLabelPos(labelNDA, 0, -0.3, 6.5, 2.5);
        syncLabelPos(labelRus, 1, -0.3, 6.5, 2.5);
        syncLabelPos(label12, 2, -0.3, 6.5, 2.5);
        syncLabelPos(labelPremium, 3, -0.3, 6.5, 2.5);
        syncLabelPos(label98, 4, -0.3, 6.5, 2.5);

        // Анимация ступенек — активная увеличивается
        meshes.forEach((mesh, index) => {
            const isHovered = (index === hoveredIndex);
            const isSelected = (index === selectedIndex);
            const isActive = isHovered || isSelected;

            // Продавливаем вниз, если выбрано или наведено
            const targetY = mesh.userData.baseY + (isActive ? -0.1 : 0);
            mesh.position.y += (targetY - mesh.position.y) * 12 * delta;

            // Увеличиваем активную ступеньку
            const targetScale = isActive ? 1.15 : 1.0;
            const currentScale = mesh.scale.x;
            const newScale = currentScale + (targetScale - currentScale) * 8 * delta;
            mesh.scale.set(newScale, newScale, newScale);

            // Если ступень выбрана — она светится ярче
            mesh.material.emissiveIntensity = isActive ? 0.9 : 0.4;
        });

        // Анимация звезды на верхушке
        starMesh.rotation.y += 1.5 * delta;
        starMesh.rotation.x += 1.0 * delta;
        starMesh.position.y = 1.2 + Math.sin(time * 3) * 0.2;

        renderer.render(scene, camera);
    }

    // Доп. адаптивность
    setTimeout(() => {
        if (container.clientWidth > 0) {
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        }
    }, 100);

    animate();
})();

