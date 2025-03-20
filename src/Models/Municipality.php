<?php

namespace Core\Models;

use Core\Traits\CleanStorage;
use Core\Traits\HasUserFootprint;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Municipality extends Model
{
    use HasFactory;
    use HasUserFootprint;
    use CleanStorage;

    protected $fillable = [
        'name',
        'name_bn',
        'subdomain',
        'financial_year',
        'division_id',
        'district_id',
        'class',
        'contact_no',
        'contact_email',
        'website',
        'logo',
        'extra',
        'code',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attribute appends with this
     */
    protected $appends = ['app_name'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    const UserPivotTable = 'municipality_user';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'extra' => 'array',
        ];
    }

    /**
     * Get the attributes that can be cleaned.
     * Also used to append the attribute.
     *
     * @return array<string>
     */
    function columnsForStorageItems(): array
    {
        return ['logo'];
    }

    /**
     * Get the user that owns the item
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            Municipality::UserPivotTable,
            'municipality_id',
            'user_id'
        );
    }

    /**
     * Get app name 
     */
    function getAppNameAttribute()
    {
        return $this->appName();
    }

    /**
     * Get the name to set the app name
     * 
     * @return string
     */
    public function appName(): string
    {
        return "{$this->name_bn} পৌরসভা";
    }

    /**
     * Get the logo to set the app logo
     * 
     * @return string
     */
    public function appLogo($path = false): string
    {
        if ($path) {
            return empty($this->logo) ? public_path('/logo.png') : Storage::path($this->getRawOriginal('logo'));
        }

        return asset($this->logo ?: '/logo.png');
    }
}
