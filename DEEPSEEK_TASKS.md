# 🚀 MOLOZIN.RU — ПОЛНАЯ ПЕРЕРАБОТКА САЙТА
# Задание для DeepSeek-V4-Pro

> **Репозиторий:** https://github.com/mdn77/molozin
> **Хостинг:** Reg.ru (shared, PHP 8.x, SQLite)
> **Цель №1:** Поисковый трафик и клиенты
> **Дата:** 2026-05-05

---

## 📋 ПРАВИЛА РАБОТЫ

1. **Все коммиты делай в GitHub** — работаем через репозиторий `mdn77/molozin`
2. **Комментарии в коде — на русском языке**
3. **Без сторонних библиотек** — не добавляй npm, composer, React и т.д. Проект на чистом PHP + vanilla JS + CSS
4. **Сохраняй существующий дизайн** — не ломай текущую визуальную тему (тёмный premium-дизайн, градиенты, glassmorphism)
5. **Не трогай** файлы: `admin/db.sqlite`, `i18n.php`, `i18n_cache.json`, `THREE.js`
6. **Каждая задача = отдельный коммит** с понятным описанием на русском
7. **Тестируй** — перед коммитом проверяй, что PHP-файлы не выдают Fatal Error
8. **Файлы с расширениями `.ps1`, `.py`, `_debug*.php`, `_test*.php`, `blog_debug*.php`** — это утилиты, их НЕ деплоить, но можно удалить из продакшена

---

## 🔥 ЗАДАЧА 1 (КРИТИЧЕСКАЯ): ЧПУ для блога

### Проблема
Сейчас адреса блога выглядят так:
```
/blog.php?article=gen-0a3df2126236e4e2a91c3ddf55d73e96f-1772351636
/blog.php?article=seo-2026-ai-behavior
```
Это **убивает SEO**. Яндекс и Google плохо индексируют GET-параметры с хешами.

### Что нужно сделать

1. **Новый формат URL блога:**
   - Список статей: `/blog/` (вместо `/blog.php`)
   - Статья: `/blog/kak-uvelichit-konversiyu-sayta/` (вместо `blog.php?article=slug`)

2. **Обновить `.htaccess`** — добавить RewriteRule:
   ```apache
   # Блог: ЧПУ
   RewriteRule ^blog/?$ blog.php [L,QSA]
   RewriteRule ^blog/([a-zA-Z0-9_-]+)/?$ blog.php?article=$1 [L,QSA]
   ```

3. **Массовая замена slug'ов в БД** — создать миграционный скрипт `admin/migrate_slugs.php`:
   - Пройтись по всем записям `blog_posts`
   - Заменить slug'и вида `gen-0a3df2126236e4e2a91c3ddf55d73e96f-1772351636` на нормальные ЧПУ
   - Генерировать slug из `title` через транслитерацию (кириллица → латиница)
   - Формат: `kak-sozdat-sajt-dlya-biznesa` (только a-z, 0-9, дефис, до 80 символов)
   - Пример транслита: А→a, Б→b, В→v, Г→g, Д→d, Е→e, Ё→yo, Ж→zh, З→z, И→i, Й→j, К→k, Л→l, М→m, Н→n, О→o, П→p, Р→r, С→s, Т→t, У→u, Ф→f, Х→kh, Ц→ts, Ч→ch, Ш→sh, Щ→shch, Ъ→, Ы→y, Ь→, Э→e, Ю→yu, Я→ya

4. **301 редиректы** со старых URL на новые (в `.htaccess` или в `blog.php`):
   ```php
   // Если slug содержит "gen-" — найти правильный slug и сделать 301
   if (strpos($slug, 'gen-') === 0) {
       // найти статью по старому slug, перенаправить на новый
       header("Location: /blog/" . $new_slug . "/", true, 301);
       exit;
   }
   ```

5. **Обновить все внутренние ссылки** на блог:
   - В `index.php`: ссылка на блог → `/blog/`
   - В `blog.php`: ссылки на статьи → `/blog/{slug}/`
   - В навигации: `/blog.php` → `/blog/`
   - В related articles

6. **Canonical URL** в `blog.php`:
   ```html
   <link rel="canonical" href="https://molozin.ru/blog/<?= htmlspecialchars($slug) ?>/">
   ```

---

## 🔥 ЗАДАЧА 2: Очистка репозитория от мусора

### Удалить из корня (эти файлы не нужны в продакшене):

```
# Дебаг-файлы
blog_debug2.php, blog_debug3.php, blog_test.php
debug_db.php, debug_db2.php, debug_db3.php, debug_db4.php
test_encode.php, check_db_counts.php, check_sizes.php, check_slugs.php
temp_auto.php, temp_blog.html

# Старые скрипты деплоя (не для GitHub)
deploy.ps1, deploy2.ps1, deploy_*.ps1, _upl.ps1, _upl2.ps1
upload_*.ps1, push_*.ps1, check_*.ps1, find_*.ps1
fix_*.ps1, extract_*.ps1, get*.ps1, make_*.ps1
reverse.ps1, ping_all.ps1, fetch_urls.ps1, generate_*.ps1

# SQL дампы (конфиденциальные данные!)
blog_posts_dump.sql, seo_landings_dump.sql
db.zip, live_db.zip, webp.zip

# Временные SQLite (не основная БД)
check_current.sqlite, check_db.sqlite, check_server_db.sqlite
check_sitemap_db.sqlite, current_db.sqlite, db.sqlite (корневой)
db_temp.sqlite, final_db.sqlite, live_db.sqlite

# Прочий мусор
downloaded_blog_page.html, index.fixed.php
layout_v2.txt, 150.txt, top150*.txt
molozin_150_urls.txt, check_sitemap.txt
sitemap_*.txt, sitemap_dl*.xml, dl_sitemap.xml
new_sitemap.xml, new_sitemap2.xml, sitemap_test.xml
ftp_commands.txt

# Утилиты разработки (не для GitHub)
convert_webp.py, cwebp.exe
update_3dbest70_footer.*, enrich_faq.php
fix_gen_slugs.php, fix_real_db.php, restore_and_fix.php
bulk_gen_150.php, regen_landings.php, seed_blogs.php
get_top_urls.php, swap_db.php, db_mover.php, unzip_db.php
ping_search_engines.php (перенести в admin/, если полезен)

# Старые дебаг в admin/
admin/fix_dates.php, admin/fix_views.php
admin/ensure_portfolio.php, admin/regen_ui.php
admin/seed_blog.php, admin/upgrade_blog.php
```

### Создать `.gitignore`:
```gitignore
# БД
*.sqlite
*.sql
*.zip

# Логи
logs/
*.log

# IDE
.vscode/
.idea/

# Системное
.DS_Store
Thumbs.db

# Деплой-скрипты
*.ps1

# Утилиты
cwebp.exe
*.py

# Кеши
i18n_cache.json

# Временные файлы
*.bak
*.tmp
*.old
```

---

## 🔥 ЗАДАЧА 3: SEO-оптимизация для поискового трафика

### 3.1. Исправить sitemap.php
- Должен динамически генерировать XML-сайтмап из:
  - Главная `/`
  - Все SEO-лендинги из `seo_landings` → `/uslugi/{slug}/`
  - Все статьи блога → `/blog/{slug}/`
  - `/privacy/`, `/terms/`
- Формат lastmod: `Y-m-d`
- Приоритеты: главная 1.0, услуги 0.8, блог 0.7, прочее 0.5

### 3.2. Добавить `<link rel="canonical">` на все страницы
- `index.php` — есть, проверить корректность
- `blog.php` (список) — `https://molozin.ru/blog/`
- `blog.php` (статья) — `https://molozin.ru/blog/{slug}/`

### 3.3. Breadcrumbs (Schema.org)
Добавить JSON-LD хлебные крошки:
- Блог: `Главная → Блог`
- Статья: `Главная → Блог → {Название статьи}`
- Услуга: `Главная → Услуги → {Название услуги}`

### 3.4. Мета-теги для блога
- Каждая статья должна иметь уникальные `title` и `description`
- Open Graph: `og:type=article`, `og:article:published_time`, `og:article:author`
- В `<head>`: `<link rel="alternate" type="application/rss+xml" href="/blog/rss.xml">`

### 3.5. RSS/Atom фид для блога
Создать `blog_rss.php` (доступ по `/blog/rss.xml` через .htaccess):
- Последние 20 статей
- Полный формат RSS 2.0

### 3.6. Исправить robots.txt
```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /logs/

User-agent: Yandex
Allow: /
Disallow: /admin/
Disallow: /api/
Clean-param: lang /

User-agent: Googlebot
Allow: /

Sitemap: https://molozin.ru/sitemap.xml
Host: https://molozin.ru
```

---

## 🔥 ЗАДАЧА 4: Адаптивность — все дисплеи

### 4.1. Проверить и исправить breakpoints в `styles.css`:
- `320px` — маленькие телефоны
- `375px` — iPhone SE / стандарт
- `428px` — iPhone Pro Max
- `768px` — планшеты
- `1024px` — маленькие десктопы / iPad Pro
- `1280px` — стандартные десктопы
- `1440px` — большие мониторы
- `1920px` — Full HD
- `2560px+` — 4K / ultrawide

### 4.2. Конкретные проблемы для исправления:

**Hero секция (мобильные):**
- 3D-элемент (`#hero3DElement`) перекрывает текст на маленьких экранах
- Кнопки CTA (`hero-cta`) имеют жёсткие inline-стили — вынести в CSS
- `margin-left: -50px` на 3D-элементе ломает мобильную вёрстку

**Навигация (мобильные):**
- Проверить работу мобильного меню (`nav-toggle`)
- Убедиться, что телефон + кнопка CTA в шапке не перекрываются

**Портфолио (планшет):**
- Карточки `case-card` — проверить grid на 768px
- Модальное окно портфолио — должно быть fullscreen на мобильных

**Блог (мобильные):**
- Grid `news-layout-grid` — на 375px карточки должны быть в 1 колонку
- Шрифт заголовков (`Playfair Display`) — проверить clamp()

**Экосистема (все размеры):**
- Grid `eco-grid` с inline-стилями `grid-template-columns: repeat(3, 1fr)` — добавить responsive fallback
- На мобильных должен быть 1 колонка

**Pricing секция:**
- Таблица тарифов — на мобильных горизонтальный скролл или stack

**Footer:**
- `footer-grid` — 4 колонки → 2 на планшете → 1 на мобильном

### 4.3. Общие правила:
- Убрать ВСЕ inline-стили из HTML — перенести в `styles.css`
- Использовать `clamp()` для шрифтов
- Использовать `min()`, `max()` для padding/margin
- Container: `max-width: min(1200px, 90vw)`
- Все изображения: `max-width: 100%; height: auto;`

---

## 🔥 ЗАДАЧА 5: Рефакторинг кода

### 5.1. index.php (91KB — слишком большой!)
Разделить на компоненты:
```
includes/
├── header.php        — шапка + навигация + языковой дропдаун
├── hero.php          — Hero-секция
├── clients.php       — Логотипы клиентов (marquee)
├── portfolio.php     — Портфолио/кейсы
├── services.php      — Услуги
├── ecosystem.php     — Экосистема продуктов
├── process.php       — Процесс работы
├── pricing.php       — Тарифы
├── testimonials.php  — Отзывы
├── contact.php       — Форма обратной связи
├── footer.php        — Подвал
└── seo-schemas.php   — Все JSON-LD схемы
```

Тогда `index.php` станет:
```php
<?php
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/admin/db.php';
// SEO-логика...
include 'includes/header.php';
include 'includes/hero.php';
include 'includes/clients.php';
// и т.д.
include 'includes/footer.php';
?>
```

### 5.2. styles.css (155KB — гигантский!)
Разделить на модули:
```
css/
├── variables.css     — CSS-переменные, палитра, шрифты
├── base.css          — Reset, типография, общие стили
├── header.css        — Шапка, навигация
├── hero.css          — Hero-секция
├── sections.css      — Все секции (services, portfolio, pricing...)
├── cards.css         — Карточки (case-card, service-card, eco-card...)
├── forms.css         — Формы, кнопки, инпуты
├── animations.css    — Все анимации (@keyframes)
├── responsive.css    — Все медиа-запросы в одном месте
└── blog.css          — Стили блога (вынести из blog.php <style>)
```

В `index.php` подключать через:
```html
<link rel="stylesheet" href="/css/variables.css">
<link rel="stylesheet" href="/css/base.css">
<!-- и т.д. -->
```
Или лучше: объединить через PHP для production:
```php
<!-- DEV: отдельные файлы, PROD: один минифицированный -->
<link rel="stylesheet" href="/css/bundle.css?v=<?= filemtime('css/bundle.css') ?>">
```

### 5.3. script.js (77KB — много!)
Разделить:
```
js/
├── core.js           — Инициализация, утилиты
├── navigation.js     — Мобильное меню, скролл, хедер
├── animations.js     — IntersectionObserver, reveal-анимации
├── hero.js           — Частицы, 3D-элемент, mouse-follow
├── forms.js          — Валидация, отправка форм, антиспам
├── modal.js          — Модальные окна портфолио
├── theme.js          — Переключение тем light/dark
└── lang.js           — Языковой дропдаун
```

### 5.4. blog.php — убрать inline стили
- Весь `<style>` блок (110+ строк) перенести в `css/blog.css`
- Дублирование навигации с `index.php` — вынести в `includes/header.php`
- Дублирование footer — вынести в `includes/footer.php`

### 5.5. Убрать inline-стили из HTML
В `index.php` огромное количество inline `style="..."`:
- Секция `clients-logos-section` — полностью через inline
- Секция `ecosystem` — все `eco-card-new` через inline
- `eco-grid` — inline grid
- Все `onmouseover/onmouseout` — заменить на CSS `:hover`

---

## 🔥 ЗАДАЧА 6: Производительность

### 6.1. Lazy-loading для изображений
- Все `<img>` ниже fold должны иметь `loading="lazy"`
- Первый видимый экран — `loading="eager"` или без атрибута

### 6.2. Preload критических ресурсов
```html
<link rel="preload" href="/css/variables.css" as="style">
<link rel="preload" href="/js/core.js" as="script">
```

### 6.3. WebP везде
- Все изображения в `assets/` должны иметь WebP-версию
- Использовать `<picture>` с fallback:
```html
<picture>
  <source srcset="/assets/image.webp" type="image/webp">
  <img src="/assets/image.jpg" alt="...">
</picture>
```

### 6.4. Минификация
- CSS: минифицировать при деплое
- JS: минифицировать при деплое
- HTML: `minify_html_output()` уже есть — ОК

### 6.5. Three.js (603KB!)
- Подключать через CDN вместо локального файла
- `<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" defer></script>`
- Или загружать асинхронно только для Hero-секции

---

## 🔥 ЗАДАЧА 7: SSL-сертификат

> ⚠️ ВНИМАНИЕ: SSL-сертификат ИСТЁК! Сайт показывает ошибку безопасности.

Нужно обновить сертификат на Reg.ru:
- Зайти в панель управления → SSL → Обновить/Перевыпустить
- Или использовать Let's Encrypt

---

## 🔥 ЗАДАЧА 8: Мелкие, но важные улучшения

### 8.1. Страница 404
Создать красивую страницу `/404.php`:
- В стиле сайта (тёмный, с градиентом)
- Кнопка «Вернуться на главную»
- Ссылки на основные разделы
- Подключить в `.htaccess`: `ErrorDocument 404 /404.php`

### 8.2. Мета robots для непубличных страниц
```php
// privacy.php, terms.php
<meta name="robots" content="noindex, follow">
```

### 8.3. Structured Data для FAQ
Проверить, что FAQ Schema корректна и не содержит XSS через `addslashes()`:
```php
// Вместо addslashes() использовать json_encode():
json_encode($item['q'], JSON_UNESCAPED_UNICODE)
```

### 8.4. Версионирование ассетов
Заменить жёсткие `?v=256` на автоматические:
```php
<link rel="stylesheet" href="/styles.css?v=<?= filemtime(__DIR__.'/styles.css') ?>">
<script src="/script.js?v=<?= filemtime(__DIR__.'/script.js') ?>"></script>
```

### 8.5. `og:image` — проверить наличие
- Файл `/assets/og-image.jpg` должен существовать (1200×630px)
- Файл `/assets/twitter-image.jpg` (1200×600px)

---

## 📊 ПРИОРИТЕТ ВЫПОЛНЕНИЯ

| # | Задача | Приоритет | Влияние на SEO |
|---|--------|-----------|----------------|
| 1 | ЧПУ для блога + 301 редиректы | 🔴 Критический | ⭐⭐⭐⭐⭐ |
| 7 | SSL-сертификат | 🔴 Критический | ⭐⭐⭐⭐⭐ |
| 2 | Очистка репозитория | 🟡 Высокий | ⭐⭐ |
| 3 | SEO-оптимизация (sitemap, canonical, breadcrumbs) | 🟡 Высокий | ⭐⭐⭐⭐⭐ |
| 4 | Адаптивность всех дисплеев | 🟡 Высокий | ⭐⭐⭐⭐ |
| 5 | Рефакторинг (разделение файлов) | 🟢 Средний | ⭐⭐ |
| 6 | Производительность | 🟢 Средний | ⭐⭐⭐ |
| 8 | Мелкие улучшения (404, версии, og:image) | 🟢 Средний | ⭐⭐⭐ |

---

## 🏗️ СТРУКТУРА ПРОЕКТА (ПОСЛЕ РЕФАКТОРИНГА)

```
molozin/
├── .gitignore
├── .htaccess
├── README.md
├── index.php                 — Главная (компактная, include'ы)
├── blog.php                  — Блог (список + статья по ЧПУ)
├── blog_rss.php              — RSS фид блога
├── privacy.php               — Политика конфиденциальности
├── terms.php                 — Условия использования
├── sitemap.php               — Динамический сайтмап
├── send-email.php            — Обработчик форм
├── 404.php                   — Страница ошибки
├── robots.txt
├── favicon.svg / .ico / .png
├── site.webmanifest
│
├── includes/                 — PHP-компоненты
│   ├── header.php
│   ├── hero.php
│   ├── clients.php
│   ├── portfolio.php
│   ├── services.php
│   ├── ecosystem.php
│   ├── process.php
│   ├── pricing.php
│   ├── testimonials.php
│   ├── contact.php
│   ├── footer.php
│   └── seo-schemas.php
│
├── css/                      — Стили
│   ├── variables.css
│   ├── base.css
│   ├── header.css
│   ├── hero.css
│   ├── sections.css
│   ├── cards.css
│   ├── forms.css
│   ├── animations.css
│   ├── responsive.css
│   └── blog.css
│
├── js/                       — Скрипты
│   ├── core.js
│   ├── navigation.js
│   ├── animations.js
│   ├── hero.js
│   ├── forms.js
│   ├── modal.js
│   ├── theme.js
│   └── lang.js
│
├── assets/                   — Медиа
│   ├── flags/
│   ├── og-image.jpg
│   ├── twitter-image.jpg
│   └── *.webp
│
├── admin/                    — Админ-панель
│   ├── index.php
│   ├── db.php
│   ├── auth.php
│   ├── api_save_seo.php
│   └── migrate_slugs.php     — Миграция slug'ов блога
│
├── api/
│   └── track.php
│
├── i18n.php                  — Интернационализация
└── tracker.js                — Аналитика
```

---

## ⚠️ ЧЕКЛИСТ ПЕРЕД ДЕПЛОЕМ

- [ ] Все URL блога — ЧПУ (`/blog/slug/`)
- [ ] 301 редиректы со старых URL работают
- [ ] Sitemap содержит все новые URL
- [ ] `robots.txt` указывает на правильный sitemap
- [ ] SSL-сертификат обновлён
- [ ] Нет 404 ошибок на основных страницах
- [ ] Мобильная версия проверена на 375px и 768px
- [ ] Canonical URL на всех страницах корректны
- [ ] `og:image` файл существует и корректен
- [ ] Консоль браузера без JS-ошибок
- [ ] Нет конфиденциальных данных в репозитории (.sqlite, .sql, .ps1)
- [ ] PageSpeed Insights: мобильный > 70, десктоп > 85
