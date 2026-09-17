<?php

use App\Services\EncryptionService;

if (!function_exists('encrypt_secure')) {
    /**
     * Enkripsi data sensitif / password eksternal secara 2-way (AES-256-CBC + HMAC SHA-256).
     * Digunakan jika data perlu dikembalikan lagi ke plaintext di kemudian hari (misal: SMTP password, API Secret, dll).
     *
     * @param string $value Plaintext yang akan di-enkripsi
     * @param string|null $customKey (Opsional) Custom key jika tidak ingin menggunakan APP_KEY default
     * @return string Ciphertext terenkripsi
     */
    function encrypt_secure(string $value, ?string $customKey = null): string
    {
        $service = app(EncryptionService::class);
        if ($customKey !== null) {
            return $service->encryptWithKey($value, $customKey);
        }
        return $service->encrypt($value);
    }
}

if (!function_exists('decrypt_secure')) {
    /**
     * Dekripsi data ciphertext 2-way secara aman.
     *
     * @param string $payload Ciphertext terenkripsi
     * @param string|null $customKey (Opsional) Custom key jika enkripsi menggunakan custom key
     * @return string|null Plaintext asli atau null jika gagal / tampered
     */
    function decrypt_secure(string $payload, ?string $customKey = null): ?string
    {
        $service = app(EncryptionService::class);
        if ($customKey !== null) {
            return $service->decryptWithKey($payload, $customKey);
        }
        return $service->decrypt($payload);
    }
}

if (!function_exists('hash_password_secure')) {
    /**
     * Hash password user login secara 1-way (Argon2id/Bcrypt).
     * WAJIB digunakan untuk password otentikasi user login (TIDAK BISA di-decrypt kembali).
     *
     * @param string $password Password plaintext
     * @return string Hashed password
     */
    function hash_password_secure(string $password): string
    {
        return app(EncryptionService::class)->hashPassword($password);
    }
}

if (!function_exists('verify_password_secure')) {
    /**
     * Verifikasi password plaintext dengan hashed password.
     *
     * @param string $password Password plaintext
     * @param string $hashedPassword Hashed password
     * @return bool True jika cocok
     */
    function verify_password_secure(string $password, string $hashedPassword): bool
    {
        return app(EncryptionService::class)->verifyPassword($password, $hashedPassword);
    }
}
