<?php

return [
    'trueprogramming_extension' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:instagram/Resources/Public/Icons/ext_icon.svg',
    ],
    'instagram-module' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        'source' => 'EXT:instagram/Resources/Public/Icons/module.png',
    ],
];
