<?php

namespace App\Hashing;

use App\Services\PasswordHashService;
use Illuminate\Contracts\Hashing\Hasher;

class SaltedHasher implements Hasher
{
    /**
     * Get information about the given hashed value.
     */
    public function info($hashedValue): array
    {
        return password_get_info($hashedValue);
    }

    /**
     * Hash the given value.
     * Catatan: make() tanpa salt tidak menyimpan salt ke DB.
     * Gunakan PasswordHashService::make() untuk proses lengkap.
     */
    public function make($value, array $options = []): string
    {
        // Fallback: gunakan bcrypt biasa jika tidak ada salt
        $salt = $options['salt'] ?? '';
        $saltedValue = $value . $salt;
        return password_hash($saltedValue, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Check the given plain value against a hash.
     */
    public function check($value, $hashedValue, array $options = []): bool
    {
        if (empty($hashedValue)) {
            return false;
        }

        $salt = $options['salt'] ?? '';
        $saltedValue = $value . $salt;
        return password_verify($saltedValue, $hashedValue);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     */
    public function needsRehash($hashedValue, array $options = []): bool
    {
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
