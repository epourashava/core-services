<?php

namespace Core\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OAuth2Client extends Client
{
    /**
     * @var \Core\Models\User
     */
    protected $authUser;

    /**
     * Get the client instance.
     * 
     * @return static
     */
    static public function instance()
    {
        $instance = new static();
        $instance->init();

        return $instance;
    }

    public function init()
    {
        /**
         * @var \Core\Models\User $user
         */
        $this->authUser = Auth::user();
        $accessToken = $this->authUser?->getAccessToken();

        if ($accessToken) {
            $this->setToken($accessToken);
        }

        if ($this->authUser?->isTokenExpiring()) {
            $response = $this->refresh();
            $this->authUser->saveLoginData($response);

            $this->setToken($this->authUser->getAccessToken());
        }
    }

    /**
     * Redirect to the OAuth server.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    function redirect()
    {
        session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => config('cloud.auth.client_id'),
            'redirect_uri' => route('auth.callback'),
            'response_type' => 'code',
            'scope' => $this->getScopes(),
            'state' => $state,
        ]);

        return redirect($this->url('/oauth/authorize') . '?' . $query);
    }

    /**
     * Authorize the user.
     * 
     * @param string $code
     * @return array|null
     */
    public function authorize($code)
    {
        $this->verifyState();

        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => config('cloud.auth.client_id'),
            'client_secret' => config('cloud.auth.client_secret'),
            'redirect_uri' => route('auth.callback'),
            'code' => $code
        ];

        $response = $this->getClient()
            ->asForm()
            ->post($this->url('/oauth/token'), $data);

        if ($response->ok()) {
            /**
             * Response contains the following keys:
             * 
             * - token_type
             * - expires_in
             * - access_token
             * - refresh_token
             */
            return $response->json();
        }

        return null;
    }

    /**
     * Refresh the access token.
     * 
     * @return array
     */
    public function refresh()
    {
        $response = $this->getClient()
            ->withToken($this->authUser->getAccessToken())
            ->post(
                $this->url('/oauth/token'),
                [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $this->authUser->getRefreshToken(),
                    'client_id' => config('cloud.auth.client_id'),
                    'client_secret' => config('cloud.auth.client_secret'),
                    'scope' => $this->getScopes()
                ]
            );

        return $response->json();
    }

    /**
     * Create a new token.
     * 
     * @param string $grantType 
     * - client_credentials, 
     * - authorization_code, 
     * - password, 
     * - refresh_token
     * @return array
     */
    public function createToken(
        $grantType = "client_credentials",
        $additionalData = []
    ) {
        $response = $this->getClient()
            ->post(
                $this->url('/oauth/token'),
                [
                    'grant_type' => $grantType,
                    'client_id' => config('cloud.auth.client_id'),
                    'client_secret' => config('cloud.auth.client_secret'),
                    'scope' => "*",
                ] + $additionalData
            );

        return $response->json();
    }

    /**
     * Create or get a token.
     *
     * @param string $grantType
     * @param array $additionalData
     * @return void
     */
    public function createOrGetToken(
        $grantType = "client_credentials",
        $additionalData = []
    ) {
        $hasSavedToken = Cache::get('token::' . $grantType, null);

        $isExpired = isset($hasSavedToken['expires_at']) && now()->gt($hasSavedToken['expires_at'] ?? '');

        if ($hasSavedToken && !$isExpired) {
            return $hasSavedToken;
        }

        $token = $this->createToken($grantType, $additionalData);

        if (!isset($token['access_token'])) {
            return $token;
        }

        $expiredAt = now()->addSeconds($token['expires_in']);

        $token['expires_at'] = $expiredAt;

        Cache::put('token::' . $grantType, $token, $token['expires_in']);

        return $token;
    }

    /**
     * Get the user.
     *
     * @param string|null $token
     * @return array|null
     */
    function getUser($token = null)
    {
        if ($token) {
            $this->getClient()->withToken($token);
        }

        $response = $this->getClient()->get($this->url('/api/user'));

        if ($response->ok()) {
            return $response->json();
        }

        return null;
    }


    /**
     * Verify the state.
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    private function verifyState()
    {
        $state = session()->get('state');

        throw_unless(
            strlen($state) > 0 && $state === request()->get('state'),
            InvalidArgumentException::class,
            'Invalid state value.'
        );
    }

    /**
     * Get the scopes.
     *
     * @return string
     */
    private function getScopes()
    {
        return implode(' ', config('cloud.auth.scopes'));
    }

    /**
     * Get the URL.
     *
     * @param string $path
     * @return string
     */
    private function url($path = '/')
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
