<?php
require_once __DIR__ . '/../src/bootstrap.php';

$page       = max(1, (int)input('page', '1'));
$filters    = [
    'direction'   => input('direction'),
    'category_id' => input('category_id'),
    'date_from'   => input('date_from'),
    'date_to'     => input('date_to'),
    'search'      => input('search'),
];
$result     = protocol_list($DB, $filters, $page);
$rows       = $result['rows'];
$total      = $result['total'];
$totalPages = max(1, (int)ceil($total / 20));
$categories = categories_all($DB, true);

layout_header('Βιβλίο Πρωτοκόλλου', 'protocols');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>Βιβλίο Πρωτοκόλλου</h4>
    <div>
        <a href="protocols_print.php?<?= http_build_query(array_filter($filters, fn($v) => $v !== '')) ?>" target="_blank" class="btn btn-outline-secondary btn-sm me-1">
            <i class="bi bi-printer me-1"></i>Εκτύπωση
        </a>
        <a href="protocol_new.php" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Νέα Καταχώρηση
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label form-label-sm">Τύπος</label>
                <select name="direction" class="form-select form-select-sm">
                    <option value="">Όλα</option>
                    <option value="IN"  <?= $filters['direction'] === 'IN'  ? 'selected' : '' ?>>Εισερχόμενα</option>
                    <option value="OUT" <?= $filters['direction'] === 'OUT' ? 'selected' : '' ?>>Εξερχόμενα</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Κατηγορία</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Όλες</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $filters['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Από</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= e($filters['date_from']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Έως</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= e($filters['date_to']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Αναζήτηση</label>
                <input type="text" name="search" class="form-control form-control-sm" value="<?= e($filters['search']) ?>" placeholder="Θέμα, αποστολέας...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark btn-sm w-100"><i class="bi bi-search me-1"></i>Φίλτρο</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Αρ. Πρωτ.</th>
                    <th>Τύπος</th>
                    <th>Ημερομηνία</th>
                    <th>Θέμα</th>
                    <th>Αποστολέας / Παραλήπτης</th>
                    <th>Κατηγορία</th>
                    <th class="text-end">Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Δεν βρέθηκαν εγγραφές</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><strong><?= e($row['protocol_number']) ?></strong></td>
                    <td><span class="<?= direction_class($row['direction']) ?>"><?= direction_label($row['direction']) ?></span></td>
                    <td><?= fmt_date($row['doc_date']) ?></td>
                    <td><?= e($row['subject']) ?></td>
                    <td><?= e($row['direction'] === 'IN' ? $row['sender'] : $row['recipient']) ?></td>
                    <td><?= e($row['category_name'] ?? '') ?></td>
                    <td class="text-end">
                        <a href="protocol_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Προβολή"><i class="bi bi-eye"></i></a>
                        <a href="protocol_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning" title="Επεξεργασία"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPages > 1): ?>
    <div class="card-footer">
        <nav>
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge(array_filter($filters, fn($v) => $v !== ''), ['page' => $p])) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<div class="mt-2 text-muted small">Σύνολο: <?= $total ?> εγγραφές</div>

<?php layout_footer(); ?>
