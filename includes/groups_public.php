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

<section class="groups-cta">
    <h2><?= e(t('groups.cta.title')) ?></h2>
    <p><?= e(t('groups.cta.text')) ?></p>
    <a href="/auth/register" class="btn btn-accent btn-lg"><?= e(t('groups.cta.button')) ?></a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
