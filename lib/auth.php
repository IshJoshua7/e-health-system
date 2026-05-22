<?php
declare(strict_types=1);
// JWT helpers using firebase/php-jwt when available.
// Falls back to a safe default secret lookup for environments without Composer.

require_once __DIR__ . '/../config.php';

// Composer autoload must be present in production. Fail fast if missing.
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    throw new \RuntimeException('Dependencies are not installed. Run "composer install" before using auth helpers.');
}
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function jwt_secret() {
    $env = getenv('JWT_SECRET');
    if ($env !== false && strlen($env) > 0) return $env;
    if (defined('JWT_SECRET') && JWT_SECRET !== 'change-me-please') return JWT_SECRET;
    return 'change-me-please';
}

/**
 * Generate a JWT for given payload.
 * Returns a compact JWT string.
 *
 * @param array $payload
 * @param int $exp lifetime in seconds
 * @return string
 */
function generate_jwt(array $payload, int $exp = 3600): string {
    $now = time();
    $claims = array_merge($payload, [
        'iat' => $now,
        'exp' => $now + (int)$exp,
    ]);

    $key = jwt_secret();
    return JWT::encode($claims, $key, 'HS256');
}

/**
 * Verify a JWT and return its payload as associative array, or false on failure.
 *
 * @param string $token
 * @return array|false
 */
function verify_jwt($token) {
    if (!$token) return false;
    try {
        $decoded = JWT::decode($token, new Key(jwt_secret(), 'HS256'));
        return json_decode(json_encode($decoded), true);
    } catch (\Throwable $e) {
        return false;
    }
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $data .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

/**
 * Parse the Authorization header (or provided header) and return JWT payload or false.
 * Useful for testing without triggering exits.
 *
 * @param string|null $authHeader
 * @return array|false
 */
function get_bearer_payload(?string $authHeader = null) {
    $h = $authHeader ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
    if (stripos($h, 'Bearer ') === 0) {
        $token = substr($h, 7);
        $payload = verify_jwt($token);
        if ($payload) {
            return $payload;
        }
    }
    return false;
}

/**
 * Require authentication for the current request. On failure this exits with 401.
 * Use `get_bearer_payload()` in tests to avoid process termination.
 */
function require_auth() {
    $payload = get_bearer_payload();
    if ($payload) return $payload;
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

