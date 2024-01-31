<?php

declare(strict_types=1);

namespace Trueprogramming\Instagram\Controller;

/*
 * This file is part of TYPO3 CMS-based extension "instagram" by true-programming.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Trueprogramming\Instagram\Domain\Model\Account;
use Trueprogramming\Instagram\Domain\Repository\AccountRepository;
use Trueprogramming\Instagram\Domain\Repository\TokenRepository;
use Trueprogramming\Instagram\Instagram\Feed;
use Trueprogramming\Instagram\Instagram\TokenState;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Backend\Routing\RouteResult;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\Buttons\GenericButton;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

#[Controller]
final class DashboardController extends ActionController
{
    protected ModuleTemplate $moduleTemplate;
    protected BackendUserAuthentication $backendUserAuthentication;

    public function __construct(
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected IconFactory $iconFactory,
        protected PageRenderer $pageRenderer,
        protected AccountRepository $accountRepository,
        protected TokenRepository $tokenRepository,
        protected UriBuilder $backendUriBuilder,
        protected Feed $feed,
        protected PageRepository $pageRepository,
    ) {}

    public function initializeAction(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->backendUserAuthentication = $GLOBALS['BE_USER'];
    }

    private function setUpDocHeader(ServerRequestInterface $request): void
    {
        $rootpage = $this->getFirstRootpage() ?? 0;
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $showLink = $this->backendUriBuilder->buildUriFromRoute('trueprogramming-instagram');
        $newUri = $this->backendUriBuilder->buildUriFromRoute(
            'record_edit',
            [
                'edit' => [
                    AccountRepository::TABLE => [
                        $rootpage => 'new',
                    ],
                ],
                'returnUrl' => $showLink,
            ]
        );

        $newButton = new GenericButton();
        $newButton
            ->setLabel(LocalizationUtility::translate('button.new.label', 'instagram'))
            ->setTag('a')
            ->setHref((string)$newUri)
            ->setIcon($this->iconFactory->getIcon('actions-plus'))
            ->setShowLabelText(true);
        $buttonBar->addButton($newButton);

        /** @var RouteResult $routing */
        $routing = $request->getAttribute('routing');
        if ($routing->getRoute()->getOptions()['action'] === 'show') {
            $shortCut = $buttonBar->makeShortcutButton();
            $shortCut->setDisplayName(LocalizationUtility::translate('button.shortcut.label', 'instagram'));
            $shortCut->setRouteIdentifier('trueprogramming-instagram');
            $buttonBar->addButton($shortCut);
        }
    }

    public function showAction(): ResponseInterface
    {
        $this->setUpDocHeader($this->request);
        $accounts = $this->accountRepository->findAll();

        /** @var Account $account */
        foreach ($accounts as $account) {
            if ($this->checkAccessForPage($account->getPid()) === false) {
                $accounts->detach($account);
            }
        }

        $this->view->assign('accounts', $accounts);
        $this->view->assign('returnUrl', $this->uriBuilder->reset()->uriFor('show'));
        $this->view->assign('returnUrl', $this->uriBuilder->reset()->uriFor('show'));
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    public function importFeedAction(int $account): ResponseInterface
    {
        $accountObject = $this->accountRepository->findByUid($account);

        try {
            $hasErrors = $this->feed->import($accountObject);

            if ($hasErrors) {
                $this->addFlashMessage(LocalizationUtility::translate('message.import.warning.label', 'instagram', [$accountObject->getName()]), '', ContextualFeedbackSeverity::WARNING);
            } else {
                $this->addFlashMessage(LocalizationUtility::translate('message.import.ok.label', 'instagram', [$accountObject->getName()]));
            }
        } catch (\Exception $e) {
            $this->addFlashMessage(LocalizationUtility::translate('message.import.error.label', 'instagram', [$accountObject->getName()]), '', ContextualFeedbackSeverity::ERROR);
        }

        return new RedirectResponse($this->uriBuilder->uriFor('show'));
    }

    public function revokeTokenAction(int $account): ResponseInterface
    {
        $account = $this->accountRepository->findByUid($account);
        $this->tokenRepository->revoke($account->getUid());
        $this->accountRepository->updateTokenState($account->getUid(), TokenState::INVALID);

        $this->addFlashMessage(LocalizationUtility::translate('message.revokeToken.ok.label', 'instagram', [$account->getName()]));

        return new RedirectResponse($this->uriBuilder->reset()->uriFor('show'));
    }

    private function getFirstRootpage(): ?int
    {
        $rootpages = $this->pageRepository->getMenu(0, 'uid', 'sorting', '', false);
        foreach ($rootpages as $rootpage) {
            if ($this->checkAccessForPage($rootpage['uid'])) {
                return $rootpage['uid'];
            }
        }

        return null;
    }

    private function checkAccessForPage(int $uid): bool
    {
        /** @var array|null $page */
        $page = $this->pageRepository->getPage($uid);

        if ($page === null) {
            return false;
        }

        return $this->backendUserAuthentication->doesUserHaveAccess($page, 1);
    }
}
