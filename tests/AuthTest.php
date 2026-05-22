<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';

class AuthTest extends TestCase {
    protected function setUp(): void {
        putenv('JWT_SECRET=testing-secret');
    }

    public function testBase64urlEncodeDecode() {
        $data = json_encode(['a' => 1, 'b' => 'x']);
        $enc = base64url_encode($data);
        $this->assertIsString($enc);
        $dec = base64url_decode($enc);
        $this->assertEquals($data, $dec);
    }

    public function testGenerateAndVerifyJwt() {
        $payload = ['sub' => 'user1'];
        $token = generate_jwt($payload, 60);
        $this->assertIsString($token);
        $this->assertEquals(2, substr_count($token, '.'));
        $decoded = verify_jwt($token);
        $this->assertIsArray($decoded);
        $this->assertEquals('user1', $decoded['sub']);
        $this->assertArrayHasKey('exp', $decoded);
    }

    public function testExpiredToken() {
        $payload = ['sub' => 'user2'];
        // Create token already expired
        $token = generate_jwt($payload, -10);
        $this->assertFalse(verify_jwt($token));
    }

    public function testInvalidSignature() {
        $payload = ['sub' => 'user3'];
        $token = generate_jwt($payload, 60);
        // Corrupt the token signature
        $parts = explode('.', $token);
        $parts[2] = strrev($parts[2]);
        $bad = implode('.', $parts);
        $this->assertFalse(verify_jwt($bad));
    }

    public function testGetBearerPayload() {
        $payload = ['sub' => 'user4'];
        $token = generate_jwt($payload, 60);
        $header = 'Bearer ' . $token;
        $out = get_bearer_payload($header);
        $this->assertIsArray($out);
        $this->assertEquals('user4', $out['sub']);
        $this->assertFalse(get_bearer_payload('Bearer invalid.token.value'));
    }
}
