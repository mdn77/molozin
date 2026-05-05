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
