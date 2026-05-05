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

    <?php require_once __DIR__ . '/includes/seo-schemas.php'; ?>
</head>

<body>

    <?php $is_blog_page = false; require_once __DIR__ . '/includes/header.php'; ?>

    <?php require_once __DIR__ . '/includes/hero.php'; ?>

    <?php require_once __DIR__ . '/includes/clients.php'; ?>

    <?php require_once __DIR__ . '/includes/portfolio.php'; ?>

    <?php require_once __DIR__ . '/includes/services.php'; ?>

    <?php require_once __DIR__ . '/includes/ecosystem.php'; ?>

    <?php require_once __DIR__ . '/includes/process.php'; ?>

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

    <?php require_once __DIR__ . '/includes/pricing.php'; ?>

    <?php require_once __DIR__ . '/includes/contact.php'; ?>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

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
</html>
