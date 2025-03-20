<?php

namespace App\Models\Scopes;

use Core\Services\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $this->withSuperAdmin($builder);
    }

    /**
     * Check if the user is super admin.
     */
    function withSuperAdmin(Builder $builder)
    {
        $builder->where(function ($query) {
            $query->whereHas('tenants', function ($tenantQuery) {
                $tenantQuery->where(
                    Tenant::columnName,
                    Tenant::getSubDomain()
                );
            })->orWhereIn('email', config('auth.super_admin_emails', []));
        });
    }

    /**
     * Simple check
     */
    function withTenant(Builder $builder)
    {
        $builder->whereHas('tenants', function ($query) {
            $query->where(Tenant::columnName, Tenant::getSubDomain());
        });
    }
}
