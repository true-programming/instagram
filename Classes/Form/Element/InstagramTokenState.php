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
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class InstagramTokenState extends AbstractFormElement
{
    private const TEMPLATE = 'EXT:instagram/Resources/Private/Templates/Form/Element/InstagramTokenState.html';
    protected StandaloneView $view;
    protected TokenRepository $tokenRepository;
    protected UriBuilder $uriBuilder;

    public function __construct(NodeFactory $nodeFactory = null, array $data = [])
    {
        parent::__construct($nodeFactory, $data);
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->tokenRepository = GeneralUtility::makeInstance(TokenRepository::class);
        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
    }

    public function render(): array
    {
        $this->view->setTemplatePathAndFilename(self::TEMPLATE);
        $result = $this->initializeResultArray();

        if (is_int($this->data['databaseRow']['uid'])) {
            $account = Account::fromDB($this->data['databaseRow']);

            $authenticationUri = Client::buildAuthenticationLink($account->getAppId(), $account->getAppReturnUrl() . '/&state=' . $account->getUid());
            $token = $this->tokenRepository->findByUid($account->getUid());
            $this->view->assignMultiple([
                'account' => $account,
                'authenticationUri' => $authenticationUri,
                'token' => $token,
            ]);
        }

        $result['html'] = $this->view->render();
        return $result;
    }
}
