<?php 
// Оптимизация: Минификация HTML и GZIP сжатие на лету
if (!ob_start("ob_gzhandler")) ob_start();

function minify_html_output($buffer) {
    if (strpos($buffer, '<pre>') !== false || strpos($buffer, '<textarea>') !== false) return $buffer;
    $search = ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--(.|\s)*?-->/'];
    $replace = ['>', '<', '\\1', ''];
    return preg_replace($search, $replace, $buffer);
}
ob_start("minify_html_output");

require_once __DIR__ . '/i18n.php'; 
require_once __DIR__ . '/admin/db.php';
// Default SEO Logic
$seo_title = "Molozin.ru - Премиальная веб-разработка";
$seo_desc = "Премиум веб-студия. Разработка и продвижение сайтов, порталов и интернет-магазинов международного уровня.";
$hero_h1_part1 = __('hero_title_1');
$hero_h1_accent = __('hero_title_accent');
$hero_subtitle = __('hero_subtitle');
$seo_content_bottom = "";
$seo_faq_json = "";
$is_seo_page = false;


if (isset($_GET['seo_slug'])) {
    $stmt = $db->prepare("SELECT * FROM seo_landings WHERE slug = ?");
    $stmt->execute([$_GET['seo_slug']]);
    $landing = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($landing) {
        $seo_title = htmlspecialchars($landing['title']);
        $seo_desc = htmlspecialchars($landing['description']);
        $hero_h1_part1 = htmlspecialchars($landing['h1']);
        $hero_h1_accent = ""; 
        $hero_subtitle = htmlspecialchars($landing['content_top']);
        $seo_content_bottom = $landing['content_bottom'];
        $seo_faq_json = $landing['faq_json'];
        $is_seo_page = true;
        
        $db->prepare("UPDATE seo_landings SET views = views + 1 WHERE id = ?")->execute([$landing['id']]);
    } else {
        header("HTTP/1.0 404 Not Found");
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= $is_rtl ? 'rtl' : 'ltr' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="google-site-verification" content="FAqMs57iu64uK1pZZNW5sVTv3HaZA1DQImeS8df2iik" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="acde20fd1d58c52c" />

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=107083206', 'ym');

        ym(107083206, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/107083206" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->

    <!-- Фавиконы -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="theme-color" content="#131018">

    <meta name="description"
        content="<?= $seo_desc ?>">
    <meta name="keywords"
        content="создание сайтов, разработка сайтов, продвижение сайтов, SEO оптимизация, веб-дизайн, landing page, интернет-магазин">
    <meta name="author" content="Molozin.ru">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://molozin.ru/<?= isset($_GET['seo_slug']) ? 'uslugi/'.htmlspecialchars($_GET['seo_slug']).'/' : '' ?>">

    <!-- hreflang — мультиязычные версии для поисковиков -->
    <?php
    $hreflang_base = 'https://molozin.ru/' . (isset($_GET['seo_slug']) ? 'uslugi/'.htmlspecialchars($_GET['seo_slug']) : '');
    $hreflang_langs = ['ru', 'en', 'zh', 'es', 'hi', 'ar', 'bn', 'pt', 'ja', 'pa'];
    foreach ($hreflang_langs as $hl): ?>
    <link rel="alternate" hreflang="<?= $hl ?>" href="<?= $hreflang_base ?>?lang=<?= $hl ?>">
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= $hreflang_base ?>">
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://molozin.ru/<?= isset($_GET['seo_slug']) ? 'uslugi/'.htmlspecialchars($_GET['seo_slug']) : '' ?>">
    <meta property="og:title" content="<?= $seo_title ?>">
    <meta property="og:description"
        content="<?= $seo_desc ?>">
    <meta property="og:image" content="https://molozin.ru/assets/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://molozin.ru/">
    <meta property="twitter:title" content="<?= $seo_title ?>">
    <meta property="twitter:description" content="<?= $seo_desc ?>">
    <meta property="twitter:image" content="https://molozin.ru/assets/twitter-image.jpg">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

    <!-- Fonts (Preload for CLS Optimization) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="/styles.css?v=256">

    <!-- Schema.org JSON-LD: Организация -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebDesignAgency",
      "name": "Molozin.ru",
      "alternateName": "Веб-студия Молозин",
      "description": "Создаём цифровые продукты, которые приводят клиентов. Разработка сайтов, интернет-магазинов, SaaS-платформ и 3D-визуализаторов. Премиум-дизайн, SEO-продвижение, AI-автоматизация.",
      "url": "https://molozin.ru/",
      "logo": "https://molozin.ru/android-chrome-512x512.png",
      "image": "https://molozin.ru/assets/og-image.jpg",
      "telephone": "+7-923-406-44-41",
      "email": "mdn77@yandex.ru",
      "foundingDate": "2014",
      "numberOfEmployees": {
        "@type": "QuantitativeValue",
        "minValue": 2,
        "maxValue": 10
      },
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "ул. Нахимова, 15",
        "addressLocality": "Томск",
        "addressRegion": "Томская область",
        "postalCode": "634003",
        "addressCountry": "RU"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "56.4846",
        "longitude": "84.9482"
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
        "opens": "09:00",
        "closes": "20:00"
      },
      "sameAs": [
        "https://vk.com/mdn77",
        "https://t.me/molozin",
        "https://wa.me/79234064441"
      ],
      "founder": {
        "@type": "Person",
        "name": "Дмитрий Молозин"
      },
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Услуги веб-разработки",
        "itemListElement": [
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Разработка сайтов и лендингов",
              "description": "Создание современных адаптивных сайтов с уникальным дизайном от 30 000 ₽"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Интернет-магазины и SaaS-платформы",
              "description": "Полноценные e-commerce решения с каталогами, калькуляторами и CRM-интеграцией"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "SEO-продвижение и контекстная реклама",
              "description": "Вывод сайтов в ТОП-10 Яндекса и Google, настройка рекламных кампаний"
            }
          }
        ]
      },
      "priceRange": "$$",
      "areaServed": [
        {"@type": "Country", "name": "Россия"},
        {"@type": "Country", "name": "Казахстан"},
        {"@type": "Country", "name": "Беларусь"},
        {"@type": "AdministrativeArea", "name": "СНГ и весь мир"}
      ],
      "knowsLanguage": ["ru", "en"],
      "slogan": "Создаём продукты, которые приводят клиентов"
    }
    </script>

    <!-- Schema.org JSON-LD: Отзывы о продуктах (SoftwareApplication — поддерживается Google) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "R-70 — Конструктор сайтов",
      "description": "SaaS-платформа для создания профессиональных сайтов. Премиум-дизайн за пару кликов, бесплатный базовый тариф.",
      "url": "https://r-70.ru/",
      "applicationCategory": "WebApplication",
      "operatingSystem": "Web",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "RUB"
      },
      "review": [
        {
          "@type": "Review",
          "reviewRating": {"@type": "Rating", "ratingValue": 5, "bestRating": 5},
          "author": {"@type": "Person", "name": "Алексей К."},
          "datePublished": "2025-11-15",
          "reviewBody": "Конструктор реально удобный — сделал сайт для строительной компании за вечер. Дизайн на уровне топовых московских студий."
        },
        {
          "@type": "Review",
          "reviewRating": {"@type": "Rating", "ratingValue": 5, "bestRating": 5},
          "author": {"@type": "Person", "name": "Марина Д."},
          "datePublished": "2026-01-20",
          "reviewBody": "Сделали сайт для кондитерской с калькулятором стоимости. Заявки пошли с первой недели. Отличная платформа!"
        },
        {
          "@type": "Review",
          "reviewRating": {"@type": "Rating", "ratingValue": 5, "bestRating": 5},
          "author": {"@type": "Person", "name": "Игорь В."},
          "datePublished": "2026-02-10",
          "reviewBody": "Перенесли бизнес на R-70 — трафик вырос в 4 раза. Встроенное SEO работает отлично. Рекомендую."
        }
      ],
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.9",
        "reviewCount": "47",
        "bestRating": "5"
      }
    }
    </script>

    <?php if ($is_seo_page && !empty($seo_faq_json)): 
        $faq_data = json_decode($seo_faq_json, true);
        if ($faq_data): ?>
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "FAQPage",
          "mainEntity": [
            <?php 
            $entities = [];
            foreach($faq_data as $item) {
                $entities[] = '{
                    "@type": "Question",
                    "name": "'.addslashes($item['q']).'",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "'.addslashes($item['a']).'"
                    }
                }';
            }
            echo implode(',', $entities);
            ?>
          ]
        }
        </script>
    <?php endif; endif; ?>

    <!-- Breadcrumbs Schema.org -->
    <?php if ($is_seo_page): ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Главная",
          "item": "https://molozin.ru/"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Услуги",
          "item": "https://molozin.ru/#services"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "<?= htmlspecialchars($seo_title) ?>",
          "item": "https://molozin.ru/uslugi/<?= htmlspecialchars($_GET['seo_slug']) ?>/"
        }
      ]
    }
    </script>
    <?php else: ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Главная",
          "item": "https://molozin.ru/"
        }
      ]
    }
    </script>
    <?php endif; ?>
</head>

<body>

    <!-- Header -->
    <header class="header" id="header">
        <div class="container">
            <nav class="nav">
                <a href="/" class="logo" style="gap: 0rem;">
                    <div class="logo-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 28V12L20 22L30 12V28" stroke="url(#logo_grad)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <defs>
                                <linearGradient id="logo_grad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#6366f1"/>
                                    <stop offset="1" stop-color="#0ea5e9"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div class="logo-type" style="margin-left: -0.4rem;">
                        <span class="logo-text">olozin</span>
                        <span class="logo-accent">.ru</span>
                    </div>
                </a>

                <ul class="nav-menu" id="navMenu">
                    <li><a href="/#services" class="nav-link"><?= __('menu_services') ?></a></li>
                    <li><a href="/#portfolio" class="nav-link"><?= __('menu_portfolio') ?></a></li>
                    <li><a href="/#process" class="nav-link"><?= __('menu_process') ?></a></li>
                    <li><a href="/#pricing" class="nav-link"><?= __('menu_pricing') ?></a></li>
                    <li><a href="/blog/" class="nav-link"><?= __('menu_blog') ?></a></li>
                    <li><a href="/#contacts" class="nav-link"><?= __('menu_contacts') ?></a></li>
                    
                    <li class="mobile-contact-item">
                        <a href="tel:+79234064441">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            +7 (923) 406-44-41
                        </a>
                        <a href="https://t.me/molozin" target="_blank">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                            @molozin
                        </a>
                    </li>
                </ul>

                <div class="header-actions">
                    <?php
                        $langs_display = [
                            'ru' => ['icon' => 'ru', 'name' => 'Русский'],
                            'en' => ['icon' => 'gb', 'name' => 'English'],
                            'zh' => ['icon' => 'cn', 'name' => '中文'],
                            'es' => ['icon' => 'es', 'name' => 'Español'],
                            'hi' => ['icon' => 'in', 'name' => 'हिन्दी'],
                            'ar' => ['icon' => 'sa', 'name' => 'العربية'],
                            'bn' => ['icon' => 'bd', 'name' => 'বাংলা'],
                            'pt' => ['icon' => 'pt', 'name' => 'Português'],
                            'ja' => ['icon' => 'jp', 'name' => '日本語'],
                            'pa' => ['icon' => 'pk', 'name' => 'ਪੰਜਾਬੀ']
                        ];
                        $curr = $langs_display[$lang] ?? $langs_display['en'];
                    ?>
                    <div class="lang-dropdown" id="langDropdown">
                        <button class="lang-dropdown-toggle" aria-label="Change Language">
                            <img src="/assets/flags/<?= $curr['icon'] ?>.svg" width="20" alt="<?= strtoupper($curr['icon']) ?>" style="border-radius: 2px;">
                            <span class="lang-code"><?= strtoupper($lang) ?></span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <div class="lang-dropdown-menu">
                            <?php foreach ($langs_display as $k => $v): ?>
                                <a href="?lang=<?= $k ?>" class="lang-option <?= $k === $lang ? 'active' : '' ?>" data-lang="<?= $k ?>">
                                    <img src="/assets/flags/<?= $v['icon'] ?>.svg" width="20" alt="<?= strtoupper($v['icon']) ?>" style="border-radius: 2px;"> <?= $v['name'] ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button class="theme-toggle" id="themeToggle" aria-label="Переключить тему">
                        <svg class="icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                        <svg class="icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                    </button>
                    
                    <!-- Телефон в шапке (как у всех ТОП конкурентов) -->
                    <a href="tel:+79234064441" class="header-phone d-none-mobile" style="display:inline-flex;align-items:center;gap:6px;font-weight:600;font-size:0.9rem;color:var(--color-text);text-decoration:none;white-space:nowrap;" title="Позвонить">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        +7 (923) 406-44-41
                    </a>

                    <a href="/#contacts" class="btn btn-primary d-none-mobile"><?= __('btn_consultation') ?></a>
                </div>

                <button class="nav-toggle" id="navToggle" aria-label="Открыть меню">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <!-- Canvas с живыми частицами -->
        <canvas id="heroParticles" style="position:absolute;inset:0;z-index:0;pointer-events:none;"></canvas>
        <!-- Aurora — анимированные цветные пятна -->
        <div class="hero-aurora">
            <div class="aurora-blob aurora-1"></div>
            <div class="aurora-blob aurora-2"></div>
            <div class="aurora-blob aurora-3"></div>
        </div>
        <!-- Интерактивный градиент фон (следует за мышью) -->
        <div class="hero-glow" id="heroGlow"></div>

        <div class="container">
            <div class="hero-split">
                <!-- ЛЕВАЯ ЧАСТЬ — текст -->
                <div class="hero-left">

                    <h1 class="hero-title" id="heroTitle">
                        <?= $hero_h1_part1 ?><?php if(!empty($hero_h1_accent)): ?><span class="gradient-text"><?= $hero_h1_accent ?></span><?php endif; ?>
                    </h1>

                    <p class="hero-subtitle">
                        <?= $hero_subtitle ?>
                    </p>

                    <?php if ($is_seo_page): ?>
                    <div class="contextual-callout" style="background: hsla(260, 100%, 65%, 0.1); border: 1px solid rgba(99, 102, 241, 0.3); padding: 15px; border-radius: 12px; margin-bottom: 25px; text-align: left; display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 2rem;">💡</div>
                        <div style="font-size: 0.95rem; line-height: 1.4;">
                            <?php if (stripos($hero_h1_part1, 'магазин') !== false || stripos($hero_h1_part1, 'commerce') !== false || stripos($hero_h1_part1, 'торговли') !== false): ?>
                                <b>Нужен готовый каталог?</b> Посмотрите наш проект <a href="https://3dcorp.ru/catalog" target="_blank" style="color:var(--color-primary); text-decoration: underline;">3dcorp.ru/catalog</a> — идеальный пример e-commerce модуля.
                            <?php elseif (stripos($hero_h1_part1, 'бесплатно') !== false || stripos($hero_h1_part1, 'самост') !== false): ?>
                                <b>Хотите создать сайт сами?</b> Попробуйте <a href="https://r-70.ru" target="_blank" style="color:var(--color-primary); text-decoration: underline;">R-70.ru</a> — профессиональный конструктор с бесплатным базовым тарифом.
                            <?php elseif (stripos($hero_h1_part1, 'печать') !== false || stripos($hero_h1_part1, 'автомат') !== false): ?>
                                <b>Автоматизация 3D-бизнеса:</b> Интегрируем расчет стоимости как на <a href="https://3dcorp.ru/print" target="_blank" style="color:var(--color-primary); text-decoration: underline;">3dcorp.ru/print</a> в ваш проект.
                            <?php else: ?>
                                <b>Molozin.ru</b> — автоматизируем бизнес через AI и сложные калькуляторы. Получите решение, которое работает за вас.
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- ПРАВАЯ ЧАСТЬ — визуал -->
                <div class="hero-right">
                    <div class="hero-visual" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                        <div id="hero3DElement" style="width: 100%; aspect-ratio: 1; position: relative; margin-left: -50px;">
                            <!-- Three.js will render here -->
                        </div>

                        <!-- Кнопки CTA перенесены под лесенку и смещены правее на 5 пикселей -->
                        <div class="hero-cta" style="width: 100%; justify-content: center; margin-top: -3rem; position: relative; z-index: 20; transform: translateX(5px);">
                            <a href="/#contacts" class="btn btn-large btn-primary magnetic-btn">
                                <span><?= __('btn_order') ?></span>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4 10H16M16 10L10 4M16 10L10 16" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a href="#portfolio" class="btn btn-large btn-secondary magnetic-btn"><?= __('btn_portfolio') ?></a>
                        </div>
                    </div>
                </div>
            </div>
    </section>



    <!-- Секция доверия: Логотипы проектов (social proof) -->
    <section class="clients-logos-section" style="padding: 3rem 0; overflow: hidden; border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
        <div class="container" style="text-align: center; margin-bottom: 1.5rem;">
            <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 3px; color: var(--color-text-muted); font-weight: 600;">Нашим продуктам доверяют</span>
        </div>
        <div class="logos-marquee" style="display: flex; gap: 4rem; animation: marqueeScroll 25s linear infinite; width: max-content;">
            <!-- Первый набор (дублируется для бесшовного скролла) -->
            <div style="display:flex;gap:4rem;align-items:center;">
                <a href="https://r-70.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">R-70.ru</a>
                <a href="https://lanacake.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">LanaCake</a>
                <a href="https://3dcorp.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">3DCorp</a>
                <a href="https://best70.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">Best70</a>
                <a href="https://ksil.r-70.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">KSIL 3D</a>
                <a href="https://arenda.r-70.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">Arenda</a>
                <a href="https://uslugi70.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">Uslugi70</a>
                <a href="https://fan3d.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">Fan3D</a>
                <a href="https://3dprint.tomsk.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">3DPrint Tomsk</a>
                <a href="https://3d-top.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">3D-Top</a>
                <a href="https://calc.3dcorp.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">3D Calc</a>
                <a href="https://catalog.3dcorp.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">3D Catalog</a>
                <a href="https://3d.best70.ru" target="_blank" rel="noopener" style="opacity:0.5;transition:opacity 0.3s;font-size:1.3rem;font-weight:700;color:var(--color-text);text-decoration:none;white-space:nowrap;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'">3D Best70</a>
            </div>
            <!-- Дубликат для бесшовного скролла -->
            <div style="display:flex;gap:4rem;align-items:center;">
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">R-70.ru</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">LanaCake</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">3DCorp</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">Best70</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">KSIL 3D</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">Arenda</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">Uslugi70</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">Fan3D</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">3DPrint Tomsk</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">3D-Top</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">3D Calc</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">3D Catalog</span>
                <span style="opacity:0.5;font-size:1.3rem;font-weight:700;color:var(--color-text);white-space:nowrap;">3D Best70</span>
            </div>
        </div>
    </section>

    <!-- Portfolio Cases Section -->
    <section class="section portfolio-premium" id="portfolio">
        <div class="container">
            <div class="section-header">
                <span class="section-label"><?= __('port_label') ?></span>
                <h2 class="section-title"><?= __('port_title') ?></h2>
                <p class="section-subtitle"><?= __('port_sub') ?></p>
            </div>

            <div class="cases-grid">
<?php
                require_once __DIR__ . '/admin/db.php';
                $portfolio_items = $db->query("SELECT * FROM portfolio ORDER BY sort_order ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);

                if (empty($portfolio_items)) {
                    // Дефолтные проекты, если база пуста
                    $default_cases = [
                        ['title' => 'Arenda.R-70.ru | Аренда квартир', 'url' => 'https://arenda.r-70.ru', 'image_url' => 'https://s0.wp.com/mshots/v1/https://arenda.r-70.ru?w=600&h=450', 'description' => 'Премиальный сервис для долгосрочной аренды жилья для рабочих бригад в различных городах.', 'tags' => 'Недвижимость, Букинг', 'sort_order' => 10],
                        ['title' => 'Lana Cake Кондитерская', 'url' => 'https://lanacake.ru/', 'image_url' => 'https://s0.wp.com/mshots/v1/https://lanacake.ru/?w=600&h=450', 'description' => 'Lana Cake интернет-магазин премиальной кондитерской: калькулятор стоимости, каталог товаров, квиз-подбор и e-commerce модуль для быстрого заказа.', 'tags' => 'E-commerce, Калькулятор', 'sort_order' => 10],
                        ['title' => '3DCorp.ru | Калькулятор 3D-печати', 'url' => 'https://3dcorp.ru/', 'image_url' => 'https://s0.wp.com/mshots/v1/https://3dcorp.ru/?w=600&h=450', 'description' => 'Инновационный интернет магазин с калькулятором множества 3D моделей и заказом услуг.', 'tags' => 'WebGL, Каталог, Калькулятор', 'sort_order' => 10],
                        ['title' => 'R-70 SaaS | Конструктор сайтов', 'url' => 'https://r-70.ru/', 'image_url' => 'https://s0.wp.com/mshots/v1/https://r-70.ru/?w=600&h=450', 'description' => 'SaaS-платформа для бизнеса. Создавайте сайты, порталы и интернет-магазины с премиум-дизайном за пару кликов прямо со смартфона. Базовый функционал бесплатен навсегда.', 'tags' => 'SaaS, Конструктор, Бесплатно', 'sort_order' => 10],
                        ['title' => 'Best70.ru | Справочник компаний', 'url' => 'https://best70.ru/', 'image_url' => 'https://s0.wp.com/mshots/v1/https://best70.ru/?w=600&h=450', 'description' => 'Справочник сайтов и компаний Томска. Аналитический хаб с обзорами, подлинным рейтингом и глубокой SEO оптимизацией.', 'tags' => 'Каталог, HighLoad SEO', 'sort_order' => 10],
                        ['title' => 'Ksil | 3D-визуализатор', 'url' => 'https://ksil.r-70.ru/', 'image_url' => 'https://s0.wp.com/mshots/v1/https://ksil.r-70.ru/?w=600&h=450', 'description' => 'Интерактивный 3D-конструктор и визуализатор детских площадок. Адаптирован для размещения на инфотерминалах и встраивания на внешние сайты.', 'tags' => 'WebGL, 3D-Терминал', 'sort_order' => 10]
                    ];
                    $stmt = $db->prepare("INSERT INTO portfolio (title, url, image_url, description, tags, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                    foreach ($default_cases as $c) {
                        $stmt->execute([$c['title'], $c['url'], $c['image_url'], $c['description'], $c['tags'], $c['sort_order']]);
                    }
                }

                $order_clause = "sort_order ASC, id DESC";
                if ($is_seo_page) {
                    $context_keyword = "";
                    if (stripos($hero_h1_part1, 'магазин') !== false) $context_keyword = 'E-commerce';
                    elseif (stripos($hero_h1_part1, 'печать') !== false || stripos($hero_h1_part1, 'автомат') !== false) $context_keyword = 'Автоматизация';
                    elseif (stripos($hero_h1_part1, 'бесплатно') !== false) $context_keyword = 'Бесплатно';
                    
                    if ($context_keyword) {
                        $order_clause = "(CASE WHEN tags LIKE '%$context_keyword%' THEN 0 ELSE 1 END), " . $order_clause;
                    }
                }
                $portfolio_items = $db->query("SELECT * FROM portfolio ORDER BY $order_clause")->fetchAll(PDO::FETCH_ASSOC);

                foreach ($portfolio_items as $item):
                    $tags = array_map('trim', explode(',', $item['tags']));
                    $translated_tags = array_map('__', $tags);
                    $tags_str = implode(', ', $translated_tags);
                    
                    $translated_title = __($item['title']);
                    $translated_desc = __($item['description']);
                    $shortTitle = explode('|', $translated_title)[0];
                ?>
                <div class="case-card open-interactive-modal" data-url="<?= htmlspecialchars($item['url']) ?>"
                    data-title="<?= htmlspecialchars($translated_title) ?>"
                    data-description="<?= htmlspecialchars($translated_desc) ?>"
                    data-tags="<?= htmlspecialchars($tags_str) ?>">
                    <div class="case-image">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($shortTitle) ?>"
                            class="case-preview-img" loading="lazy">
                        <div class="case-overlay">
                            <span><?= __('port_interactive') ?></span>
                        </div>
                    </div>
                    <div class="case-content">
                        <h3 class="case-title"><?= htmlspecialchars($shortTitle) ?></h3>
                        <p class="case-description"><?= htmlspecialchars($item['description']) ?></p>
                        <div class="case-tags">
                            <?php foreach ($tags as $tag): if(empty($tag)) continue; ?>
                            <span class="case-tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Schema.org Portfolio (CreativeWork) -->
                    <script type="application/ld+json">
                    {
                      "@context": "https://schema.org",
                      "@type": "CreativeWork",
                      "name": "<?= htmlspecialchars($translated_title) ?>",
                      "description": "<?= htmlspecialchars($translated_desc) ?>",
                      "image": "https://molozin.ru<?= htmlspecialchars($item['image_url']) ?>",
                      "author": {
                        "@type": "Organization",
                        "name": "Molozin.ru"
                      },
                      "url": "https://molozin.ru<?= htmlspecialchars($item['url']) ?>",
                      "keywords": "<?= htmlspecialchars($tags_str) ?>"
                    }
                    </script>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="portfolio-cta">
                <a href="/#contacts" class="btn btn-primary btn-large">
                    <span><?= __('port_btn') ?></span>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section services" id="services">
        <div class="container">
            <div class="section-header">
                <span class="section-label"><?= __('serv_label') ?></span>
                <h2 class="section-title"><?= __('serv_title') ?></h2>
                <p class="section-subtitle"><?= __('serv_sub') ?></p>
            </div>

            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <rect x="4" y="4" width="24" height="24" rx="2" stroke="currentColor" stroke-width="2" />
                            <path d="M4 12H28" stroke="currentColor" stroke-width="2" />
                            <circle cx="8" cy="8" r="1" fill="currentColor" />
                            <circle cx="12" cy="8" r="1" fill="currentColor" />
                            <circle cx="16" cy="8" r="1" fill="currentColor" />
                        </svg>
                    </div>
                    <h3 class="service-title"><?= __('serv_1_title') ?></h3>
                    <p class="service-description"><?= __('serv_1_desc') ?></p>
                    <ul class="service-features">
                        <li><?= __('serv_1_f1') ?></li>
                        <li><?= __('serv_1_f2') ?></li>
                        <li><?= __('serv_1_f3') ?></li>
                    </ul>
                    <a href="#pricing" class="service-link">
                        <span><?= __('serv_more') ?></span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <path d="M16 4L4 10L16 16L28 10L16 4Z" stroke="currentColor" stroke-width="2"
                                stroke-linejoin="round" />
                            <path d="M4 16L16 22L28 16" stroke="currentColor" stroke-width="2"
                                stroke-linejoin="round" />
                            <path d="M4 22L16 28L28 22" stroke="currentColor" stroke-width="2"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="service-title"><?= __('serv_2_title') ?></h3>
                    <p class="service-description"><?= __('serv_2_desc') ?></p>
                    <ul class="service-features">
                        <li><?= __('serv_2_f1') ?></li>
                        <li><?= __('serv_2_f2') ?></li>
                        <li><?= __('serv_2_f3') ?></li>
                    </ul>
                    <a href="#pricing" class="service-link">
                        <span><?= __('serv_more') ?></span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="12" stroke="currentColor" stroke-width="2" />
                            <path d="M16 8V16L22 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <h3 class="service-title"><?= __('serv_3_title') ?></h3>
                    <p class="service-description"><?= __('serv_3_desc') ?></p>
                    <ul class="service-features">
                        <li><?= __('serv_3_f1') ?></li>
                        <li><?= __('serv_3_f2') ?></li>
                        <li><?= __('serv_3_f3') ?></li>
                    </ul>
                    <a href="#pricing" class="service-link">
                        <span><?= __('serv_more') ?></span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <rect x="6" y="6" width="20" height="20" rx="2" stroke="currentColor" stroke-width="2" />
                            <path d="M16 12V20M12 16H20" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                    </div>
                    <h3 class="service-title"><?= __('serv_4_title') ?></h3>
                    <p class="service-description"><?= __('serv_4_desc') ?></p>
                    <ul class="service-features">
                        <li><?= __('serv_4_f1') ?></li>
                        <li><?= __('serv_4_f2') ?></li>
                        <li><?= __('serv_4_f3') ?></li>
                    </ul>
                    <a href="#pricing" class="service-link">
                        <span><?= __('serv_more') ?></span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <path
                                d="M4 12C4 8.68629 6.68629 6 10 6H22C25.3137 6 28 8.68629 28 12V20C28 23.3137 25.3137 26 22 26H10C6.68629 26 4 23.3137 4 20V12Z"
                                stroke="currentColor" stroke-width="2" />
                            <path d="M28 12L16 18L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <h3 class="service-title"><?= __('serv_5_title') ?></h3>
                    <p class="service-description"><?= __('serv_5_desc') ?></p>
                    <ul class="service-features">
                        <li><?= __('serv_5_f1') ?></li>
                        <li><?= __('serv_5_f2') ?></li>
                        <li><?= __('serv_5_f3') ?></li>
                    </ul>
                    <a href="#pricing" class="service-link">
                        <span><?= __('serv_more') ?></span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <rect x="4" y="8" width="24" height="16" rx="2" stroke="currentColor" stroke-width="2" />
                            <path d="M4 14H28" stroke="currentColor" stroke-width="2" />
                            <circle cx="20" cy="19" r="2" fill="currentColor" />
                        </svg>
                    </div>
                    <h3 class="service-title"><?= __('serv_6_title') ?></h3>
                    <p class="service-description"><?= __('serv_6_desc') ?></p>
                    <ul class="service-features">
                        <li><?= __('serv_6_f1') ?></li>
                        <li><?= __('serv_6_f2') ?></li>
                        <li><?= __('serv_6_f3') ?></li>
                    </ul>
                    <a href="#pricing" class="service-link">
                        <span><?= __('serv_more') ?></span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Ecosystem Strategic Section -->
    <section class="section ecosystem" id="ecosystem" style="background: var(--color-bg-dark); position: relative; overflow: hidden; padding: 100px 0;">
        <div class="hero-background" style="opacity: 0.3;">
            <div class="gradient-orb orb-2" style="top: -20%; right: -10%;"></div>
        </div>
        <div class="container">
            <div class="section-header">
                <span class="section-label"><?= __('eco_label') ?></span>
                <h2 class="section-title"><?= __('eco_title') ?></h2>
                <p class="section-subtitle"><?= __('eco_sub') ?></p>
            </div>

            <div class="eco-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-top: 50px;">
                <!-- R-70.ru -->
                <a href="https://r-70.ru" target="_blank" class="eco-card-new" style="
                    display: flex; flex-direction: column;
                    background: var(--color-bg-light);
                    border: 1px solid var(--color-border);
                    border-radius: 24px;
                    overflow: hidden;
                    text-decoration: none;
                    transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
                    box-shadow: 0 4px 24px rgba(0,0,0,0.18);
                ">
                    <div style="position: relative; height: 200px; overflow: hidden;">
                        <picture>
                            <source srcset="/assets/r70-preview.webp" type="image/webp">
                            <img src="/assets/r70-preview.png" alt="R-70.ru" loading="lazy" style="
                                width: 100%; height: 100%;
                                object-fit: cover; object-position: top;
                                transition: transform 0.6s ease;
                                display: block;
                            " class="eco-preview-img">
                        </picture>
                        <div style="
                            position: absolute; inset: 0;
                            background: linear-gradient(to bottom, rgba(10,10,20,0.1) 0%, rgba(10,10,20,0.75) 100%);
                        "></div>
                        <div style="
                            position: absolute; bottom: 16px; left: 20px;
                            font-size: 1.4rem; font-weight: 800;
                            color: #fff; font-family: var(--font-display);
                            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
                        "><?= __('eco_1_title') ?></div>
                    </div>
                    <div style="padding: 24px 28px 28px;">
                        <p style="color: var(--color-text-secondary); line-height: 1.6; margin-bottom: 18px; font-size: 0.95rem;"><?= __('eco_1_desc') ?></p>
                        <span class="service-link" style="pointer-events:none;"><?= __('serv_more') ?></span>
                    </div>
                </a>

                <!-- 3DCorp.ru -->
                <a href="https://3dcorp.ru" target="_blank" class="eco-card-new" style="
                    display: flex; flex-direction: column;
                    background: var(--color-bg-light);
                    border: 1px solid var(--color-border);
                    border-radius: 24px;
                    overflow: hidden;
                    text-decoration: none;
                    transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
                    box-shadow: 0 4px 24px rgba(0,0,0,0.18);
                ">
                    <div style="position: relative; height: 200px; overflow: hidden;">
                        <picture>
                            <source srcset="/assets/3dcorp-preview.webp" type="image/webp">
                            <img src="/assets/3dcorp-preview.png" alt="3DCorp.ru" loading="lazy" style="
                                width: 100%; height: 100%;
                                object-fit: cover; object-position: top;
                                transition: transform 0.6s ease;
                                display: block;
                            " class="eco-preview-img">
                        </picture>
                        <div style="
                            position: absolute; inset: 0;
                            background: linear-gradient(to bottom, rgba(10,10,20,0.1) 0%, rgba(10,10,20,0.75) 100%);
                        "></div>
                        <div style="
                            position: absolute; bottom: 16px; left: 20px;
                            font-size: 1.4rem; font-weight: 800;
                            color: #fff; font-family: var(--font-display);
                            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
                        "><?= __('eco_2_title') ?></div>
                    </div>
                    <div style="padding: 24px 28px 28px;">
                        <p style="color: var(--color-text-secondary); line-height: 1.6; margin-bottom: 18px; font-size: 0.95rem;"><?= __('eco_2_desc') ?></p>
                        <span class="service-link" style="pointer-events:none;"><?= __('serv_more') ?></span>
                    </div>
                </a>

                <!-- Best70.ru -->
                <a href="https://best70.ru" target="_blank" class="eco-card-new" style="
                    display: flex; flex-direction: column;
                    background: var(--color-bg-light);
                    border: 1px solid var(--color-border);
                    border-radius: 24px;
                    overflow: hidden;
                    text-decoration: none;
                    transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
                    box-shadow: 0 4px 24px rgba(0,0,0,0.18);
                ">
                    <div style="position: relative; height: 200px; overflow: hidden;">
                        <picture>
                            <source srcset="/assets/best70-preview.webp" type="image/webp">
                            <img src="/assets/best70-preview.png" alt="Best70.ru" loading="lazy" style="
                                width: 100%; height: 100%;
                                object-fit: cover; object-position: top;
                                transition: transform 0.6s ease;
                                display: block;
                            " class="eco-preview-img">
                        </picture>
                        <div style="
                            position: absolute; inset: 0;
                            background: linear-gradient(to bottom, rgba(10,10,20,0.1) 0%, rgba(10,10,20,0.75) 100%);
                        "></div>
                        <div style="
                            position: absolute; bottom: 16px; left: 20px;
                            font-size: 1.4rem; font-weight: 800;
                            color: #fff; font-family: var(--font-display);
                            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
                        "><?= __('eco_3_title') ?></div>
                    </div>
                    <div style="padding: 24px 28px 28px;">
                        <p style="color: var(--color-text-secondary); line-height: 1.6; margin-bottom: 18px; font-size: 0.95rem;"><?= __('eco_3_desc') ?></p>
                        <span class="service-link" style="pointer-events:none;"><?= __('serv_more') ?></span>
                    </div>
                </a>
            </div>

            <style>
                /* Hover для карточек экосистемы */
                .eco-card-new:hover {
                    transform: translateY(-8px);
                    border-color: var(--color-primary);
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 30px hsla(260,100%,65%,0.15);
                }
                .eco-card-new:hover .eco-preview-img {
                    transform: scale(1.06);
                }
            </style>



        </div>
    </section>

    <!-- Process Section -->
    <section class="section process" id="process">
        <div class="container">
            <div class="section-header">
                <span class="section-label"><?= __('proc_label') ?></span>
                <h2 class="section-title"><?= __('proc_title') ?></h2>
                <p class="section-subtitle"><?= __('proc_sub') ?></p>
            </div>

            <div class="process-timeline">
                <div class="process-step">
                    <div class="step-number">01</div>
                    <div class="step-content">
                        <h3 class="step-title"><?= __('proc_1_title') ?></h3>
                        <p class="step-description"><?= __('proc_1_desc') ?></p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">02</div>
                    <div class="step-content">
                        <h3 class="step-title"><?= __('proc_2_title') ?></h3>
                        <p class="step-description"><?= __('proc_2_desc') ?></p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">03</div>
                    <div class="step-content">
                        <h3 class="step-title"><?= __('proc_3_title') ?></h3>
                        <p class="step-description"><?= __('proc_3_desc') ?></p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">04</div>
                    <div class="step-content">
                        <h3 class="step-title"><?= __('proc_4_title') ?></h3>
                        <p class="step-description"><?= __('proc_4_desc') ?></p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">05</div>
                    <div class="step-content">
                        <h3 class="step-title"><?= __('proc_5_title') ?></h3>
                        <p class="step-description"><?= __('proc_5_desc') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Calculator Section -->
    <section class="section calculator-section" id="calculator">
        <div class="container">
            <div class="section-header">
                <span class="section-label"><?= __('calc_label') ?></span>
                <h2 class="section-title"><?= __('calc_title') ?></h2>
                <p class="section-subtitle"><?= __('calc_sub') ?></p>
            </div>
            
            <div class="calc-glass-panel">
                <div class="calc-grid">
                    <div class="calc-controls">
                        <div class="calc-group">
                            <h4 class="calc-group-title"><?= __('calc_type_title') ?></h4>
                            <div class="calc-options">
                                <label class="calc-radio">
                                    <input type="radio" name="calc_type" value="30000" checked>
                                    <span class="calc-radio-btn"><?= __('calc_type_1') ?></span>
                                </label>
                                <label class="calc-radio">
                                    <input type="radio" name="calc_type" value="80000">
                                    <span class="calc-radio-btn"><?= __('calc_type_2') ?></span>
                                </label>
                                <label class="calc-radio">
                                    <input type="radio" name="calc_type" value="150000">
                                    <span class="calc-radio-btn"><?= __('calc_type_3') ?></span>
                                </label>
                                <label class="calc-radio">
                                    <input type="radio" name="calc_type" value="250000">
                                    <span class="calc-radio-btn"><?= __('calc_type_4') ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="calc-group">
                            <h4 class="calc-group-title"><?= __('calc_addons_title') ?></h4>
                            <div class="calc-options calc-checkboxes">
                                <label class="calc-check">
                                    <input type="checkbox" value="15000" class="calc-addon">
                                    <div class="calc-check-box">
                                        <span class="check-icon">✓</span>
                                        <span class="check-text"><?= __('calc_addon_1') ?></span>
                                    </div>
                                </label>
                                <label class="calc-check">
                                    <input type="checkbox" value="20000" class="calc-addon">
                                    <div class="calc-check-box">
                                        <span class="check-icon">✓</span>
                                        <span class="check-text"><?= __('calc_addon_2') ?></span>
                                    </div>
                                </label>
                                <label class="calc-check">
                                    <input type="checkbox" value="10000" class="calc-addon">
                                    <div class="calc-check-box">
                                        <span class="check-icon">✓</span>
                                        <span class="check-text"><?= __('calc_addon_3') ?></span>
                                    </div>
                                </label>
                                <label class="calc-check">
                                    <input type="checkbox" value="25000" class="calc-addon">
                                    <div class="calc-check-box">
                                        <span class="check-icon">✓</span>
                                        <span class="check-text"><?= __('calc_addon_4') ?></span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="calc-group">
                            <h4 class="calc-group-title"><?= __('calc_time_title') ?></h4>
                            <div class="calc-slider-wrap">
                                <input type="range" class="calc-slider" id="calcSlider" min="1" max="4" value="2">
                                <div class="calc-slider-labels">
                                    <span><?= __('calc_time_1') ?></span>
                                    <span><?= __('calc_time_2') ?></span>
                                    <span><?= __('calc_time_3') ?></span>
                                    <span><?= __('calc_time_4') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="calc-result-panel">
                        <div class="calc-result-inner">
                            <h4 class="calc-result-title"><?= __('calc_res_title') ?></h4>
                            <div class="calc-total-wrap">
                                <span class="calc-total-val" id="calcTotal">30 000</span>
                                <span class="calc-total-cur"><?= __('price_cur') ?></span>
                            </div>
                            <p class="calc-result-desc"><?= __('calc_res_sub') ?></p>
                            
                            <div class="calc-capture">
                                <button class="btn btn-primary btn-full" onclick="document.getElementById('contacts').scrollIntoView({behavior:'smooth'});" style="font-size: 1.1rem; padding: 15px;"><?= __('calc_btn') ?></button>
                                <p class="calc-capture-hint"><?= __('calc_hint') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="section pricing" id="pricing">
        <div class="container">
            <div class="section-header">
                <span class="section-label"><?= __('price_label') ?></span>
                <h2 class="section-title"><?= __('price_title') ?></h2>
                <p class="section-subtitle"><?= __('price_sub') ?></p>
            </div>

            <div class="pricing-grid">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3 class="pricing-title"><?= __('price_1_title') ?></h3>
                        <div class="pricing-price">
                            <span class="price-value"><?= __('price_1_val') ?></span>
                            <span class="price-currency"><?= __('price_cur') ?></span>
                        </div>
                        <p class="pricing-description"><?= __('price_1_desc') ?></p>
                    </div>
                    <ul class="pricing-features">
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_1_f1') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_1_f2') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_1_f3') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_1_f4') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_1_f5') ?></span>
                        </li>
                    </ul>
                    <a href="/#contacts" class="btn btn-outline btn-full"><?= __('price_btn') ?></a>
                </div>

                <div class="pricing-card pricing-featured">
                    <div class="pricing-badge"><?= __('price_2_badge') ?></div>
                    <div class="pricing-header">
                        <h3 class="pricing-title"><?= __('price_2_title') ?></h3>
                        <div class="pricing-price">
                            <span class="price-value"><?= __('price_2_val') ?></span>
                            <span class="price-currency"><?= __('price_cur') ?></span>
                        </div>
                        <p class="pricing-description"><?= __('price_2_desc') ?></p>
                    </div>
                    <ul class="pricing-features">
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_2_f1') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_2_f2') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_2_f3') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_2_f4') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_2_f5') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_2_f6') ?></span>
                        </li>
                    </ul>
                    <a href="/#contacts" class="btn btn-primary btn-full"><?= __('price_btn') ?></a>
                </div>

                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3 class="pricing-title"><?= __('price_3_title') ?></h3>
                        <div class="pricing-price">
                            <span class="price-value"><?= __('price_3_val') ?></span>
                            <span class="price-currency"><?= __('price_cur') ?></span>
                        </div>
                        <p class="pricing-description"><?= __('price_3_desc') ?></p>
                    </div>
                    <ul class="pricing-features">
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_3_f1') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_3_f2') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_3_f3') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_3_f4') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_3_f5') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_3_f6') ?></span>
                        </li>
                        <li>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.6 5L7.5 14.1L3.4 10" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span><?= __('price_3_f7') ?></span>
                        </li>
                    </ul>
                    <a href="/#contacts" class="btn btn-outline btn-full"><?= __('price_btn') ?></a>
                </div>
            </div>
        </div>
    </section>



    <!-- SEO Content Bottom -->
    <?php if ($is_seo_page && (!empty($seo_content_bottom) || !empty($seo_faq_json))): ?>
    <section class="section seo-content">
        <div class="container text-content">
            <?php if (!empty($seo_content_bottom)): ?>
                <?= $seo_content_bottom; ?>
            <?php endif; ?>
            
            <?php if (!empty($seo_faq_json)): 
                $faq_items = json_decode($seo_faq_json, true);
                if ($faq_items): ?>
                <div class="faq-accordion" style="margin-top: 40px;">
                    <h2 style="margin-bottom: 30px;">Часто задаваемые вопросы</h2>
                    <?php foreach($faq_items as $i => $item): ?>
                    <div class="faq-item">
                        <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                            <span><?= htmlspecialchars($item['q']) ?></span>
                            <span class="faq-icon">+</span>
                        </div>
                        <div class="faq-answer">
                            <p><?= htmlspecialchars($item['a']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer" id="contacts">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <a href="/" class="footer-logo">
                        <div class="logo-icon logo-icon-sm">
                            <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
                                <rect width="40" height="40" rx="12" fill="url(#footer_logo_grad)" fill-opacity="0.1"/>
                                <path d="M10 28V12L20 22L30 12V28" stroke="url(#footer_logo_grad)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <defs>
                                    <linearGradient id="footer_logo_grad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#6366f1"/>
                                        <stop offset="1" stop-color="#0ea5e9"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>
                        <div class="logo-type">
                            <span class="logo-text">Molozin</span>
                            <span class="logo-accent">.ru</span>
                        </div>
                    </a>
                    <p class="footer-description">
                        <?= __('footer_desc') ?><br>
                        <?= __('contact_address') ?>
                    </p>
                    <div class="social-links-text" style="display:flex; flex-direction:column; gap:12px;">
                        <a href="https://vk.com/mdn77" target="_blank" rel="noopener noreferrer" style="color:var(--color-text); text-decoration:none; display:flex; align-items:center; gap:8px;" aria-label="VK">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12.7 20.7h-1.4c-6.1 0-9.6-4.2-9.7-11.1v-.4h3c0 5.2 2.4 7.4 4.2 7.9V9.2h2.8v4.2c1.8-.2 3.7-2.2 4.3-4.2h2.8c-.5 2.6-2.5 4.5-3.9 5.3 1.4.6 3.7 2.3 4.6 5.2h-3.1c-.7-1.9-2.4-3.4-4.6-3.6v3.6z" />
                            </svg>
                            В Контакте
                        </a>
                        <a href="https://t.me/molozin" target="_blank" rel="noopener noreferrer" style="color:var(--color-text); text-decoration:none; display:flex; align-items:center; gap:8px;" aria-label="Telegram">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" />
                            </svg>
                            Telegram @molozin
                        </a>
                        <a href="https://wa.me/79234064441" target="_blank" rel="noopener noreferrer" style="color:var(--color-text); text-decoration:none; display:flex; align-items:center; gap:8px;" aria-label="WhatsApp">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            Ватсап +79234064441
                        </a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4 class="footer-title"><?= __('footer_l1') ?></h4>
                    <ul class="footer-links">
                        <li><a href="/#services"><?= __('footer_l1_1') ?></a></li>
                        <li><a href="/#services"><?= __('footer_l1_2') ?></a></li>
                        <li><a href="/#services"><?= __('footer_l1_3') ?></a></li>
                        <li><a href="/#services"><?= __('footer_l1_4') ?></a></li>
                        <li><a href="/#services"><?= __('footer_l1_5') ?></a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4 class="footer-title"><?= __('footer_l2') ?></h4>
                    <ul class="footer-links">
                        <li><a href="/#portfolio"><?= __('footer_l2_1') ?></a></li>
                        <li><a href="/#process"><?= __('footer_l2_2') ?></a></li>
                        <li><a href="/#pricing"><?= __('footer_l2_3') ?></a></li>
                        <li><a href="/#contacts"><?= __('footer_l2_4') ?></a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4 class="footer-title"><?= __('footer_l3') ?></h4>
                    <ul class="footer-links">
                        <li><a href="https://r-70.ru/" target="_blank"><?= __('footer_l3_1') ?></a></li>
                        <li><a href="http://3dcorp.ru/" target="_blank"><?= __('footer_l3_2') ?></a></li>
                        <li><a href="https://best70.ru/" target="_blank"><?= __('footer_l3_3') ?></a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="footer-copyright"><?= __('footer_copy') ?></p>
                <div class="footer-bottom-links">
                    <a href="/privacy"><?= __('footer_privacy') ?></a>
                    <a href="/terms"><?= __('footer_terms') ?></a>
                </div>
            </div>
            <!-- Юридическая информация -->
            <div style="text-align: center; padding-top: 8px; border-top: 1px solid var(--color-border); margin-top: 8px; font-size: 0.75rem; color: var(--color-text-secondary); line-height: 1.6;">
                ИП Молозина Светлана Алексеевна &nbsp;|&nbsp; Свидетельство о гос. регистрации 70№001544792 &nbsp;|&nbsp; ИНН 701714853100 &nbsp;|&nbsp; ОГРНИП 310701723500037
            </div>
        </div>
    </footer>



    <!-- Interactive Portfolio Modal Viewer -->
    <div class="portfolio-modal" id="portfolioModal" aria-hidden="true">
        <div class="portfolio-modal-overlay" id="portfolioModalOverlay"></div>
        <div class="portfolio-modal-container">
            <div class="portfolio-modal-header">
                <div class="portfolio-modal-info">
                    <h3 class="portfolio-modal-title" id="portfolioModalTitle"><?= __('modal_title_default') ?></h3>
                    <a href="#" class="portfolio-modal-link" id="portfolioModalLink" target="_blank">
                        <?= __('modal_open') ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3" />
                        </svg>
                    </a>
                </div>

                <div class="portfolio-modal-devices">
                    <button class="device-btn active" data-device="desktop" title="<?= __('modal_desktop') ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                            <line x1="8" y1="21" x2="16" y2="21" />
                            <line x1="12" y1="17" x2="12" y2="21" />
                        </svg>
                    </button>
                    <button class="device-btn" data-device="mobile" title="<?= __('modal_mobile') ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2" />
                            <line x1="12" y1="18" x2="12.01" y2="18" />
                        </svg>
                    </button>
                </div>

                <button class="portfolio-modal-close" id="portfolioModalClose" aria-label="<?= __('modal_close') ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>

            <div class="portfolio-modal-body" style="display:flex; flex-direction:row; background: var(--color-bg-secondary); padding: 0;">
                <div class="portfolio-sidebar" id="portfolioSidebar">
                    <div class="sidebar-badge"><?= __('modal_badge') ?></div>
                    <h4 id="portfolioSidebarTitle" class="sidebar-title"><?= __('modal_title_default') ?></h4>
                    <p id="portfolioSidebarDesc" class="sidebar-desc"><?= __('modal_desc_default') ?></p>
                    
                    <div class="sidebar-features">
                        <h5><?= __('modal_what_done') ?></h5>
                        <ul id="portfolioSidebarTags" class="sidebar-tags-list">
                        </ul>
                    </div>

                    <div class="sidebar-maker">
                        <strong><?= __('modal_studio') ?></strong>
                        <span><?= __('modal_studio_desc') ?></span>
                    </div>
                </div>
                <div class="portfolio-iframe-container" style="flex:1; border-left: 1px solid var(--color-border); position: relative; overflow-y: auto; overflow-x: hidden; background: var(--color-bg); display: flex; flex-direction: column;">
                    <div class="iframe-wrapper loader-active" id="iframeWrapper">
                        <div class="iframe-loader">
                            <div class="spinner"></div>
                            <span><?= __('modal_loading') ?></span>
                        </div>
                        <iframe id="portfolioIframe" title="Интерактивный просмотр сайта" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" defer></script>
    <!-- Custom Script -->
    <script src="/script.js?v=256" defer></script>
    <!-- Analytics Tracker -->
    <script src="/tracker.js" defer></script>
</body>
