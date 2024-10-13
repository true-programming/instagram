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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trueprogramming\Instagram\Domain\Repository\AccountRepository;
use Trueprogramming\Instagram\Domain\Repository\TokenRepository;
use Trueprogramming\Instagram\Instagram\Client;

class RefreshTokenCommand extends Command
{
    public function __construct(
        protected AccountRepository $accountRepository,
        protected TokenRepository $tokenRepository,
        protected Client $client,
        private readonly LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
    }

    public function configure(): void
    {
        $this->addArgument(
            'account',
            InputArgument::REQUIRED,
            'Account uid to update the token'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $account = $this->accountRepository->findByUid((int)$input->getArgument('account'));
        $token = $this->tokenRepository->findByUid($account->getUid());

        try {
            $tokenResult = $this->client->getRefreshToken($token['token']);

            $expireDate = new \DateTime();
            $expireDate->modify('+ ' . $tokenResult['expires_in'] . ' seconds');
            $this->tokenRepository->add($account->getUid(), ['token' => $tokenResult['access_token'], 'expires' => $expireDate->getTimestamp(), 'user_id' => $token['user_id']]);

            $this->logger->info('Refresh token successfully updated');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
