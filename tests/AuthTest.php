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
}
