# Molozin.ru — Премиальная веб-студия

> **Стек:** PHP 8.x + vanilla JS + CSS + SQLite  
> **Хостинг:** Reg.ru (shared hosting)  
> **Цель:** поисковый трафик → клиенты

## 🚀 Особенности

### Дизайн

- Премиальный тёмный дизайн с градиентами и glassmorphism
- Полностью адаптивный для всех устройств (320px–2560px+)
- Плавные анимации и микро-взаимодействия
- Three.js 3D-эффекты в Hero-секции

### SEO

- ЧПУ-адреса: `/blog/`, `/blog/{slug}/`, `/uslugi/{slug}/`
- Schema.org JSON-LD: хлебные крошки, статья, блог, организация
- Canonical URL на всех страницах
- Open Graph и Twitter Card разметка
- RSS-фид блога: `/blog/rss.xml`
- Мультиязычность: 10 языков (ru, en, zh, es, hi, ar, bn, pt, ja, pa)

### Безопасность

- XSS-защита: `htmlspecialchars()` на всём выводе
- Антиспам-защита форм: honeypot + анализ времени
- Заголовки: X-Content-Type-Options, X-Frame-Options, X-XSS-Protection

## 📁 Структура проекта

```
molozin.ru/
├── index.php              # Главная страница (SEO-лендинги через /uslugi/{slug}/)
├── blog.php               # Блог: список /blog/ и статья /blog/{slug}/
├── blog_rss.php           # RSS-фид блога (/blog/rss.xml)
├── privacy.php            # Политика конфиденциальности
├── terms.php              # Условия использования
├── 404.php                # Страница ошибки 404 (тёмный premium-дизайн)
├── sitemap.php            # Динамическая карта сайта (/sitemap.xml)
├── send-email.php         # Обработчик формы обратной связи
├── i18n.php               # Система мультиязычности (10 языков)
├── i18n_cache.json        # Кэш переводов (игнорируется git)
│
├── styles.css             # Основные стили сайта
├── script.js              # Основной JavaScript
├── tracker.js             # Трекер аналитики
│
├── .htaccess              # Конфигурация Apache: ЧПУ, 404, безопасность, кэширование
├── robots.txt             # Правила для поисковых роботов
├── site.webmanifest       # PWA-манифест
│
├── includes/              # PHP-компоненты (подключаются в index.php)
│   ├── header.php         # Шапка + навигация + языковой переключатель
│   ├── hero.php           # Hero-секция с 3D (Three.js)
│   ├── clients.php        # Логотипы клиентов (marquee)
│   ├── portfolio.php      # Портфолио / кейсы
│   ├── services.php       # Услуги
│   ├── ecosystem.php      # Экосистема продуктов
│   ├── process.php        # Процесс работы
│   ├── pricing.php        # Тарифы
│   ├── testimonials.php   # Отзывы (блок неактивен)
│   ├── contact.php        # Форма обратной связи
│   ├── footer.php         # Подвал сайта
│   └── seo-schemas.php    # JSON-LD схемы (Organization, Breadcrumbs, FAQ)
│
├── css/                   # Стили
│   └── blog.css           # Стили блога (вынесены из blog.php)
│
├── assets/                # Статические ресурсы
│   ├── flags/             # SVG-флаги для языкового переключателя (10 шт.)
│   ├── *.png, *.webp      # Превью портфолио, иконки
│   ├── og-image.jpg       # Open Graph изображение (1200×630)
│   └── twitter-image.jpg  # Twitter Card изображение (1200×600)
│
├── api/                   # API-эндпоинты
│   └── track.php          # Трекер аналитики
│
└── admin/                 # Административная панель
    ├── index.php          # Главная админки
    ├── auth.php           # Аутентификация
    ├── db.php             # Подключение к SQLite + создание таблиц
    ├── db.sqlite          # База данных (игнорируется git)
    ├── api_save_seo.php   # API сохранения SEO-данных
    └── migrate_slugs.php  # Миграция slug'ов блога на ЧПУ
```

## 🎨 Цветовая палитра

- **Primary**: `hsl(260, 100%, 65%)` — фиолетовый
- **Secondary**: `hsl(200, 100%, 60%)` — голубой
- **Accent**: `hsl(340, 100%, 60%)` — розовый
- **Background**: `hsl(260, 20%, 8%)` — тёмный
- **Text**: `hsl(0, 0%, 95%)` — светлый

## 📊 SEO-структура

| Страница      | URL               | Приоритет |
| ------------- | ----------------- | --------- |
| Главная       | `/`               | 1.0       |
| SEO-лендинги  | `/uslugi/{slug}/` | 0.8       |
| Блог (список) | `/blog/`          | 0.9       |
| Блог (статья) | `/blog/{slug}/`   | 0.7       |
| Privacy       | `/privacy/`       | 0.5       |
| Terms         | `/terms/`         | 0.5       |

## 🔧 Запуск

1. Загрузите файлы на хостинг (Reg.ru shared)
2. Убедитесь, что PHP >= 8.0 с модулями PDO SQLite и mbstring
3. База данных `admin/db.sqlite` создаётся автоматически при первом запросе
4. Для миграции slug'ов блога на ЧПУ запустите: `php admin/migrate_slugs.php`

## 📱 Breakpoints адаптивности

| Breakpoint | Устройство                    |
| ---------- | ----------------------------- |
| 320px      | Маленькие телефоны            |
| 375px      | iPhone SE                     |
| 428px      | iPhone Pro Max                |
| 768px      | Планшеты                      |
| 1024px     | iPad Pro / маленькие десктопы |
| 1280px     | Стандартные десктопы          |
| 1440px     | Большие мониторы              |
| 1920px     | Full HD                       |
| 2560px+    | 4K / Ultrawide                |

## 🎯 Контакты

- 📧 Email: mdn77@yandex.ru
- 📱 Телефон: +7 (923) 406-44-41
- 🌐 Сайт: https://molozin.ru

---

© 2000–2026 Molozin.ru. Все права защищены.
