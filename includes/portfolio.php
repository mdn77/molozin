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
            require_once __DIR__ . '/../admin/db.php';
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
