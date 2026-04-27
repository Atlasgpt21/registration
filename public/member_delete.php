<?php
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('members.php');
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    member_delete($DB, $id);
    flash_set('success', 'Το μέλος διαγράφηκε.');
}
redirect('members.php');
