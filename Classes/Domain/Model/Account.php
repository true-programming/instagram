<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\Domain\Model;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class Account
{
    public function __construct(
        protected int $uid,
        protected int $pid,
        protected bool $hidden,
        protected string $name,
        protected string $username,
        protected string $appId,
        protected string $appSecret,
        protected string $appReturnUrl,
        protected int $tokenState
    ) {}

    public static function fromDB(array $account): self
    {
        return new self(
            $account['uid'],
            $account['pid'],
            (bool)$account['hidden'],
            $account['name'],
            $account['username'],
            $account['app_id'],
            $account['app_secret'],
            $account['app_return_url'],
            $account['token_state'],
        );
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    public function getAppReturnUrl(): string
    {
        return $this->appReturnUrl;
    }

    public function getTokenState(): int
    {
        return $this->tokenState;
    }
}
