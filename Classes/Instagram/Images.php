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

use Trueprogramming\Instagram\Domain\Model\Post;
use Trueprogramming\Instagram\Domain\Repository\PostRepository;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class Images
{
    private const POSTS_PATH = 'instagram/posts';

    public function __construct(
        protected DataHandler $dataHandler
    ) {}

    public function importImage(Post $post): int
    {
        /** @var ResourceStorage $storage */
        $storage = GeneralUtility::makeInstance(StorageRepository::class)->findByUid(1);
        if (!$storage->hasFolder(self::POSTS_PATH)) {
            $storage->createFolder(self::POSTS_PATH);
        }

        $folder = $storage->getFolder(self::POSTS_PATH);

        $fileData = GeneralUtility::getUrl($post->getMediaUrl());
        $fileName = $post->getInstagramId() . $post->getFileType();

        $file = $folder->createFile($fileName);
        $file->setContents($fileData);

        $newId = StringUtility::getUniqueId('NEW');
        $data = [
            'sys_file_reference' => [
                $newId => [
                    'uid_local' => $file->getUid(),
                    'tablenames' => 'tt_content',
                    'uid_foreign' => $post->getUid(),
                    'fieldname' => 'assets',
                    'pid' => $post->getPid(),
                ],
            ],
            PostRepository::TABLE => [
                $post->getUid() => [
                    'image' => $newId,
                ],
            ],
        ];

        $this->dataHandler->start($data, []);
        $this->dataHandler->process_datamap();
        return (int)$this->dataHandler->substNEWwithIDs[$newId];
    }
}
