<?php
// Lightweight auth helpers (scaffold)
// Integrate into existing app flow and replace with production-grade OAuth2/OpenID Connect provider.

require_once __DIR__ . '/../config.php';

function jwt_secret() {
    // Prefer environment variable, then config constant. Keep a safe default for local dev.
    $env = getenv('JWT_SECRET');
    if ($env !== false && strlen($env) > 0) return $env;
    if (defined('JWT_SECRET') && JWT_SECRET !== 'change-me-please') return JWT_SECRET;
    return 'change-me-please';
}

function generate_jwt($payload, $exp = 3600) {
    // Minimal JWT creation — use a maintained library in production (eg. firebase/php-jwt)
    $payload['exp'] = time() + $exp;
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
    $body = json_encode($payload);

    $h = base64url_encode($header);
    $b = base64url_encode($body);
    $sig = hash_hmac('sha256', "$h.$b", jwt_secret(), true);
    $s = base64url_encode($sig);
    return "$h.$b.$s";
}

function verify_jwt($token) {
    // Minimal verification — use a robust library in production
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    list($header, $body, $sig) = $parts;

    $expected_raw = hash_hmac('sha256', "$header.$body", jwt_secret(), true);
    if (!hash_equals($expected_raw, base64url_decode($sig))) return false;

    $payload_json = base64url_decode($body);
    $payload = json_decode($payload_json, true);
    if (!$payload) return false;
    if (isset($payload['exp']) && time() > $payload['exp']) return false;
    return $payload;
}

// Helpers: base64url encode/decode without padding
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

function require_auth() {
    $h = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if(strpos($h,'Bearer ') === 0) {
        $token = substr($h,7);
        $payload = verify_jwt($token);
        if($payload) {
            return $payload; // array with user info
        }
    }
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

?>
