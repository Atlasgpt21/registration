<?php
declare(strict_types=1);

/** @return list<array> */
function categories_all(PDO $db, bool $onlyActive = false): array
{
    $sql = 'SELECT * FROM categories';
    if ($onlyActive) {
        $sql .= ' WHERE active = 1';
    }
    $sql .= ' ORDER BY name';
    return $db->query($sql)->fetchAll();
}

function category_find(PDO $db, int $id): ?array
{
    $stmt = $db->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function category_create(PDO $db, string $name, ?string $description): int
{
    $stmt = $db->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
    $stmt->execute([$name, $description]);
    return (int)$db->lastInsertId();
}

function category_update(PDO $db, int $id, string $name, ?string $description, int $active): void
{
    $stmt = $db->prepare('UPDATE categories SET name = ?, description = ?, active = ? WHERE id = ?');
    $stmt->execute([$name, $description, $active, $id]);
}

function category_delete(PDO $db, int $id): void
{
    $stmt = $db->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$id]);
}

function category_count_protocols(PDO $db, int $id): int
{
    $stmt = $db->prepare('SELECT COUNT(*) FROM protocols WHERE category_id = ?');
    $stmt->execute([$id]);
    return (int)$stmt->fetchColumn();
}
