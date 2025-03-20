<?php

namespace Core\Models;

use Core\Casts\JsonOrString;
use Core\Helpers\DataHelper;
use Core\Models\Scopes\TenantScope;
use Core\Traits\HasTenant;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([TenantScope::class])]
class Setting extends Model
{
    use HasTenant;

    /*
	 * Disabled the timestamps column
	 */
    public $timestamps = false;

    /*
	 * The attributes that are mass assignable.
     *
     * @var array
	 */
    protected $fillable = ['option_name', 'option_value'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'option_value' => JsonOrString::class,
        ];
    }

    /**
     * Get The Cached Settings
     * @param  boolean $regenerate regenerate the cache
     * @return mixed           array|null
     */
    public static function cachedSettings($regenerate = false)
    {
        if ($regenerate) {
            Cache::forget('app_settings');
        }

        return Cache::remember(
            'app_settings',
            60 * 60 * 24,
            function () {
                return Setting::all();
            }
        );
    }

    /**
     * Get Setting data
     * @param string $key The Settings key name
     * @param mixed $fallback Fallback data
     * @param boolean $toArray If json data containes then return as array or not
     * @return mixed array|string
     */
    public static function getValue($key, $fallback = null, $toArray = false)
    {
        $setting = Setting::where('option_name', $key)->first();
        if (!$setting) {
            return $fallback;
        }
        if (json_validate($setting->option_value)) {
            return json_decode($setting->option_value, $toArray) ?? $fallback;;
        }
        return filterValue($setting->option_value) ?? $fallback;
    }

    /**
     * Set Setting data
     * @param string $key The Settings key name
     * @param mixed $value Store data
     * 
     * @return mixed array|string
     */
    public static function setValue($key, $value = null)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        try {
            Setting::updateOrCreate([
                'option_name' => $key
            ], ['option_value' => $value ?? null]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function data()
    {
        $settings = Setting::cachedSettings();

        $data = new DataHelper(
            $settings->pluck('option_value', 'option_name')->toArray()
        );

        return $data;
    }
}
