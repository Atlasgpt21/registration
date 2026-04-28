<?php
declare(strict_types=1);

function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function flash_set(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

/** @return list<array{type:string,message:string}> */
function flash_take(): array
{
    $out = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $out;
}

function direction_label(?string $dir): string
{
    return match (strtoupper((string)$dir)) {
        'IN'  => 'Εισερχόμενο',
        'OUT' => 'Εξερχόμενο',
        default => (string)$dir,
    };
}

function direction_class(?string $dir): string
{
    return match (strtoupper((string)$dir)) {
        'IN'  => 'badge bg-success',
        'OUT' => 'badge bg-primary',
        default => 'badge bg-secondary',
    };
}

function fmt_datetime(?string $s): string
{
    if ($s === null || $s === '') return '';
    $t = strtotime($s);
    return $t === false ? (string)$s : date('d/m/Y H:i', $t);
}

function fmt_date(?string $s): string
{
    if ($s === null || $s === '') return '';
    $t = strtotime($s);
    return $t === false ? (string)$s : date('d/m/Y', $t);
}

function input(string $key, string $default = ''): string
{
    return trim((string)($_POST[$key] ?? $_GET[$key] ?? $default));
}

function escape_like(string $s): string
{
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $s);
}
