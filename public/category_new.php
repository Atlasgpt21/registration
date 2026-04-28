<?php
require_once __DIR__ . '/../src/bootstrap.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name = input('name');
    $desc = input('description');

    if ($name === '') $errors[] = 'Συμπληρώστε όνομα κατηγορίας.';

    if (empty($errors)) {
        try {
            category_create($DB, $name, $desc ?: null);
            flash_set('success', 'Η κατηγορία δημιουργήθηκε.');
            redirect('categories.php');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = 'Υπάρχει ήδη κατηγορία με αυτό το όνομα.';
            } else {
                throw $e;
            }
        }
    }
}

layout_header('Νέα Κατηγορία', 'categories');
?>

<div class="d-flex align-items-center mb-3">
    <a href="categories.php" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0">Νέα Κατηγορία</h4>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Όνομα <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required maxlength="150" value="<?= e(input('name')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Περιγραφή</label>
                <textarea name="description" class="form-control" rows="2" maxlength="500"><?= e(input('description')) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Αποθήκευση</button>
            <a href="categories.php" class="btn btn-outline-secondary ms-2">Ακύρωση</a>
        </form>
    </div>
</div>

<?php layout_footer(); ?>
