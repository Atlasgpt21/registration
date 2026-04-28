<?php
require_once __DIR__ . '/../src/bootstrap.php';

$id  = (int)($_GET['id'] ?? 0);
$rec = member_find($DB, $id);
if (!$rec) {
    flash_set('error', 'Το μέλος δεν βρέθηκε.');
    redirect('members.php');
}

layout_header('Προβολή Μέλους', 'members');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <a href="members.php" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0"><?= e($rec['last_name'] . ' ' . $rec['first_name']) ?></h4>
        <span class="<?= member_type_class($rec['member_type']) ?> ms-2"><?= member_type_label($rec['member_type']) ?></span>
        <?php if ($rec['status'] === 'ACTIVE'): ?>
            <span class="badge bg-success ms-1">Ενεργό</span>
        <?php else: ?>
            <span class="badge bg-secondary ms-1">Ανενεργό</span>
        <?php endif; ?>
    </div>
    <div>
        <a href="member_registration_print.php?id=<?= $id ?>" target="_blank" class="btn btn-info btn-sm text-white"><i class="bi bi-printer me-1"></i>Φόρμα Εγγραφής</a>
        <a href="member_edit.php?id=<?= $id ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Επεξεργασία</a>
        <form method="post" action="member_delete.php" class="d-inline" onsubmit="return confirm('Διαγραφή αυτού του μέλους;')">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Διαγραφή</button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person me-1"></i>Προσωπικά Στοιχεία</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Αρ. Μητρώου</dt>
                    <dd class="col-sm-7"><?= e($rec['member_number'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Επώνυμο</dt>
                    <dd class="col-sm-7"><strong><?= e($rec['last_name']) ?></strong></dd>

                    <dt class="col-sm-5">Όνομα</dt>
                    <dd class="col-sm-7"><strong><?= e($rec['first_name']) ?></strong></dd>

                    <dt class="col-sm-5">Πατρώνυμο</dt>
                    <dd class="col-sm-7"><?= e($rec['father_name'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Μητρώνυμο</dt>
                    <dd class="col-sm-7"><?= e($rec['mother_name'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Ημ. Γέννησης</dt>
                    <dd class="col-sm-7"><?= fmt_date($rec['birth_date']) ?: '—' ?></dd>

                    <dt class="col-sm-5">Επάγγελμα</dt>
                    <dd class="col-sm-7"><?= e($rec['occupation'] ?? '—') ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-card-text me-1"></i>Ταυτοποίηση</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">ΑΔΤ</dt>
                    <dd class="col-sm-7"><?= e($rec['id_number'] ?? '—') ?></dd>

                    <dt class="col-sm-5">ΑΦΜ</dt>
                    <dd class="col-sm-7"><?= e($rec['tax_number'] ?? '—') ?></dd>

                    <dt class="col-sm-5">ΑΜΚΑ</dt>
                    <dd class="col-sm-7"><?= e($rec['amka'] ?? '—') ?></dd>
                </dl>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-geo-alt me-1"></i>Στοιχεία Επικοινωνίας</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Διεύθυνση</dt>
                    <dd class="col-sm-7"><?= e($rec['address'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Πόλη</dt>
                    <dd class="col-sm-7"><?= e($rec['city'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Τ.Κ.</dt>
                    <dd class="col-sm-7"><?= e($rec['postal_code'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Τηλέφωνο</dt>
                    <dd class="col-sm-7"><?= e($rec['phone'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Κινητό</dt>
                    <dd class="col-sm-7"><?= e($rec['mobile'] ?? '—') ?></dd>

                    <dt class="col-sm-5">Email</dt>
                    <dd class="col-sm-7"><?= e($rec['email'] ?? '—') ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><i class="bi bi-building me-1"></i>Σωματείο</div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Τύπος Εγγραφής</dt>
            <dd class="col-sm-9"><span class="<?= member_type_class($rec['member_type']) ?>"><?= member_type_label($rec['member_type']) ?></span></dd>

            <dt class="col-sm-3">Ιδιότητα</dt>
            <dd class="col-sm-9"><?= e($rec['role_in_club'] ?? '—') ?></dd>

            <dt class="col-sm-3">Ημ. Εγγραφής</dt>
            <dd class="col-sm-9"><?= fmt_date($rec['registration_date']) ?: '—' ?></dd>

            <dt class="col-sm-3">Σημειώσεις</dt>
            <dd class="col-sm-9"><?= nl2br(e($rec['notes'] ?? '—')) ?></dd>

            <dt class="col-sm-3">Καταχωρήθηκε</dt>
            <dd class="col-sm-9"><?= fmt_datetime($rec['created_at']) ?></dd>
        </dl>
    </div>
</div>

<?php if (isset($_GET['print']) && $_GET['print'] === '1'): ?>
<script>window.addEventListener('DOMContentLoaded', function(){ window.open('member_registration_print.php?id=<?= $id ?>', '_blank'); });</script>
<?php endif; ?>

<?php layout_footer(); ?>
