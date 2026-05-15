<?php
admin_required();
$members = get_db()->query("SELECT id,first_name,last_name FROM users ORDER BY last_name,first_name")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $uid = (int)($_POST['user_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    if (!$uid || $amount <= 0) { flash('Membre et montant requis.', 'error'); redirect('/admin/payments/add'); }
    payment_record_create([
        'user_id' => $uid,
        'amount' => $amount,
        'payment_type' => trim($_POST['payment_type'] ?? '') ?: null,
        'payment_method' => trim($_POST['payment_method'] ?? '') ?: null,
        'payment_date' => $_POST['payment_date'] ?: date('Y-m-d'),
        'status' => $_POST['status'] ?? 'paid',
        'notes' => trim($_POST['notes'] ?? '') ?: null,
    ]);
    flash('Paiement enregistré.', 'success');
    redirect('/admin/payments');
}
$__title = 'Ajouter un paiement - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_pay';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header"><h2>Ajouter un paiement</h2><a href="/admin/payments" class="btn btn-secondary">Retour</a></div>
    <section class="admin-section"><div class="admin-section-body">
    <form method="POST" class="form">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-row">
            <div class="form-group"><label>Membre *</label>
                <select name="user_id" required style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <option value="">-- Sélectionner un membre --</option>
                    <?php foreach ($members as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['last_name']) ?> <?= e($m['first_name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Montant (€) *</label><input type="number" name="amount" step="0.01" min="0" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Type</label><input type="text" name="payment_type" placeholder="cotisation, compétition..."></div>
            <div class="form-group"><label>Méthode</label>
                <select name="payment_method" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <option value="">--</option>
                    <option>Virement</option><option>Espèces</option><option>Chèque</option><option>Carte</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Date</label><input type="date" name="payment_date" value="<?= date('Y-m-d') ?>"></div>
            <div class="form-group"><label>Statut</label>
                <select name="status" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <option value="paid">Payé</option><option value="pending">En attente</option>
                </select>
            </div>
        </div>
        <div class="form-group"><label>Notes</label><input type="text" name="notes" placeholder="Remarques..."></div>
        <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-md);">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/admin/payments" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
