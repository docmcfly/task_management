<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

(static function (): void{

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['taskmanagement_taskboard'] = 'pi_flexform';
    ExtensionManagementUtility::addPiFlexFormValue(
        // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
        'taskmanagement_taskboard',
        // Flexform configuration schema file
        'FILE:EXT:task_management/Configuration/FlexForms/TaskBoard.xml'
    );

    ExtensionUtility::registerPlugin(
        'TaskManagement',
        'TaskBoard',
        'LLL:EXT:task_management/Resources/Private/Language/locallang_be_taskboard.xlf:plugin.name',
        'EXT:task_management/Resources/Public/Icons/taskmanagement_plugin_taskboard.svg'
    );

    ExtensionUtility::registerPlugin(
        'TaskManagement',
        'Settings',
        'LLL:EXT:task_management/Resources/Private/Language/locallang_be_settings.xlf:plugin.name',
        'EXT:task_management/Resources/Public/Icons/taskmanagement_plugin_settings.svg'
    );
})();