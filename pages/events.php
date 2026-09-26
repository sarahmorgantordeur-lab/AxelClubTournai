<?php
// Liste des événements à venir — à mettre à jour manuellement au fil de la saison.
$events = [
    [
        'title' => 'Réunion d’information du club',
        'date' => '2026-09-26',
        'time' => '14:00',
        'location' => 'Sports Rive Droite',
        'address' => 'Quai des Vicinaux 29, 7500 Tournai',
        'map_url' => 'https://www.google.com/maps/search/?api=1&query=quai+des+Vicinaux+29+7500+Tournai',
        'description' => 'Bonjour à toutes et à tous ! Nous vous rappelons que la réunion d’information de notre club aura lieu le samedi 26 septembre 2026 à 14 h aux Sports Rive Droite.',
        'notice' => 'Attention : le pont permettant habituellement d’accéder au quai des Vicinaux est fermé. Nous vous conseillons de suivre les déviations mises en place et de prévoir un peu plus de temps pour votre trajet afin d’arriver à l’heure.',
        'closing' => 'Nous vous remercions d’avance pour votre présence et avons hâte de vous retrouver lors de cette réunion.',
        'signature' => 'Bien sportivement, le comité',
    ],
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
            <p style="color:var(--text-light);font-weight:600;margin-bottom:var(--space-xs);"><?= format_date($ev['date'], 'd F Y') ?><?php if (!empty($ev['time'])): ?> · <?= e(str_replace(':', ' h ', $ev['time'])) ?><?php endif; ?><?= !empty($ev['location']) ? ' — ' . e($ev['location']) : '' ?></p>
            <?php if (!empty($ev['address'])): ?>
            <p><?php if (!empty($ev['map_url'])): ?><a href="<?= e($ev['map_url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($ev['address']) ?></a><?php else: ?><?= e($ev['address']) ?><?php endif; ?></p>
            <?php endif; ?>
            <?php if (!empty($ev['description'])): ?><p style="color:var(--text-medium);"><?= e($ev['description']) ?></p><?php endif; ?>
            <?php if (!empty($ev['notice'])): ?><p class="alert alert-warning"><strong><?= e($ev['notice']) ?></strong></p><?php endif; ?>
            <?php if (!empty($ev['closing'])): ?><p><?= e($ev['closing']) ?></p><?php endif; ?>
            <?php if (!empty($ev['signature'])): ?><p><em><?= e($ev['signature']) ?></em></p><?php endif; ?>
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
