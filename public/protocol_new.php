<?php
require_once __DIR__ . '/../src/bootstrap.php';

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
        $format = $CFG['app']['protocol_format'] ?? '{SEQ}/{YYYY}';
        $DB->beginTransaction();
        try {
            $data['protocol_number'] = protocol_next_number($DB, $format);
            $id = protocol_create($DB, $data);
            $DB->commit();
        } catch (\Throwable $e) {
            $DB->rollBack();
            throw $e;
        }
        flash_set('success', 'Η καταχώρηση αποθηκεύτηκε. Αρ. Πρωτ.: ' . $data['protocol_number']);
        redirect('protocol_view.php?id=' . $id);
    }
}

layout_header('Νέα Καταχώρηση', 'new');
?>

<div class="d-flex align-items-center mb-3">
    <a href="protocols.php" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0">Νέα Καταχώρηση Πρωτοκόλλου</h4>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/_protocol_form.php'; ?>

<?php layout_footer(); ?>
