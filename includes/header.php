<?php
$__user = auth_user();
$__flash = get_flash_messages();
$__active = $__active ?? '';
$__extra_css = $__extra_css ?? [];
$__extra_head = $__extra_head ?? '';
$__body_class = $__body_class ?? '';
?>
<!DOCTYPE html>
<html lang="<?= e($__locale ?? 'fr') ?>">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/static/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($__title ?? SITE_NAME) ?></title>
    <?php if (!empty($__meta_desc)): ?>
    <meta name="description" content="<?= e($__meta_desc) ?>">
    <meta name="robots" content="index, follow">
    <?php endif; ?>
    <link rel="stylesheet" href="/static/css/base.css">
    <?php foreach ($__extra_css as $css): ?>
    <link rel="stylesheet" href="/static/css/<?= e($css) ?>">
    <?php endforeach; ?>
    <link rel="stylesheet" href="/static/css/style.css">
    <?= $__extra_head ?>
</head>
<body<?= $__body_class ? ' class="' . e($__body_class) . '"' : '' ?>>
<nav class="navbar">
    <div class="container">
        <a href="/"><img src="/static/images/logo.png" alt="Axel Club" class="logo-img"></a>
        <div class="menu-toggle"><span></span><span></span><span></span></div>
        <ul class="nav-links">
            <?php if (($__body_class ?? '') === 'admin-layout'): ?>
                <li><a href="/admin" <?= $__active==='admin_dashboard'?'class="active"':'' ?>>Tableau de Bord</a></li>
                <li><a href="/admin/users" <?= $__active==='admin_users'?'class="active"':'' ?>>Membres</a></li>
                <li><a href="/admin/groups" <?= $__active==='admin_groups'?'class="active"':'' ?>>Groupes</a></li>
                <li><a href="/admin/registrations" <?= $__active==='admin_reg'?'class="active"':'' ?>>Réinscriptions</a></li>
                <li><a href="/admin/attendance" <?= $__active==='admin_att'?'class="active"':'' ?>>Présences</a></li>
                <li><a href="/admin/payments" <?= $__active==='admin_pay'?'class="active"':'' ?>>Paiements</a></li>
                <li><a href="/admin/reports" <?= $__active==='admin_reports'?'class="active"':'' ?>>Rapports</a></li>
                <li><a href="/admin/send-email" <?= $__active==='admin_email'?'class="active"':'' ?>>Email</a></li>
                <li><a href="/dashboard" class="btn btn-outline btn-sm">← Retour</a></li>
                <li><a href="/auth/logout">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="/" <?= $__active==='home'?'class="active"':'' ?>><?= e(t('nav.home')) ?></a></li>
                <li><a href="/groupes" <?= $__active==='groups'?'class="active"':'' ?>><?= e(t('nav.groups')) ?></a></li>
                <li><a href="/evenements" <?= $__active==='events'?'class="active"':'' ?>><?= e(t('nav.events')) ?></a></li>
                <li><a href="/a-propos" <?= $__active==='about'?'class="active"':'' ?>><?= e(t('nav.about')) ?></a></li>
                <li><a href="/contact" <?= $__active==='contact'?'class="active"':'' ?>><?= e(t('nav.contact')) ?></a></li>
                <?php if ($__user): ?>
                    <li><a href="/dashboard" <?= $__active==='dashboard'?'class="active"':'' ?>><?= e(t('nav.dashboard')) ?></a></li>
                    <li><a href="/auth/profile" <?= $__active==='profile'?'class="active"':'' ?>><?= e(t('nav.profile')) ?></a></li>
                    <?php if (user_has_role($__user, 'admin')): ?>
                        <li><a href="/admin" class="btn btn-outline btn-sm"><?= e(t('nav.admin')) ?></a></li>
                    <?php endif; ?>
                    <li><a href="/auth/logout"><?= e(t('nav.logout')) ?></a></li>
                <?php else: ?>
                    <li><a href="/auth/login"><?= e(t('nav.login')) ?></a></li>
                <?php endif; ?>
                <li class="lang-switch" style="display:flex;gap:.35rem;align-items:center;">
                    <?php $__lc = count(SUPPORTED_LOCALES); foreach (SUPPORTED_LOCALES as $__i => $__l): ?>
                    <a href="<?= e(lang_switch_url($__l)) ?>" style="font-size:.75rem;font-weight:<?= ($__locale??'fr')===$__l?'700':'400'?>;opacity:<?= ($__locale??'fr')===$__l?'1':'.55'?>;text-transform:uppercase;"><?= e($__l) ?></a><?php if ($__i < $__lc - 1): ?><span style="opacity:.4;">|</span><?php endif; ?>
                    <?php endforeach; ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<?php if ($__flash): ?>
<div style="max-width:900px;margin:1rem auto;padding:0 1rem;">
    <?php foreach ($__flash as $f): ?>
    <div class="alert alert-<?= e($f['type']) ?>" style="padding:.75rem 1rem;border-radius:6px;margin-bottom:.5rem;background:<?= $f['type']==='error'?'#fee':'#eff';?>;border-left:4px solid <?= $f['type']==='error'?'var(--danger)':($f['type']==='success'?'var(--success)':'var(--ice-deep)') ?>;color:var(--text-main);">
        <?= e($f['message']) ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
