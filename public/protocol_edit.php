<?php
require_once __DIR__ . '/../src/bootstrap.php';

$id  = (int)($_GET['id'] ?? 0);
$rec = protocol_find($DB, $id);
if (!$rec) {
    flash_set('error', 'Η εγγραφή δεν βρέθηκε.');
    redirect('protocols.php');
}

$categories = categories_all($DB, true);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $data = [
        'direction'   => input('direction'),
        'doc_date'    => input('doc_date'),
        'subject'     => input('subject'),
        'sender'      => input('sender'),
        'recipient'   => input('recipient'),
        'category_id' => input('category_id'),
        'ref_number'  => input('ref_number'),
        'notes'       => input('notes'),
    ];

    if ($data['direction'] === '')  $errors[] = 'Επιλέξτε τύπο εγγράφου.';
    if ($data['doc_date'] === '')   $errors[] = 'Συμπληρώστε ημερομηνία.';
    if ($data['subject'] === '')    $errors[] = 'Συμπληρώστε θέμα.';

    if (empty($errors)) {
        protocol_update($DB, $id, $data);
        flash_set('success', 'Η εγγραφή ενημερώθηκε.');
        redirect('protocol_view.php?id=' . $id);
    }
}

layout_header('Επεξεργασία — ' . $rec['protocol_number'], 'protocols');
?>

<div class="d-flex align-items-center mb-3">
    <a href="protocol_view.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0">Επεξεργασία: <?= e($rec['protocol_number']) ?></h4>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/_protocol_form.php'; ?>

<?php layout_footer(); ?>
