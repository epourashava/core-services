<?php

namespace Core\Traits;

use Core\Models\Municipality;
use Core\Services\Tenant;

trait HasTenant
{
    /**
     * Boot the trait.
     */
    public static function bootHasTenant()
    {
        static::creating(function ($model) {
            if (
                empty($model->subdomain) &&
                !empty(Tenant::getSubDomain())
            ) {
                $model->subdomain = Tenant::getSubDomain();
            }
        });
    }

    /**
     * Get the tenant.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(
            Municipality::class,
            Tenant::columnName,
            Tenant::columnName
        );
    }

    /**
     * Get the tenant id.
     * 
     * @return int
     */
    public function getTenantIdAttribute()
    {
        return $this->tenant->id;
    }

    /**
     * Get the tenant
     * 
     * @return Municipality
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * Set the tenant.
     * 
     * @param Municipality $tenant
     * @return Tenant
     */
    public function initTenant()
    {
        if (!Tenant::getTenant() && $this->tenant) {
            Tenant::setSubDomain($this->tenant->subdomain);
        }

        return Tenant::getInstance();
    }
}
