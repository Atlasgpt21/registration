<?php
declare(strict_types=1);

function protocol_next_number(PDO $db, string $format): string
{
    $year = (int)date('Y');

    $stmt = $db->prepare(
        'INSERT INTO protocol_sequence (seq_year, last_number) VALUES (?, 1)
         ON DUPLICATE KEY UPDATE last_number = last_number + 1'
    );
    $stmt->execute([$year]);

    $stmt = $db->prepare('SELECT last_number FROM protocol_sequence WHERE seq_year = ?');
    $stmt->execute([$year]);
    $seq = (int)$stmt->fetchColumn();

    return str_replace(
        ['{YYYY}', '{YY}', '{SEQ}'],
        [(string)$year, substr((string)$year, 2), (string)$seq],
        $format
    );
}

function protocol_create(PDO $db, array $data): int
{
    $stmt = $db->prepare(
        'INSERT INTO protocols
         (protocol_number, direction, doc_date, subject, sender, recipient, category_id, ref_number, notes)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $data['protocol_number'],
        $data['direction'],
        $data['doc_date'],
        $data['subject'],
        $data['sender'] ?: null,
        $data['recipient'] ?: null,
        $data['category_id'] ?: null,
        $data['ref_number'] ?: null,
        $data['notes'] ?: null,
    ]);
    return (int)$db->lastInsertId();
}

function protocol_update(PDO $db, int $id, array $data): void
{
    $stmt = $db->prepare(
        'UPDATE protocols SET
            direction = ?, doc_date = ?, subject = ?, sender = ?,
            recipient = ?, category_id = ?, ref_number = ?, notes = ?
         WHERE id = ?'
    );
    $stmt->execute([
        $data['direction'],
        $data['doc_date'],
        $data['subject'],
        $data['sender'] ?: null,
        $data['recipient'] ?: null,
        $data['category_id'] ?: null,
        $data['ref_number'] ?: null,
        $data['notes'] ?: null,
        $id,
    ]);
}

function protocol_find(PDO $db, int $id): ?array
{
    $stmt = $db->prepare(
        'SELECT p.*, c.name AS category_name
         FROM protocols p
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.id = ?'
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function protocol_delete(PDO $db, int $id): void
{
    $stmt = $db->prepare('DELETE FROM protocols WHERE id = ?');
    $stmt->execute([$id]);
}

/**
 * @return array{rows:list<array>, total:int}
 */
function protocol_list(PDO $db, array $filters = [], int $page = 1, int $perPage = 20): array
{
    $where  = [];
    $params = [];

    if (!empty($filters['direction'])) {
        $where[]  = 'p.direction = ?';
        $params[] = $filters['direction'];
    }
    if (!empty($filters['category_id'])) {
        $where[]  = 'p.category_id = ?';
        $params[] = (int)$filters['category_id'];
    }
    if (!empty($filters['date_from'])) {
        $where[]  = 'p.doc_date >= ?';
        $params[] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $where[]  = 'p.doc_date <= ?';
        $params[] = $filters['date_to'];
    }
    if (!empty($filters['search'])) {
        $where[]  = '(p.subject LIKE ? OR p.sender LIKE ? OR p.recipient LIKE ? OR p.protocol_number LIKE ? OR p.ref_number LIKE ?)';
        $like     = '%' . $filters['search'] . '%';
        $params   = array_merge($params, [$like, $like, $like, $like, $like]);
    }

    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $countSQL = "SELECT COUNT(*) FROM protocols p {$whereSQL}";
    $stmt     = $db->prepare($countSQL);
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    $offset = ($page - 1) * $perPage;
    $dataSQL = "SELECT p.*, c.name AS category_name
                FROM protocols p
                LEFT JOIN categories c ON c.id = p.category_id
                {$whereSQL}
                ORDER BY p.doc_date DESC, p.id DESC
                LIMIT {$perPage} OFFSET {$offset}";
    $stmt = $db->prepare($dataSQL);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    return ['rows' => $rows, 'total' => $total];
}

/**
 * @return list<array>
 */
function protocol_list_all(PDO $db, array $filters = []): array
{
    $where  = [];
    $params = [];

    if (!empty($filters['direction'])) {
        $where[]  = 'p.direction = ?';
        $params[] = $filters['direction'];
    }
    if (!empty($filters['category_id'])) {
        $where[]  = 'p.category_id = ?';
        $params[] = (int)$filters['category_id'];
    }
    if (!empty($filters['date_from'])) {
        $where[]  = 'p.doc_date >= ?';
        $params[] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $where[]  = 'p.doc_date <= ?';
        $params[] = $filters['date_to'];
    }
    if (!empty($filters['search'])) {
        $where[]  = '(p.subject LIKE ? OR p.sender LIKE ? OR p.recipient LIKE ? OR p.protocol_number LIKE ? OR p.ref_number LIKE ?)';
        $like     = '%' . $filters['search'] . '%';
        $params   = array_merge($params, [$like, $like, $like, $like, $like]);
    }

    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "SELECT p.*, c.name AS category_name
            FROM protocols p
            LEFT JOIN categories c ON c.id = p.category_id
            {$whereSQL}
            ORDER BY p.doc_date DESC, p.id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
