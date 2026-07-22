<?php
/**
 * Swiss System Class
 * Υλοποίηση του Ελβετικού Συστήματος (5 γύροι)
 * Κριτήρια τάι-μπρέικ: Buchholz-Fine Buchholz, Διαφορά πόντων
 */

declare(strict_types=1);

class SwissSystem
{
    private TournamentDB $db;
    private \PDO $pdo;
    private array $tables;
    private int $swissRounds = 5;

    public function __construct(TournamentDB $db, \PDO $pdo, array $tables)
    {
        $this->db = $db;
        $this->pdo = $pdo;
        $this->tables = $tables;
    }

    /**
     * Δημιουργία αγώνων για ένα γύρο
     */
    public function generateRound(int $championshipId, int $categoryId, int $roundNumber): bool
    {
        $teams = $this->db->getChampionshipTeams($championshipId, $categoryId);
        if (count($teams) < 2) {
            throw new \RuntimeException('Δεν υπάρχουν αρκετές ομάδες');
        }

        // Ταξινόμηση για κληρωτέα
        if ($roundNumber === 1) {
            // Γύρος 1: Τυχαία κληρωτέα
            shuffle($teams);
        } else {
            // Γύροι 2-5: Ταξινόμηση σύμφωνα με κατάταξη
            $standings = $this->db->getStandings($championshipId, $categoryId);
            $teamOrder = [];
            foreach ($standings as $standing) {
                foreach ($teams as $team) {
                    if ($team['id'] == $standing['team_id']) {
                        $teamOrder[] = $team;
                        break;
                    }
                }
            }
            $teams = $teamOrder;
        }

        // Δημιουργία γύρου
        $stmt = $this->pdo->prepare("
            INSERT INTO `{$this->tables['swiss_rounds']}`
            (championship_id, category_id, round_number, status)
            VALUES (?, ?, ?, 'scheduled')
        ");
        $stmt->execute([$championshipId, $categoryId, $roundNumber]);
        $roundId = (int)$this->pdo->lastInsertId();

        // Δημιουργία αγώνων
        $matches = $this->pairTeams($teams);
        foreach ($matches as $match) {
            $this->db->createSwissMatch([
                'championship_id' => $championshipId,
                'category_id' => $categoryId,
                'round_id' => $roundId,
                'home_team_id' => $match['home_team_id'],
                'away_team_id' => $match['away_team_id'],
            ]);
        }

        return true;
    }

    /**
     * Ζευγάρωμα ομάδων (round-robin)
     */
    private function pairTeams(array $teams): array
    {
        $matches = [];
        $count = count($teams);

        // Αν μονός αριθμός, προστίθεται bye
        if ($count % 2 === 1) {
            $teams[] = null;
            $count++;
        }

        for ($i = 0; $i < $count / 2; $i++) {
            $homeTeam = $teams[$i];
            $awayTeam = $teams[$count - 1 - $i];

            if ($homeTeam && $awayTeam) {
                $matches[] = [
                    'home_team_id' => $homeTeam['id'],
                    'away_team_id' => $awayTeam['id'],
                ];
            }
        }

        return $matches;
    }

    /**
     * Ενημέρωση κατάταξης μετά το τέλος ενός γύρου
     */
    public function updateStandings(int $championshipId, int $categoryId): bool
    {
        $teams = $this->db->getChampionshipTeams($championshipId, $categoryId);

        foreach ($teams as $team) {
            $stats = $this->calculateTeamStats($team['id'], $championshipId, $categoryId);
            $this->db->updateStanding($team['id'], $championshipId, $categoryId, $stats);
        }

        // Αναθέτηση θέσεων
        $this->rankTeams($championshipId, $categoryId);

        return true;
    }

    /**
     * Υπολογισμός στατιστικών ομάδας
     */
    private function calculateTeamStats(int $teamId, int $championshipId, int $categoryId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                m.*,
                CASE 
                    WHEN m.home_team_id = ? AND m.home_score > m.away_score THEN 1
                    WHEN m.away_team_id = ? AND m.away_score > m.home_score THEN 1
                    ELSE 0
                END as is_win,
                CASE 
                    WHEN m.home_team_id = ? THEN m.home_score
                    WHEN m.away_team_id = ? THEN m.away_score
                    ELSE 0
                END as scored,
                CASE 
                    WHEN m.home_team_id = ? THEN m.away_score
                    WHEN m.away_team_id = ? THEN m.home_score
                    ELSE 0
                END as conceded
            FROM `{$this->tables['swiss_matches']}` m
            WHERE m.championship_id = ? AND m.category_id = ? AND m.status = 'completed'
            AND (m.home_team_id = ? OR m.away_team_id = ?)
        ");
        
        $stmt->execute([
            $teamId, $teamId, $teamId, $teamId, $teamId, $teamId,
            $championshipId, $categoryId, $teamId, $teamId
        ]);
        
        $matches = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $wins = 0;
        $scoredPoints = 0;
        $concededPoints = 0;
        $opponentPoints = [];

        foreach ($matches as $match) {
            $wins += (int)$match['is_win'];
            $scoredPoints += (int)$match['scored'];
            $concededPoints += (int)$match['conceded'];

            // Πόντοι αντιπάλου για Buchholz
            $opponentId = $match['home_team_id'] == $teamId ? $match['away_team_id'] : $match['home_team_id'];
            $opponentStats = $this->getOpponentPoints($opponentId, $championshipId, $categoryId);
            $opponentPoints[] = $opponentStats;
        }

        // Buchholz: άθροισμα πόντων αντιπάλων
        $buchholz = array_sum($opponentPoints);

        // Fine Buchholz: Buchholz χωρίς τη χειρότερη νίκη αντιπάλου
        $fineBuchholz = $buchholz;
        if (!empty($opponentPoints)) {
            $fineBuchholz -= min($opponentPoints);
        }

        return [
            'wins' => $wins,
            'buchholz' => $buchholz,
            'fine_buchholz' => $fineBuchholz,
            'point_diff' => $scoredPoints - $concededPoints,
            'scored_points' => $scoredPoints,
            'conceded_points' => $concededPoints,
        ];
    }

    /**
     * Πόντοι αντιπάλου (wins * 3)
     */
    private function getOpponentPoints(int $teamId, int $championshipId, int $categoryId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as wins FROM `{$this->tables['swiss_matches']}`
            WHERE championship_id = ? AND category_id = ? AND status = 'completed'
            AND (
                (home_team_id = ? AND home_score > away_score) OR
                (away_team_id = ? AND away_score > home_score)
            )
        ");
        $stmt->execute([$championshipId, $categoryId, $teamId, $teamId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return ((int)$result['wins']) * 3;
    }

    /**
     * Κατάταξη ομάδων σύμφωνα με κριτήρια τάι-μπρέικ
     */
    private function rankTeams(int $championshipId, int $categoryId): bool
    {
        $standings = $this->db->getStandings($championshipId, $categoryId);

        // Ταξινόμηση: Πόντοι → Fine Buchholz → Buchholz → Διαφορά πόντων
        usort($standings, function ($a, $b) {
            if ($a['points'] != $b['points']) {
                return $b['points'] - $a['points'];
            }
            if ($a['fine_buchholz'] != $b['fine_buchholz']) {
                return $b['fine_buchholz'] <=> $a['fine_buchholz'];
            }
            if ($a['buchholz'] != $b['buchholz']) {
                return $b['buchholz'] <=> $a['buchholz'];
            }
            return $b['point_diff'] - $a['point_diff'];
        });

        // Ενημέρωση θέσεων
        foreach ($standings as $index => $standing) {
            $stmt = $this->pdo->prepare("
                UPDATE `{$this->tables['standings']}`
                SET position = ?
                WHERE id = ?
            ");
            $stmt->execute([$index + 1, $standing['id']]);
        }

        return true;
    }

    /**
     * Ολοκλήρωση Ελβετικού Συστήματος (5 γύροι)
     */
    public function completeSwissRounds(int $championshipId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE `{$this->tables['championships']}`
            SET status = 'knockout'
            WHERE id = ?
        ");
        return $stmt->execute([$championshipId]);
    }
}
