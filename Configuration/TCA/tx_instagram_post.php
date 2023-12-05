<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'Instagram posts',
        'label' => 'caption',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'default_sortby' => 'timestamp DESC',
        'iconfile' => 'EXT:instagram/Resources/Public/Icons/module.png',
        'origUid' => 't3_origuid',
        'searchFields' => 'caption',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_post.columns.hidden.label',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'caption' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_post.columns.caption.label',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'media_type' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_post.columns.media_type.label',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'media_url' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_post.columns.media_url.label',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'permalink' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_post.columns.permalink.label',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'timestamp' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_post.columns.timestamp.label',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'readOnly' => true,
            ],
        ],
        'instagram_id' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_post.columns.instagram_id.label',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'account' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_post.columns.account.label',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_instagram_account',
                'readOnly' => true,
            ],
        ],
        'image' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_post.columns.image.label',
            'config' => [
                'type' => 'file',
                'maxitems' => 1,
                'allowed' => 'common-image-types',
                'readOnly' => true,
                'overrideChildTca' => [
                    'types' => [
                        0 => [
                            'showitem' => '
                                --palette--;;filePalette
                            ',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                            'showitem' => '
                                --palette--;;filePalette
                            ',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                                --palette--;;filePalette
                            ',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
                                --palette--;;filePalette
                            ',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
                                --palette--;;filePalette
                            ',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                            'showitem' => '
                                --palette--;;filePalette
                            ',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'palettes' => [
        'hidden' => [
            'showitem' => 'hidden',
        ],
        'default' => [
            'showitem' => '
                caption,
                media_type,
                --linebreak--,
                media_url,
                permalink,
                --linebreak--,
                timestamp,
                instagram_id,
                account,
                --linebreak--,
                image,
            ',
        ],
    ],
    'types' => [
        [
            'showitem' => '
                    --div--;LLL:EXT:instagram/Resources/Private/Language/be_locallang.xlf:tca.tx_instagram_account.pallets.default.label,
                        --palette--;;hidden,
                        --palette--;;default,
                ',
        ],
    ],
];
