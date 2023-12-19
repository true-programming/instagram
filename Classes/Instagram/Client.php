<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\Instagram;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Trueprogramming\Instagram\Domain\Model\Account;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;

class Client
{
    private const INSTAGRAM_API_URI = 'https://api.instagram.com';
    private const INSTAGRAM_GRAPH_URI = 'https://graph.instagram.com';
    private const INSTAGRAM_API_AUTHORIZATION_PATH = 'oauth/authorize';
    private const INSTAGRAM_API_ACCESS_TOKEN_PATH = 'oauth/access_token';
    private const INSTAGRAM_GRAPH_ACCESS_TOKEN_PATH = 'access_token';
    private const INSTAGRAM_GRAPH_MEDIA_PATH = 'media';
    private const INSTAGRAM_REFRESH_TOKEN_PATH = 'refresh_access_token';

    public function __construct(
        protected RequestFactory $requestFactory
    ) {}

    public static function buildAuthenticationLink(string $clientId, string $redirectUri): string
    {
        $uri = new Uri(self::INSTAGRAM_API_URI);
        return (string)$uri
            ->withPath(self::INSTAGRAM_API_AUTHORIZATION_PATH)
            ->withQuery('client_id=' . $clientId . '&redirect_uri=' . $redirectUri . '&scope=user_profile,user_media&response_type=code');
    }

    public function getInstagramApiAccessToken(Account $account, string $code): array
    {
        $request = $this->requestFactory->request(
            (string)(new Uri(self::INSTAGRAM_API_URI))->withPath(self::INSTAGRAM_API_ACCESS_TOKEN_PATH),
            'POST',
            [
                'form_params' => [
                    'client_id' => $account->getAppId(),
                    'client_secret' => $account->getAppSecret(),
                    'redirect_uri' => $account->getAppReturnUrl(),
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                ],
            ]
        );

        if ($request->getStatusCode() !== 200) {
            throw new \Exception('Instagram api error: ' . $request->getStatusCode() . ' - ' . $request->getBody()->getContents(), 1701160984);
        }

        $result = json_decode($request->getBody()->getContents(), true);

        if ($result['access_token'] === '' || $result['user_id'] === '') {
            throw new \Exception('Instagram api error: No token in result', 1701161224);
        }

        return $result;
    }

    public function getInstagramGraphAccessToken(Account $account, string $token): array
    {
        $uri = new Uri(self::INSTAGRAM_GRAPH_URI);
        $uri = $uri
            ->withPath(self::INSTAGRAM_GRAPH_ACCESS_TOKEN_PATH)
            ->withQuery('grant_type=ig_exchange_token&client_secret=' . $account->getAppSecret() . '&access_token=' . $token);

        $request = $this->requestFactory->request((string)$uri);

        if ($request->getStatusCode() !== 200) {
            throw new \Exception('Instagram api error: ' . $request->getStatusCode() . ' - ' . $request->getBody()->getContents(), 1701160984);
        }

        $result = json_decode($request->getBody()->getContents(), true);

        if (empty($result['access_token']) || empty($result['expires_in'])) {
            throw new \Exception('Instagram api error: No token in result', 1701161224);
        }

        return $result;
    }

    public function getFeedFromUserId(string $token, int $userId): array
    {
        $uri = (new Uri(self::INSTAGRAM_GRAPH_URI))
            ->withPath($userId . '/' . self::INSTAGRAM_GRAPH_MEDIA_PATH)
            ->withQuery('fields=media,caption,media_type,media_url,permalink,thumbnail_url,timestamp,username,children&access_token=' . $token);

        $request = $this->requestFactory->request((string)$uri);

        if ($request->getStatusCode() !== 200) {
            throw new \Exception('Instagram feed not imported', 1701280879);
        }

        $result = json_decode($request->getBody()->getContents(), true);

        if (!isset($result['data'][0])) {
            throw new \Exception('No instagram posts in feed', 1701280939);
        }

        return $result;
    }

    public function getRefreshToken(string $token): array
    {
        $uri = (new Uri(self::INSTAGRAM_GRAPH_URI))
            ->withPath(self::INSTAGRAM_REFRESH_TOKEN_PATH)
            ->withQuery('grant_type=ig_refresh_token&access_token=' . $token);

        $request = $this->requestFactory->request((string)$uri);

        if ($request->getStatusCode() !== 200) {
            throw new \Exception('Could not refresh token', 1703020071);
        }

        $result = json_decode($request->getBody()->getContents(), true);

        if (empty($result['access_token']) || empty($result['expires_in'])) {
            throw new \Exception('Instagram api error: No token in result', 1703020491);
        }

        return $result;
    }
}
