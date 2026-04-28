-- Σχήμα βάσης δεδομένων για το σύστημα Πρωτοκόλλου
-- Τρέξτε: mysql -u root -p protocol_db < sql/schema.sql

CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)  NOT NULL,
    description VARCHAR(500)  DEFAULT NULL,
    active      TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS protocols (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    protocol_number VARCHAR(30)   NOT NULL,
    direction       ENUM('IN','OUT') NOT NULL COMMENT 'IN=Εισερχόμενο, OUT=Εξερχόμενο',
    doc_date        DATE          NOT NULL COMMENT 'Ημερομηνία εγγράφου',
    subject         VARCHAR(500)  NOT NULL,
    sender          VARCHAR(300)  DEFAULT NULL COMMENT 'Αποστολέας (για εισερχόμενα)',
    recipient       VARCHAR(300)  DEFAULT NULL COMMENT 'Παραλήπτης (για εξερχόμενα)',
    category_id     INT UNSIGNED  DEFAULT NULL,
    ref_number      VARCHAR(100)  DEFAULT NULL COMMENT 'Αρ. εγγράφου αποστολέα',
    notes           TEXT          DEFAULT NULL,
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_protocols_direction (direction),
    KEY idx_protocols_doc_date  (doc_date),
    KEY idx_protocols_category  (category_id),
    CONSTRAINT fk_protocols_category FOREIGN KEY (category_id)
        REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Αύξων αριθμός ανά έτος
CREATE TABLE IF NOT EXISTS protocol_sequence (
    seq_year    YEAR          NOT NULL PRIMARY KEY,
    last_number INT UNSIGNED  NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Μητρώο μελών σωματείου
CREATE TABLE IF NOT EXISTS members (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_number   VARCHAR(30)   DEFAULT NULL COMMENT 'Αριθμός μητρώου',
    last_name       VARCHAR(150)  NOT NULL,
    first_name      VARCHAR(150)  NOT NULL,
    father_name     VARCHAR(150)  DEFAULT NULL COMMENT 'Πατρώνυμο',
    mother_name     VARCHAR(150)  DEFAULT NULL COMMENT 'Μητρώνυμο',
    birth_date      DATE          DEFAULT NULL,
    id_number       VARCHAR(30)   DEFAULT NULL COMMENT 'Αριθμός Δελτίου Ταυτότητας',
    tax_number      VARCHAR(20)   DEFAULT NULL COMMENT 'ΑΦΜ',
    amka            VARCHAR(20)   DEFAULT NULL COMMENT 'ΑΜΚΑ',
    member_type     ENUM('MEMBER','ATHLETE') NOT NULL DEFAULT 'MEMBER' COMMENT 'MEMBER=Μέλος, ATHLETE=Μέλος & Αθλητής',
    address         VARCHAR(300)  DEFAULT NULL,
    city            VARCHAR(150)  DEFAULT NULL,
    postal_code     VARCHAR(10)   DEFAULT NULL,
    phone           VARCHAR(30)   DEFAULT NULL,
    mobile          VARCHAR(30)   DEFAULT NULL,
    email           VARCHAR(200)  DEFAULT NULL,
    occupation      VARCHAR(200)  DEFAULT NULL COMMENT 'Επάγγελμα',
    role_in_club    VARCHAR(200)  DEFAULT NULL COMMENT 'Ιδιότητα στο σωματείο (π.χ. Πρόεδρος, Μέλος ΔΣ)',
    registration_date DATE        DEFAULT NULL COMMENT 'Ημερομηνία εγγραφής στο σωματείο',
    status          ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    notes           TEXT          DEFAULT NULL,
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_members_status (status),
    KEY idx_members_last_name (last_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
