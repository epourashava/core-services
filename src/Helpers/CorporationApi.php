<?php

namespace Core\Helpers;

use Core\Services\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CorporationApi
{
    // Cache time - 7 days
    const CACHE_TIME = 60 * 24 * 7;

    public function getApiUrl($path)
    {
        return rtrim(config('corporation.api_url'), '/') . '/' . ltrim($path, '/');
    }

    public function getMunicipality($subdomain)
    {
        // return with cache forever
        return Cache::rememberForever(
            $subdomain . '_municipality',
            function () use ($subdomain) {
                return Http::get($this->getApiUrl('/get-municipality'), [
                    'subdomain' => $subdomain,
                ])->json();
            }
        );
    }

    public function getWards($subdomain)
    {
        return Cache::remember(
            $subdomain . '_wards',
            self::CACHE_TIME,
            function () use ($subdomain) {
                return Http::get($this->getApiUrl('/get-wards'), [
                    'subdomain' => $subdomain,
                ])->json();
            }
        );
    }

    public function getWard($wardId)
    {
        $wards = $this->getWards(Tenant::getSubDomain());

        return collect($wards['data'])->firstWhere('id', $wardId);
    }

    public function getColony($wardId, $colonyId)
    {
        $colonies = $this->getParas($wardId, Tenant::getSubDomain());

        return collect($colonies['data'])->firstWhere('id', $colonyId);
    }


    public function getParas($wardId, $subdomain = null)
    {
        $subdomain = $subdomain ?? Tenant::getSubDomain();

        return Cache::remember(
            $subdomain . '_paras_' . $wardId,
            self::CACHE_TIME,
            function () use ($wardId) {
                return Http::get($this->getApiUrl('/get-paras'), [
                    'ward_id' => $wardId,
                ])->json();
            }
        );
    }

    public function getHoldings($paraId, $subdomain = null)
    {
        $subdomain = $subdomain ?? Tenant::getSubDomain();

        return Cache::remember(
            $subdomain . '_holdings_' . $paraId,
            self::CACHE_TIME,
            function () use ($paraId) {
                return Http::get($this->getApiUrl('/get-holdings'), [
                    'para_id' => $paraId,
                ])->json();
            }
        );
    }

    public function getHolding($paraId, $holdingId)
    {
        $holdings = $this->getHoldings($paraId, Tenant::getSubDomain());

        return collect($holdings['data'])->firstWhere('id', $holdingId);
    }
}
