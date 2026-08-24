<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Log;

class EncryptionService
{
    /**
     * Cipher default untuk enkripsi custom key
     */
    protected string $defaultCipher = 'AES-256-CBC';

    /**
     * Enkripsi string menggunakan Laravel Crypt (AES-256-CBC / AES-128-CBC + HMAC SHA-256 MAC).
     * Metode ini adalah 2-way encryption paling aman untuk data sensitif / kredensial eksternal.
     *
     * @param string $value String yang ingin di-enkripsi
     * @return string Ciphertext yang terenkripsi dan ter-sign (Base64 JSON payload)
     */
    public function encrypt(string $value): string
    {
        return Crypt::encryptString($value);
    }

    /**
     * Dekripsi payload ciphertext kembali ke string asli.
     *
     * @param string $payload Ciphertext terenkripsi
     * @return string|null String asli jika berhasil, null jika payload tidak valid / dimodifikasi
     */
    public function decrypt(string $payload): ?string
    {
        try {
            return Crypt::decryptString($payload);
        } catch (DecryptException $e) {
            Log::warning('EncryptionService: Gagal mendekripsi payload (data tidak valid atau telah dimodifikasi)', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Dekripsi payload ciphertext dan throw exception jika gagal.
     *
     * @param string $payload Ciphertext terenkripsi
     * @return string String asli
     * @throws DecryptException jika payload tidak valid
     */
    public function decryptOrFail(string $payload): string
    {
        return Crypt::decryptString($payload);
    }

    /**
     * Enkripsi string menggunakan custom secret key (bukan APP_KEY dari .env).
     *
     * @param string $value Data/password yang akan di-enkripsi
     * @param string $secretKey Secret key custom (minimal 32 karakter untuk AES-256)
     * @param string|null $cipher Cipher yang digunakan (default: AES-256-CBC)
     * @return string Ciphertext terenkripsi
     */
    public function encryptWithKey(string $value, string $secretKey, ?string $cipher = null): string
    {
        $cipher = $cipher ?? $this->defaultCipher;
        $key = $this->prepareKey($secretKey, $cipher);
        $encrypter = new Encrypter($key, $cipher);

        return $encrypter->encryptString($value);
    }

    /**
     * Dekripsi payload terenkripsi yang menggunakan custom secret key.
     *
     * @param string $payload Ciphertext terenkripsi
     * @param string $secretKey Secret key custom
     * @param string|null $cipher Cipher yang digunakan (default: AES-256-CBC)
     * @return string|null String asli jika berhasil, null jika gagal
     */
    public function decryptWithKey(string $payload, string $secretKey, ?string $cipher = null): ?string
    {
        try {
            $cipher = $cipher ?? $this->defaultCipher;
            $key = $this->prepareKey($secretKey, $cipher);
            $encrypter = new Encrypter($key, $cipher);

            return $encrypter->decryptString($payload);
        } catch (DecryptException $e) {
            Log::warning('EncryptionService: Gagal mendekripsi payload dengan custom key', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Hash password (1-way hashing) menggunakan Argon2id/Bcrypt.
     * CATATAN KEAMANAN: Password akun user LOGIN WAJIB di-hash (satu arah) dan TIDAK BOLEH di-enkripsi 2-way!
     *
     * @param string $password Plaintext password
     * @return string Hashed password
     */
    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * Verifikasi plaintext password terhadap hashed password.
     *
     * @param string $password Plaintext password yang diinput
     * @param string $hashedPassword Hashed password dari database
     * @return bool True jika cocok, false jika salah
     */
    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return Hash::check($password, $hashedPassword);
    }

    /**
     * Memastikan format key sesuai panjang yang dibutuhkan cipher (misal 32 byte untuk AES-256).
     */
    protected function prepareKey(string $secretKey, string $cipher): string
    {
        $keyByteLength = strtolower($cipher) === 'aes-128-cbc' ? 16 : 32;

        if (strlen($secretKey) === $keyByteLength) {
            return $secretKey;
        }

        // Jika key berupa string biasa, derive key menggunakan SHA-256 (32 bytes binary)
        return substr(hash('sha256', $secretKey, true), 0, $keyByteLength);
    }
}
