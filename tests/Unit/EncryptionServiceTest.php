<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EncryptionService;

class EncryptionServiceTest extends TestCase
{
    private EncryptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EncryptionService::class);
    }

    public function test_two_way_encryption_and_decryption(): void
    {
        $plainText = 'SecretPassword123!';

        $encrypted = $this->service->encrypt($plainText);
        $this->assertNotEquals($plainText, $encrypted);

        $decrypted = $this->service->decrypt($encrypted);
        $this->assertEquals($plainText, $decrypted);
    }

    public function test_decryption_with_invalid_payload_returns_null(): void
    {
        $invalidPayload = 'invalid_base64_payload_string';

        $decrypted = $this->service->decrypt($invalidPayload);
        $this->assertNull($decrypted);
    }

    public function test_two_way_encryption_with_custom_key(): void
    {
        $plainText = 'SuperSecretAPIKey2026';
        $customKey = 'MyCustomSecretKey32BytesLength!!';

        $encrypted = $this->service->encryptWithKey($plainText, $customKey);
        $this->assertNotEquals($plainText, $encrypted);

        $decrypted = $this->service->decryptWithKey($encrypted, $customKey);
        $this->assertEquals($plainText, $decrypted);

        // Key salah harus gagal didekripsi (mengembalikan null)
        $wrongKey = 'WrongCustomSecretKey32BytesLength!';
        $decryptedWrong = $this->service->decryptWithKey($encrypted, $wrongKey);
        $this->assertNull($decryptedWrong);
    }

    public function test_one_way_password_hashing_and_verification(): void
    {
        $password = 'UserSecretPass99#';

        $hashed = $this->service->hashPassword($password);
        $this->assertNotEquals($password, $hashed);

        $this->assertTrue($this->service->verifyPassword($password, $hashed));
        $this->assertFalse($this->service->verifyPassword('WrongPassword', $hashed));
    }

    public function test_global_helper_functions(): void
    {
        $data = 'ConfidentialData';

        $encrypted = encrypt_secure($data);
        $this->assertEquals($data, decrypt_secure($encrypted));

        $customKey = 'CustomSecretKeyForHelperFunction!';
        $encryptedCustom = encrypt_secure($data, $customKey);
        $this->assertEquals($data, decrypt_secure($encryptedCustom, $customKey));

        $password = 'MyHelperPassword';
        $hashed = hash_password_secure($password);
        $this->assertTrue(verify_password_secure($password, $hashed));
        $this->assertFalse(verify_password_secure('Wrong', $hashed));
    }
}
