<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\Command;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trueprogramming\Instagram\Domain\Model\Account;
use Trueprogramming\Instagram\Domain\Repository\AccountRepository;
use Trueprogramming\Instagram\Instagram\Feed;
use TYPO3\CMS\Core\Core\Bootstrap;

class ImportCommand extends Command
{
    public function __construct(
        protected Feed $feed,
        protected AccountRepository $accountRepository,
        private readonly LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        Bootstrap::initializeBackendAuthentication();
        $accounts = $this->accountRepository->findAll();

        try {
            /** @var Account $account */
            foreach ($accounts as $account) {
                $this->feed->import($account);
            }

            $this->logger->debug('Import finished successfully');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
