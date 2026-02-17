<?php

namespace App\Services;

class PasswordHashService
{
    /**
     * Panjang salt dalam byte → menghasilkan string hex 64 karakter
     */
    private const SALT_LENGTH = 32;

    /**
     * bcrypt cost factor
     */
    private const COST = 12;

    /**
     * Generate salt acak yang kuat secara kriptografis
     */
    public static function generateSalt(): string
    {
        return bin2hex(random_bytes(self::SALT_LENGTH));
    }

    /**
     * Hash password dengan salt
     * Proses: bcrypt( password + salt )
     */
    public static function hash(string $password, string $salt): string
    {
        return password_hash(
            $password . $salt,
            PASSWORD_BCRYPT,
            ['cost' => self::COST]
        );
    }

    /**
     * Verifikasi password plain terhadap hash + salt
     */
    public static function verify(string $password, string $hashedPassword, string $salt): bool
    {
        return password_verify($password . $salt, $hashedPassword);
    }

    /**
     * Generate salt baru + hash password sekaligus
     *
     * @return array{hash: string, salt: string}
     */
    public static function make(string $password): array
    {
        $salt = self::generateSalt();

        return [
            'hash' => self::hash($password, $salt),
            'salt' => $salt,
        ];
    }

    /**
     * Cek apakah hash perlu di-rehash (misal cost factor naik)
     */
    public static function needsRehash(string $hashedPassword): bool
    {
        return password_needs_rehash(
            $hashedPassword,
            PASSWORD_BCRYPT,
            ['cost' => self::COST]
        );
    }
}
