<?php
admin_required();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/admin/groups'); }
csrf_check();
$gid = (int)($GLOBALS['group_id'] ?? 0);
group_delete($gid);
flash('Groupe supprimé.', 'success');
redirect('/admin/groups');
