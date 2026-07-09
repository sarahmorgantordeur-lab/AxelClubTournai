<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section footer-brand">
                <h4>⛸ Axel Club</h4>
                <p><?= t('footer.tagline') ?></p>
                <div class="footer-sparkle"></div>
            </div>
            <div class="footer-section">
                <h4><?= e(t('footer.nav.title')) ?></h4>
                <ul>
                    <li><a href="/"><?= e(t('nav.home')) ?></a></li>
                    <li><a href="/groupes"><?= e(t('nav.groups')) ?></a></li>
                    <li><a href="/evenements"><?= e(t('nav.events')) ?></a></li>
                    <li><a href="/a-propos"><?= e(t('nav.about')) ?></a></li>
                    <li><a href="/contact"><?= e(t('nav.contact')) ?></a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4><?= e(t('footer.contact.title')) ?></h4>
                <ul class="contact-list">
                    <li>
                        <span class="contact-icon">✉</span>
                        <a href="mailto:axelclubtournai@federe.com">axelclubtournai@federe.com</a>
                    </li>
                    <li>
                        <span class="contact-icon">📞</span>
                        <a href="tel:+32491365328">+32 491 36 53 28</a>
                    </li>
                    <li>
                        <span class="contact-icon">📷</span>
                        <a href="https://www.instagram.com/axelclubtournai/" target="_blank" rel="noopener noreferrer">Instagram</a>
                    </li>
                </ul>
            </div>
            <div class="footer-section">
                <h4><?= e(t('footer.hours.title')) ?></h4>
                <p><?= t('footer.hours.text') ?></p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p>&copy; <?= t('footer.copyright', ['year' => date('Y')]) ?></p>
                <p>
                    <a href="/mentions-legales" style="color:rgba(255,255,255,0.5);font-size:0.8rem;"><?= e(t('footer.legal')) ?></a>
                    · <a href="/reglement-interieur" style="color:rgba(255,255,255,0.5);font-size:0.8rem;"><?= e(t('footer.roi')) ?></a>
                    · <a href="/politique-rgpd" style="color:rgba(255,255,255,0.5);font-size:0.8rem;"><?= e(t('footer.rgpd')) ?></a>
                </p>
                <p class="footer-credit"><?= t('footer.credit') ?></p>
            </div>
        </div>
    </div>
</footer>
<script src="/static/js/nav.js"></script>
</body>
</html>
