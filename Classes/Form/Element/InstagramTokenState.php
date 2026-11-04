<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\Form\Element;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Trueprogramming\Instagram\Domain\Model\Account;
use Trueprogramming\Instagram\Domain\Repository\TokenRepository;
use Trueprogramming\Instagram\Instagram\Client;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class InstagramTokenState extends AbstractFormElement
{
    private const TEMPLATE = 'EXT:instagram/Resources/Private/Templates/Form/Element/InstagramTokenState.html';

    public function __construct(
        private readonly TokenRepository $tokenRepository,
    ) {}

    /**
     * TYPO3 v12 only instantiates nodes via dependency injection if they provide this method
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function render(): array
    {
        $result = $this->initializeResultArray();

        if (!is_int($this->data['databaseRow']['uid'])) {
            return $result;
        }

        $account = Account::fromDB($this->data['databaseRow']);
        $authenticationUri = Client::buildAuthenticationLink($account->getAppId(), $account->getRedirectUri() . '&state=' . $account->getUid());
        $token = $this->tokenRepository->findByUid($account->getUid());

        $view = $this->createView();
        $view->assignMultiple([
            'account' => $account,
            'authenticationUri' => $authenticationUri,
            'token' => $token,
        ]);
        $result['html'] = $view->render();

        return $result;
    }

    /**
     * The view factory exists since TYPO3 v13, v12 still needs the standalone view
     */
    private function createView(): object
    {
        if (interface_exists(ViewFactoryInterface::class)) {
            return GeneralUtility::makeInstance(ViewFactoryInterface::class)->create(new ViewFactoryData(
                templatePathAndFilename: self::TEMPLATE,
                request: $this->data['request'] ?? $GLOBALS['TYPO3_REQUEST'],
            ));
        }

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(self::TEMPLATE);
        return $view;
    }
}