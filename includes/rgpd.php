<?php
$__title = 'Politique de Confidentialité RGPD - Axel Club Tournai';
$__extra_css = ['home.css'];
$__extra_head = '<style>.legal-container{max-width:800px;margin:0 auto;padding:calc(80px + var(--space-xl)) var(--space-md) var(--space-xl);}.legal-container h1{color:var(--ice-deep);margin-bottom:var(--space-lg);font-size:clamp(1.6rem,4vw,2.2rem);}.legal-container h2{color:var(--ice-dark);margin-top:var(--space-lg);margin-bottom:var(--space-sm);font-size:1.1rem;text-transform:uppercase;letter-spacing:.5px;}.legal-container p,.legal-container li{color:var(--text-medium);line-height:1.8;font-size:.95rem;}.legal-container ul{padding-left:1.4rem;}.legal-divider{border:none;border-top:1px solid var(--silver-light);margin:var(--space-lg) 0;}</style>';
require __DIR__ . '/../includes/header.php';
$__rgpd_email = '<a href="mailto:axelclubtournaifedere@gmail.com">axelclubtournaifedere@gmail.com</a>';
?>
<div class="legal-container">
    <h1><?= e(t('rgpd.title')) ?></h1>
    <p><?= e(t('rgpd.updated', ['date' => date('d/m/Y')])) ?></p>
    <hr class="legal-divider">

    <h2><?= e(t('rgpd.controller.title')) ?></h2>
    <p><?= t('rgpd.controller.text') ?></p>

    <hr class="legal-divider">
    <h2><?= e(t('rgpd.data.title')) ?></h2>
    <p><?= e(t('rgpd.data.intro')) ?></p>
    <ul>
        <li><?= e(t('rgpd.data.item1')) ?></li>
        <li><?= e(t('rgpd.data.item2')) ?></li>
        <li><?= e(t('rgpd.data.item3')) ?></li>
        <li><?= e(t('rgpd.data.item4')) ?></li>
        <li><?= e(t('rgpd.data.item5')) ?></li>
        <li><?= e(t('rgpd.data.item6')) ?></li>
    </ul>

    <hr class="legal-divider">
    <h2><?= e(t('rgpd.purpose.title')) ?></h2>
    <p><?= e(t('rgpd.purpose.intro')) ?></p>
    <ul>
        <li><?= e(t('rgpd.purpose.item1')) ?></li>
        <li><?= e(t('rgpd.purpose.item2')) ?></li>
        <li><?= e(t('rgpd.purpose.item3')) ?></li>
        <li><?= e(t('rgpd.purpose.item4')) ?></li>
    </ul>

    <hr class="legal-divider">
    <h2><?= e(t('rgpd.legal_basis.title')) ?></h2>
    <p><?= e(t('rgpd.legal_basis.text')) ?></p>

    <hr class="legal-divider">
    <h2><?= e(t('rgpd.recipients.title')) ?></h2>
    <p><?= e(t('rgpd.recipients.text')) ?></p>

    <hr class="legal-divider">
    <h2><?= e(t('rgpd.hosting.title')) ?></h2>
    <p><?= e(t('rgpd.hosting.text')) ?></p>

    <hr class="legal-divider">
    <h2><?= e(t('rgpd.retention.title')) ?></h2>
    <p><?= e(t('rgpd.retention.text')) ?></p>

    <hr class="legal-divider">
    <h2><?= e(t('rgpd.rights.title')) ?></h2>
    <p><?= t('rgpd.rights.text1', ['email' => $__rgpd_email]) ?></p>
    <p><?= e(t('rgpd.rights.text2')) ?></p>

    <hr class="legal-divider">
    <h2><?= e(t('rgpd.cookies.title')) ?></h2>
    <p><?= e(t('rgpd.cookies.text')) ?></p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
