-- =====================================================================
-- Petanque Tournament Management System - Database Schema
-- =====================================================================

-- Κατηγορίες Πρωταθλημάτων
CREATE TABLE IF NOT EXISTS petanque_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    gender ENUM('M', 'F', 'MIX') NOT NULL,
    team_size INT NOT NULL COMMENT '2 ή 3',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Πρωταθλήματα
CREATE TABLE IF NOT EXISTS petanque_championships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    year INT,
    status ENUM('draft', 'registration', 'swiss', 'knockout', 'finished') DEFAULT 'draft',
    start_date DATE,
    end_date DATE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Κατηγορίες ανά πρωτάθλημα (π.χ. CHAMP001 -> 3vs3 Άνδρες, 2vs2 Γυναίκες κλπ)
CREATE TABLE IF NOT EXISTS petanque_championship_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    championship_id INT NOT NULL,
    category_id INT NOT NULL,
    max_teams INT DEFAULT 0 COMMENT 'Περιορισμός ομάδων, 0=unlimited',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (championship_id) REFERENCES petanque_championships(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES petanque_categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_champ_cat (championship_id, category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ομάδες
CREATE TABLE IF NOT EXISTS petanque_teams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    championship_id INT NOT NULL,
    category_id INT NOT NULL,
    club_id INT,
    name VARCHAR(100) NOT NULL,
    players TEXT COMMENT 'JSON array ή comma-separated playercode list',
    status ENUM('registered', 'active', 'inactive') DEFAULT 'registered',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (championship_id) REFERENCES petanque_championships(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES petanque_categories(id) ON DELETE CASCADE,
    KEY idx_champ_cat (championship_id, category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- ΕΛΒΕΤΙΚΟ ΣΥΣΤΗΜΑ (5 ΓΥΡΟΙ)
-- =====================================================================

-- Γύροι (Rounds)
CREATE TABLE IF NOT EXISTS petanque_swiss_rounds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    championship_id INT NOT NULL,
    category_id INT NOT NULL,
    round_number INT NOT NULL COMMENT '1-5',
    status ENUM('scheduled', 'ongoing', 'completed') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (championship_id) REFERENCES petanque_championships(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES petanque_categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_champ_cat_round (championship_id, category_id, round_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Αγώνες Ελβετικού Συστήματος
CREATE TABLE IF NOT EXISTS petanque_swiss_matches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    championship_id INT NOT NULL,
    category_id INT NOT NULL,
    round_id INT NOT NULL,
    home_team_id INT NOT NULL,
    away_team_id INT NOT NULL,
    home_score INT,
    away_score INT,
    winner_id INT COMMENT 'Το ID της νικήσασας ομάδας',
    status ENUM('scheduled', 'ongoing', 'completed') DEFAULT 'scheduled',
    match_date DATETIME,
    venue VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (championship_id) REFERENCES petanque_championships(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES petanque_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (round_id) REFERENCES petanque_swiss_rounds(id) ON DELETE CASCADE,
    FOREIGN KEY (home_team_id) REFERENCES petanque_teams(id) ON DELETE CASCADE,
    FOREIGN KEY (away_team_id) REFERENCES petanque_teams(id) ON DELETE CASCADE,
    FOREIGN KEY (winner_id) REFERENCES petanque_teams(id) ON DELETE SET NULL,
    KEY idx_champ_cat_round (championship_id, category_id, round_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- ΚΑΤΆΤΑΞΗ (RANKING) μετά τα 5 γύρο του Ελβετικού Συστήματος
-- =====================================================================

CREATE TABLE IF NOT EXISTS petanque_standings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    championship_id INT NOT NULL,
    category_id INT NOT NULL,
    team_id INT NOT NULL,
    position INT COMMENT 'Θέση κατάταξης (1, 2, 3...)',
    wins INT DEFAULT 0 COMMENT 'Αριθμός νικών',
    points INT DEFAULT 0 COMMENT 'Σύνολο πόντων (wins * 3)',
    buchholz DECIMAL(10, 2) DEFAULT 0 COMMENT 'Buchholz score (άθροισμα πόντων αντιπάλων)',
    fine_buchholz DECIMAL(10, 2) DEFAULT 0 COMMENT 'Fine Buchholz (χωρίς worst round)',
    point_diff INT DEFAULT 0 COMMENT 'Διαφορά πόντων (scored - conceded)',
    scored_points INT DEFAULT 0 COMMENT 'Πόντοι που σημείωσε',
    conceded_points INT DEFAULT 0 COMMENT 'Πόντοι που δέχθηκε',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (championship_id) REFERENCES petanque_championships(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES petanque_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES petanque_teams(id) ON DELETE CASCADE,
    UNIQUE KEY unique_champ_cat_team (championship_id, category_id, team_id),
    KEY idx_standings_position (championship_id, category_id, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- KNOCKOUT SYSTEM (Προημιτελικά, Ημιτελικά, Τελικά)
-- =====================================================================

-- Knockout Stages: 'quarterfinals', 'semifinals', 'finals'
CREATE TABLE IF NOT EXISTS petanque_knockout_matches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    championship_id INT NOT NULL,
    category_id INT NOT NULL,
    stage ENUM('quarterfinals', 'semifinals', 'finals') NOT NULL,
    bracket ENUM('main', 'plate') NOT NULL COMMENT 'main=κύριο τουρνουά, plate=κύπελλο φιλίας',
    home_team_id INT NOT NULL,
    away_team_id INT NOT NULL,
    home_score INT,
    away_score INT,
    winner_id INT,
    status ENUM('scheduled', 'ongoing', 'completed') DEFAULT 'scheduled',
    match_date DATETIME,
    venue VARCHAR(200),
    match_number INT COMMENT 'Αύξων αριθμός αγώνα στο stage',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (championship_id) REFERENCES petanque_championships(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES petanque_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (home_team_id) REFERENCES petanque_teams(id) ON DELETE CASCADE,
    FOREIGN KEY (away_team_id) REFERENCES petanque_teams(id) ON DELETE CASCADE,
    FOREIGN KEY (winner_id) REFERENCES petanque_teams(id) ON DELETE SET NULL,
    KEY idx_knockout (championship_id, category_id, stage, bracket)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- ΒΟΗΘΗΤΙΚΟΙ ΠΊΝΑΚΕΣ
-- =====================================================================

-- Λογ αγώνων (audit trail)
CREATE TABLE IF NOT EXISTS petanque_match_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    match_type ENUM('swiss', 'knockout') NOT NULL,
    match_id INT NOT NULL,
    old_score_home INT,
    old_score_away INT,
    new_score_home INT,
    new_score_away INT,
    changed_by VARCHAR(100),
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- INDEXES για Performance
-- =====================================================================

CREATE INDEX idx_teams_championship ON petanque_teams(championship_id);
CREATE INDEX idx_swiss_matches_championship ON petanque_swiss_matches(championship_id);
CREATE INDEX idx_standings_championship ON petanque_standings(championship_id);
CREATE INDEX idx_knockout_championship ON petanque_knockout_matches(championship_id);

-- =====================================================================
-- INSERT DEFAULT CATEGORIES
-- =====================================================================

INSERT INTO petanque_categories (code, name, gender, team_size) VALUES
('3m', '3vs3 Άνδρες', 'M', 3),
('3f', '3vs3 Γυναίκες', 'F', 3),
('2m', '2vs2 Άνδρες', 'M', 2),
('2f', '2vs2 Γυναίκες', 'F', 2),
('2mix', '2vs2 Mix', 'MIX', 2)
ON DUPLICATE KEY UPDATE name=VALUES(name);
