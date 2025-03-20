<?php

namespace Core\Models;

use App\Models\Scopes\UserScope;
use Core\Services\Tenant;
use Core\Traits\CleanStorage;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[ScopedBy([UserScope::class])]
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use CleanStorage;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'image',
        'address',
        'designation',
        // Auth
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
        'expires_at',
        'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function (User $model) {
            $tenant = Tenant::getTenant();
            if ($tenant) {
                $tenant->users()->attach($model->id);
            }
        });
    }

    /**
     * Get the user's avatar.
     *
     * @param string $value
     * @return string
     */
    public function getAvatarAttribute($value): string
    {
        return $value ?? 'https://www.gravatar.com/avatar/' . md5($this->email) . '?d=mp';
    }

    /**
     * Save the user's token.
     *
     * @param array $data - The token data.
     * @return void
     */
    public function saveToken($data)
    {
        $this->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at' => now()->addSeconds($data['expires_in']),
        ]);
    }

    /**
     * Check if the user's token is expired.
     *
     * @return bool
     */
    public function tokenExpired($isExpiring = false): bool
    {
        if ($isExpiring) {
            return now()->gte($this->expires_at->subMinutes(5));
        }

        return now()->gte($this->expires_at);
    }

    /**
     * Check if the user has a token.
     *
     * @return bool
     */
    public function hasToken(): bool
    {
        return $this->access_token !== null;
    }

    /**
     * Clear the user's token.
     *
     * @return void
     */
    public function clearToken()
    {
        $this->update([
            'access_token' => null,
            'refresh_token' => null,
            'expires_at' => null,
        ]);
    }

    /**
     * Scope a query to only include users without tenant.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutTenant($query)
    {
        $query->withoutGlobalScope(UserScope::class);
    }

    /**
     * Get the municipalities that owns the user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMunicipalitiesAttribute()
    {
        return Municipality::query()
            ->when(
                !$this->isSuperAdmin(),
                fn(Builder $query) => $query->whereHas(
                    'users',
                    fn(Builder $nested) => $nested->withoutTenant()
                )
            )
            ->get();
    }

    /**
     * Get the tenant that owns the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tenants()
    {
        return $this->belongsToMany(
            Municipality::class,
            Municipality::UserPivotTable,
            'user_id',
            'municipality_id'
        );
    }

    /**
     * Check if the user is super admin.
     * 
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return in_array(
            $this->email,
            config('auth.super_admin_emails', [])
        );
    }

    /**
     * Set the cleanable fields.
     * 
     * @return array
     */
    public function columnsForStorageItems(): array
    {
        return ['image'];
    }
}
