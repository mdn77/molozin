<?php
require_once __DIR__ . '/i18n.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= $is_rtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('Privacy Policy') ?> | Molozin.ru</title>
    <link rel="canonical" href="https://molozin.ru/privacy/">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="styles.css?v=15">
    <script src="tracker.js" defer></script>
</head>
<body>
    <header class="header scrolled" id="header">
        <div class="container">
            <nav class="nav">
                <a href="/" class="logo">
                    <span class="logo-text">Molozin</span>
                    <span class="logo-accent">.ru</span>
                </a>

                <ul class="nav-menu" id="navMenu">
                    <li><a href="/#services" class="nav-link"><?= __('menu_services') ?></a></li>
                    <li><a href="/#portfolio" class="nav-link"><?= __('menu_portfolio') ?></a></li>
                    <li><a href="/#process" class="nav-link"><?= __('menu_process') ?></a></li>
                    <li><a href="/#pricing" class="nav-link"><?= __('menu_pricing') ?></a></li>
                    <li><a href="/blog/" class="nav-link"><?= __('menu_blog') ?></a></li>
                    <li><a href="/#contacts" class="nav-link"><?= __('menu_contacts') ?></a></li>
                </ul>

                <div class="header-actions">
                    <?php
                        $langs_display = [
                            'ru' => ['icon' => '🇷🇺', 'name' => 'Русский'],
                            'en' => ['icon' => '🇬🇧', 'name' => 'English'],
                            'zh' => ['icon' => '🇨🇳', 'name' => '中文'],
                            'es' => ['icon' => '🇪🇸', 'name' => 'Español'],
                            'hi' => ['icon' => '🇮🇳', 'name' => 'हिन्दी'],
                            'ar' => ['icon' => '🇸🇦', 'name' => 'العربية'],
                            'bn' => ['icon' => '🇧🇩', 'name' => 'বাংলা'],
                            'pt' => ['icon' => '🇵🇹', 'name' => 'Português'],
                            'ja' => ['icon' => '🇯🇵', 'name' => '日本語'],
                            'pa' => ['icon' => '🇵🇰', 'name' => 'ਪੰਜਾਬੀ']
                        ];
                        $curr = $langs_display[$lang] ?? $langs_display['en'];
                    ?>
                    <div class="lang-dropdown" id="langDropdown">
                        <button class="lang-dropdown-toggle" aria-label="Change Language">
                            <span class="lang-icon"><?= $curr['icon'] ?></span>
                            <span class="lang-code"><?= strtoupper($lang) ?></span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <div class="lang-dropdown-menu">
                            <?php foreach ($langs_display as $k => $v): ?>
                                <a href="?lang=<?= $k ?>" class="lang-option <?= $k === $lang ? 'active' : '' ?>" data-lang="<?= $k ?>">
                                    <span class="lang-icon"><?= $v['icon'] ?></span> <?= $v['name'] ?>
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

    <div class="legal-page">
        <a href="/" class="btn-back">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            <?= __('Back to Home') ?>
        </a>
        <h1 class="legal-title"><?= __('Политика конфиденциальности') ?></h1>
        
        <div class="legal-content">
            <p><?= __('Настоящая Политика конфиденциальности персональных данных (далее – Политика конфиденциальности) действует в отношении всей информации, которую сайт Molozin.ru может получить о Пользователе во время использования сайта, программ и продуктов сайта.') ?></p>
            
            <h2><?= __('1. Определение терминов') ?></h2>
            <p><?= __('1.1 В настоящей Политике конфиденциальности используются следующие термины: "Администрация сайта" – уполномоченные сотрудники на управление сайтом, действующие от имени Molozin.ru, которые организуют и (или) осуществляют обработку персональных данных, а также определяют цели обработки персональных данных, состав персональных данных, подлежащих обработке, действия или операции, совершаемые с персональными данными.') ?></p>
            
            <h2><?= __('2. Общие положения') ?></h2>
            <p><?= __('2.1 Использование Пользователем сайта означает согласие с настоящей Политикой конфиденциальности и условиями обработки персональных данных Пользователя. В случае несогласия с условиями Политики конфиденциальности Пользователь должен прекратить использование сайта.') ?></p>
            
            <h2><?= __('3. Предмет политики конфиденциальности') ?></h2>
            <p><?= __('3.1 Настоящая Политика конфиденциальности устанавливает обязательства Администрации сайта по неразглашению и обеспечению режима защиты конфиденциальности персональных данных, которые Пользователь предоставляет по запросу Администрации сайта при регистрации на сайте или при оформлении заказа для приобретения Услуг.') ?></p>

            <h2><?= __('4. Цели сбора персональной информации') ?></h2>
            <p><?= __('4.1 Персональные данные Пользователя Администрация сайта может использовать в целях: Идентификации Пользователя, зарегистрированного на сайте, для оформления заказа и (или) заключения Договора купли-продажи товара дистанционным способом. Установления с Пользователем обратной связи, включая направление уведомлений, запросов, касающихся использования сайта, оказания услуг, обработка запросов и заявок от Пользователя.') ?></p>
        </div>
    </div>

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
                </div>
                <div class="footer-col">
                    <h4 class="footer-title"><?= __('footer_l1') ?></h4>
                    <ul class="footer-links">
                        <li><a href="/#services"><?= __('footer_l1_1') ?></a></li>
                        <li><a href="/#services"><?= __('footer_l1_2') ?></a></li>
                        <li><a href="/#services"><?= __('footer_l1_3') ?></a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 class="footer-title"><?= __('footer_l2') ?></h4>
                    <ul class="footer-links">
                        <li><a href="/#portfolio"><?= __('footer_l2_1') ?></a></li>
                        <li><a href="/#process"><?= __('footer_l2_2') ?></a></li>
                        <li><a href="/#pricing"><?= __('footer_l2_3') ?></a></li>
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
    <script src="script.js?v=4"></script>
</body>
</html>
