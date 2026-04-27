<?php
require_once __DIR__ . '/../src/bootstrap.php';

$filters = [
    'direction'   => input('direction'),
    'category_id' => input('category_id'),
    'date_from'   => input('date_from'),
    'date_to'     => input('date_to'),
    'search'      => input('search'),
];

$rows = protocol_list_all($DB, $filters);

layout_print_header('Βιβλίο Πρωτοκόλλου');
?>

<?php if (array_filter($filters)): ?>
<p class="text-muted small">
    Φίλτρα:
    <?php if ($filters['direction']): ?> Τύπος: <?= direction_label($filters['direction']) ?> |<?php endif; ?>
    <?php if ($filters['date_from']): ?> Από: <?= e($filters['date_from']) ?> |<?php endif; ?>
    <?php if ($filters['date_to']): ?> Έως: <?= e($filters['date_to']) ?> |<?php endif; ?>
    <?php if ($filters['search']): ?> Αναζήτηση: "<?= e($filters['search']) ?>" |<?php endif; ?>
</p>
<?php endif; ?>

<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>Αρ. Πρωτ.</th>
            <th>Τύπος</th>
            <th>Ημερομηνία</th>
            <th>Θέμα</th>
            <th>Αποστολέας / Παραλήπτης</th>
            <th>Κατηγορία</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= e($row['protocol_number']) ?></td>
            <td><?= direction_label($row['direction']) ?></td>
            <td><?= fmt_date($row['doc_date']) ?></td>
            <td><?= e($row['subject']) ?></td>
            <td><?= e($row['direction'] === 'IN' ? $row['sender'] : $row['recipient']) ?></td>
            <td><?= e($row['category_name'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p class="text-muted small">Σύνολο: <?= count($rows) ?> εγγραφές</p>

<?php layout_print_footer(); ?>
