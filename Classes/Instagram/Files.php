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
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class Files
{
    private const POSTS_PATH = 'instagram/posts';
    private const PREVIEW_FILE_NAME_PREFIX = 'preview_';
    protected array $data = [];

    public function __construct(
        protected DataHandler $dataHandler,
        protected FileRepository $fileRepository
    ) {}

    public function import(Post $post): void
    {
        $this->data = [];
        $files['media'] = $this->importFile($post);

        if ($post->hasThumbnail()) {
            $files['thumbnail'] = $this->importFile($post, true);
        }

        $this->data[PostRepository::TABLE] = [
            $post->getUid() => $files,
        ];

        $this->dataHandler->start($this->data, []);
        $this->dataHandler->process_datamap();
    }

    private function importFile(Post $post, bool $isThumbnail = false): string
    {
        /** @var ResourceStorage $storage */
        $storage = GeneralUtility::makeInstance(StorageRepository::class)->findByUid(1);
        if (!$storage->hasFolder(self::POSTS_PATH)) {
            $storage->createFolder(self::POSTS_PATH);
        }

        $folder = $storage->getFolder(self::POSTS_PATH);

        $url = $post->getMediaUrl();
        $fileType = $post->getFileType();
        $fileName = $post->getInstagramId() . $fileType;
        $fieldName = 'media';

        if ($isThumbnail) {
            $url = $post->getThumbnailUrl();
            $fileType = Post::FILE_TYPE_IMAGE;
            $fileName = self::PREVIEW_FILE_NAME_PREFIX . $post->getInstagramId() . $fileType;
            $fieldName = 'thumbnail';
        }

        $fileContent = GeneralUtility::getUrl($url);
        $fileObject = $folder->createFile($fileName);
        $fileObject->setContents($fileContent);

        $existingFiles = $this->fileRepository->findByRelation(PostRepository::TABLE, $fieldName, $post->getUid());
        $fileId = StringUtility::getUniqueId('NEW');

        if ($existingFiles) {
            /** @var FileReference $file */
            $file = $existingFiles[0];
            $fileId = (string)$file->getUid();
        }

        $this->data['sys_file_reference'][$fileId] = [
            'uid_local' => $fileObject->getUid(),
            'tablenames' => 'tx_instagram_post',
            'uid_foreign' => $post->getUid(),
            'fieldname' => $fieldName,
            'pid' => $post->getPid(),
        ];

        return $fileId;
    }
}
