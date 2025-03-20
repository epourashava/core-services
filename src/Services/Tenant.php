<?php

namespace Core\Services;

use Core\Exceptions\TenantNotFoundException;
use Core\Helpers\DataHelper;
use Core\Models\Municipality;
use Core\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Tenant
{
    /**
     * The instance of the class.
     *
     * @var Tenant
     */
    private static $obj;

    /**
     * The subdomain.
     *
     * @var string
     */
    public string $subdomain;

    /**
     * The model class.
     *
     * @var string
     */
    public string $modelClass = Municipality::class;

    /**
     * The subdomain.
     *
     * @var Municipality
     */
    public $model;

    /**
     * Get the tenant column name
     * 
     * @var string
     */
    public const columnName = 'subdomain';

    /**
     * Create a new class instance.
     */
    private final function  __construct()
    {
        $this->subdomain = self::determineSubdomainFromUrl();
    }
    /**
     * Get the tenant by subdomain.
     *
     * @param string $subdomain
     * @return Model
     */
    public static function getTenantBySubdomain($subdomain)
    {
        /**
         * @var Model $model
         */
        $model = self::getInstance()->getTenantModel();

        return $model::where(self::columnName, $subdomain)->first();
    }

    /**
     * Get the tenant.
     *
     * @return Model
     */
    public static function getTenant()
    {
        if (is_null(self::getInstance()->model)) {
            self::getInstance()->model = self::getTenantBySubdomain(
                self::getSubDomain() ?? self::determineSubdomainFromUrl()
            );
        }
        return self::getInstance()->model;
    }

    /**
     * Get the tenant id.
     *
     * @return int
     */
    public static function getTenantId()
    {
        return self::getTenant()->id;
    }

    /**
     * Set the subdomain.
     *
     * @param string $subdomain
     * @return Tenant
     */
    public static function setSubDomain($subdomain)
    {
        self::getInstance()->subdomain = $subdomain;

        self::getInstance()->model = self::getTenantBySubdomain($subdomain);

        self::getInstance()->setDefaults();

        return self::getInstance();
    }

    /**
     * Set the defaults.
     *
     * @return self
     */
    public function setDefaults()
    {
        // Set cache prefix
        config(['cache.prefix' => Tenant::getSubDomain() . '_']);
        // Set app name
        config([
            'app.name' => Tenant::getTenant()?->appName(),
            'app.url' => Tenant::getBaseUrl(),
        ]);

        // Set the root url
        URL::forceRootUrl(Tenant::getBaseUrl());
    }

    /**
     * Refresh the tenant.
     *
     * @return self
     */
    public function refresh()
    {
        self::getInstance()->model = self::getTenantBySubdomain($this->subdomain);

        return $this;
    }

    /**
     * Set the tenant
     *
     * @param Model $model
     * @return self
     */
    public static function setTenant(Model $model)
    {
        self::getInstance()->model = $model;

        return self::getInstance();
    }

    /**
     * Get the subdomain.
     *
     * @return string
     */
    public static function getSubDomain()
    {
        return self::getInstance()->subdomain;
    }

    /**
     * Get the base url.
     *
     * @return string
     */
    public static function getBaseUrl()
    {
        $base = config('app.base_url');

        $subdomain = self::getSubDomain();

        $scheme = URL::formatScheme(app()->environment('production'));

        return "{$scheme}{$subdomain}.{$base}";
    }


    /**
     * Determine the subdomain from the URL.
     *
     * @return string
     */
    public static function determineSubdomainFromUrl()
    {
        $pieces = explode('.', request()->getHost());

        $subdomain = $pieces[0] ?? '';

        return request()->getHost() === config('app.base_url') ? '' : $subdomain;
    }

    /**
     * Get the route parameters.
     *
     * @param array $params
     * @return array
     */
    public static function routeParams($params = [])
    {
        $defaultParams = [
            'subdomain' => self::getSubDomain(),
        ];

        return $defaultParams + $params;
    }

    /**
     * Get the storage path.
     *
     * @return string
     */
    public static function getStoragePath($path = '')
    {
        return self::getSubDomain() . '/' . ltrim($path, '/');
    }

    /**
     * Get the settings.
     *
     * @return array
     */
    public static function getSettings()
    {
        return new DataHelper(
            Setting::all()->pluck('option_value', 'option_name')->toArray()
        );
    }

    /**
     * Get the instance of the class.
     *
     * @return Tenant
     */
    public static function getInstance()
    {
        if (!isset(self::$obj)) {
            self::$obj = new Tenant();
        }
        return self::$obj;
    }

    /**
     * Check if the tenant exists.
     * Else, throw an exception.
     *
     * @return mixed
     */
    public static function checkTenant()
    {
        if (!self::getTenant()) {
            throw new TenantNotFoundException();
        }

        return self::getInstance();
    }

    /**
     * Use tenant model.
     *
     * @return this
     */
    public static function useTenantModel($model)
    {
        self::getInstance()->modelClass = $model;

        return self::getInstance();
    }

    /**
     * Get the tenant model.
     *
     * @return string
     */
    public static function getTenantModel()
    {
        return self::getInstance()->modelClass;
    }
}
