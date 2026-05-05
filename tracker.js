// Дичайщая аналитика поведения
(function () {
    let scrollDepth = 0;

    // Отслеживание скролла (макс. глубина в процентах)
    window.addEventListener('scroll', () => {
        let maxScroll = document.documentElement.scrollHeight - window.innerHeight;
        let currentScroll = window.pageYOffset;
        let p = Math.round((currentScroll / maxScroll) * 100);
        if (p > scrollDepth) scrollDepth = p;
    }, { passive: true });

    // Отправка пинга (1 раз в 15 сек)
    setInterval(() => {
        fetch('/api/track.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: 'ping',
                page: window.location.pathname,
                screen: `${window.screen.width}x${window.screen.height}`,
                scrollDepth: scrollDepth
            })
        }).catch(e => { });
    }, 15000);

    // Первичный пинг сразу
    setTimeout(() => {
        fetch('/api/track.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: 'ping',
                page: window.location.pathname,
                screen: `${window.screen.width}x${window.screen.height}`,
                scrollDepth: 0
            })
        }).catch(e => { });
    }, 2000);

    // Отслеживание важных кликов
    document.addEventListener('click', (e) => {
        // Ловим клики по ссылкам и кнопкам
        const clickable = e.target.closest('a, button, .case-card');
        if (clickable) {
            let elName = clickable.tagName.toLowerCase();
            if (clickable.id) elName += '#' + clickable.id;
            if (clickable.className && typeof clickable.className === 'string') {
                elName += '.' + clickable.className.split(' ').join('.');
            }
            if (clickable.getAttribute('href')) elName += ` [href=${clickable.getAttribute('href')}]`;

            fetch('/api/track.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    type: 'click',
                    page: window.location.pathname,
                    element: elName,
                    x: e.clientX,
                    y: e.clientY
                })
            }).catch(e => { });
        }
    });
})();
