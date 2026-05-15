<?php
admin_required();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/admin/registrations'); }
csrf_check();
$uid = (int)($GLOBALS['user_id'] ?? 0);
$group_id = (int)($_GET['group_id'] ?? 0);
if ($uid && $group_id) {
    get_db()->prepare("UPDATE users SET group_id=? WHERE id=?")->execute([$group_id, $uid]);
    flash('Membre inscrit dans le groupe.', 'success');
}
redirect('/admin/registrations?group_id=' . $group_id);
