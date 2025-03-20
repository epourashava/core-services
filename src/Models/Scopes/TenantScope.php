<?php

namespace Core\Models\Scopes;

use Core\Services\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where(
            $model->qualifyColumn(Tenant::columnName),
            Tenant::getSubDomain()
        );
    }
}
