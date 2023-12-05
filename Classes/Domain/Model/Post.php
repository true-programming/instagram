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

class Post
{
    private const POST_MEDIA_VIDEO = 'VIDEO';
    private const FILE_TYPE_IMAGE = '.jpg';
    private const FILE_TYPE_VIDEO = '.mov';

    public function __construct(
        protected int $uid,
        protected int $pid,
        protected bool $hidden,
        protected string $caption,
        protected string $mediaType,
        protected string $mediaUrl,
        protected string $permalink,
        protected int $timestamp,
        protected string $instagramId,
        protected int $account
    ) {}

    public static function fromDB(array $post): self
    {
        return new self(
            $post['uid'],
            $post['pid'],
            (bool)$post['hidden'],
            $post['caption'],
            $post['media_type'],
            $post['media_url'],
            $post['permalink'],
            $post['timestamp'],
            $post['instagram_id'],
            $post['account'],
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

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function getMediaUrl(): string
    {
        return $this->mediaUrl;
    }

    public function getPermalink(): string
    {
        return $this->permalink;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getInstagramId(): string
    {
        return $this->instagramId;
    }

    public function getAccount(): int
    {
        return $this->account;
    }

    public function getFileType(): string
    {
        return match ($this->getMediaType()) {
            self::POST_MEDIA_VIDEO => self::FILE_TYPE_VIDEO,
            default => self::FILE_TYPE_IMAGE,
        };
    }

    public function toArray(): array
    {
        return [
            'uid' => $this->uid,
            'pid' => $this->pid,
            'hidden' => $this->hidden,
            'caption' => $this->caption,
            'mediaType' => $this->mediaType,
            'mediaUrl' => $this->mediaUrl,
            'permaLink' => $this->permalink,
            'timestamp' => $this->timestamp,
            'instagramId' => $this->instagramId,
            'account' => $this->account,
        ];
    }
}
