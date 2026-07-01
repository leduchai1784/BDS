<?php

namespace App\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SafeUuidBelongsTo extends BelongsTo
{
    /**
     * Set the constraints on an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        // Get keys and filter only valid UUIDs to prevent PostgreSQL type mismatch errors
        $keys = $this->getEagerModelKeys($models);
        $validUuidKeys = array_filter($keys, function ($key) {
            return !is_null($key) && Str::isUuid((string)$key);
        });

        if (empty($validUuidKeys)) {
            $validUuidKeys = ['00000000-0000-0000-0000-000000000000'];
        }

        $this->query->whereIn($this->ownerKey, array_values($validUuidKeys));
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        $parentKey = $this->getParentKey();
        if (is_null($parentKey) || !Str::isUuid((string)$parentKey)) {
            return $this->getDefaultFor($this->parent);
        }

        return $this->query->first() ?: $this->getDefaultFor($this->parent);
    }
}
