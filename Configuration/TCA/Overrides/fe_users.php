<?php
defined('TYPO3') || die();

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$translatePrefix = 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_user';


ExtensionManagementUtility::addTCAcolumns(
    'fe_users',
    [

        'info_mail_when_repeated_task_added' => [
            'label' => "$translatePrefix.info_mail_when_repeated_task_added",
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                    ]
                ],
                'readOnly' => false,
            ]
        ],

    ]
);

ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    "--div--;$translatePrefix.tab_settings, info_mail_when_repeated_task_added"
);
