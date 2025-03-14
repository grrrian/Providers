<?php

namespace SocialiteProviders\Atlassian;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'ATLASSIAN';

    /**
     * {@inheritdoc}
     */
    protected $parameters = [
        'prompt'   => 'consent',
        'audience' => 'api.atlassian.com',
    ];

    protected $scopes = ['read:me'];

    protected $scopeSeparator = ' ';

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://auth.atlassian.com/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return 'https://auth.atlassian.com/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.atlassian.com/me', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
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
            'id'       => $user['account_id'],
            'nickname' => $user['email'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'avatar'   => $user['picture'],
        ]);
    }
}
