<!-- Footer -->
<footer class="footer" id="contacts">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <a href="/" class="footer-logo">
                    <div class="logo-icon logo-icon-sm">
                        <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
                            <rect width="40" height="40" rx="12" fill="url(#footer_logo_grad)" fill-opacity="0.1"/>
                            <path d="M10 28V12L20 22L30 12V28" stroke="url(#footer_logo_grad)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <defs>
                                <linearGradient id="footer_logo_grad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
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
                <p class="footer-description">
                    <?= __('footer_desc') ?><br>
                    <?= __('contact_address') ?>
                </p>
                <div class="social-links-text" style="display:flex; flex-direction:column; gap:12px;">
                    <a href="https://vk.com/mdn77" target="_blank" rel="noopener noreferrer" style="color:var(--color-text); text-decoration:none; display:flex; align-items:center; gap:8px;" aria-label="VK">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12.7 20.7h-1.4c-6.1 0-9.6-4.2-9.7-11.1v-.4h3c0 5.2 2.4 7.4 4.2 7.9V9.2h2.8v4.2c1.8-.2 3.7-2.2 4.3-4.2h2.8c-.5 2.6-2.5 4.5-3.9 5.3 1.4.6 3.7 2.3 4.6 5.2h-3.1c-.7-1.9-2.4-3.4-4.6-3.6v3.6z" />
                        </svg>
                        В Контакте
                    </a>
                    <a href="https://t.me/molozin" target="_blank" rel="noopener noreferrer" style="color:var(--color-text); text-decoration:none; display:flex; align-items:center; gap:8px;" aria-label="Telegram">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" />
                        </svg>
                        Telegram @molozin
                    </a>
                    <a href="https://wa.me/79234064441" target="_blank" rel="noopener noreferrer" style="color:var(--color-text); text-decoration:none; display:flex; align-items:center; gap:8px;" aria-label="WhatsApp">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        Ватсап +79234064441
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
        <!-- Юридическая информация -->
        <div style="text-align: center; padding-top: 8px; border-top: 1px solid var(--color-border); margin-top: 8px; font-size: 0.75rem; color: var(--color-text-secondary); line-height: 1.6;">
            ИП Молозина Светлана Алексеевна &nbsp;|&nbsp; Свидетельство о гос. регистрации 70№001544792 &nbsp;|&nbsp; ИНН 701714853100 &nbsp;|&nbsp; ОГРНИП 310701723500037
        </div>
    </div>
</footer>
