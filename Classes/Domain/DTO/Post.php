<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\Domain\DTO;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class Post
{
    public function __construct(
        protected string $caption,
        protected string $mediaType,
        protected string $mediaUrl,
        protected string $permalink,
        protected int $timestamp,
        protected string $instagramId,
        protected int $account
    ) {}

    public static function fromFeed(array $data, int $account): self
    {
        return new self(
            $data['caption'] ?? '',
            $data['media_type'],
            $data['media_url'],
            $data['permalink'],
            (new \DateTime($data['timestamp']))->getTimestamp(),
            $data['id'],
            $account,
        );
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function getParsedCaption(): string
    {
        // Match Enclosed Alphanumeric Supplement
        $regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
        $clear_string = preg_replace($regex_alphanumeric, '', $this->caption);

        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $clear_string);

        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);

        // Match Supplemental Symbols and Pictographs
        $regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
        $clear_string = preg_replace($regex_supplemental, '', $clear_string);

        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        return preg_replace($regex_dingbats, '', $clear_string);
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

    public function toArray(): array
    {
        return [
            'caption' => $this->getParsedCaption(),
            'media_type' => $this->mediaType,
            'media_url' => $this->mediaUrl,
            'permalink' => $this->permalink,
            'timestamp' => $this->timestamp,
            'instagram_id' => $this->instagramId,
            'account' => $this->account,
        ];
    }
}
