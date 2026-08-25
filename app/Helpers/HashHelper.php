<?php

use Vinkla\Hashids\Facades\Hashids;

if (!function_exists('encode_id')) {
    /**
     * Encode un ID entier en hash string.
     *
     * @param int|null $id
     * @return string|null
     */
    function encode_id(?int $id): ?string
    {
        if ($id === null) {
            return null;
        }
        return Hashids::encode($id);
    }
}

if (!function_exists('decode_id')) {
    /**
     * Décode un hash string ou un entier en ID entier.
     * Renvoie null si le hash est invalide.
     *
     * @param string|int|null $hash
     * @return int|null
     */
    function decode_id($hash): ?int
    {
        if (empty($hash)) {
            return null;
        }

        if (is_numeric($hash)) {
            return (int) $hash;
        }

        $decoded = Hashids::decode((string) $hash);

        return !empty($decoded) ? $decoded[0] : null;
    }
}
