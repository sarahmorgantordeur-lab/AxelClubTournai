<?php
admin_required();
$uid = (int)($GLOBALS['user_id'] ?? 0);
$user = user_find($uid) or (flash('Utilisateur introuvable.','error') && redirect('/admin/users'));
$year = (int)($_GET['year'] ?? date('Y'));
[$season_start, $season_end] = current_season();
$season_year = (int)substr($season_start, 0, 4);
$year = $year ?: $season_year;
$sp = season_payment_get($uid, $year);
$records = get_db()->prepare("SELECT * FROM payment_records WHERE user_id=? ORDER BY payment_date DESC")->execute([$uid]) ? null : null;
$stmt = get_db()->prepare("SELECT * FROM payment_records WHERE user_id=? ORDER BY payment_date DESC");
$stmt->execute([$uid]);
$records = $stmt->fetchAll();
$__title = 'Paiements de ' . $user['first_name'] . ' - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_pay';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header">
        <h2>Paiements — <?= e($user['first_name']) ?> <?= e($user['last_name']) ?></h2>
        <div style="display:flex;gap:.5rem;">
            <a href="/admin/users/<?= $uid ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
            <a href="/admin/users" class="btn btn-outline btn-sm">Retour</a>
        </div>
    </div>

    <section class="admin-section">
        <h3>Saison <?= $year ?>-<?= $year+1 ?>
            <span style="font-size:.9rem;font-weight:normal;margin-left:1rem;">
                <?php foreach ([date('Y')-1, date('Y'), date('Y')+1] as $y): ?>
                <a href="?year=<?= $y ?>" class="btn btn-sm <?= $y===$year?'btn-primary':'btn-outline' ?>" style="margin-right:.25rem;"><?= $y ?></a>
                <?php endforeach; ?>
            </span>
        </h3>
        <div class="admin-section-body">
        <form method="POST" action="/admin/users/<?= $uid ?>/payments/update" class="form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            <div class="table-container"><table class="table">
                <thead><tr><th>Poste</th><th>Dû (€)</th><th>Payé (€)</th></tr></thead>
                <tbody>
                <?php
                $posts = ['licence'=>'Licence','saison_tournai'=>'Saison Tournai','wasquehal_p1'=>'Wasquehal P1','wasquehal_p2'=>'Wasquehal P2','competitions'=>'Compétitions'];
                foreach ($posts as $key => $label): ?>
                <tr>
                    <td><?= $label ?></td>
                    <td><input type="number" name="<?= $key ?>_due" step="0.01" min="0" value="<?= e($sp[$key.'_due']??0) ?>" style="width:100px;padding:6px 10px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);"></td>
                    <td><input type="number" name="<?= $key ?>_paid" step="0.01" min="0" value="<?= e($sp[$key.'_paid']??0) ?>" style="width:100px;padding:6px 10px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);"></td>
                </tr>
                <?php endforeach; ?>
                <tr style="font-weight:bold;background:var(--ice-light);">
                    <td>Total</td>
                    <td><?= number_format(sp_total_due($sp),2) ?> €</td>
                    <td><?= number_format(sp_total_paid($sp),2) ?> €</td>
                </tr>
                </tbody>
            </table></div>
            <div class="form-group" style="margin-top:var(--space-sm);">
                <label>Notes</label><input type="text" name="notes" value="<?= e($sp['notes']??'') ?>" placeholder="Remarques...">
            </div>
            <?php $bal = sp_balance($sp); ?>
            <div style="margin:var(--space-sm) 0;">
                <span class="badge <?= $bal<=0?'badge-success':'badge-danger' ?>" style="font-size:1rem;padding:.5rem 1rem;">
                    Solde : <?= number_format($bal,2) ?> €
                    <?= $bal<=0 ? '✓ Soldé' : '⚠ Reste à payer' ?>
                </span>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
        </div>
    </section>

    <section class="admin-section" style="margin-top:var(--space-lg);">
        <h3>Historique des paiements</h3>
        <div class="admin-section-body">
        <?php if ($records): ?>
        <div class="table-container"><table class="table">
            <thead><tr><th>Date</th><th>Type</th><th>Méthode</th><th>Montant</th><th>Statut</th><th>Notes</th></tr></thead>
            <tbody>
            <?php foreach ($records as $r): ?>
            <tr>
                <td><?= format_date($r['payment_date'], 'd/m/Y') ?></td>
                <td><?= e($r['payment_type'] ?? '-') ?></td>
                <td><?= e($r['payment_method'] ?? '-') ?></td>
                <td><?= number_format((float)$r['amount'],2) ?> €</td>
                <td><span class="badge <?= $r['status']==='paid'?'badge-success':'badge-warning' ?>"><?= e(ucfirst($r['status'])) ?></span></td>
                <td><?= e($r['notes'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php else: ?>
        <p style="color:var(--text-light)">Aucun paiement enregistré.</p>
        <?php endif; ?>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
