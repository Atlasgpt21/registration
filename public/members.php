<?php
require_once __DIR__ . '/../src/bootstrap.php';

$page    = max(1, (int)input('page', '1'));
$filters = [
    'status'      => input('status'),
    'member_type' => input('member_type'),
    'search'      => input('search'),
];
$result     = member_list($DB, $filters, $page);
$rows       = $result['rows'];
$total      = $result['total'];
$totalPages = max(1, (int)ceil($total / 20));

layout_header('Μητρώο Μελών', 'members');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-people me-2"></i>Μητρώο Μελών</h4>
    <div>
        <a href="members_print.php?<?= http_build_query(array_filter($filters)) ?>" target="_blank" class="btn btn-outline-secondary btn-sm me-1">
            <i class="bi bi-printer me-1"></i>Εκτύπωση
        </a>
        <a href="member_new.php" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i>Νέο Μέλος
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label form-label-sm">Κατάσταση</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Όλα</option>
                    <option value="ACTIVE"   <?= $filters['status'] === 'ACTIVE'   ? 'selected' : '' ?>>Ενεργά</option>
                    <option value="INACTIVE" <?= $filters['status'] === 'INACTIVE' ? 'selected' : '' ?>>Ανενεργά</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm">Τύπος</label>
                <select name="member_type" class="form-select form-select-sm">
                    <option value="">Όλοι</option>
                    <option value="MEMBER"  <?= $filters['member_type'] === 'MEMBER'  ? 'selected' : '' ?>>Μέλος</option>
                    <option value="ATHLETE" <?= $filters['member_type'] === 'ATHLETE' ? 'selected' : '' ?>>Μέλος & Αθλητής</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label form-label-sm">Αναζήτηση</label>
                <input type="text" name="search" class="form-control form-control-sm" value="<?= e($filters['search']) ?>" placeholder="Όνομα, ΑΔΤ, ΑΦΜ, τηλ...">
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
                    <th>Αρ. Μητρ.</th>
                    <th>Επώνυμο</th>
                    <th>Όνομα</th>
                    <th>Πατρώνυμο</th>
                    <th>Τύπος</th>
                    <th>Τηλέφωνο</th>
                    <th>Κατάσταση</th>
                    <th class="text-end">Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Δεν βρέθηκαν μέλη</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['member_number'] ?? '') ?></td>
                    <td><strong><?= e($row['last_name']) ?></strong></td>
                    <td><?= e($row['first_name']) ?></td>
                    <td><?= e($row['father_name'] ?? '') ?></td>
                    <td><span class="<?= member_type_class($row['member_type']) ?>"><?= member_type_label($row['member_type']) ?></span></td>
                    <td><?= e($row['mobile'] ?: $row['phone'] ?? '') ?></td>
                    <td>
                        <?php if ($row['status'] === 'ACTIVE'): ?>
                            <span class="badge bg-success">Ενεργό</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Ανενεργό</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a href="member_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Προβολή"><i class="bi bi-eye"></i></a>
                        <a href="member_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning" title="Επεξεργασία"><i class="bi bi-pencil"></i></a>
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
                        <a class="page-link" href="?<?= http_build_query(array_merge(array_filter($filters), ['page' => $p])) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<div class="mt-2 text-muted small">Σύνολο: <?= $total ?> μέλη</div>

<?php layout_footer(); ?>
