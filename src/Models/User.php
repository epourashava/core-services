<?php

namespace Core\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'access_token',
        'refresh_token',
        'expires_at',
        'avatar',
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
}
