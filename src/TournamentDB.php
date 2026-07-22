<?php
/**
 * Tournament Database Class
 * Χειρίζεται όλες τις database operations για τα τουρνουά
 */

declare(strict_types=1);

class TournamentDB
{
    private \PDO $pdo;
    private array $tables;

    public function __construct(\PDO $pdo, array $tables)
    {
        $this->pdo = $pdo;
        $this->tables = $tables;
    }

    // =====================================================================
    // CHAMPIONSHIPS
    // =====================================================================

    /**
     * Δημιουργία νέου πρωταθλήματος
     */
    public function createChampionship(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO `{$this->tables['championships']}`
            (code, name, year, status, start_date, end_date, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['code'],
            $data['name'],
            $data['year'] ?? date('Y'),
            $data['status'] ?? 'draft',
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['description'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Ανάκτηση πρωταθλήματος
     */
    public function getChampionship(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->tables['championships']}` WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Ενημέρωση πρωταθλήματος
     */
    public function updateChampionship(int $id, array $data): bool
    {
        $updates = [];
        $values = [];
        foreach ($data as $key => $value) {
            $updates[] = "`$key` = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE `{$this->tables['championships']}` SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Λίστα όλων των πρωταθλημάτων
     */
    public function getAllChampionships(string $status = ''): array
    {
        $sql = "SELECT * FROM `{$this->tables['championships']}`";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY year DESC, created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // =====================================================================
    // CHAMPIONSHIP CATEGORIES
    // =====================================================================

    /**
     * Προσθήκη κατηγορίας σε πρωτάθλημα
     */
    public function addCategoryToChampionship(int $championshipId, int $categoryId, int $maxTeams = 0): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO `{$this->tables['championship_categories']}`
            (championship_id, category_id, max_teams)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$championshipId, $categoryId, $maxTeams]);
    }

    /**
     * Κατηγορίες ενός πρωταθλήματος
     */
    public function getChampionshipCategories(int $championshipId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT cc.*, c.code, c.name, c.gender, c.team_size
            FROM `{$this->tables['championship_categories']}` cc
            JOIN `{$this->tables['categories']}` c ON cc.category_id = c.id
            WHERE cc.championship_id = ?
            ORDER BY c.team_size DESC, c.gender
        ");
        $stmt->execute([$championshipId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // =====================================================================
    // TEAMS
    // =====================================================================

    /**
     * Δημιουργία ομάδας
     */
    public function createTeam(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO `{$this->tables['tournament_teams']}`
            (championship_id, category_id, club_id, name, players, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['championship_id'],
            $data['category_id'],
            $data['club_id'] ?? null,
            $data['name'],
            is_array($data['players']) ? json_encode($data['players']) : $data['players'],
            $data['status'] ?? 'registered',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Ανάκτηση ομάδας
     */
    public function getTeam(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->tables['tournament_teams']}` WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result && !empty($result['players'])) {
            $result['players'] = json_decode($result['players'], true) ?? explode(',', $result['players']);
        }
        return $result ?: null;
    }

    /**
     * Ομάδες πρωταθλήματος
     */
    public function getChampionshipTeams(int $championshipId, ?int $categoryId = null): array
    {
        $sql = "SELECT * FROM `{$this->tables['tournament_teams']}` WHERE championship_id = ?";
        $params = [$championshipId];
        if ($categoryId) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
        }
        $sql .= " ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $teams = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($teams as &$team) {
            if (!empty($team['players'])) {
                $team['players'] = json_decode($team['players'], true) ?? explode(',', $team['players']);
            }
        }
        return $teams;
    }

    // =====================================================================
    // SWISS SYSTEM - MATCHES & STANDINGS
    // =====================================================================

    /**
     * Δημιουργία αγώνα Ελβετικού Συστήματος
     */
    public function createSwissMatch(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO `{$this->tables['swiss_matches']}`
            (championship_id, category_id, round_id, home_team_id, away_team_id, status, match_date, venue)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['championship_id'],
            $data['category_id'],
            $data['round_id'],
            $data['home_team_id'],
            $data['away_team_id'],
            $data['status'] ?? 'scheduled',
            $data['match_date'] ?? null,
            $data['venue'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Ενημέρωση αποτελέσματος αγώνα
     */
    public function updateMatchResult(int $matchId, int $homeScore, int $awayScore, string $matchType = 'swiss'): bool
    {
        $table = $matchType === 'swiss' ? $this->tables['swiss_matches'] : $this->tables['knockout_matches'];
        $winnerId = $homeScore > $awayScore ? null : null; // Θα ενημερωθεί στη λογική
        
        $stmt = $this->pdo->prepare("
            UPDATE `{$table}`
            SET home_score = ?, away_score = ?, status = 'completed', updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$homeScore, $awayScore, $matchId]);
    }

    /**
     * Ανάκτηση αγώνων γύρου
     */
    public function getRoundMatches(int $roundId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT m.*, 
                   ht.name as home_team_name, 
                   at.name as away_team_name
            FROM `{$this->tables['swiss_matches']}` m
            LEFT JOIN `{$this->tables['tournament_teams']}` ht ON m.home_team_id = ht.id
            LEFT JOIN `{$this->tables['tournament_teams']}` at ON m.away_team_id = at.id
            WHERE m.round_id = ?
            ORDER BY m.match_date, m.id
        ");
        $stmt->execute([$roundId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Κατάταξη κατηγορίας
     */
    public function getStandings(int $championshipId, int $categoryId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT s.*, t.name as team_name, c.name as club_name
            FROM `{$this->tables['standings']}` s
            LEFT JOIN `{$this->tables['tournament_teams']}` t ON s.team_id = t.id
            LEFT JOIN `{$this->tables['clubs']}` c ON t.club_id = c.id
            WHERE s.championship_id = ? AND s.category_id = ?
            ORDER BY s.position ASC
        ");
        $stmt->execute([$championshipId, $categoryId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Ενημέρωση κατάταξης
     */
    public function updateStanding(int $teamId, int $championshipId, int $categoryId, array $stats): bool
    {
        // Υπολογισμός points
        $points = $stats['wins'] * 3;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO `{$this->tables['standings']}`
            (championship_id, category_id, team_id, wins, points, buchholz, fine_buchholz, point_diff, scored_points, conceded_points)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                wins = ?, points = ?, buchholz = ?, fine_buchholz = ?, point_diff = ?, 
                scored_points = ?, conceded_points = ?, updated_at = NOW()
        ");
        return $stmt->execute([
            $championshipId, $categoryId, $teamId,
            $stats['wins'], $points,
            $stats['buchholz'] ?? 0, $stats['fine_buchholz'] ?? 0,
            $stats['point_diff'] ?? 0, $stats['scored_points'] ?? 0, $stats['conceded_points'] ?? 0,
            // Duplicate key values
            $stats['wins'], $points,
            $stats['buchholz'] ?? 0, $stats['fine_buchholz'] ?? 0,
            $stats['point_diff'] ?? 0, $stats['scored_points'] ?? 0, $stats['conceded_points'] ?? 0,
        ]);
    }

    // =====================================================================
    // KNOCKOUT SYSTEM
    // =====================================================================

    /**
     * Δημιουργία αγώνα knockout
     */
    public function createKnockoutMatch(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO `{$this->tables['knockout_matches']}`
            (championship_id, category_id, stage, bracket, home_team_id, away_team_id, status, match_date, venue, match_number)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['championship_id'],
            $data['category_id'],
            $data['stage'],
            $data['bracket'] ?? 'main',
            $data['home_team_id'],
            $data['away_team_id'],
            $data['status'] ?? 'scheduled',
            $data['match_date'] ?? null,
            $data['venue'] ?? null,
            $data['match_number'] ?? 1,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Αγώνες knockout σταδίου
     */
    public function getKnockoutMatches(int $championshipId, int $categoryId, string $stage, string $bracket = 'main'): array
    {
        $stmt = $this->pdo->prepare("
            SELECT k.*, 
                   ht.name as home_team_name, 
                   at.name as away_team_name
            FROM `{$this->tables['knockout_matches']}` k
            LEFT JOIN `{$this->tables['tournament_teams']}` ht ON k.home_team_id = ht.id
            LEFT JOIN `{$this->tables['tournament_teams']}` at ON k.away_team_id = at.id
            WHERE k.championship_id = ? AND k.category_id = ? AND k.stage = ? AND k.bracket = ?
            ORDER BY k.match_number
        ");
        $stmt->execute([$championshipId, $categoryId, $stage, $bracket]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // =====================================================================
    // HELPER METHODS
    // =====================================================================

    /**
     * Καταγραφή αλλαγής αποτελέσματος
     */
    public function logMatchChange(string $type, int $matchId, int $oldHome, int $oldAway, int $newHome, int $newAway, string $changedBy): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO `{$this->tables['match_logs']}`
            (match_type, match_id, old_score_home, old_score_away, new_score_home, new_score_away, changed_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$type, $matchId, $oldHome, $oldAway, $newHome, $newAway, $changedBy]);
    }

    /**
     * Αρίθμηση θέσεων κατάταξης
     */
    public function updatePositions(int $championshipId, int $categoryId): bool
    {
        $standings = $this->getStandings($championshipId, $categoryId);
        foreach ($standings as $index => $standing) {
            $position = $index + 1;
            $stmt = $this->pdo->prepare("
                UPDATE `{$this->tables['standings']}`
                SET position = ?
                WHERE id = ?
            ");
            $stmt->execute([$position, $standing['id']]);
        }
        return true;
    }
}
