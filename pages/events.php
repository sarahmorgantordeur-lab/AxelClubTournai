<?php
// Liste des événements à venir — à mettre à jour manuellement au fil de la saison.
$events = [
    // ['title' => 'Gala de fin de saison', 'date' => '2026-06-14', 'location' => 'Patinoire de Tournai', 'description' => 'Notre gala annuel où tous les patineurs présentent leur programme.'],
];
usort($events, fn($a, $b) => strcmp($a['date'], $b['date']));

$__title = 'Événements - Axel Club Tournai';
$__meta_desc = t('events.meta_desc');
$__extra_css = ['home.css'];
$__active = 'events';
require __DIR__ . '/../includes/header.php';
$__ig_link = '<a href="https://www.instagram.com/axelclubtournai/" target="_blank" rel="noopener noreferrer">' . e(t('events.instagram_link_text')) . '</a>';
?>
<section class="groups-hero">
    <h1><?= e(t('events.hero.title')) ?></h1>
    <p><?= e(t('events.hero.subtitle')) ?></p>
</section>

<div class="container" style="padding:var(--space-xl) var(--space-md);">
    <?php if ($events): ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:var(--space-lg);">
        <?php foreach ($events as $ev): ?>
        <div class="card">
            <h3 style="color:var(--ice-deep);margin-bottom:var(--space-xs);"><?= e($ev['title']) ?></h3>
            <p style="color:var(--text-light);font-weight:600;margin-bottom:var(--space-xs);"><?= format_date($ev['date'], 'd F Y') ?><?= !empty($ev['location']) ? ' — ' . e($ev['location']) : '' ?></p>
            <?php if (!empty($ev['description'])): ?><p style="color:var(--text-medium);"><?= e($ev['description']) ?></p><?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align:center;padding:60px 20px;">
        <div style="font-size:4rem;margin-bottom:20px;">📅</div>
        <h3><?= e(t('events.empty.title')) ?></h3>
        <p style="color:var(--text-light);"><?= t('events.empty.text', ['link' => $__ig_link]) ?></p>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
