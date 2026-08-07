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
     * Décode un hash string en ID entier.
     * Renvoie null si le hash est invalide.
     *
     * @param string|null $hash
     * @return int|null
     */
    function decode_id(?string $hash): ?int
    {
        if (empty($hash)) {
            return null;
        }

        // Hashids::decode renvoie un tableau d'entiers. 
        // Comme on encode toujours un seul ID, on prend le premier élément.
        $decoded = Hashids::decode($hash);

        return !empty($decoded) ? $decoded[0] : null;
    }
}
