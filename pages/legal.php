<?php
$__title = 'Mentions Légales - Axel Club Tournai';
$__extra_css = ['home.css'];
$__extra_head = '<style>.legal-container{max-width:800px;margin:0 auto;padding:calc(80px + var(--space-xl)) var(--space-md) var(--space-xl);}.legal-container h1{color:var(--ice-deep);margin-bottom:var(--space-lg);font-size:clamp(1.6rem,4vw,2.2rem);}.legal-container h2{color:var(--ice-dark);margin-top:var(--space-lg);margin-bottom:var(--space-sm);font-size:1.1rem;text-transform:uppercase;letter-spacing:.5px;}.legal-container p,.legal-container li{color:var(--text-medium);line-height:1.8;font-size:.95rem;}.legal-divider{border:none;border-top:1px solid var(--silver-light);margin:var(--space-lg) 0;}</style>';
require __DIR__ . '/../includes/header.php';
$__rgpd_link = '<a href="/politique-rgpd">' . e(t('legal.rgpd_link_text')) . '</a>';
$__roi_link = '<a href="/reglement-interieur">' . e(t('legal.roi_link_text')) . '</a>';
$__team = [
    ['name' => 'Marie-Hélène', 'role' => t('legal.team.role.president')],
    ['name' => 'Isabelle Dupriez', 'role' => t('legal.team.role.secretary')],
    ['name' => 'Élisabeth Piers', 'role' => t('legal.team.role.coach')],
    ['name' => 'Patricia Adam', 'role' => t('legal.team.role.admin')],
    ['name' => 'Sarah Tordeur', 'role' => t('legal.team.role.admin')],
];
?>
<div class="legal-container">
    <h1><?= e(t('legal.title')) ?></h1>
    <h2><?= e(t('legal.editor.title')) ?></h2>
    <p><?= t('legal.editor.text') ?></p>
    <hr class="legal-divider">
    <h2><?= e(t('legal.team.title')) ?></h2>
    <ul>
        <?php foreach ($__team as $__member): ?>
        <li><?= e($__member['name']) ?> — <?= e($__member['role']) ?></li>
        <?php endforeach; ?>
    </ul>
    <hr class="legal-divider">
    <h2><?= e(t('legal.hosting.title')) ?></h2>
    <p><?= e(t('legal.hosting.text')) ?></p>
    <hr class="legal-divider">
    <h2><?= e(t('legal.ip.title')) ?></h2>
    <p><?= e(t('legal.ip.text')) ?></p>
    <hr class="legal-divider">
    <h2><?= e(t('legal.data.title')) ?></h2>
    <p><?= e(t('legal.data.text')) ?><br><?= t('legal.data.link', ['link' => $__rgpd_link]) ?></p>
    <hr class="legal-divider">
    <h2><?= e(t('legal.cookies.title')) ?></h2>
    <p><?= e(t('legal.cookies.text')) ?></p>
    <hr class="legal-divider">
    <h2><?= e(t('legal.roi.title')) ?></h2>
    <p><?= t('legal.roi.text', ['link' => $__roi_link]) ?></p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
