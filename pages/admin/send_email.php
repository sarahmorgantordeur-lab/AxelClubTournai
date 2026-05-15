<?php
admin_required();
$groups = group_all();
$sent = false;
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $target = $_POST['target'] ?? 'all';
    $group_id = (int)($_POST['group_id'] ?? 0);
    if (!$subject || !$body) { $error = 'Objet et message requis.'; }
    else {
        if ($target === 'group' && $group_id) {
            $stmt = get_db()->prepare("SELECT email FROM users WHERE group_id=? AND status='active'");
            $stmt->execute([$group_id]);
        } elseif ($target === 'patineurs') {
            $stmt = get_db()->prepare("SELECT email FROM users WHERE roles LIKE '%\"patineur\"%' AND status='active'");
            $stmt->execute([]);
        } elseif ($target === 'parents') {
            $stmt = get_db()->prepare("SELECT email FROM users WHERE roles LIKE '%\"parent\"%' AND status='active'");
            $stmt->execute([]);
        } else {
            $stmt = get_db()->query("SELECT email FROM users WHERE status='active'");
        }
        $emails = array_column($stmt->fetchAll(), 'email');
        $count = 0;
        $headers = "From: " . SITE_NAME . " <" . SITE_EMAIL . ">\r\nContent-Type: text/html; charset=UTF-8\r\n";
        $html_body = nl2br(e($body));
        foreach ($emails as $email) {
            if (mail($email, $subject, "<html><body>$html_body</body></html>", $headers)) $count++;
        }
        flash("Email envoyé à $count destinataire(s).", 'success');
        $sent = true;
    }
}
$__title = 'Envoyer un email - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_email';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header"><h2>Envoyer un email groupé</h2></div>
    <section class="admin-section"><div class="admin-section-body">
    <?php if ($error): ?><div class="alert alert-error" style="margin-bottom:var(--space-md);padding:1rem;background:#fee;border:1px solid #fcc;border-radius:var(--radius-sm);color:#c00;"><?= e($error) ?></div><?php endif; ?>
    <form method="POST" class="form">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-row">
            <div class="form-group"><label>Destinataires</label>
                <select name="target" id="target" onchange="document.getElementById('group_row').style.display=this.value==='group'?'block':'none'" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <option value="all">Tous les membres actifs</option>
                    <option value="patineurs">Patineurs</option>
                    <option value="parents">Parents</option>
                    <option value="group">Un groupe spécifique</option>
                </select>
            </div>
            <div class="form-group" id="group_row" style="display:none;"><label>Groupe</label>
                <select name="group_id" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <option value="">--</option>
                    <?php foreach ($groups as $g): ?><option value="<?= $g['id'] ?>"><?= e($g['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group"><label>Objet *</label><input type="text" name="subject" required></div>
        <div class="form-group"><label>Message *</label><textarea name="body" rows="10" required style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);font-size:.95rem;resize:vertical;"></textarea></div>
        <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-md);">
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </div>
    </form>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
