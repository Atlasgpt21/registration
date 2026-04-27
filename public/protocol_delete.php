<?php
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('protocols.php');
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    protocol_delete($DB, $id);
    flash_set('success', 'Η εγγραφή διαγράφηκε.');
}
redirect('protocols.php');
