<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HasHashid
{
    /**
     * Obtenir la clé de route pour le modèle.
     * On dit à Laravel d'utiliser 'hashid' pour la liaison implicite des routes.
     * 
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'hashid'; // Pseudo-attribut pour forcer l'usage du mutator ci-dessous
    }

    /**
     * Définir la valeur de la clé de route.
     * C'est cette valeur qui sera utilisée quand on fait route('route.name', $model)
     * 
     * @return mixed
     */
    public function getRouteKey()
    {
        return encode_id($this->getKey());
    }

    /**
     * Résoudre le modèle depuis une route (Implicit Route Binding)
     * 
     * @param  mixed  $value Le hash de l'URL
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (empty($value)) {
            return null;
        }

        $id = decode_id($value);

        if ($id === null) {
            return null;
        }

        return $this->where($this->getKeyName(), $id)->first();
    }
}
