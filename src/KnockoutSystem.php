<?php
/**
 * Knockout System Class
 * Διαχείριση Προημιτελικών, Ημιτελικών, Τελικών
 */

declare(strict_types=1);

class KnockoutSystem
{
    private TournamentDB $db;
    private \PDO $pdo;
    private array $tables;

    public function __construct(TournamentDB $db, \PDO $pdo, array $tables)
    {
        $this->db = $db;
        $this->pdo = $pdo;
        $this->tables = $tables;
    }

    /**
     * Δημιουργία Προημιτελικών (Quarterfinals)
     * Άνδρες: Top 16 (main) + 17-32 (plate)
     * Γυναίκες: Top 8 (main) + 9-16 (plate)
     */
    public function generateQuarterfinals(int $championshipId, int $categoryId): bool
    {
        $standings = $this->db->getStandings($championshipId, $categoryId);
        
        // Προσδιορισμός κατηγορίας φύλου
        $stmt = $this->pdo->prepare("
            SELECT c.gender FROM `{$this->tables['categories']}` c WHERE c.id = ?
        ");
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch(\PDO::FETCH_ASSOC);
        $gender = $category['gender'] ?? 'M';

        // Κατώφλια
        $mainThreshold = ($gender === 'F') ? 8 : 16;
        $plateStart = $mainThreshold + 1;
        $plateEnd = $plateStart + ($mainThreshold - 1);

        // Κύριο τουρνουά (Main)
        $mainTeams = array_slice($standings, 0, $mainThreshold);
        $this->createQuarterfinalsMatches($championshipId, $categoryId, $mainTeams, 'main');

        // Κύπελλο Φιλίας (Plate)
        $plateTeams = array_slice($standings, $mainThreshold, $mainThreshold);
        $this->createQuarterfinalsMatches($championshipId, $categoryId, $plateTeams, 'plate');

        return true;
    }

    /**
     * Δημιουργία αγώνων Προημιτελικών
     */
    private function createQuarterfinalsMatches(int $championshipId, int $categoryId, array $teams, string $bracket): void
    {
        $count = count($teams);
        $pairs = [];

        // Ζευγάρωμα: 1-last, 2-(last-1), κλπ
        for ($i = 0; $i < $count / 2; $i++) {
            $pairs[] = [
                'home' => $teams[$i]['team_id'],
                'away' => $teams[$count - 1 - $i]['team_id'],
                'matchNumber' => $i + 1,
            ];
        }

        foreach ($pairs as $pair) {
            $this->db->createKnockoutMatch([
                'championship_id' => $championshipId,
                'category_id' => $categoryId,
                'stage' => 'quarterfinals',
                'bracket' => $bracket,
                'home_team_id' => $pair['home'],
                'away_team_id' => $pair['away'],
                'match_number' => $pair['matchNumber'],
            ]);
        }
    }

    /**
     * Δημιουργία Ημιτελικών (Semifinals)
     * Παίζουν οι νικητές των Προημιτελικών
     */
    public function generateSemifinals(int $championshipId, int $categoryId, string $bracket = 'main'): bool
    {
        $quarterfinals = $this->db->getKnockoutMatches($championshipId, $categoryId, 'quarterfinals', $bracket);

        $winners = [];
        foreach ($quarterfinals as $match) {
            if ($match['winner_id']) {
                $winners[] = $match['winner_id'];
            }
        }

        if (count($winners) < 2) {
            throw new \RuntimeException('Δεν υπάρχουν αρκετοί νικητές Προημιτελικών');
        }

        // Ημιτελικά: 1-4, 2-3
        for ($i = 0; $i < count($winners) / 2; $i++) {
            $this->db->createKnockoutMatch([
                'championship_id' => $championshipId,
                'category_id' => $categoryId,
                'stage' => 'semifinals',
                'bracket' => $bracket,
                'home_team_id' => $winners[$i],
                'away_team_id' => $winners[count($winners) - 1 - $i],
                'match_number' => $i + 1,
            ]);
        }

        return true;
    }

    /**
     * Δημιουργία Τελικού (Final)
     */
    public function generateFinal(int $championshipId, int $categoryId, string $bracket = 'main'): bool
    {
        $semifinals = $this->db->getKnockoutMatches($championshipId, $categoryId, 'semifinals', $bracket);

        $winners = [];
        foreach ($semifinals as $match) {
            if ($match['winner_id']) {
                $winners[] = $match['winner_id'];
            }
        }

        if (count($winners) !== 2) {
            throw new \RuntimeException('Πρέπει να υπάρχουν ακριβώς 2 νικητές Ημιτελικών');
        }

        $this->db->createKnockoutMatch([
            'championship_id' => $championshipId,
            'category_id' => $categoryId,
            'stage' => 'finals',
            'bracket' => $bracket,
            'home_team_id' => $winners[0],
            'away_team_id' => $winners[1],
            'match_number' => 1,
        ]);

        return true;
    }

    /**
     * Ενημέρωση νικητή αγώνα knockout
     */
    public function updateKnockoutResult(int $matchId, int $homeScore, int $awayScore): bool
    {
        $winnerId = ($homeScore > $awayScore) ? null : null;
        
        // Θα βρεθεί από τα scores
        $stmt = $this->pdo->prepare("
            SELECT home_team_id, away_team_id FROM `{$this->tables['knockout_matches']}` WHERE id = ?
        ");
        $stmt->execute([$matchId]);
        $match = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $winnerId = ($homeScore > $awayScore) ? $match['home_team_id'] : $match['away_team_id'];

        $stmt = $this->pdo->prepare("
            UPDATE `{$this->tables['knockout_matches']}`
            SET home_score = ?, away_score = ?, winner_id = ?, status = 'completed', updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$homeScore, $awayScore, $winnerId, $matchId]);
    }
}
