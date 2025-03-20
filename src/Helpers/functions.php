<?php

use Core\Models\Setting;
use Core\Helpers\Converter;
use Core\Services\Tenant;

if (!function_exists('tenant')) {
    /**
     * Get the tenant instance
     *
     * @param bool $instance
     * @return \Core\Services\Tenant|\Core\Models\Municipality
     */
    function tenant($instance = false)
    {
        if ($instance) {
            return Tenant::getInstance();
        }

        return Tenant::getTenant();
    }
}

if (!function_exists('converter')) {
    /**
     * Get the tenant instance
     *
     * @return \Core\Helpers\Converter
     */
    function converter()
    {
        return new Converter();
    }
}

if (!function_exists('in_production')) {
    /**
     * Check if the app is in production
     *
     * @return bool
     */
    function in_production(): bool
    {
        return app()->environment('production', 'prod');
    }
}

if (!function_exists('filterValue')) {
    /**
     * Filter value
     *
     * @param mixed $value
     * @return mixed
     */
    function filterValue($value)
    {
        if (is_null($value)) return $value;
        $bool = filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );
        if ($bool === null) {
            return $value;
        }
        return $bool;
    }
}


if (!function_exists('settings')) {
    /**
     * Get or set settings
     *
     * @param string|array $name
     * @param mixed $fallback
     * @return mixed
     */
    function settings($name, $fallback = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $setting = Setting::updateOrCreate([
                    'option_name' => $key
                ], [
                    'option_value' => $value ?? null
                ]);
            }
        } elseif (is_string($name)) {
            $setting = Setting::cachedSettings()
                ->where('option_name', $name)
                ->first();
        } else {
            return $fallback;
        }
        return filterValue($setting->option_value ?? $fallback);
    }
}

if (!function_exists('flashMessage')) {
    /**
     * Flash message
     *
     * @param string $title
     * @param mixed $message = success | error | info | warning
     * @param mixed $type
     * @return mixed
     */
    function flashMessage($title, $message, $type = 'success', $extra = [])
    {
        session()->flash('flash', [
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'extra' => $extra ?? []
        ]);
    }
}

if (!function_exists('is_unicode')) {
    /**
     * Check if the message is unicode
     *
     * @param string $message
     * @return bool
     */
    function is_unicode(string $message): bool
    {
        return strlen($message) !== mb_strlen($message);
    }
}

if (!function_exists('find_arr')) {
    function find_arr($arr, $key, $value)
    {
        return array_column(
            $arr,
            null,
            $key
        )[$value] ?? null;
    }
}
