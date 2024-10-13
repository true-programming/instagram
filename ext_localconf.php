<?php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1701164824] = [
    'nodeName' => 'instagramTokenState',
    'priority' => 40,
    'class' => \Trueprogramming\Instagram\Form\Element\InstagramTokenState::class,
];

$GLOBALS['TYPO3_CONF_VARS']['LOG']['Trueprogramming']['Instagram']['writerConfiguration'] = [
    \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
        \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
            'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/instagram_import.log'
        ]
    ],
];
