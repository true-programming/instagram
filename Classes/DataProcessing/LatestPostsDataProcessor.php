<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\DataProcessing;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Trueprogramming\Instagram\Domain\Model\Post;
use Trueprogramming\Instagram\Domain\Repository\PostRepository;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class LatestPostsDataProcessor implements DataProcessorInterface
{
    public function __construct(
        protected PostRepository $postRepository,
        protected FileRepository $fileRepository
    ) {}

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $account = (int)$cObj->stdWrapValue('account', $processorConfiguration);
        $limit = (int)$cObj->stdWrapValue('limit', $processorConfiguration);
        $posts = $this->postRepository->findLatestForAccount($account, $limit);

        $processedPosts = [];
        /** @var Post $post */
        foreach ($posts as $post) {
            $processedPosts[$post->getUid()] = $post->toArray();

            $media = $this->fileRepository->findByRelation(PostRepository::TABLE, 'media', $post->getUid());
            $processedPosts[$post->getUid()]['media'] = $media;

            if ($post->hasThumbnail()) {
                $thumbnail = $this->fileRepository->findByRelation(PostRepository::TABLE, 'thumbnail', $post->getUid());
                $processedPosts[$post->getUid()]['thumbnail'] = $thumbnail;
            }
        }

        $processedData['posts'] = $processedPosts;

        return $processedData;
    }
}
