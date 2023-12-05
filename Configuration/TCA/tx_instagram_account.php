<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'Instagram accounts',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'default_sortby' => 'name',
        'iconfile' => 'EXT:instagram/Resources/Public/Icons/module.png',
        'origUid' => 't3_origuid',
        'searchFields' => 'name',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_account.columns.hidden.label',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'name' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_account.columns.name.label',
            'config' => [
                'type' => 'input',
                'required' => true,
            ],
        ],
        'username' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_account.columns.username.label',
            'config' => [
                'type' => 'input',
                'required' => true,
            ],
        ],
        'app_id' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_account.columns.app_id.label',
            'config' => [
                'type' => 'input',
                'required' => true,
            ],
        ],
        'app_secret' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_account.columns.app_secret.label',
            'config' => [
                'type' => 'input',
                'required' => true,
            ],
        ],
        'app_return_url' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_account.columns.app_return_url.label',
            'config' => [
                'type' => 'input',
                'required' => true,
            ],
        ],
        'token_state' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_account.columns.token_state.label',
            'config' => [
                'type' => 'user',
                'renderType' => 'instagramTokenState',
            ],
        ],
    ],
    'palettes' => [
        'hidden' => [
            'showitem' => 'hidden',
        ],
        'default' => [
            'showitem' => '
                name,
                username
            ',
        ],
        'app' => [
            'showitem' => '
                app_id,
                app_secret,
                --linebreak--,
                app_return_url,
                --linebreak--,
                token_state,
            ',
        ],
    ],
    'types' => [
        [
            'showitem' => '
                --div--;LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_account.pallets.default.label,
                    --palette--;;hidden,
                    --palette--;;default,
                --div--;App,
                    --palette--;;app,
            ',
        ],
    ],
];
