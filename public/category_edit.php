<?php
require_once __DIR__ . '/../src/bootstrap.php';

$id  = (int)($_GET['id'] ?? 0);
$cat = category_find($DB, $id);
if (!$cat) {
    flash_set('error', 'Η κατηγορία δεν βρέθηκε.');
    redirect('categories.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name   = input('name');
    $desc   = input('description');
    $active = (int)input('active', '1');

    if ($name === '') $errors[] = 'Συμπληρώστε όνομα κατηγορίας.';

    if (empty($errors)) {
        try {
            category_update($DB, $id, $name, $desc ?: null, $active);
            flash_set('success', 'Η κατηγορία ενημερώθηκε.');
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

layout_header('Επεξεργασία Κατηγορίας', 'categories');
?>

<div class="d-flex align-items-center mb-3">
    <a href="categories.php" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0">Επεξεργασία: <?= e($cat['name']) ?></h4>
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
                <input type="text" name="name" class="form-control" required maxlength="150"
                       value="<?= e(input('name', $cat['name'])) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Περιγραφή</label>
                <textarea name="description" class="form-control" rows="2" maxlength="500"><?= e(input('description', $cat['description'] ?? '')) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Κατάσταση</label>
                <select name="active" class="form-select">
                    <option value="1" <?= (input('active', (string)$cat['active']) === '1') ? 'selected' : '' ?>>Ενεργή</option>
                    <option value="0" <?= (input('active', (string)$cat['active']) === '0') ? 'selected' : '' ?>>Ανενεργή</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Αποθήκευση</button>
            <a href="categories.php" class="btn btn-outline-secondary ms-2">Ακύρωση</a>
        </form>
    </div>
</div>

<?php layout_footer(); ?>
