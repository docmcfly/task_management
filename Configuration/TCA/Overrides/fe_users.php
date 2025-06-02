<?php
defined('TYPO3') || die();

$translatePrefix = 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_user';


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    $GLOBALS['TCA']['fe_users']['ctrl']['type'],
    '',
    'after:' . $GLOBALS['TCA']['fe_users']['ctrl']['label']
);

$tmp_taskmanagement_columns = [
   
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
    
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users',$tmp_taskmanagement_columns);

/* inherit and extend the show items from the parent class */

if (isset($GLOBALS['TCA']['fe_users']['types']['0']['showitem'])) {
    $GLOBALS['TCA']['fe_users']['types']['Tx_TaskManagement_User']['showitem'] = $GLOBALS['TCA']['fe_users']['types']['0']['showitem'];
} elseif(is_array($GLOBALS['TCA']['fe_users']['types'])) {
    // use first entry in types array
    $fe_users_type_definition = reset($GLOBALS['TCA']['fe_users']['types']);
    $GLOBALS['TCA']['fe_users']['types']['Tx_TaskManagement_User']['showitem'] = $fe_users_type_definition['showitem'];
} else {
    $GLOBALS['TCA']['fe_users']['types']['Tx_TaskManagement_User']['showitem'] = '';
}

$GLOBALS['TCA']['fe_users']['columns'][$GLOBALS['TCA']['fe_users']['ctrl']['type']]['config']['items'][] = ["$translatePrefix.tx_extbase_type",'Tx_TaskManagement_User'];

$tmp_types = array_keys($GLOBALS['TCA']['fe_users']['types']);
foreach($tmp_types as $type){
    $GLOBALS['TCA']['fe_users']['types'][$type]['showitem'] .= ", --div--;$translatePrefix.tab_settings, info_mail_when_repeated_task_added ";
}

