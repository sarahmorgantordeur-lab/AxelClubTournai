<?php
// Source : doc_reu_26_09_26.pdf, tableaux des pages 5 à 7.
$timetable = json_decode(file_get_contents(__DIR__ . '/group_schedules.json'), true, 512, JSON_THROW_ON_ERROR);
?>
<section class="container group-timetable" aria-labelledby="timetable-title">
    <h2 id="timetable-title"><?= e(t('groups.timetable.title')) ?></h2>
    <p><?= e(t('groups.timetable.intro')) ?></p>
    <div class="timetable-groups">
        <?php foreach ($timetable as $group): ?>
        <details class="timetable-group">
            <summary><?= e($group['name']) ?></summary>
            <div class="timetable-content">
                <?php foreach ($group['periods'] as $period): ?>
                <section class="timetable-period">
                    <h3><?= e(t('groups.timetable.' . $period['key'])) ?></h3>
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
            </div>
        </details>
        <?php endforeach; ?>
    </div>
    <p class="timetable-note"><?= e(t('groups.timetable.ppg_help')) ?></p>
    <p class="timetable-note"><?= e(t('groups.timetable.locations')) ?></p>
</section>
