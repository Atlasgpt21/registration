<?php
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('categories.php');
}
csrf_verify();

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $count = category_count_protocols($DB, $id);
    if ($count > 0) {
        flash_set('error', 'Δεν μπορεί να διαγραφεί: υπάρχουν ' . $count . ' εγγραφές σε αυτή την κατηγορία.');
    } else {
        category_delete($DB, $id);
        flash_set('success', 'Η κατηγορία διαγράφηκε.');
    }
}
redirect('categories.php');
