<?php
admin_required();
$page = max(1, (int)($_GET['page'] ?? 1));
$result = payment_records_paginated($page, 20);
$records = $result['items'];
$total_pages = $result['total_pages'];
$total_rev = total_revenue();
$__title = 'Paiements - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_pay';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header">
        <h2>Gestion des Paiements</h2>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <span class="badge badge-success" style="font-size:1rem;padding:.5rem 1rem;">Total encaissé : <?= number_format($total_rev,2) ?> €</span>
            <a href="/admin/payments/add" class="btn btn-primary">+ Ajouter</a>
        </div>
    </div>
    <section class="admin-section"><div class="admin-section-body">
        <?php if ($records): ?>
        <div class="table-container"><table class="table">
            <thead><tr><th>Date</th><th>Membre</th><th>Type</th><th>Méthode</th><th>Montant</th><th>Statut</th><th>Notes</th></tr></thead>
            <tbody>
            <?php foreach ($records as $r): ?>
            <tr>
                <td><?= format_date($r['payment_date'], 'd/m/Y') ?></td>
                <td><a href="/admin/users/<?= $r['user_id'] ?>/payments"><?= e($r['first_name']) ?> <?= e($r['last_name']) ?></a></td>
                <td><?= e($r['payment_type'] ?? '-') ?></td>
                <td><?= e($r['payment_method'] ?? '-') ?></td>
                <td><strong><?= number_format((float)$r['amount'],2) ?> €</strong></td>
                <td><span class="badge <?= $r['status']==='paid'?'badge-success':'badge-warning' ?>"><?= e(ucfirst($r['status'])) ?></span></td>
                <td><?= e($r['notes'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php if ($total_pages > 1): ?>
        <div style="display:flex;gap:.5rem;margin-top:var(--space-md);justify-content:center;flex-wrap:wrap;">
            <?php for ($p=1;$p<=$total_pages;$p++): ?>
            <a href="?page=<?= $p ?>" class="btn btn-sm <?= $p===$page?'btn-primary':'btn-outline' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state"><div class="empty-state-icon">💳</div><h3>Aucun paiement</h3><a href="/admin/payments/add" class="btn btn-primary">Ajouter un paiement</a></div>
        <?php endif; ?>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
