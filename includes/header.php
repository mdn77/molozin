<!-- Header -->
<header class="header<?= $is_blog_page ? ' scrolled' : '' ?>" id="header">
    <div class="container">
        <nav class="nav">
            <a href="/" class="logo">
                <div class="logo-icon">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <?php if ($is_blog_page): ?>
                        <rect width="40" height="40" rx="12" fill="url(#logo_grad_header)" fill-opacity="0.1"/>
                        <?php endif; ?>
                        <path d="M10 28V12L20 22L30 12V28" stroke="url(#logo_grad_header)" stroke-width="<?= $is_blog_page ? '3.5' : '4' ?>" stroke-linecap="round" stroke-linejoin="round"/>
                        <defs>
                            <linearGradient id="logo_grad_header" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#6366f1"/>
                                <stop offset="1" stop-color="#0ea5e9"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="logo-type">
                    <span class="logo-text">olozin</span>
                    <span class="logo-accent">.ru</span>
                </div>
            </a>

            <ul class="nav-menu" id="navMenu">
                <li><a href="/#services" class="nav-link"><?= __('menu_services') ?></a></li>
                <li><a href="/#portfolio" class="nav-link"><?= __('menu_portfolio') ?></a></li>
                <li><a href="/#process" class="nav-link"><?= __('menu_process') ?></a></li>
                <li><a href="/#pricing" class="nav-link"><?= __('menu_pricing') ?></a></li>
                <li><a href="/blog/" class="nav-link<?= $is_blog_page ? ' nav-link-active' : '' ?>"><?= __('menu_blog') ?></a></li>
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
                        <img src="/assets/flags/<?= $curr['icon'] ?>.svg" width="20" alt="<?= strtoupper($curr['icon']) ?>" class="lang-flag">
                        <span class="lang-code"><?= strtoupper($lang) ?></span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    <div class="lang-dropdown-menu">
                        <?php foreach ($langs_display as $k => $v): ?>
                            <a href="?lang=<?= $k ?><?= $is_blog_page && isset($slug) ? '&article='.urlencode($slug) : '' ?>" class="lang-option <?= $k === $lang ? 'active' : '' ?>" data-lang="<?= $k ?>">
                                <img src="/assets/flags/<?= $v['icon'] ?>.svg" width="20" alt="<?= strtoupper($v['icon']) ?>" class="lang-flag"> <?= $v['name'] ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button class="theme-toggle" id="themeToggle" aria-label="Переключить тему">
                    <svg class="icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                    <svg class="icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </button>
                
                <?php if (!$is_blog_page): ?>
                <!-- Телефон в шапке (как у всех ТОП конкурентов) -->
                <a href="tel:+79234064441" class="header-phone d-none-mobile" title="Позвонить">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    +7 (923) 406-44-41
                </a>
                <?php endif; ?>

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
