<?php
declare(strict_types=1);

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = (string)($_POST['_csrf'] ?? '');
    if (!is_string($_POST['_csrf'] ?? '') || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        echo 'Μη έγκυρο CSRF token. Ανανεώστε τη σελίδα και δοκιμάστε ξανά.';
        exit;
    }
}
