<?php
admin_required();
[$season_start, $season_end] = current_season();
$season_year = (int)substr($season_start, 0, 4);

$total_members = user_count_by_role('patineur');
$total_parents = user_count_by_role('parent');
$groups = group_all();
$total_revenue = total_revenue();

$stmt = get_db()->query("SELECT COUNT(*) FROM attendances WHERE status='present'");
$total_present = (int)$stmt->fetchColumn();
$stmt = get_db()->query("SELECT COUNT(*) FROM attendances");
$total_att = (int)$stmt->fetchColumn();
$att_rate = $total_att > 0 ? round($total_present / $total_att * 100, 1) : 0;

$stmt = get_db()->prepare("SELECT SUM(CASE WHEN sp_balance.bal > 0 THEN 1 ELSE 0 END) as unpaid FROM (SELECT (COALESCE(licence_due,0)+COALESCE(saison_tournai_due,0)+COALESCE(wasquehal_p1_due,0)+COALESCE(wasquehal_p2_due,0)+COALESCE(competitions_due,0))-(COALESCE(licence_paid,0)+COALESCE(saison_tournai_paid,0)+COALESCE(wasquehal_p1_paid,0)+COALESCE(wasquehal_p2_paid,0)+COALESCE(competitions_paid,0)) as bal FROM season_payments WHERE season_year=?) sp_balance");
$stmt->execute([$season_year]);
$unpaid_count = (int)$stmt->fetchColumn();

$__title = 'Rapports - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_reports';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header"><h2>Rapports — Saison <?= $season_year ?>-<?= $season_year+1 ?></h2></div>

    <div class="stats">
        <div class="stat-card"><h4>Patineurs</h4><p class="stat-number"><?= $total_members ?></p></div>
        <div class="stat-card"><h4>Parents</h4><p class="stat-number"><?= $total_parents ?></p></div>
        <div class="stat-card"><h4>Groupes</h4><p class="stat-number"><?= count($groups) ?></p></div>
        <div class="stat-card"><h4>Revenu total</h4><p class="stat-number"><?= number_format($total_revenue,2) ?> €</p></div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-md);margin-top:var(--space-lg);">
        <section class="admin-section">
            <h3>Présences</h3>
            <div class="admin-section-body">
                <p><strong>Total séances enregistrées :</strong> <?= $total_att ?></p>
                <p><strong>Présences confirmées :</strong> <?= $total_present ?></p>
                <p><strong>Taux de présence global :</strong> <?= $att_rate ?> %</p>
                <div style="background:var(--silver-light);border-radius:var(--radius-sm);height:12px;margin-top:.5rem;">
                    <div style="background:var(--ice-blue);height:100%;border-radius:var(--radius-sm);width:<?= $att_rate ?>%;"></div>
                </div>
            </div>
        </section>
        <section class="admin-section">
            <h3>Paiements en attente</h3>
            <div class="admin-section-body">
                <p><strong>Membres avec solde impayé :</strong> <span class="badge badge-danger"><?= $unpaid_count ?></span></p>
                <p><a href="/admin/payments" class="btn btn-outline btn-sm">Voir les paiements</a></p>
            </div>
        </section>
    </div>

    <section class="admin-section" style="margin-top:var(--space-lg);">
        <h3>Répartition par groupe</h3>
        <div class="admin-section-body">
        <?php if ($groups): ?>
        <div class="table-container"><table class="table">
            <thead><tr><th>Groupe</th><th>Horaire</th><th>Membres</th><th>Prix/saison</th></tr></thead>
            <tbody>
            <?php foreach ($groups as $g): $cnt = group_member_count($g['id']); ?>
            <tr>
                <td><strong><?= e($g['name']) ?></strong></td>
                <td><?= e($g['schedule'] ?? '-') ?></td>
                <td><?= $cnt ?></td>
                <td><?= $g['price_per_season'] ? number_format((float)$g['price_per_season'],2).' €' : '-' ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php else: ?><p style="color:var(--text-light)">Aucun groupe.</p><?php endif; ?>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
