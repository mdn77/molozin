<!-- Ecosystem Strategic Section -->
<section class="section ecosystem" id="ecosystem">
    <div class="hero-background" style="opacity: 0.3;">
        <div class="gradient-orb orb-2" style="top: -20%; right: -10%;"></div>
    </div>
    <div class="container">
        <div class="section-header">
            <span class="section-label"><?= __('eco_label') ?></span>
            <h2 class="section-title"><?= __('eco_title') ?></h2>
            <p class="section-subtitle"><?= __('eco_sub') ?></p>
        </div>

        <div class="eco-grid">
            <!-- R-70.ru -->
            <a href="https://r-70.ru" target="_blank" class="eco-card-new">
                <div class="eco-card-image-wrap">
                    <picture>
                        <source srcset="/assets/r70-preview.webp" type="image/webp">
                        <img src="/assets/r70-preview.png" alt="R-70.ru" loading="lazy" class="eco-preview-img">
                    </picture>
                    <div class="eco-card-image-overlay"></div>
                    <div class="eco-card-image-title"><?= __('eco_1_title') ?></div>
                </div>
                <div class="eco-card-body">
                    <p class="eco-card-desc"><?= __('eco_1_desc') ?></p>
                    <span class="service-link" style="pointer-events:none;"><?= __('serv_more') ?></span>
                </div>
            </a>

            <!-- 3DCorp.ru -->
            <a href="https://3dcorp.ru" target="_blank" class="eco-card-new">
                <div class="eco-card-image-wrap">
                    <picture>
                        <source srcset="/assets/3dcorp-preview.webp" type="image/webp">
                        <img src="/assets/3dcorp-preview.png" alt="3DCorp.ru" loading="lazy" class="eco-preview-img">
                    </picture>
                    <div class="eco-card-image-overlay"></div>
                    <div class="eco-card-image-title"><?= __('eco_2_title') ?></div>
                </div>
                <div class="eco-card-body">
                    <p class="eco-card-desc"><?= __('eco_2_desc') ?></p>
                    <span class="service-link" style="pointer-events:none;"><?= __('serv_more') ?></span>
                </div>
            </a>

            <!-- Best70.ru -->
            <a href="https://best70.ru" target="_blank" class="eco-card-new">
                <div class="eco-card-image-wrap">
                    <picture>
                        <source srcset="/assets/best70-preview.webp" type="image/webp">
                        <img src="/assets/best70-preview.png" alt="Best70.ru" loading="lazy" class="eco-preview-img">
                    </picture>
                    <div class="eco-card-image-overlay"></div>
                    <div class="eco-card-image-title"><?= __('eco_3_title') ?></div>
                </div>
                <div class="eco-card-body">
                    <p class="eco-card-desc"><?= __('eco_3_desc') ?></p>
                    <span class="service-link" style="pointer-events:none;"><?= __('serv_more') ?></span>
                </div>
            </a>
        </div>
    </div>
</section>
