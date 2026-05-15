<?php
$__title = 'Axel Club Tournai - Club de Patinage Artistique en Belgique';
$__meta_desc = 'Axel Club Tournai, club de patinage artistique affilié à la FFPA. Cours pour tous les âges dès 3 ans, groupes par niveau, compétitions. Essai gratuit !';
$__extra_css = ['home.css'];
$__extra_head = '<script type="application/ld+json">{"@context":"https://schema.org","@type":"SportsClub","name":"Axel Club Tournai","description":"Club de patinage artistique affilié à la FFPA, situé à Tournai, Belgique.","url":"' . base_url() . '","email":"axelclubtournai@federe.com","telephone":"+32491365328","foundingDate":"2010","sport":"Patinage artistique","address":{"@type":"PostalAddress","addressLocality":"Tournai","addressRegion":"Hainaut","addressCountry":"BE"}}</script>';
require __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div class="sparkles-overlay">
        <?php for($i=0;$i<12;$i++) echo '<span class="sparkle"></span>'; ?>
    </div>
    <div class="hero-content">
        <div class="hero-icon animate-elegant"><img src="/static/images/logo.png" alt="Axel Club" style="width:50px;height:50px;object-fit:contain;"></div>
        <h1>
            <span>Bienvenue au Axel Club</span>
            <span>L'élégance sur glace</span>
        </h1>
        <p>Découvrez la passion du patinage artistique dans un environnement chaleureux et professionnel, pour tous les âges et tous les niveaux.</p>
        <?php if (!is_logged_in()): ?>
            <a href="/auth/register" class="btn btn-rhinestone btn-lg competition-glow">Rejoignez-nous</a>
        <?php endif; ?>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2>Nos Services</h2>
        <div class="features-grid">
            <div class="feature-card animate-fade">
                <div class="feature-icon">👥</div>
                <h3>Groupes par Niveau</h3>
                <p>Débutant, Intermédiaire, Avancé et Compétition - Un parcours adapté à chaque patineur</p>
            </div>
            <div class="feature-card animate-fade">
                <div class="feature-icon">🏅</div>
                <h3>Coachs Expérimentés</h3>
                <p>Suivi personnalisé par des professionnels passionnés et certifiés</p>
            </div>
            <div class="feature-card animate-fade">
                <div class="feature-icon">📊</div>
                <h3>Suivi des Progrès</h3>
                <p>Suivi des présences, évaluations régulières et statistiques détaillées</p>
            </div>
            <div class="feature-card animate-fade">
                <div class="feature-icon">🎯</div>
                <h3>Événements</h3>
                <p>Galas, compétitions et événements conviviaux tout au long de l'année</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="cta-content">
        <h2>Prêt à glisser ?</h2>
        <p>Rejoignez notre communauté de patineurs passionnés et découvrez le plaisir de la glisse.</p>
        <a href="/groupes" class="btn btn-primary btn-lg">Découvrir nos groupes</a>
    </div>
</section>

<script>
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('scrolled', window.scrollY > 50);
});
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
