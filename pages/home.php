<?php
$__title = t('home.meta_title');
$__meta_desc = t('home.meta_desc');
$__extra_css = ['home.css'];
$__extra_head = '<script type="application/ld+json">{"@context":"https://schema.org","@type":"SportsClub","name":"Axel Club Tournai","description":"' . t('home.schema_desc') . '","url":"' . base_url() . '","email":"axelclubtournaifedere@gmail.com","foundingDate":"2010","sport":"Patinage artistique","address":{"@type":"PostalAddress","addressLocality":"Tournai","addressRegion":"Hainaut","addressCountry":"BE"}}</script>';
require __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div class="sparkles-overlay">
        <?php for($i=0;$i<12;$i++) echo '<span class="sparkle"></span>'; ?>
    </div>
    <div class="hero-content">
        <div class="hero-icon animate-elegant"><img src="/static/images/logo.png" alt="Axel Club" style="width:50px;height:50px;object-fit:contain;"></div>
        <h1>
            <span><?= e(t('home.hero.line1')) ?></span>
            <span><?= e(t('home.hero.line2')) ?></span>
        </h1>
        <p><?= e(t('home.hero.subtitle')) ?></p>
        <?php if (!is_logged_in()): ?>
            <a href="/auth/register" class="btn btn-rhinestone btn-lg competition-glow"><?= e(t('home.hero.cta')) ?></a>
        <?php endif; ?>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2><?= e(t('home.services.title')) ?></h2>
        <div class="features-grid">
            <div class="feature-card animate-fade">
                <div class="feature-icon">👥</div>
                <h3><?= e(t('home.service1.title')) ?></h3>
                <p><?= e(t('home.service1.text')) ?></p>
            </div>
            <div class="feature-card animate-fade">
                <div class="feature-icon">🏅</div>
                <h3><?= e(t('home.service2.title')) ?></h3>
                <p><?= e(t('home.service2.text')) ?></p>
            </div>
            <div class="feature-card animate-fade">
                <div class="feature-icon">📊</div>
                <h3><?= e(t('home.service3.title')) ?></h3>
                <p><?= e(t('home.service3.text')) ?></p>
            </div>
            <div class="feature-card animate-fade">
                <div class="feature-icon">🎯</div>
                <h3><?= e(t('home.service4.title')) ?></h3>
                <p><?= e(t('home.service4.text')) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="cta-content">
        <h2><?= e(t('home.cta.title')) ?></h2>
        <p><?= e(t('home.cta.text')) ?></p>
        <a href="/groupes" class="btn btn-primary btn-lg"><?= e(t('home.cta.button')) ?></a>
    </div>
</section>

<script>
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('scrolled', window.scrollY > 50);
});
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
