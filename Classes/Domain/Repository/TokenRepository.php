<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\Domain\Repository;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Registry;

class TokenRepository
{
    protected const REGISTRY_NAMESPACE = 'trueprogramming_instagram';
    protected const REGISTRY_PREFIX = 'token__';

    public function __construct(
        protected Registry $registry
    ) {}

    public function add(int $uid, array $tokenData): void
    {
        $this->registry->set(self::REGISTRY_NAMESPACE, self::REGISTRY_PREFIX . $uid, json_encode($tokenData));
    }

    public function findByUid(int $uid): array
    {
        $tokenData = $this->registry->get(self::REGISTRY_NAMESPACE, self::REGISTRY_PREFIX . $uid) ?? '';

        if ($tokenData) {
            return json_decode($tokenData, true);
        }

        return [];
    }

    public function revoke(int $uid): void
    {
        $this->registry->remove(self::REGISTRY_NAMESPACE, self::REGISTRY_PREFIX . $uid);
    }
}
