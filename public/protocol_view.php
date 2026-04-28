<?php
require_once __DIR__ . '/../src/bootstrap.php';

$id  = (int)($_GET['id'] ?? 0);
$rec = protocol_find($DB, $id);
if (!$rec) {
    flash_set('error', 'Η εγγραφή δεν βρέθηκε.');
    redirect('protocols.php');
}

layout_header('Προβολή — ' . $rec['protocol_number'], 'protocols');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <a href="protocols.php" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0"><?= e($rec['protocol_number']) ?></h4>
        <span class="<?= direction_class($rec['direction']) ?> ms-2"><?= direction_label($rec['direction']) ?></span>
    </div>
    <div>
        <a href="protocol_edit.php?id=<?= $id ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Επεξεργασία</a>
        <form method="post" action="protocol_delete.php" class="d-inline" onsubmit="return confirm('Διαγραφή αυτής της εγγραφής;')">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Διαγραφή</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Αρ. Πρωτοκόλλου</dt>
            <dd class="col-sm-9"><strong><?= e($rec['protocol_number']) ?></strong></dd>

            <dt class="col-sm-3">Τύπος</dt>
            <dd class="col-sm-9"><span class="<?= direction_class($rec['direction']) ?>"><?= direction_label($rec['direction']) ?></span></dd>

            <dt class="col-sm-3">Ημερομηνία Εγγράφου</dt>
            <dd class="col-sm-9"><?= fmt_date($rec['doc_date']) ?></dd>

            <dt class="col-sm-3">Θέμα</dt>
            <dd class="col-sm-9"><?= e($rec['subject']) ?></dd>

            <dt class="col-sm-3">Αποστολέας</dt>
            <dd class="col-sm-9"><?= e($rec['sender'] ?? '—') ?></dd>

            <dt class="col-sm-3">Παραλήπτης</dt>
            <dd class="col-sm-9"><?= e($rec['recipient'] ?? '—') ?></dd>

            <dt class="col-sm-3">Κατηγορία</dt>
            <dd class="col-sm-9"><?= e($rec['category_name'] ?? '—') ?></dd>

            <dt class="col-sm-3">Αρ. Εγγράφου Αποστολέα</dt>
            <dd class="col-sm-9"><?= e($rec['ref_number'] ?? '—') ?></dd>

            <dt class="col-sm-3">Σημειώσεις</dt>
            <dd class="col-sm-9"><?= nl2br(e($rec['notes'] ?? '—')) ?></dd>

            <dt class="col-sm-3">Καταχωρήθηκε</dt>
            <dd class="col-sm-9"><?= fmt_datetime($rec['created_at']) ?></dd>

            <dt class="col-sm-3">Τελ. Ενημέρωση</dt>
            <dd class="col-sm-9"><?= fmt_datetime($rec['updated_at']) ?></dd>
        </dl>
    </div>
</div>

<?php layout_footer(); ?>
