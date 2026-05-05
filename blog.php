<?php
require_once __DIR__ . '/i18n.php';
require 'admin/db.php';

$slug = isset($_GET['article']) ? $_GET['article'] : null;

// Ensure published_at exists (migration fallback for old sites)
try {
    $db->query("SELECT published_at, views, read_time FROM blog_posts LIMIT 1");
} catch(Exception $e) {
    // If columns don't exist yet, we silently ignore here and wait for admin migration.
}

if ($slug) {
    // Article mode
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ?");
    $stmt->execute([$slug]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    // 301 редирект: если slug старый (gen-...) или статья найдена по old_slug
    if (!$article) {
        // Попытка найти по old_slug (если миграция уже выполнена)
        try {
            $stmt2 = $db->prepare("SELECT * FROM blog_posts WHERE old_slug = ?");
            $stmt2->execute([$slug]);
            $article = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($article) {
                header("Location: /blog/" . urlencode($article['slug']) . "/", true, 301);
                exit;
            }
        } catch (Exception $e) {}
    }

    // 301 редирект: генерённые slug'и (gen-...)
    if ($article && strpos($article['slug'], 'gen-') === 0) {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
        exit;
    }

    if (!$article) {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
        exit;
    }
    
    // FETCH RELATED POSTS FOR INTERLINKING
    $rel_stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug != ? AND is_published = 1 ORDER BY RANDOM() LIMIT 3");
    $rel_stmt->execute([$slug]);
    $related_articles = $rel_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Increment views
    try {
        $db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$article['id']]);
        $article['views']++;
    } catch(Exception $e) {}

} else {
    // List mode
    try {
        // Fetch only published articles
        $articles = $db->query("SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e) {
        $articles = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= $is_rtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
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

    <title><?= $slug ? htmlspecialchars($article['seo_title'] ?? $article['title']) : 'Блог Molozin.ru - Развитие бизнеса и веб-разработка' ?></title>
    <meta name="description" content="<?= $slug ? htmlspecialchars($article['seo_desc'] ?? $article['excerpt']) : 'Экспертные статьи по веб-разработке, продвижению, SaaS-инструментам и росту бизнеса от студии Molozin.ru.' ?>">
    
    <meta property="og:title" content="<?= $slug ? htmlspecialchars($article['seo_title'] ?? $article['title']) : 'Блог Molozin.ru' ?>">
    <meta property="og:description" content="<?= $slug ? htmlspecialchars($article['seo_desc'] ?? $article['excerpt']) : 'Статьи о создании и продвижении сайтов, привлечении B2B-клиентов.' ?>">
    <meta property="og:type" content="<?= $slug ? 'article' : 'website' ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://molozin.ru<?= $slug ? '/blog/' . urlencode($article['slug']) . '/' : '/blog/' ?>">

    <!-- RSS фид блога -->
    <link rel="alternate" type="application/rss+xml" href="/blog/rss.xml" title="Блог Molozin.ru">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php if ($slug): ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Article",
      "headline": "<?= htmlspecialchars($article['seo_title'] ?? $article['title']) ?>",
      "description": "<?= htmlspecialchars($article['seo_desc'] ?? $article['excerpt']) ?>",
      "datePublished": "<?= date('Y-m-d\TH:i:sP', strtotime($article['published_at'] ?? $article['created_at'])) ?>",
      "author": { "@type": "Organization", "name": "Molozin.ru" },
      "publisher": {
        "@type": "Organization", "name": "Molozin.ru",
        "logo": { "@type": "ImageObject", "url": "https://molozin.ru/favicon.png" }
      }
    }
    </script>
    <?php else: ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Blog",
      "name": "Блог Molozin.ru",
      "url": "https://molozin.ru/blog/",
      "description": "Решения для бизнеса: как сайты генерируют прибыль, снижают расходы и привлекают лиды."
    }
    </script>
    <?php endif; ?>

    <!-- Breadcrumbs Schema.org -->
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
          "name": "Блог",
          "item": "https://molozin.ru/blog/"
        }<?php if ($slug): ?>,
        {
          "@type": "ListItem",
          "position": 3,
          "name": "<?= htmlspecialchars($article['title']) ?>",
          "item": "https://molozin.ru/blog/<?= urlencode($article['slug']) ?>/"
        }
        <?php endif; ?>
      ]
    }
    </script>

    <!-- Стили (версионирование через filemtime) -->
    <link rel="stylesheet" href="/styles.css?v=<?= filemtime(__DIR__.'/styles.css') ?>">
    <link rel="stylesheet" href="/css/blog.css?v=<?= filemtime(__DIR__.'/css/blog.css') ?>">
    <script src="/tracker.js" defer></script>
</head>
<body>
    <?php $is_blog_page = true; require_once __DIR__ . '/includes/header.php'; ?>

    <div class="container">
        <?php if ($slug): ?>
            <!-- ЧТЕНИЕ СТАТЬИ -->
            <div class="article-page">
                <a href="/blog/" class="btn-back-blog">
                    <i class="fas fa-arrow-left btn-back-icon"></i> В блог
                </a>
                
                <div class="article-meta-top">
                    <?php 
                        $catName = $article['category_name'] ?? 'Аналитика';
                        $hash = crc32($catName);
                        $gradClass = "badge-grad-" . (($hash % 4) + 1);
                    ?>
                    <span class="badge <?= $gradClass ?>"><?= htmlspecialchars($catName) ?></span>
                    <span><i class="far fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($article['published_at'] ?? $article['created_at'])) ?></span>
                    <span class="article-meta-accent"><i class="far fa-clock"></i> <?= $article['read_time'] ?? ceil(str_word_count(strip_tags($article['content']))/200) ?> мин</span>
                    <span><i class="far fa-eye"></i> <?= $article['views'] ?? 1 ?></span>
                </div>
                
                <h1 class="article-title-main"><?= htmlspecialchars($article['title']) ?></h1>
                
                <?php if (!empty($article['image'])): ?>
                    <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="article-featured-img">
                <?php endif; ?>

                <article class="article-content">
                    <?php
                        // Auto-interlinking
                        $content = $article['content'];
                        $links = [
                            '/(\bсоздани[еяю]\s+сайт[аов]\b)/ui' => '<a href="/#services" class="magic-link">$1</a>',
                            '/(\bпродвижени[еяю]\b)/ui' => '<a href="/#services" class="magic-link">$1</a>',
                            '/(\b(SEO|сео)\b)/ui' => '<a href="/#services" class="magic-link">$1</a>',
                            '/(\bзаказать\s+сайт\b)/ui' => '<a href="/#contacts" class="magic-link">$1</a>',
                            '/(\bкалькулятор\b)/ui' => '<a href="http://3dcorp.ru/" target="_blank" class="magic-link">$1</a>',
                            '/(\bSaaS\b)/ui' => '<a href="https://r-70.ru/" target="_blank" class="magic-link">$1</a>',
                            '/(\bсправочник\b)/ui' => '<a href="https://best70.ru/" target="_blank" class="magic-link">$1</a>',
                            '/(\b(Molozin|Молозин)\b)/ui' => '<a href="/" class="magic-link">$1</a>',
                            '/(\bдизайн[ау]?\b)/ui' => '<a href="/#services" class="magic-link">$1</a>'
                        ];
                        
                        // Protect existing links by replacing them with placeholders
                        $placeholders = [];
                        $content = preg_replace_callback('/<a[^>]*>.*?<\/a>/is', function($m) use (&$placeholders) {
                            $key = '@@LINK_PLACEHOLDER_' . count($placeholders) . '@@';
                            $placeholders[$key] = $m[0];
                            return $key;
                        }, $content);
                        
                        // Protect headers (h1-h6) to avoid linking titles
                        $content = preg_replace_callback('/<h[1-6][^>]*>.*?<\/h[1-6]>/is', function($m) use (&$placeholders) {
                            $key = '@@HEADER_PLACEHOLDER_' . count($placeholders) . '@@';
                            $placeholders[$key] = $m[0];
                            return $key;
                        }, $content);

                        foreach ($links as $pattern => $replacement) {
                            // Only replace once per pattern to avoid over-linking
                            $content = preg_replace($pattern, $replacement, $content, 1);
                        }
                        
                        // Restore placeholders
                        foreach ($placeholders as $key => $html) {
                            $content = str_replace($key, $html, $content);
                        }
                        
                        echo $content;
                    ?>
                </article>
                
                <div class="article-cta-block">
                    <h3 class="article-cta-title">Готовы опередить конкурентов?</h3>
                    <p class="article-cta-desc">Доверьте разработку сайта или портала профессионалам. Мы создаем инструменты, которые приносят прибыль.</p>
                    <a href="/#contacts" class="btn btn-primary btn-large article-cta-btn">Обсудить ваш проект</a>
                </div>

                <!-- ПЕРЕЛИНКОВКА (Читайте также) -->
                <?php if (!empty($related_articles)): ?>
                <div class="related-articles-section">
                    <h3 class="related-articles-title">Читайте также</h3>
                    <div class="related-articles-grid">
                        <?php foreach($related_articles as $r_art): 
                            $r_catName = $r_art['category_name'] ?? 'Решения';
                            $r_hash = crc32($r_catName);
                            $r_gradClass = "badge-grad-" . (($r_hash % 4) + 1);
                        ?>
                        <div class="news-card related-article-card">
                            <?php if(!empty($r_art['image'])): ?>
                            <a href="/blog/<?= urlencode($r_art['slug']) ?>/" class="news-img-wrap">
                                <img src="<?= htmlspecialchars($r_art['image']) ?>" alt="<?= htmlspecialchars($r_art['title']) ?>" class="news-img" loading="lazy">
                                <div class="news-badges">
                                    <span class="news-badge news-badge-right <?= $r_gradClass ?>"><?= htmlspecialchars($r_catName) ?></span>
                                </div>
                            </a>
                            <?php endif; ?>
                            <div class="news-body">
                                <div class="news-meta-row">
                                    <span><?= date('d.m.Y', strtotime($r_art['published_at'] ?? $r_art['created_at'])) ?></span>
                                </div>
                                <h4 class="news-title">
                                    <a href="/blog/<?= urlencode($r_art['slug']) ?>/">
                                        <?= htmlspecialchars(__($r_art['title'])) ?>
                                    </a>
                                </h4>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <!-- СПИСОК СТАТЕЙ КАРТОЧКАМИ -->
            <div class="blog-header">
                <h1><?= __('blog_main_title') ?></h1>
                <p class="blog-header-desc">
                    <?= __('blog_main_desc') ?>
                </p>
            </div>
            
            <div class="news-layout-grid">
                <?php if (empty($articles)): ?>
                    <div class="blog-empty-state">
                        <i class="far fa-folder-open fa-3x blog-empty-icon"></i>
                        <h3><?= __('blog_empty_title') ?></h3>
                        <p><?= __('blog_empty_desc') ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($articles as $index => $art): 
                        $hasImage = !empty($art['image']);
                        $catName = $art['category_name'] ?? 'Решения';
                        $hash = crc32($catName);
                        $gradClass = "badge-grad-" . (($hash % 4) + 1);
                        
                        $wordCount = str_word_count(strip_tags($art['content'] ?? ''));
                        if ($wordCount < 10) $wordCount = mb_strlen(strip_tags($art['content'] ?? '')) / 6;
                        $readTime = $art['read_time'] ?? max(1, ceil($wordCount / 200));
                        
                        $isHot = ($art['views'] ?? 0) > 100;
                    ?>
                        <?php if ($hasImage): ?>
                            <!-- CARD WITH IMAGE -->
                            <article class="news-card">
                                <a href="/blog/<?= urlencode($art['slug']) ?>/" class="news-img-wrap">
                                    <img src="<?= htmlspecialchars($art['image']) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="news-img" loading="lazy">
                                    <div class="news-badges">
                                        <span class="news-badge news-badge-right <?= $gradClass ?>"><?= htmlspecialchars($catName) ?></span>
                                        <?php if($isHot): ?>
                                            <span class="news-badge news-badge-hot">🔥 Hot</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <div class="news-body">
                                    <div class="news-meta-row">
                                        <div class="news-meta-left">
                                            <span><?= date('d.m.Y', strtotime($art['published_at'] ?? $art['created_at'])) ?></span>
                                            <span class="reading-time">
                                                <i class="far fa-clock"></i> <?= $readTime ?> мин
                                            </span>
                                        </div>
                                        <div><i class="far fa-eye"></i> <?= $art['views'] ?? 1 ?></div>
                                    </div>
                                    <h3 class="news-title">
                                        <a href="/blog/<?= urlencode($art['slug']) ?>/">
                                            <?= htmlspecialchars(__($art['title'])) ?>
                                        </a>
                                    </h3>
                                    <div class="news-excerpt">
                                        <?= htmlspecialchars(__($art['excerpt'] ?? mb_substr(strip_tags($art['content'] ?? ''), 0, 150).'...')) ?>
                                    </div>
                                </div>
                            </article>
                        <?php else: ?>
                            <!-- TEXT ONLY CARD -->
                            <article class="news-card news-card-text-only">
                                <div class="news-body">
                                    <div class="news-badges">
                                        <span class="news-badge <?= $gradClass ?>"><?= htmlspecialchars($catName) ?></span>
                                    </div>
                                    <h3 class="news-title news-title-auto">
                                        <a href="/blog/<?= urlencode($art['slug']) ?>/">
                                            <?= htmlspecialchars(__($art['title'])) ?>
                                        </a>
                                    </h3>
                                    <div class="news-excerpt">
                                        <?= htmlspecialchars(__($art['excerpt'] ?? mb_substr(strip_tags($art['content'] ?? ''), 0, 250).'...')) ?>
                                    </div>
                                    <div class="news-meta-row">
                                        <div class="news-meta-left">
                                            <span><?= date('d.m.Y', strtotime($art['published_at'] ?? $art['created_at'])) ?></span>
                                            <span class="reading-time">
                                                <i class="far fa-clock"></i> <?= $readTime ?> мин
                                            </span>
                                        </div>
                                        <div><i class="far fa-eye"></i> <?= $art['views'] ?? 1 ?></div>
                                    </div>
                                </div>
                            </article>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <!-- Кнопка «Наверх» -->
    <button class="scroll-top-btn" id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Наверх" title="Наверх">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    <script>
    // Показ/скрытие кнопки «Наверх» при прокрутке
    (function() {
        var btn = document.getElementById('scrollTopBtn');
        if (!btn) return;
        var ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    if (window.scrollY > 400) {
                        btn.classList.add('visible');
                    } else {
                        btn.classList.remove('visible');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    })();
    </script>

    <!-- Основной скрипт (версионирование через filemtime) -->
    <script src="/script.js?v=<?= filemtime(__DIR__.'/script.js') ?>"></script>
</body>
</html>
