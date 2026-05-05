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
      "url": "https://molozin.ru/blog.php",
      "description": "Решения для бизнеса: как сайты генерируют прибыль, снижают расходы и привлекают лиды."
    }
    </script>
    <?php endif; ?>

    <link rel="stylesheet" href="styles.css?v=20">
    <script src="tracker.js" defer></script>
    
    <style>
        /* Modern Blog Grid System */
        .blog-header { padding: 140px 0 40px; text-align: center; background: radial-gradient(circle at center 20%, rgba(99, 102, 241, 0.05) 0%, transparent 60%); }
        .blog-header h1 { font-family: 'Playfair Display', serif; font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 900; color: var(--color-text); margin-bottom: 20px; letter-spacing: -0.5px; }
        
        .news-layout-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 24px; padding-bottom: 80px; }
        .news-card { grid-column: span 12; }
        
        @media (min-width: 992px) {
            /* 1,2: 2 cols */
            .news-card:nth-child(15n+1), .news-card:nth-child(15n+2) { grid-column: span 6; }
            .news-card:nth-child(15n+1) .news-title, .news-card:nth-child(15n+2) .news-title { font-size: 2rem; }
            /* 3,4,5,6: 4 cols */
            .news-card:nth-child(15n+3), .news-card:nth-child(15n+4), .news-card:nth-child(15n+5), .news-card:nth-child(15n+6) { grid-column: span 3; }
            /* 7,8,9: 3 cols */
            .news-card:nth-child(15n+7), .news-card:nth-child(15n+8), .news-card:nth-child(15n+9) { grid-column: span 4; }
            /* 10,11: 2 cols */
            .news-card:nth-child(15n+10), .news-card:nth-child(15n+11) { grid-column: span 6; }
            .news-card:nth-child(15n+10) .news-title, .news-card:nth-child(15n+11) .news-title { font-size: 2rem; }
            /* 12,13,14,15: 4 cols */
            .news-card:nth-child(15n+12), .news-card:nth-child(15n+13), .news-card:nth-child(15n+14), .news-card:nth-child(15n+15) { grid-column: span 3; }
        }
        @media (min-width: 600px) and (max-width: 991px) {
            .news-card { grid-column: span 6 !important; }
        }

        .news-card { background: var(--color-bg-card); border: 1px solid var(--color-border); border-radius: 16px; overflow: hidden; transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); display: flex; flex-direction: column; height: 100%; box-shadow: var(--shadow-md); }
        .news-card:hover { transform: translateY(-8px); border-color: var(--color-primary); box-shadow: var(--shadow-lg); }
        
        .news-img-wrap { position: relative; padding-top: 60%; overflow: hidden; background: var(--color-bg-secondary); display: block; border-bottom: 1px solid var(--color-border); }
        .news-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; }
        .news-card:hover .news-img { transform: scale(1.08); }

        .news-body { padding: 25px; display: flex; flex-direction: column; flex-grow: 1; }
        .news-meta-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; font-size: 0.8rem; color: var(--color-text-secondary); font-weight: 500; font-family: 'Inter', sans-serif; }
        .news-meta-left { display: flex; align-items: center; gap: 12px; }
        .reading-time { display: inline-flex; align-items: center; gap: 6px; color: var(--color-primary); background: rgba(44, 152, 240, 0.1); padding: 4px 8px; border-radius: 6px; }

        .news-title { font-size: 1.4rem; font-weight: 700; margin: 0 0 15px 0; line-height: 1.35; font-family: 'Playfair Display', serif; word-wrap: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; }
        .news-title a { color: var(--color-text); text-decoration: none; transition: color 0.3s; }
        .news-card:hover .news-title a { color: var(--color-primary); }

        .news-excerpt { font-size: 1rem; color: var(--color-text-secondary); line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 20px; flex-grow: 1; font-family: 'Inter', sans-serif; }

        /* Text Only Cards */
        .news-card-text-only { border-left: 6px solid var(--color-primary); min-height: 320px; justify-content: flex-start; }
        .news-card-text-only .news-body { padding: 35px; justify-content: space-between; }
        .news-card-text-only .news-title { font-size: 1.7rem; color: var(--color-text); }
        .news-card-text-only .news-excerpt { -webkit-line-clamp: 5; line-clamp: 5; color: var(--color-text-secondary); font-size: 1.05rem; }
        .news-card-text-only .news-meta-row { border-top: 1px solid var(--color-border); padding-top: 20px; margin-bottom: 0; margin-top: auto; }

        /* Badges */
        .news-badges { position: absolute; top: 0; left: 0; display: flex; z-index: 2; }
        .news-badge { padding: 8px 16px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #fff; letter-spacing: 0.5px; box-shadow: 2px 2px 10px rgba(0,0,0,0.2); backdrop-filter: blur(4px); }
        .badge-grad-1 { background: linear-gradient(135deg, #FF9966 0%, #FF5E62 90%); }
        .badge-grad-2 { background: linear-gradient(135deg, #56CCF2 0%, #2F80ED 90%); }
        .badge-grad-3 { background: linear-gradient(135deg, #11998e 0%, #38ef7d 90%); }
        .badge-grad-4 { background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 90%); }
        .news-card-text-only .news-badges { position: relative; margin-bottom: 25px; border-radius: 6px; overflow: hidden; display: inline-flex; }
        .news-card-text-only .news-badge { border-radius: 6px; }

        /* Article Reader view */
        .article-page { max-width: 800px; margin: 0 auto; padding: 140px 20px 100px; font-family: 'Inter', sans-serif; }
        .article-meta-top { margin-bottom: 30px; display: flex; align-items: center; justify-content: center; gap: 20px; color: var(--color-text-secondary); font-size: 0.95rem; }
        .article-meta-top .badge { color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .article-title-main { font-family: 'Playfair Display', serif; font-size: clamp(2.2rem, 4vw, 3.5rem); font-weight: 900; line-height: 1.2; text-align: center; color: var(--color-text); margin-bottom: 40px; }
        
        .article-content { font-size: 1.15rem; line-height: 1.8; color: var(--color-text-secondary); }
        .article-content h2, .article-content h3 { font-family: 'Playfair Display', serif; color: var(--color-text); margin: 50px 0 25px; font-weight: 700; line-height: 1.3;}
        .article-content h2 { font-size: 2rem; border-left: 4px solid var(--color-primary); padding-left: 20px; }
        .article-content img { max-width: 100%; height: auto; border-radius: 12px; margin: 40px 0; box-shadow: var(--shadow-lg); }
        .article-content p { margin-bottom: 25px; }
        .article-content ul, .article-content ol { margin-bottom: 25px; padding-left: 20px; }
        .article-content li { margin-bottom: 10px; }
        .article-content blockquote { padding: 30px; background: var(--color-bg-secondary); border-left: 4px solid var(--color-primary); border-radius: 0 12px 12px 0; font-style: italic; font-size: 1.25rem; margin: 40px 0; color: var(--color-text); }
        
        .btn-back-blog { display: inline-flex; align-items: center; justify-content: center; padding: 12px 28px; background: var(--color-bg-card); border: 1px solid var(--color-border); color: var(--color-text); border-radius: 30px; text-decoration: none; font-weight: 600; transition: all 0.3s; margin-bottom: 40px; box-shadow: var(--shadow-sm); }
        .btn-back-blog:hover { background: var(--color-primary); color: #fff; transform: translateX(-5px); border-color: var(--color-primary); box-shadow: var(--shadow-md); }
    </style>
</head>
<body style="background: var(--color-bg);">
    <header class="header scrolled" id="header">
        <div class="container">
            <nav class="nav">
                <a href="/" class="logo">
                    <div class="logo-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="40" height="40" rx="12" fill="url(#logo_grad_blog)" fill-opacity="0.1"/>
                            <path d="M10 28V12L20 22L30 12V28" stroke="url(#logo_grad_blog)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <defs>
                                <linearGradient id="logo_grad_blog" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
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

                <ul class="nav-menu" id="navMenu">
                    <li><a href="/#services" class="nav-link"><?= __('menu_services') ?></a></li>
                    <li><a href="/#portfolio" class="nav-link"><?= __('menu_portfolio') ?></a></li>
                    <li><a href="/#process" class="nav-link"><?= __('menu_process') ?></a></li>
                    <li><a href="/#pricing" class="nav-link"><?= __('menu_pricing') ?></a></li>
                    <li><a href="/blog.php" class="nav-link" style="color:var(--color-primary)"><?= __('menu_blog') ?></a></li>
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
                                <a href="?lang=<?= $k ?><?= $slug ? '&article='.urlencode($slug) : '' ?>" class="lang-option <?= $k === $lang ? 'active' : '' ?>" data-lang="<?= $k ?>">
                                    <img src="/assets/flags/<?= $v['icon'] ?>.svg" width="20" alt="<?= strtoupper($v['icon']) ?>" style="border-radius: 2px;"> <?= $v['name'] ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button class="theme-toggle" id="themeToggle" aria-label="Переключить тему">
                        <svg class="icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                        <svg class="icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                    </button>
                    
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

    <div class="container">
        <?php if ($slug): ?>
            <!-- ЧТЕНИЕ СТАТЬИ -->
            <div class="article-page">
                <a href="/blog.php" class="btn-back-blog">
                    <i class="fas fa-arrow-left" style="margin-right:8px;"></i> В блог
                </a>
                
                <div class="article-meta-top">
                    <?php 
                        $catName = $article['category_name'] ?? 'Аналитика';
                        $hash = crc32($catName);
                        $gradClass = "badge-grad-" . (($hash % 4) + 1);
                    ?>
                    <span class="badge <?= $gradClass ?>"><?= htmlspecialchars($catName) ?></span>
                    <span><i class="far fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($article['published_at'] ?? $article['created_at'])) ?></span>
                    <span style="color:var(--color-primary)"><i class="far fa-clock"></i> <?= $article['read_time'] ?? ceil(str_word_count(strip_tags($article['content']))/200) ?> мин</span>
                    <span><i class="far fa-eye"></i> <?= $article['views'] ?? 1 ?></span>
                </div>
                
                <h1 class="article-title-main"><?= htmlspecialchars($article['title']) ?></h1>
                
                <?php if (!empty($article['image'])): ?>
                    <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" style="width:100%; border-radius:16px; margin-bottom: 50px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
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
                
                <div style="text-align:center; margin-top:80px; padding-top:40px; border-top:1px solid var(--color-border);">
                    <h3 style="font-family:'Playfair Display',serif; color:var(--color-text); font-size:2rem; margin-bottom:20px;">Готовы опередить конкурентов?</h3>
                    <p style="color:var(--color-text-secondary); font-size:1.1rem; margin-bottom:30px;">Доверьте разработку сайта или портала профессионалам. Мы создаем инструменты, которые приносят прибыль.</p>
                    <a href="/#contacts" class="btn btn-primary btn-large" style="padding:15px 40px; font-size:1.1rem;">Обсудить ваш проект</a>
                </div>

                <!-- ПЕРЕЛИНКОВКА (Читайте также) -->
                <?php if (!empty($related_articles)): ?>
                <div style="margin-top: 80px; padding-top: 40px; border-top: 1px solid var(--color-border);">
                    <h3 style="font-family:'Playfair Display',serif; color:var(--color-text); font-size:2rem; margin-bottom:40px; text-align:center;">Читайте также</h3>
                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap:24px;">
                        <?php foreach($related_articles as $r_art): 
                            $r_catName = $r_art['category_name'] ?? 'Решения';
                            $r_hash = crc32($r_catName);
                            $r_gradClass = "badge-grad-" . (($r_hash % 4) + 1);
                        ?>
                        <div class="news-card" style="display:flex; flex-direction:column; height:100%;">
                            <?php if(!empty($r_art['image'])): ?>
                            <a href="blog.php?article=<?= urlencode($r_art['slug']) ?>" class="news-img-wrap" style="padding-top:60%;">
                                <img src="<?= htmlspecialchars($r_art['image']) ?>" alt="<?= htmlspecialchars($r_art['title']) ?>" class="news-img" loading="lazy">
                                <div class="news-badges">
                                    <span class="news-badge <?= $r_gradClass ?>" style="border-radius: 0 0 12px 0;"><?= htmlspecialchars($r_catName) ?></span>
                                </div>
                            </a>
                            <?php endif; ?>
                            <div class="news-body" style="padding:20px; flex-grow:1; display:flex; flex-direction:column;">
                                <div class="news-meta-row" style="margin-bottom:12px; border-bottom:none;">
                                    <span><?= date('d.m.Y', strtotime($r_art['published_at'] ?? $r_art['created_at'])) ?></span>
                                </div>
                                <h4 class="news-title" style="font-size:1.2rem; margin-bottom:0px;">
                                    <a href="blog.php?article=<?= urlencode($r_art['slug']) ?>">
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
                <p style="color:var(--color-text-secondary); font-size:1.2rem; max-width:700px; margin:0 auto; line-height:1.6;">
                    <?= __('blog_main_desc') ?>
                </p>
            </div>
            
            <div class="news-layout-grid">
                <?php if (empty($articles)): ?>
                    <div style="grid-column: 1 / -1; text-align:center; padding: 60px; color:#666;">
                        <i class="far fa-folder-open fa-3x mb-3" style="opacity:0.5; display:block; margin-bottom:20px;"></i>
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
                                <a href="blog.php?article=<?= urlencode($art['slug']) ?>" class="news-img-wrap">
                                    <img src="<?= htmlspecialchars($art['image']) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="news-img" loading="lazy">
                                    <div class="news-badges">
                                        <span class="news-badge <?= $gradClass ?>" style="border-radius: 0 0 12px 0;"><?= htmlspecialchars($catName) ?></span>
                                        <?php if($isHot): ?>
                                            <span class="news-badge" style="background:#e74c3c; border-radius: 0 0 12px 0;">🔥 Hot</span>
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
                                        <a href="blog.php?article=<?= urlencode($art['slug']) ?>">
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
                                    <h3 class="news-title" style="margin-top:auto;">
                                        <a href="blog.php?article=<?= urlencode($art['slug']) ?>">
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
    <!-- Footer -->
    <footer class="footer" id="contacts">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <a href="/" class="footer-logo">
                        <span class="logo-text">Molozin</span>
                        <span class="logo-accent">.ru</span>
                    </a>
                    <p class="footer-description">
                        <?= __('footer_desc') ?><br>
                        <?= __('contact_address') ?>
                    </p>
                    <div class="social-links">
                        <a href="https://vk.com/mdn77" target="_blank" rel="noopener noreferrer" class="social-link"
                            aria-label="VK">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12.7 20.7h-1.4c-6.1 0-9.6-4.2-9.7-11.1v-.4h3c0 5.2 2.4 7.4 4.2 7.9V9.2h2.8v4.2c1.8-.2 3.7-2.2 4.3-4.2h2.8c-.5 2.6-2.5 4.5-3.9 5.3 1.4.6 3.7 2.3 4.6 5.2h-3.1c-.7-1.9-2.4-3.4-4.6-3.6v3.6z" />
                            </svg>
                        </a>
                        <a href="https://t.me/molozin" target="_blank" rel="noopener noreferrer" class="social-link"
                            aria-label="Telegram">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" />
                            </svg>
                        </a>
                        <a href="https://wa.me/79234064441" target="_blank" rel="noopener noreferrer"
                            class="social-link" aria-label="WhatsApp">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
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
        </div>
    </footer>


    <!-- Кнопка «Наверх» -->
    <button class="scroll-top-btn" id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Наверх" title="Наверх">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    <style>
    /* Кнопка «Наверх» — блог */
    .scroll-top-btn {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 999;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #6366f1, #0ea5e9);
        color: #fff;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: opacity 0.35s ease, visibility 0.35s ease, transform 0.35s ease, box-shadow 0.25s ease;
    }
    .scroll-top-btn.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .scroll-top-btn:hover {
        box-shadow: 0 6px 25px rgba(99, 102, 241, 0.6);
        transform: translateY(-3px);
    }
    .scroll-top-btn:active {
        transform: translateY(0);
    }
    @media (max-width: 480px) {
        .scroll-top-btn {
            width: 44px;
            height: 44px;
            bottom: 1.2rem;
            right: 1rem;
        }
    }
    </style>

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

    <script src="script.js?v=4"></script>
</body>
</html>

