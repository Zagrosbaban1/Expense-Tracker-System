<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

if (!defined('APP_SECURITY_HELPERS')) {
    define('APP_SECURITY_HELPERS', true);

    function e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    function csrf_token()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    function csrf_input()
    {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }

    function verify_csrf_or_die()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }
    }

    function require_login()
    {
        if (empty($_SESSION['detsuid'])) {
            header('Location: logout.php');
            exit;
        }
    }

    function normalize_date($value)
    {
        $dt = DateTime::createFromFormat('Y-m-d', (string)$value);
        if (!$dt || $dt->format('Y-m-d') !== (string)$value) {
            return null;
        }
        return $dt->format('Y-m-d');
    }

    function normalize_currency($value)
    {
        $value = strtoupper(trim((string)$value));
        $value = preg_replace('/[^A-Z]/', '', $value);
        if ($value === '' || strlen($value) > 10) {
            return 'USD';
        }
        return $value;
    }

    function verify_password_legacy_aware($plainPassword, $storedHash)
    {
        if (!is_string($storedHash) || $storedHash === '') {
            return false;
        }

        if (preg_match('/^[a-f0-9]{32}$/i', $storedHash)) {
            return hash_equals(strtolower($storedHash), md5((string)$plainPassword));
        }

        return password_verify((string)$plainPassword, $storedHash);
    }
}
