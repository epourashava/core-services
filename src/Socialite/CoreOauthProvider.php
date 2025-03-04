<?php

namespace Core\Socialite;

use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class CoreOauthProvider extends AbstractProvider
{
    public const IDENTIFIER = 'CORE_OAUTH2';

    protected $scopes = [
        'openid',
        'profile',
        'email',
    ];

    protected $baseUrl;

    protected $scopeSeparator = ' ';


    /**
     * Create a new provider instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  string  $redirectUrl
     * @param  array  $guzzle
     * @return void
     */
    public function __construct(
        Request $request,
        $clientId,
        $clientSecret,
        $redirectUrl,
        $baseUrl = null,
        $guzzle = []
    ) {
        parent::__construct(
            $request,
            $clientId,
            $clientSecret,
            $redirectUrl,
            $guzzle
        );

        $this->baseUrl = $baseUrl;
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return $this->baseUrl . '/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->baseUrl . '/userinfo', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['sub'],
            'nickname' => $user['nickname'],
            'name'     => Arr::get($user, 'given_name', '') . ' ' . Arr::get($user, 'family_name', ''),
            'email'    => Arr::get($user, 'email'),
            'avatar'   => null,
        ]);
    }
}
