<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\Middleware;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Trueprogramming\Instagram\Domain\Repository\AccountRepository;
use Trueprogramming\Instagram\Domain\Repository\TokenRepository;
use Trueprogramming\Instagram\Instagram\Client;
use Trueprogramming\Instagram\Instagram\TokenState;
use TYPO3\CMS\Core\Http\RedirectResponse;

class GetInstagramAuthenticationCode implements MiddlewareInterface
{
    public function __construct(
        protected AccountRepository $accountRepository,
        protected TokenRepository $tokenRepository,
        protected Client $client,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestHeaders = $request->getHeaders();

        if (!isset($requestHeaders['referer']) || !str_contains($requestHeaders['referer'][0], 'instagram')) {
            return $handler->handle($request);
        }

        $code = $request->getQueryParams()['code'] ?? '';
        $accountUid = $request->getQueryParams()['state'] ?? 0;

        if (strlen($code) > 0) {
            $account = $this->accountRepository->findByUid((int)$accountUid);

            if ($account === null) {
                return $handler->handle($request);
            }

            $instagramApi = $this->client->getInstagramApiAccessToken(
                $account,
                $code
            );

            $instagramGraphApi = $this->client->getInstagramGraphAccessToken($account, $instagramApi['access_token']);

            $this->accountRepository->updateTokenState(
                $account->getUid(),
                TokenState::ACTIVE
            );

            $expireDate = new \DateTime();
            $expireDate->modify('+ ' . $instagramGraphApi['expires_in'] . ' seconds');
            $this->tokenRepository->add($account->getUid(), ['token' => $instagramGraphApi['access_token'], 'expires' => $expireDate->getTimestamp(), 'user_id' => $instagramApi['user_id']]);

            return new RedirectResponse('/', 301);
        }

        return $handler->handle($request);
    }
}
