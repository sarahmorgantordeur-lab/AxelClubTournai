<?php
$__title = "Règlement d'Ordre Intérieur - Axel Club Tournai";
$__extra_css = ['home.css'];
$__extra_head = '<style>.legal-container{max-width:800px;margin:0 auto;padding:calc(80px + var(--space-xl)) var(--space-md) var(--space-xl);}.legal-container h1{color:var(--ice-deep);margin-bottom:var(--space-lg);font-size:clamp(1.6rem,4vw,2.2rem);}.legal-container h2{color:var(--ice-dark);margin-top:var(--space-lg);margin-bottom:var(--space-sm);font-size:1.1rem;text-transform:uppercase;letter-spacing:.5px;}.legal-container p,.legal-container li{color:var(--text-medium);line-height:1.8;font-size:.95rem;}.legal-container ul{padding-left:1.4rem;}.legal-divider{border:none;border-top:1px solid var(--silver-light);margin:var(--space-lg) 0;}</style>';
require __DIR__ . '/../includes/header.php';
?>
<div class="legal-container">
    <h1><?= e(t('roi.title')) ?></h1>
    <p><?= e(t('roi.updated', ['date' => date('d/m/Y')])) ?></p>
    <hr class="legal-divider">

    <?php for ($i = 1; $i <= 10; $i++): ?>
    <h2><?= e(t("roi.s$i.title")) ?></h2>
    <p><?= e(t("roi.s$i.text")) ?></p>
    <hr class="legal-divider">
    <?php endfor; ?>

    <h2><?= e(t('roi.contact.title')) ?></h2>
    <p><?= e(t('roi.contact.text')) ?> <a href="mailto:axelclubtournai@federe.com">axelclubtournai@federe.com</a></p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
