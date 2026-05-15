<?php
admin_required();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/admin/registrations'); }
csrf_check();
$uid = (int)($GLOBALS['user_id'] ?? 0);
$group_id = (int)($_GET['group_id'] ?? 0);
if ($uid) {
    get_db()->prepare("UPDATE users SET group_id=NULL WHERE id=?")->execute([$uid]);
    flash('Membre retiré du groupe.', 'success');
}
redirect('/admin/registrations?group_id=' . $group_id);
