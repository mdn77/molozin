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
