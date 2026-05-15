<?php
$__title = "Groupes d'Entraînement - Axel Club Tournai";
$__meta_desc = "Découvrez les groupes d'entraînement de l'Axel Club Tournai : débutants, intermédiaires, avancés et compétition.";
$__extra_css = ['home.css', 'groups.css'];
$groups = group_all();
require __DIR__ . '/../includes/header.php';
?>
<section class="groups-hero">
    <h1>Nos Groupes d'Entraînement</h1>
    <p>Trouvez le groupe qui correspond à votre niveau et à vos ambitions sur la glace.</p>
</section>

<section class="groups-section">
    <?php if ($groups): ?>
    <div class="groups-grid">
        <?php foreach ($groups as $g): ?>
        <div class="group-card">
            <div class="group-card-header">
                <h3><?= e($g['name']) ?></h3>
                <?php if ($g['description']): ?><p><?= e($g['description']) ?></p><?php endif; ?>
            </div>
            <div class="group-card-body">
                <div class="group-info-item">
                    <div class="group-info-icon">🕐</div>
                    <div class="group-info-text">
                        <label>Horaires</label>
                        <span><?= e($g['schedule'] ?: 'À confirmer') ?></span>
                    </div>
                </div>
                <div class="group-info-item">
                    <div class="group-info-icon">👥</div>
                    <div class="group-info-text">
                        <label>Membres</label>
                        <span><?= group_member_count($g['id']) ?> membres</span>
                    </div>
                </div>
                <div class="group-info-item">
                    <div class="group-info-icon">💰</div>
                    <div class="group-info-text">
                        <label>Tarif</label>
                        <span><?= $g['price_per_season'] ? number_format($g['price_per_season'], 0) . '€ / saison' : 'Sur demande' ?></span>
                    </div>
                </div>
            </div>
            <div class="group-card-footer">
                <a href="mailto:axelclubtournai@federe.com" class="btn btn-primary btn-sm">Nous contacter</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align:center;padding:60px 20px;">
        <div style="font-size:4rem;margin-bottom:20px;">⛸</div>
        <h3>Aucun groupe disponible</h3>
        <p style="color:var(--text-light);">Les groupes d'entraînement seront bientôt disponibles.</p>
    </div>
    <?php endif; ?>
</section>

<section class="groups-cta">
    <h2>Prêt à nous rejoindre ?</h2>
    <p>Inscrivez-vous dès maintenant et découvrez le plaisir du patinage artistique.</p>
    <a href="/auth/register" class="btn btn-accent btn-lg">Créer un compte</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
