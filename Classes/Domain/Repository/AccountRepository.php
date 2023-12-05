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

use Trueprogramming\Instagram\Domain\Model\Account;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class AccountRepository
{
    public const TABLE = 'tx_instagram_account';

    private function getQueryBuilder(): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::TABLE);
    }

    public function findAll(): ObjectStorage
    {
        $objects = new ObjectStorage();
        $results = $this->getQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($results as $account) {
            $objects->attach(Account::fromDB($account));
        }

        return $objects;
    }

    public function findByUid(int $uid): ?Account
    {
        $qb = $this->getQueryBuilder();
        $account = $qb
            ->select('*')
            ->from(AccountRepository::TABLE)
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($account) {
            return Account::fromDB($account);
        }

        return null;
    }

    public function updateTokenState(int $uid, int $state): void
    {
        $qb = $this->getQueryBuilder();
        $qb
            ->update(self::TABLE)
            ->set('token_state', $state)
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->executeStatement();
    }
}
