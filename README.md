# Molozin.ru - Профессиональная веб-студия

Премиальный сайт веб-студии по созданию, редактированию и продвижению сайтов международного уровня.

## 🚀 Особенности

### Дизайн и UX
- ✨ **Премиальный дизайн** уровня ведущих мировых агентств
- 🎨 **Современные визуальные эффекты**: Glassmorphism, градиенты, анимации
- 📱 **Полностью адаптивный** дизайн для всех устройств
- ⚡ **Плавные анимации** и микро-взаимодействия
- 🌈 **Премиальная цветовая палитра** с использованием HSL

### SEO и производительность
- 🔍 **Полная SEO-оптимизация**:
  - Meta теги (title, description, keywords)
  - Open Graph разметка для социальных сетей
  - Twitter Card разметка
  - Schema.org JSON-LD разметка
  - Семантичная HTML5 структура
  - Оптимизированная структура заголовков (H1-H6)
  
- 📊 **Инструменты для аналитики**:
  - Готовность к интеграции Google Analytics
  - Готовность к интеграции Яндекс.Метрики
  - Schema.org разметка для лучшей индексации
  
- ⚡ **Производительность**:
  - Оптимизированный CSS и JavaScript
  - Lazy loading для изображений
  - Минимальное количество HTTP-запросов
  - Быстрая загрузка страниц

### Безопасность и анти-спам
- 🛡️ **Продвинутая анти-спам защита**:
  - Отслеживание времени заполнения формы
  - Анализ взаимодействий пользователя
  - Отслеживание движений мыши
  - Проверка на подозрительные паттерны
  - Валидация email адресов
  - Маркировка подозрительных отправлений
  
- 🔒 **Безопасность**:
  - XSS защита
  - CSRF защита
  - Защита заголовков HTTP
  - Санитизация входных данных

### Функциональность
- 📧 **Email интеграция**:
  - Оптимизировано для хостинга Reg.ru
  - Использование флага -f для Envelope Sender
  - Логирование всех отправлений
  - Опциональная интеграция с Telegram Bot
  
- 📱 **Социальные сети**:
  - Интеграция с VK, Telegram, WhatsApp
  - Open Graph теги для красивых превью
  - Иконки социальных сетей
  
- 🎯 **Формы**:
  - Валидация на стороне клиента и сервера
  - Красивые уведомления об успехе/ошибке
  - Отслеживание источника заявки

## 📁 Структура проекта

```
Molozin.ru/
├── index.html          # Главная страница
├── styles.css          # Стили сайта
├── script.js           # JavaScript логика
├── send-email.php      # Обработчик формы
├── sitemap.xml         # Карта сайта
├── robots.txt          # Правила для поисковых роботов
├── logs/               # Логи форм
│   └── contact-form.log
└── assets/             # Ресурсы (изображения, иконки)
```

## 🎨 Цветовая палитра

- **Primary**: hsl(260, 100%, 65%) - Фиолетовый
- **Secondary**: hsl(200, 100%, 60%) - Голубой
- **Accent**: hsl(340, 100%, 60%) - Розовый
- **Background**: hsl(260, 20%, 8%) - Темный
- **Text**: hsl(0, 0%, 95%) - Светлый

## 🔧 Настройка

### 1. Email настройки

Откройте `send-email.php` и убедитесь, что настройки корректны:

```php
$to = 'mdn77@yandex.ru'; // Адрес получателя
$params = '-f3d@best70.ru'; // Envelope Sender для Reg.ru
```

### 2. Telegram уведомления (опционально)

Для включения уведомлений в Telegram:

1. Создайте бота через @BotFather
2. Получите токен бота
3. Узнайте свой Chat ID через @userinfobot
4. Замените в `send-email.php`:

```php
$botToken = 'YOUR_BOT_TOKEN';
$chatId = 'YOUR_CHAT_ID';
```

### 3. Создание папки для логов

```bash
mkdir logs
chmod 755 logs
```

### 4. Фавиконы

Разместите фавиконы в корне сайта:
- `favicon-16x16.png`
- `favicon-32x32.png`
- `apple-touch-icon.png`

### 5. Open Graph изображение

Создайте изображение для социальных сетей:
- `assets/og-image.jpg` (1200x630px)
- `assets/twitter-image.jpg` (1200x600px)

## 📊 SEO чеклист

- ✅ Уникальные title и description для каждой страницы
- ✅ Семантичная HTML5 разметка
- ✅ Schema.org JSON-LD
- ✅ Open Graph и Twitter Card
- ✅ Sitemap.xml
- ✅ Robots.txt
- ✅ Адаптивный дизайн (Mobile-First)
- ✅ Быстрая загрузка страниц
- ✅ Alt теги для всех изображений
- ✅ Оптимизированные заголовки (H1-H6)

## 🚀 Запуск

1. Загрузите все файлы на хостинг
2. Убедитесь, что PHP >= 7.4
3. Настройте права доступа для папки `logs/`
4. Обновите email настройки в `send-email.php`
5. Проверьте работу формы обратной связи

## 📱 Адаптивность

Сайт полностью адаптивен и отлично выглядит на:
- 📱 Мобильных устройствах (320px+)
- 💻 Планшетах (768px+)
- 🖥️ Десктопах (1024px+)
- 🖥️ Больших экранах (1920px+)

## 🔐 Безопасность

- XSS защита через htmlspecialchars()
- Валидация всех входных данных
- Защита от CSRF атак
- Secure HTTP заголовки
- Логирование всех действий

## 📈 Аналитика

Для подключения аналитики добавьте перед `</head>`:

### Google Analytics 4
```html
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

### Яндекс.Метрика
```html
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(XXXXXXXX, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
```

## 🎯 Контакты

- 📧 Email: mdn77@yandex.ru
- 📱 Телефон: +7 (923) 406-44-41
- 📱 Телефон: +7 (3822) 93-63-62
- 🌐 Сайт: https://molozin.ru

## 📝 Лицензия

© 2000-2026 Molozin.ru. Все права защищены.

---

**Разработано с ❤️ для Molozin.ru**
