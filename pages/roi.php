<?php
$__title = "Règlement d'Ordre Intérieur - Axel Club Tournai";
$__extra_css = ['home.css'];
$__extra_head = '<style>.legal-container{max-width:800px;margin:0 auto;padding:calc(80px + var(--space-xl)) var(--space-md) var(--space-xl);}.legal-container h1{color:var(--ice-deep);margin-bottom:var(--space-lg);font-size:clamp(1.6rem,4vw,2.2rem);}.legal-container h2{color:var(--ice-dark);margin-top:var(--space-lg);margin-bottom:var(--space-sm);font-size:1.1rem;text-transform:uppercase;letter-spacing:.5px;}.legal-container p,.legal-container li{color:var(--text-medium);line-height:1.8;font-size:.95rem;}.legal-container ul,.legal-container ol{padding-left:1.4rem;}.legal-divider{border:none;border-top:1px solid var(--silver-light);margin:var(--space-lg) 0;}.legal-container li{margin-bottom:.65rem;}.roi-payments{overflow-x:auto;margin:1rem 0;}.roi-payments table{width:100%;border-collapse:collapse;}.roi-payments caption{text-align:left;font-weight:600;margin-bottom:.5rem;}.roi-payments th,.roi-payments td{padding:.75rem;text-align:left;border:1px solid var(--silver-light);}.roi-payments th{background:var(--ice-light);}.roi-notice{padding:1rem;border-left:4px solid var(--ice-deep);background:var(--ice-light);}</style>';
require __DIR__ . '/../includes/header.php';
?>
<div class="legal-container">
    <h1><?= e(t('roi.title')) ?></h1>
    <p><?= e(t('roi.document_notice')) ?></p>
    <hr class="legal-divider">

    <?php require __DIR__ . '/../includes/roi_document.php'; ?>

    <h2><?= e(t('roi.contact.title')) ?></h2>
    <p><?= e(t('roi.contact.text')) ?> <a href="mailto:axelclubtournaifedere@gmail.com">axelclubtournaifedere@gmail.com</a></p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
