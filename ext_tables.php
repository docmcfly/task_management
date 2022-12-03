<?php
defined('TYPO3_MODE') || die('Access denied.');


call_user_func(
    function()
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Cylancer.TaskManagement',
            'TaskBoard',
            'LLL:EXT:task_management/Resources/Private/Language/locallang_be_taskboard.xlf:plugin.name',
            'EXT:task_management/Resources/Public/Icons/taskmanagement_plugin_taskboard.svg'
            );
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Cylancer.TaskManagement',
            'Settings',
            'LLL:EXT:task_management/Resources/Private/Language/locallang_be_settings.xlf:plugin.name',
            'EXT:task_management/Resources/Public/Icons/taskmanagement_plugin_settings.svg'
            );
        
}
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('task_management', 'Configuration/TypoScript', 'TaskManagement');