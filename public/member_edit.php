<?php
require_once __DIR__ . '/../src/bootstrap.php';

$id  = (int)($_GET['id'] ?? 0);
$rec = member_find($DB, $id);
if (!$rec) {
    flash_set('error', 'Το μέλος δεν βρέθηκε.');
    redirect('members.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $data = [
        'member_number'    => input('member_number'),
        'last_name'        => input('last_name'),
        'first_name'       => input('first_name'),
        'father_name'      => input('father_name'),
        'mother_name'      => input('mother_name'),
        'birth_date'       => input('birth_date'),
        'id_number'        => input('id_number'),
        'tax_number'       => input('tax_number'),
        'amka'             => input('amka'),
        'member_type'      => input('member_type'),
        'address'          => input('address'),
        'city'             => input('city'),
        'postal_code'      => input('postal_code'),
        'phone'            => input('phone'),
        'mobile'           => input('mobile'),
        'email'            => input('email'),
        'occupation'       => input('occupation'),
        'role_in_club'     => input('role_in_club'),
        'registration_date'=> input('registration_date'),
        'status'           => input('status'),
        'notes'            => input('notes'),
    ];

    if ($data['last_name'] === '')  $errors[] = 'Συμπληρώστε το επώνυμο.';
    if ($data['first_name'] === '') $errors[] = 'Συμπληρώστε το όνομα.';

    if (empty($errors)) {
        member_update($DB, $id, $data);
        flash_set('success', 'Τα στοιχεία ενημερώθηκαν.');
        redirect('member_view.php?id=' . $id);
    }
}

layout_header('Επεξεργασία Μέλους', 'members');
?>

<div class="d-flex align-items-center mb-3">
    <a href="member_view.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="mb-0">Επεξεργασία: <?= e($rec['last_name'] . ' ' . $rec['first_name']) ?></h4>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/_member_form.php'; ?>

<?php layout_footer(); ?>
