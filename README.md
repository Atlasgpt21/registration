# Σύστημα Πρωτοκόλλου & Μητρώου Μελών

Εφαρμογή καταχώρησης πρωτοκόλλου εισερχομένων/εξερχομένων εγγράφων και μητρώου μελών σωματείου.

## Δυνατότητες

### Πρωτόκολλο Εγγράφων
- Καταχώρηση εισερχομένων & εξερχομένων εγγράφων
- Αυτόματη αρίθμηση πρωτοκόλλου (ανά έτος)
- Διαχείριση κατηγοριών εγγράφων (δημιουργία, επεξεργασία, διαγραφή)
- Φίλτρα αναζήτησης (τύπος, κατηγορία, ημερομηνία, ελεύθερο κείμενο)
- Εκτύπωση λίστας

### Μητρώο Μελών
- Καταχώρηση μελών με πλήρη στοιχεία (ΑΔΤ, ΑΦΜ, ΑΜΚΑ, διεύθυνση, κ.λπ.)
- Επιλογή τύπου: Μέλος ή Μέλος & Αθλητής
- Ιδιότητα στο σωματείο (Πρόεδρος, Μέλος ΔΣ, κ.λπ.)
- Φίλτρα αναζήτησης & εκτύπωση λίστας

## Τεχνολογίες

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.2+
- Bootstrap 5.3 (CDN)
- HTML / CSS / vanilla JS

## Εγκατάσταση

### 1. Clone

```bash
git clone https://github.com/Atlasgpt21/registration.git
```

### 2. Ρύθμιση config.php

Αντιγράψτε το `config.php.example` σε `config.php` και τροποποιήστε τα DB credentials:

```bash
cp config.php.example config.php
```

```php
'db' => [
    'host'     => 'localhost',
    'port'     => 3306,
    'dbname'   => 'protocol_db',
    'user'     => 'your_user',
    'password' => 'your_password',
    'charset'  => 'utf8mb4',
],
```

> **Hostinger**: Αποθηκεύστε το `config.php` **ΕΞΩ** από το `public_html` για ασφάλεια. Ο bootstrap ψάχνει πρώτα στο app root και μετά στον parent.

### 3. Δημιουργία βάσης

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS protocol_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p protocol_db < sql/schema.sql
mysql -u root -p protocol_db < sql/seed.sql   # προαιρετικά, για demo κατηγορίες
```

### 4. Τοπικό τεστ

```bash
php -S 127.0.0.1:8000 -t public
```

Ανοίξτε http://127.0.0.1:8000/protocols.php

## Εγκατάσταση στο Hostinger

1. **Δημιουργήστε MySQL βάση** από το hPanel → Databases
2. **Ανεβάστε τα αρχεία** μέσω File Manager:
   - `public/` → μέσα στο `public_html/` (ή subdomain folder)
   - `src/` και `config.php` → **ΕΞΩ** από `public_html/` (π.χ. στο `/home/user/`)
3. **Ρυθμίστε** τα DB credentials στο `config.php`
4. **Εκτελέστε** το `schema.sql` μέσω phpMyAdmin

## Δομή

```
registration/
├── config.php.example      # Template ρυθμίσεων
├── sql/
│   ├── schema.sql           # Δημιουργία πινάκων
│   └── seed.sql             # Demo κατηγορίες (προαιρετικό)
├── src/                     # Business logic
│   ├── bootstrap.php
│   ├── db.php
│   ├── helpers.php
│   ├── csrf.php
│   ├── layout.php
│   ├── protocols.php
│   ├── categories.php
│   └── members.php
└── public/                  # Web root (document root)
    ├── index.php
    ├── protocols.php         # Λίστα πρωτοκόλλου
    ├── protocol_new.php
    ├── protocol_edit.php
    ├── protocol_view.php
    ├── protocol_delete.php
    ├── protocols_print.php
    ├── categories.php        # Διαχείριση κατηγοριών
    ├── category_new.php
    ├── category_edit.php
    ├── category_delete.php
    ├── members.php           # Μητρώο μελών
    ├── member_new.php
    ├── member_edit.php
    ├── member_view.php
    ├── member_delete.php
    ├── members_print.php
    └── assets/
        ├── app.css
        ├── app.js
        └── print.css
```
