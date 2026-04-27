<?php
$rec = $rec ?? [];
$isEdit = !empty($rec);
$action = $isEdit ? 'protocol_edit.php?id=' . (int)$rec['id'] : 'protocol_new.php';
?>
<div class="card">
    <div class="card-body">
        <form method="post" action="<?= e($action) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Τύπος Εγγράφου <span class="text-danger">*</span></label>
                    <select name="direction" class="form-select" required>
                        <option value="">-- Επιλέξτε --</option>
                        <option value="IN"  <?= (input('direction', $rec['direction'] ?? '') === 'IN')  ? 'selected' : '' ?>>Εισερχόμενο</option>
                        <option value="OUT" <?= (input('direction', $rec['direction'] ?? '') === 'OUT') ? 'selected' : '' ?>>Εξερχόμενο</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ημερομηνία Εγγράφου <span class="text-danger">*</span></label>
                    <input type="date" name="doc_date" class="form-control" required
                           value="<?= e(input('doc_date', $rec['doc_date'] ?? date('Y-m-d'))) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Κατηγορία</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Χωρίς --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (input('category_id', (string)($rec['category_id'] ?? '')) == $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Θέμα <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control" required maxlength="500"
                           value="<?= e(input('subject', $rec['subject'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Αποστολέας</label>
                    <input type="text" name="sender" class="form-control" maxlength="300"
                           value="<?= e(input('sender', $rec['sender'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Παραλήπτης</label>
                    <input type="text" name="recipient" class="form-control" maxlength="300"
                           value="<?= e(input('recipient', $rec['recipient'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Αρ. Εγγράφου Αποστολέα</label>
                    <input type="text" name="ref_number" class="form-control" maxlength="100"
                           value="<?= e(input('ref_number', $rec['ref_number'] ?? '')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Σημειώσεις</label>
                    <textarea name="notes" class="form-control" rows="3"><?= e(input('notes', $rec['notes'] ?? '')) ?></textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Αποθήκευση</button>
                <a href="protocols.php" class="btn btn-outline-secondary ms-2">Ακύρωση</a>
            </div>
        </form>
    </div>
</div>
