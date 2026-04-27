<?php
declare(strict_types=1);

function member_create(PDO $db, array $data): int
{
    $stmt = $db->prepare(
        'INSERT INTO members
         (member_number, last_name, first_name, father_name, mother_name, birth_date,
          id_number, tax_number, amka, member_type, address, city, postal_code,
          phone, mobile, email, occupation, role_in_club, registration_date, status, notes)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        $data['member_number'] ?: null,
        $data['last_name'],
        $data['first_name'],
        $data['father_name'] ?: null,
        $data['mother_name'] ?: null,
        $data['birth_date'] ?: null,
        $data['id_number'] ?: null,
        $data['tax_number'] ?: null,
        $data['amka'] ?: null,
        $data['member_type'] ?: 'MEMBER',
        $data['address'] ?: null,
        $data['city'] ?: null,
        $data['postal_code'] ?: null,
        $data['phone'] ?: null,
        $data['mobile'] ?: null,
        $data['email'] ?: null,
        $data['occupation'] ?: null,
        $data['role_in_club'] ?: null,
        $data['registration_date'] ?: null,
        $data['status'] ?: 'ACTIVE',
        $data['notes'] ?: null,
    ]);
    return (int)$db->lastInsertId();
}

function member_update(PDO $db, int $id, array $data): void
{
    $stmt = $db->prepare(
        'UPDATE members SET
            member_number = ?, last_name = ?, first_name = ?, father_name = ?,
            mother_name = ?, birth_date = ?, id_number = ?, tax_number = ?,
            amka = ?, member_type = ?, address = ?, city = ?, postal_code = ?,
            phone = ?, mobile = ?, email = ?, occupation = ?, role_in_club = ?,
            registration_date = ?, status = ?, notes = ?
         WHERE id = ?'
    );
    $stmt->execute([
        $data['member_number'] ?: null,
        $data['last_name'],
        $data['first_name'],
        $data['father_name'] ?: null,
        $data['mother_name'] ?: null,
        $data['birth_date'] ?: null,
        $data['id_number'] ?: null,
        $data['tax_number'] ?: null,
        $data['amka'] ?: null,
        $data['member_type'] ?: 'MEMBER',
        $data['address'] ?: null,
        $data['city'] ?: null,
        $data['postal_code'] ?: null,
        $data['phone'] ?: null,
        $data['mobile'] ?: null,
        $data['email'] ?: null,
        $data['occupation'] ?: null,
        $data['role_in_club'] ?: null,
        $data['registration_date'] ?: null,
        $data['status'] ?: 'ACTIVE',
        $data['notes'] ?: null,
        $id,
    ]);
}

function member_find(PDO $db, int $id): ?array
{
    $stmt = $db->prepare('SELECT * FROM members WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function member_delete(PDO $db, int $id): void
{
    $stmt = $db->prepare('DELETE FROM members WHERE id = ?');
    $stmt->execute([$id]);
}

function member_type_label(?string $type): string
{
    return match (strtoupper((string)$type)) {
        'MEMBER'  => 'Μέλος',
        'ATHLETE' => 'Μέλος & Αθλητής',
        default   => (string)$type,
    };
}

function member_type_class(?string $type): string
{
    return match (strtoupper((string)$type)) {
        'MEMBER'  => 'badge bg-info',
        'ATHLETE' => 'badge bg-warning text-dark',
        default   => 'badge bg-secondary',
    };
}

function member_status_label(?string $status): string
{
    return match (strtoupper((string)$status)) {
        'ACTIVE'   => 'Ενεργό',
        'INACTIVE' => 'Ανενεργό',
        default    => (string)$status,
    };
}

/**
 * @return array{rows:list<array>, total:int}
 */
function member_list(PDO $db, array $filters = [], int $page = 1, int $perPage = 20): array
{
    $where  = [];
    $params = [];

    if (!empty($filters['status'])) {
        $where[]  = 'm.status = ?';
        $params[] = $filters['status'];
    }
    if (!empty($filters['member_type'])) {
        $where[]  = 'm.member_type = ?';
        $params[] = $filters['member_type'];
    }
    if (!empty($filters['search'])) {
        $where[]  = '(m.last_name LIKE ? OR m.first_name LIKE ? OR m.id_number LIKE ? OR m.tax_number LIKE ? OR m.amka LIKE ? OR m.phone LIKE ? OR m.mobile LIKE ? OR m.email LIKE ? OR m.member_number LIKE ?)';
        $like     = '%' . $filters['search'] . '%';
        $params   = array_merge($params, [$like, $like, $like, $like, $like, $like, $like, $like, $like]);
    }

    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $countSQL = "SELECT COUNT(*) FROM members m {$whereSQL}";
    $stmt     = $db->prepare($countSQL);
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    $offset  = ($page - 1) * $perPage;
    $dataSQL = "SELECT m.* FROM members m {$whereSQL}
                ORDER BY m.last_name, m.first_name
                LIMIT {$perPage} OFFSET {$offset}";
    $stmt = $db->prepare($dataSQL);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    return ['rows' => $rows, 'total' => $total];
}

/**
 * @return list<array>
 */
function member_list_all(PDO $db, array $filters = []): array
{
    $where  = [];
    $params = [];

    if (!empty($filters['status'])) {
        $where[]  = 'm.status = ?';
        $params[] = $filters['status'];
    }
    if (!empty($filters['member_type'])) {
        $where[]  = 'm.member_type = ?';
        $params[] = $filters['member_type'];
    }
    if (!empty($filters['search'])) {
        $where[]  = '(m.last_name LIKE ? OR m.first_name LIKE ? OR m.id_number LIKE ? OR m.tax_number LIKE ? OR m.amka LIKE ? OR m.phone LIKE ? OR m.mobile LIKE ? OR m.email LIKE ? OR m.member_number LIKE ?)';
        $like     = '%' . $filters['search'] . '%';
        $params   = array_merge($params, [$like, $like, $like, $like, $like, $like, $like, $like, $like]);
    }

    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "SELECT m.* FROM members m {$whereSQL} ORDER BY m.last_name, m.first_name";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
