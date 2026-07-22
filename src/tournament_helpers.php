<?php
/**
 * Tournament Helper Functions
 */

declare(strict_types=1);

/**
 * HTML escape
 */
function h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirect helper
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Flash messages
 */
function flash_set(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'msg' => $message];
}

function flash_pop(): array
{
    $f = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $f;
}

function render_flash(): string
{
    $out = '';
    foreach (flash_pop() as $f) {
        $cls = match ($f['type']) {
            'success' => 'alert alert-success',
            'error'   => 'alert alert-danger',
            'warning' => 'alert alert-warning',
            default   => 'alert alert-info',
        };
        $out .= '<div class="' . $cls . '" role="alert">' . h($f['msg']) . '</div>';
    }
    return $out;
}

/**
 * CSRF Token
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function csrf_check(): void
{
    $t = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF'] ?? '';
    if (!hash_equals($_SESSION['_csrf'] ?? '', (string)$t)) {
        http_response_code(419);
        exit('CSRF token invalid');
    }
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . h(csrf_token()) . '">';
}

/**
 * Current User
 */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool
{
    return ($_SESSION['user']['role'] ?? null) === 'admin';
}

/**
 * URL Helper
 */
function url(string $path): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $base = dirname($script);
    if (str_contains($script, '/admin/') || str_contains($script, '/club/') || str_contains($script, '/public/')) {
        $base = dirname($base);
    }
    $base = rtrim($base, '/\\');
    if ($base === '' || $base === '\\') {
        $base = '';
    }
    return $base . '/' . ltrim($path, '/');
}

/**
 * JSON Response
 */
function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Format Score
 */
function format_score(int $home, int $away): string
{
    return h($home) . ' - ' . h($away);
}

/**
 * Get Category Label
 */
function get_category_label(string $code): string
{
    return match ($code) {
        '3m' => '3vs3 Άνδρες',
        '3f' => '3vs3 Γυναίκες',
        '2m' => '2vs2 Άνδρες',
        '2f' => '2vs2 Γυναίκες',
        '2mix' => '2vs2 Mix',
        default => $code,
    };
}

/**
 * Get Stage Label
 */
function get_stage_label(string $stage): string
{
    return match ($stage) {
        'quarterfinals' => 'Προημιτελικά',
        'semifinals' => 'Ημιτελικά',
        'finals' => 'Τελικά',
        default => $stage,
    };
}

/**
 * Get Bracket Label
 */
function get_bracket_label(string $bracket): string
{
    return match ($bracket) {
        'main' => 'Κύριο Τουρνουά',
        'plate' => 'Κύπελλο Φιλίας',
        default => $bracket,
    };
}

/**
 * Format Points
 */
function format_points(int $wins): string
{
    return (string)($wins * 3);
}

/**
 * Database Connection
 */
function db_connect(array $cfg): \PDO
{
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $cfg['host'],
        (int)($cfg['port'] ?? 3306),
        $cfg['dbname'],
        $cfg['charset'] ?? 'utf8mb4'
    );

    return new \PDO($dsn, $cfg['user'], $cfg['password'], [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

/**
 * DB Query Helpers
 */
function db_one(\PDO $pdo, string $sql, array $params = []): ?array
{
    $st = $pdo->prepare($sql);
    $st->execute($params);
    $r = $st->fetch(\PDO::FETCH_ASSOC);
    return $r === false ? null : $r;
}

function db_all(\PDO $pdo, string $sql, array $params = []): array
{
    $st = $pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll(\PDO::FETCH_ASSOC);
}

/**
 * Require Admin
 */
function require_admin(): void
{
    if (!is_admin()) {
        flash_set('error', 'Δεν έχετε δικαίωμα πρόσβασης');
        redirect(url('index.php'));
    }
}

/**
 * Require Club
 */
function require_club(): void
{
    $user = current_user();
    if ($user['role'] !== 'club') {
        flash_set('error', 'Δεν έχετε δικαίωμα πρόσβασης');
        redirect(url('index.php'));
    }
}
