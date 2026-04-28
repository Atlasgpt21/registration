<?php
require_once __DIR__ . '/../src/bootstrap.php';

$id  = (int)($_GET['id'] ?? 0);
$rec = member_find($DB, $id);
if (!$rec) {
    flash_set('error', 'Το μέλος δεν βρέθηκε.');
    redirect('members.php');
}

$clubName      = $CFG['club']['name']      ?? 'ΣΩΜΑΤΕΙΟ';
$clubLogo      = $CFG['club']['logo']      ?? '';
$clubPresident = $CFG['club']['president'] ?? 'Ο Πρόεδρος';
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <title>Φόρμα Εγγραφής — <?= e($rec['last_name'] . ' ' . $rec['first_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Segoe UI', system-ui, 'Helvetica Neue', sans-serif;
            font-size: 13px;
            color: #222;
            background: #fff;
        }
        .reg-container {
            max-width: 750px;
            margin: 0 auto;
            padding: 20px 30px;
        }
        /* Header */
        .reg-header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .reg-header .club-logo {
            max-height: 80px;
            max-width: 120px;
            margin-bottom: 8px;
        }
        .reg-header h2 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 5px 0;
        }
        .reg-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #444;
            margin: 3px 0 0;
        }
        .reg-header .reg-date {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }
        /* Section titles */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            background: #f0f0f0;
            padding: 5px 10px;
            margin: 15px 0 10px;
            border-left: 4px solid #333;
        }
        /* Data rows */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .data-table td {
            padding: 5px 8px;
            vertical-align: top;
            border-bottom: 1px dotted #ccc;
        }
        .data-table .label-cell {
            font-weight: 600;
            color: #555;
            width: 35%;
            white-space: nowrap;
        }
        .data-table .value-cell {
            color: #111;
            width: 65%;
        }
        /* Member type highlight */
        .member-type-badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 12px;
        }
        .member-type-badge.member-only {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #90caf9;
        }
        .member-type-badge.member-athlete {
            background: #fff8e1;
            color: #f57f17;
            border: 1px solid #ffcc02;
        }
        /* Signatures */
        .signatures {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 60px auto 5px;
        }
        .signature-label {
            font-weight: 600;
            font-size: 12px;
        }
        .signature-name {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }
        /* Footer */
        .reg-footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
            .reg-container { padding: 0; }
        }
        @media screen {
            .no-print-btn {
                position: fixed;
                top: 15px;
                right: 15px;
                z-index: 1000;
            }
        }
    </style>
</head>
<body>

<div class="no-print no-print-btn">
    <button class="btn btn-primary btn-sm" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 10a1 1 0 0 1-1-1v-3h8v3a1 1 0 0 1-1 1z"/></svg>
        Εκτύπωση
    </button>
</div>

<div class="reg-container">

    <!-- Header with logo and club name -->
    <div class="reg-header">
        <?php if ($clubLogo): ?>
            <img src="<?= e($clubLogo) ?>" alt="Λογότυπο" class="club-logo"><br>
        <?php endif; ?>
        <h2><?= e($clubName) ?></h2>
        <h3>Αίτηση Εγγραφής Μέλους</h3>
        <div class="reg-date">Ημερομηνία: <?= fmt_date($rec['registration_date']) ?: date('d/m/Y') ?></div>
    </div>

    <!-- Member Type -->
    <div class="text-center mb-3">
        <?php
        $typeClass = $rec['member_type'] === 'ATHLETE' ? 'member-athlete' : 'member-only';
        ?>
        <span class="member-type-badge <?= $typeClass ?>">
            <?= member_type_label($rec['member_type']) ?>
        </span>
    </div>

    <!-- Personal Details -->
    <div class="section-title">Προσωπικά Στοιχεία</div>
    <table class="data-table">
        <tr>
            <td class="label-cell">Αρ. Μητρώου</td>
            <td class="value-cell"><?= e($rec['member_number'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">Επώνυμο</td>
            <td class="value-cell"><strong><?= e($rec['last_name']) ?></strong></td>
        </tr>
        <tr>
            <td class="label-cell">Όνομα</td>
            <td class="value-cell"><strong><?= e($rec['first_name']) ?></strong></td>
        </tr>
        <tr>
            <td class="label-cell">Πατρώνυμο</td>
            <td class="value-cell"><?= e($rec['father_name'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">Μητρώνυμο</td>
            <td class="value-cell"><?= e($rec['mother_name'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">Ημ. Γέννησης</td>
            <td class="value-cell"><?= fmt_date($rec['birth_date']) ?: '—' ?></td>
        </tr>
        <tr>
            <td class="label-cell">Επάγγελμα</td>
            <td class="value-cell"><?= e($rec['occupation'] ?? '—') ?></td>
        </tr>
    </table>

    <!-- Identification -->
    <div class="section-title">Στοιχεία Ταυτοποίησης</div>
    <table class="data-table">
        <tr>
            <td class="label-cell">Αρ. Δελτίου Ταυτότητας (ΑΔΤ)</td>
            <td class="value-cell"><?= e($rec['id_number'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">ΑΦΜ</td>
            <td class="value-cell"><?= e($rec['tax_number'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">ΑΜΚΑ</td>
            <td class="value-cell"><?= e($rec['amka'] ?? '—') ?></td>
        </tr>
    </table>

    <!-- Contact -->
    <div class="section-title">Στοιχεία Επικοινωνίας</div>
    <table class="data-table">
        <tr>
            <td class="label-cell">Διεύθυνση</td>
            <td class="value-cell"><?= e($rec['address'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">Πόλη</td>
            <td class="value-cell"><?= e($rec['city'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">Τ.Κ.</td>
            <td class="value-cell"><?= e($rec['postal_code'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">Τηλέφωνο</td>
            <td class="value-cell"><?= e($rec['phone'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">Κινητό</td>
            <td class="value-cell"><?= e($rec['mobile'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">Email</td>
            <td class="value-cell"><?= e($rec['email'] ?? '—') ?></td>
        </tr>
    </table>

    <!-- Club info -->
    <div class="section-title">Στοιχεία Σωματείου</div>
    <table class="data-table">
        <tr>
            <td class="label-cell">Τύπος Εγγραφής</td>
            <td class="value-cell"><?= member_type_label($rec['member_type']) ?></td>
        </tr>
        <tr>
            <td class="label-cell">Ιδιότητα στο Σωματείο</td>
            <td class="value-cell"><?= e($rec['role_in_club'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label-cell">Ημ. Εγγραφής</td>
            <td class="value-cell"><?= fmt_date($rec['registration_date']) ?: '—' ?></td>
        </tr>
        <?php if (!empty($rec['notes'])): ?>
        <tr>
            <td class="label-cell">Σημειώσεις</td>
            <td class="value-cell"><?= e($rec['notes']) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <!-- Signatures -->
    <div class="signatures">
        <div class="row">
            <div class="col-6 signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Το Μέλος</div>
                <div class="signature-name"><?= e($rec['last_name'] . ' ' . $rec['first_name']) ?></div>
            </div>
            <div class="col-6 signature-box">
                <div class="signature-line"></div>
                <div class="signature-label"><?= e($clubPresident) ?></div>
                <div class="signature-name"><?= e($clubName) ?></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="reg-footer">
        <?= e($clubName) ?> &mdash; Εκτυπώθηκε: <?= date('d/m/Y H:i') ?>
    </div>

</div>

<script>window.onload = function(){ window.print(); };</script>
</body>
</html>
