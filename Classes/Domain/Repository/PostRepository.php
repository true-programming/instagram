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

use Trueprogramming\Instagram\Domain\DTO\Post;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class PostRepository
{
    public const TABLE = 'tx_instagram_post';

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

        foreach ($results as $post) {
            $objects->attach(\Trueprogramming\Instagram\Domain\Model\Post::fromDB($post));
        }

        return $objects;
    }

    public function findByUid(int $uid): ?\Trueprogramming\Instagram\Domain\Model\Post
    {
        $qb = $this->getQueryBuilder();
        $post = $qb
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($post) {
            return \Trueprogramming\Instagram\Domain\Model\Post::fromDB($post);
        }

        return null;
    }

    public function add(Post $post): int
    {
        $this->getQueryBuilder()
            ->insert(self::TABLE)
            ->values($post->toArray())
            ->executeStatement();
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        return (int)$connection->lastInsertId(self::TABLE);
    }

    public function findByInstagramId(string $id): ?\Trueprogramming\Instagram\Domain\Model\Post
    {
        $qb = $this->getQueryBuilder();
        $result = $qb
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $qb->expr()->eq('instagram_id', $qb->createNamedParameter($id, \PDO::PARAM_STR))
            )
            ->executeQuery()
            ->fetchAssociative();

        if (!$result) {
            return null;
        }

        return \Trueprogramming\Instagram\Domain\Model\Post::fromDB($result);
    }

    public function findLatestForAccount(int $accountUid, int $amount): ObjectStorage
    {
        $objects = new ObjectStorage();
        $qb = $this->getQueryBuilder();
        $results = $qb
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $qb->expr()->eq('account', $qb->createNamedParameter($accountUid, \PDO::PARAM_INT))
            )
            ->orderBy('timestamp', 'DESC')
            ->setMaxResults($amount)
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($results as $post) {
            $objects->attach(\Trueprogramming\Instagram\Domain\Model\Post::fromDB($post));
        }

        return $objects;
    }

    public function update(int $uid, Post $post): void
    {
        $qb = $this->getQueryBuilder();
        $qb
            ->update(self::TABLE)
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($uid, \PDO::PARAM_INT))
            );

        foreach ($post->toArray() as $field => $value) {
            $qb->set($field, $value);
        }

        $qb->executeStatement();
    }
}
