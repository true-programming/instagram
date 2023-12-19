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

use Trueprogramming\Instagram\Domain\DTO\Post;
use Trueprogramming\Instagram\Domain\Model\Account;
use Trueprogramming\Instagram\Domain\Repository\AccountRepository;
use Trueprogramming\Instagram\Domain\Repository\PostRepository;
use Trueprogramming\Instagram\Domain\Repository\TokenRepository;

class Feed
{
    public function __construct(
        protected AccountRepository $accountRepository,
        protected TokenRepository $tokenRepository,
        protected PostRepository $postRepository,
        protected Files $files,
        protected Client $client,
    ) {}

    public function import(Account $account): bool
    {
        $token = $this->tokenRepository->findByUid($account->getUid());
        $feed = $this->client->getFeedFromUserId($token['token'], $token['user_id']);

        $errors = false;
        foreach ($feed['data'] as $feedItem) {
            $existingPost = $this->postRepository->findByInstagramId($feedItem['id']);
            $feedPost = Post::fromFeed($feedItem, $account->getUid());

            try {
                if ($existingPost) {
                    $this->postRepository->update($existingPost->getUid(), $feedPost);
                    $post = $existingPost;
                } else {
                    $newId = $this->postRepository->add($feedPost);
                    $post = $this->postRepository->findByUid($newId);

                    if ($post === null) {
                        continue;
                    }
                }

                $this->files->import($post);
            } catch (\Exception $e) {
                $errors = true;
            }
        }

        return $errors;
    }
}
