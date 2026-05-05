<!-- Contact / Footer CTA Section -->
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
