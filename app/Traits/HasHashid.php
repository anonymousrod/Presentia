<?php

namespace App\Traits;

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
     * @param  mixed  $value Le hash ou la valeur de l'URL
     * @param  string|null  $field Le champ spécifié dans la route (ex: slug)
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (empty($value)) {
            return null;
        }

        // Si un champ spécifique est demandé dans la route (ex: {model:slug})
        if ($field && $field !== 'hashid') {
            return $this->where($field, $value)->first();
        }

        $id = decode_id($value);

        if ($id !== null) {
            return $this->where($this->getKeyName(), $id)->first();
        }

        // Si ce n'est pas un hashid valide, tentative par id direct (si numérique)
        if (is_numeric($value)) {
            return $this->where($this->getKeyName(), (int)$value)->first();
        }

        return null;
    }
}
