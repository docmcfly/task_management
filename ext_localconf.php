<?php
use Cylancer\CyTaskManagement\Controller\SettingsController;
use Cylancer\CyTaskManagement\Controller\TaskboardController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die('Access denied.');

ExtensionUtility::configurePlugin(
    'CyTaskManagement',
    'Taskboard',
    [
        TaskboardController::class => 'show, create, done, remove, duplicate'
    ],
    [
        TaskboardController::class => 'show, create, done, remove, duplicate'
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);
ExtensionUtility::configurePlugin(
    'CyTaskManagement',
    'UserSettings',
    [
        SettingsController::class => 'show, save'
    ],
    [
        SettingsController::class => 'show, save'
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);


// Add task for optimizing database tables
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Cylancer\CyTaskManagement\Task\TaskManagementInformerTask::class] = [
    'extension' => 'taskmanagement',
    'title' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.title',
    'description' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.description',
    'additionalFields' => \Cylancer\CyTaskManagement\Task\TaskManagementInformerAdditionalFieldProvider::class
];

// E-Mail-Templates
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths']['cy_task_management'] = 'EXT:cy_task_management/Resources/Private/Templates/TaskManagementInfoMail/';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths']['cy_task_management'] = 'EXT:cy_task_management/Resources/Private/Layouts/TaskManagementInfoMail/';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths']['cy_task_management'] = 'EXT:cy_task_management/Resources/Private/Partials/TaskManagementInfoMail/';

