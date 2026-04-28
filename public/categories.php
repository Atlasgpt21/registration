<?php
require_once __DIR__ . '/../src/bootstrap.php';

$categories = categories_all($DB);

layout_header('Κατηγορίες Εγγράφων', 'categories');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-tags me-2"></i>Κατηγορίες Εγγράφων</h4>
    <a href="category_new.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Νέα Κατηγορία</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Όνομα</th>
                    <th>Περιγραφή</th>
                    <th>Κατάσταση</th>
                    <th>Εγγραφές</th>
                    <th class="text-end">Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Δεν υπάρχουν κατηγορίες</td></tr>
                <?php endif; ?>
                <?php foreach ($categories as $cat): ?>
                <?php $count = category_count_protocols($DB, (int)$cat['id']); ?>
                <tr>
                    <td><strong><?= e($cat['name']) ?></strong></td>
                    <td><?= e($cat['description'] ?? '') ?></td>
                    <td>
                        <?php if ($cat['active']): ?>
                            <span class="badge bg-success">Ενεργή</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Ανενεργή</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $count ?></td>
                    <td class="text-end">
                        <a href="category_edit.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-warning" title="Επεξεργασία"><i class="bi bi-pencil"></i></a>
                        <?php if ($count === 0): ?>
                        <form method="post" action="category_delete.php" class="d-inline" onsubmit="return confirm('Διαγραφή κατηγορίας;')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Διαγραφή"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php layout_footer(); ?>
