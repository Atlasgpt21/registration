<?php
require_once __DIR__ . '/../src/bootstrap.php';

$filters = [
    'status'      => input('status'),
    'member_type' => input('member_type'),
    'search'      => input('search'),
];

$rows = member_list_all($DB, $filters);

layout_print_header('Μητρώο Μελών');
?>

<?php if (array_filter($filters, fn($v) => $v !== '')): ?>
<p class="text-muted small">
    Φίλτρα:
    <?php if ($filters['status']): ?> Κατάσταση: <?= member_status_label($filters['status']) ?> |<?php endif; ?>
    <?php if ($filters['member_type']): ?> Τύπος: <?= member_type_label($filters['member_type']) ?> |<?php endif; ?>
    <?php if ($filters['search']): ?> Αναζήτηση: "<?= e($filters['search']) ?>" |<?php endif; ?>
</p>
<?php endif; ?>

<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>Αρ. Μητρ.</th>
            <th>Επώνυμο</th>
            <th>Όνομα</th>
            <th>Πατρώνυμο</th>
            <th>ΑΔΤ</th>
            <th>ΑΦΜ</th>
            <th>ΑΜΚΑ</th>
            <th>Τύπος</th>
            <th>Τηλέφωνο</th>
            <th>Κατάσταση</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= e($row['member_number'] ?? '') ?></td>
            <td><?= e($row['last_name']) ?></td>
            <td><?= e($row['first_name']) ?></td>
            <td><?= e($row['father_name'] ?? '') ?></td>
            <td><?= e($row['id_number'] ?? '') ?></td>
            <td><?= e($row['tax_number'] ?? '') ?></td>
            <td><?= e($row['amka'] ?? '') ?></td>
            <td><?= member_type_label($row['member_type']) ?></td>
            <td><?= e($row['mobile'] ?: $row['phone'] ?? '') ?></td>
            <td><?= member_status_label($row['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p class="text-muted small">Σύνολο: <?= count($rows) ?> μέλη</p>

<?php layout_print_footer(); ?>
