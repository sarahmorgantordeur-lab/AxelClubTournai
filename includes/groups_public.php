<?php
$__title = "Groupes d'Entraînement - Axel Club Tournai";
$__meta_desc = t('groups.meta_desc');
$__extra_css = ['home.css', 'groups.css'];
$__active = 'groups';
$groups = group_all();
require __DIR__ . '/../includes/header.php';
?>
<section class="groups-hero">
    <h1><?= e(t('groups.hero.title')) ?></h1>
    <p><?= e(t('groups.hero.subtitle')) ?></p>
</section>

<?php require __DIR__ . '/../includes/group_timetable.php'; ?>

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
                        <label><?= e(t('groups.schedule.label')) ?></label>
                        <span><?= e($g['schedule'] ?: t('groups.schedule.tbd')) ?></span>
                    </div>
                </div>
                <div class="group-info-item">
                    <div class="group-info-icon">👥</div>
                    <div class="group-info-text">
                        <label><?= e(t('groups.members.label')) ?></label>
                        <span><?= e(t('groups.members.count', ['count' => group_member_count($g['id'])])) ?></span>
                    </div>
                </div>
                <div class="group-info-item">
                    <div class="group-info-icon">💰</div>
                    <div class="group-info-text">
                        <label><?= e(t('groups.price.label')) ?></label>
                        <span><?= $g['price_per_season'] ? e(t('groups.price.per_season', ['price' => number_format($g['price_per_season'], 0)])) : e(t('groups.price.on_request')) ?></span>
                    </div>
                </div>
            </div>
            <div class="group-card-footer">
                <a href="mailto:axelclubtournaifedere@gmail.com" class="btn btn-primary btn-sm"><?= e(t('groups.contact_button')) ?></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align:center;padding:60px 20px;">
        <div style="font-size:4rem;margin-bottom:20px;">⛸</div>
        <h3><?= e(t('groups.empty.title')) ?></h3>
        <p style="color:var(--text-light);"><?= e(t('groups.empty.text')) ?></p>
    </div>
    <?php endif; ?>
</section>

<section class="groups-cta">
    <h2><?= e(t('groups.cta.title')) ?></h2>
    <p><?= e(t('groups.cta.text')) ?></p>
    <a href="/auth/register" class="btn btn-accent btn-lg"><?= e(t('groups.cta.button')) ?></a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
