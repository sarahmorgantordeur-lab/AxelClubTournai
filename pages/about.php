<?php
$__title = 'À Propos - Axel Club Tournai';
$__meta_desc = t('about.meta_desc');
$__extra_css = ['home.css', 'about.css'];
$__active = 'about';
require __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <h1><?= e(t('about.title')) ?></h1>
    <section>
        <h2><?= e(t('about.history.title')) ?></h2>
        <p><?= t('about.history.text') ?></p>
    </section>
    <section>
        <h2><?= e(t('about.mission.title')) ?></h2>
        <p><?= e(t('about.mission.text')) ?></p>
    </section>
    <section>
        <h2><?= e(t('about.coach.title')) ?></h2>
        <p><?= e(t('about.coach.text')) ?></p>
    </section>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
