<?php
$rec = $rec ?? [];
$isEdit = !empty($rec);
$action = $isEdit ? 'member_edit.php?id=' . (int)$rec['id'] : 'member_new.php';
?>
<div class="card">
    <div class="card-body">
        <form method="post" action="<?= e($action) ?>">
            <?= csrf_field() ?>

            <h6 class="text-muted border-bottom pb-2 mb-3"><i class="bi bi-person me-1"></i>Προσωπικά Στοιχεία</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Αρ. Μητρώου</label>
                    <input type="text" name="member_number" class="form-control" maxlength="30"
                           value="<?= e(input('member_number', $rec['member_number'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Τύπος Εγγραφής <span class="text-danger">*</span></label>
                    <select name="member_type" class="form-select" required>
                        <option value="MEMBER"  <?= (input('member_type', $rec['member_type'] ?? 'MEMBER') === 'MEMBER')  ? 'selected' : '' ?>>Μέλος</option>
                        <option value="ATHLETE" <?= (input('member_type', $rec['member_type'] ?? '') === 'ATHLETE') ? 'selected' : '' ?>>Μέλος & Αθλητής</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Κατάσταση</label>
                    <select name="status" class="form-select">
                        <option value="ACTIVE"   <?= (input('status', $rec['status'] ?? 'ACTIVE') === 'ACTIVE')   ? 'selected' : '' ?>>Ενεργό</option>
                        <option value="INACTIVE" <?= (input('status', $rec['status'] ?? '') === 'INACTIVE') ? 'selected' : '' ?>>Ανενεργό</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ημ. Εγγραφής</label>
                    <input type="date" name="registration_date" class="form-control"
                           value="<?= e(input('registration_date', $rec['registration_date'] ?? '')) ?>">
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label class="form-label">Επώνυμο <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" required maxlength="150"
                           value="<?= e(input('last_name', $rec['last_name'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Όνομα <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" required maxlength="150"
                           value="<?= e(input('first_name', $rec['first_name'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ημ. Γέννησης</label>
                    <input type="date" name="birth_date" class="form-control"
                           value="<?= e(input('birth_date', $rec['birth_date'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Πατρώνυμο</label>
                    <input type="text" name="father_name" class="form-control" maxlength="150"
                           value="<?= e(input('father_name', $rec['father_name'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Μητρώνυμο</label>
                    <input type="text" name="mother_name" class="form-control" maxlength="150"
                           value="<?= e(input('mother_name', $rec['mother_name'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Επάγγελμα</label>
                    <input type="text" name="occupation" class="form-control" maxlength="200"
                           value="<?= e(input('occupation', $rec['occupation'] ?? '')) ?>">
                </div>
            </div>

            <h6 class="text-muted border-bottom pb-2 mb-3 mt-4"><i class="bi bi-card-text me-1"></i>Ταυτοποίηση</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">ΑΔΤ</label>
                    <input type="text" name="id_number" class="form-control" maxlength="30"
                           value="<?= e(input('id_number', $rec['id_number'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ΑΦΜ</label>
                    <input type="text" name="tax_number" class="form-control" maxlength="20"
                           value="<?= e(input('tax_number', $rec['tax_number'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ΑΜΚΑ</label>
                    <input type="text" name="amka" class="form-control" maxlength="20"
                           value="<?= e(input('amka', $rec['amka'] ?? '')) ?>">
                </div>
            </div>

            <h6 class="text-muted border-bottom pb-2 mb-3 mt-4"><i class="bi bi-geo-alt me-1"></i>Στοιχεία Επικοινωνίας</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Διεύθυνση</label>
                    <input type="text" name="address" class="form-control" maxlength="300"
                           value="<?= e(input('address', $rec['address'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Πόλη</label>
                    <input type="text" name="city" class="form-control" maxlength="150"
                           value="<?= e(input('city', $rec['city'] ?? '')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Τ.Κ.</label>
                    <input type="text" name="postal_code" class="form-control" maxlength="10"
                           value="<?= e(input('postal_code', $rec['postal_code'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Τηλέφωνο</label>
                    <input type="tel" name="phone" class="form-control" maxlength="30"
                           value="<?= e(input('phone', $rec['phone'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Κινητό</label>
                    <input type="tel" name="mobile" class="form-control" maxlength="30"
                           value="<?= e(input('mobile', $rec['mobile'] ?? '')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" maxlength="200"
                           value="<?= e(input('email', $rec['email'] ?? '')) ?>">
                </div>
            </div>

            <h6 class="text-muted border-bottom pb-2 mb-3 mt-4"><i class="bi bi-building me-1"></i>Σωματείο</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Ιδιότητα στο Σωματείο</label>
                    <input type="text" name="role_in_club" class="form-control" maxlength="200"
                           placeholder="π.χ. Πρόεδρος, Μέλος ΔΣ, Αθλητής..."
                           value="<?= e(input('role_in_club', $rec['role_in_club'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Σημειώσεις</label>
                    <textarea name="notes" class="form-control" rows="2"><?= e(input('notes', $rec['notes'] ?? '')) ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Αποθήκευση</button>
                <a href="members.php" class="btn btn-outline-secondary ms-2">Ακύρωση</a>
            </div>
        </form>
    </div>
</div>
