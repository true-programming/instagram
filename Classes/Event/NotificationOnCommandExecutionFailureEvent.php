<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\Event;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class NotificationOnCommandExecutionFailureEvent
{
    public function __construct(
        protected string $errorMessage,
        protected string $context,
    ) {}

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
