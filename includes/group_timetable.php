<?php
// Source : doc_reu_26_09_26.pdf, tableaux des pages 5 à 7.
$timetable = json_decode(file_get_contents(__DIR__ . '/group_schedules.json'), true, 512, JSON_THROW_ON_ERROR);
// Rattacher les informations administrées aux noms du document.
$normalizeGroup = static function (string $name): string {
    $name = strtr(mb_strtolower(trim($name)), ['é' => 'e', 'è' => 'e']);
    return ['cigognes' => 'cigogne', 'fentes' => 'fente', 'adultes arabesque' => 'arabesque'][$name] ?? $name;
};
$storedGroups = [];
foreach ($groups as $storedGroup) {
    $storedGroups[$normalizeGroup($storedGroup['name'])] = $storedGroup;
}
foreach ($timetable as &$entry) {
    $key = $normalizeGroup($entry['name']);
    $entry['stored'] = $storedGroups[$key] ?? null;
    unset($storedGroups[$key]);
}
unset($entry);
foreach ($storedGroups as $storedGroup) {
    $timetable[] = ['name' => $storedGroup['name'], 'periods' => [], 'stored' => $storedGroup];
}
?>
<section class="groups-section group-timetable" aria-labelledby="timetable-title">
    <h2 id="timetable-title"><?= e(t('groups.timetable.title')) ?></h2>
    <p><?= e(t('groups.timetable.intro')) ?></p>
    <div class="groups-grid">
        <?php foreach ($timetable as $group): ?>
        <?php $g = $group['stored']; ?>
        <article class="group-card">
            <div class="group-card-header">
                <h3><?= e($group['name']) ?></h3>
                <?php if (!empty($g['description'])): ?><p><?= e($g['description']) ?></p><?php endif; ?>
            </div>
            <div class="group-card-body">
                <div class="group-info-item timetable-heading">
                    <div class="group-info-icon" aria-hidden="true">🕐</div>
                    <div class="group-info-text"><label><?= e(t('groups.schedule.label')) ?></label></div>
                </div>
                <?php if (!$group['periods']): ?><p><?= e($g['schedule'] ?: t('groups.schedule.tbd')) ?></p><?php endif; ?>
                <?php foreach ($group['periods'] as $period): ?>
                <section class="timetable-period">
                    <h4><?= e(t('groups.timetable.' . $period['key'])) ?></h4>
                    <ul>
                        <?php foreach ($period['slots'] as $slot): ?>
                        <li>
                            <strong><?= e(t($slot['day'] === 'sunday' ? 'groups.timetable.sunday' : 'groups.schedule_table.day.' . $slot['day'])) ?></strong>
                            <span><?= e($slot['time'] ?: t('groups.timetable.pending')) ?></span>
                            <span class="timetable-location"><?= e(t('groups.timetable.' . $slot['activity'])) ?> · <?= e($slot['place']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php endforeach; ?>
                <?php if ($g): ?>
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
                <?php endif; ?>
            </div>
            <div class="group-card-footer">
                <a href="mailto:axelclubtournaifedere@gmail.com" class="btn btn-primary btn-sm"><?= e(t('groups.contact_button')) ?></a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <p class="timetable-note"><?= e(t('groups.timetable.ppg_help')) ?></p>
    <p class="timetable-note"><?= e(t('groups.timetable.locations')) ?></p>
</section>
