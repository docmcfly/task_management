<?php
use Cylancer\TaskManagement\Controller\TaskBoardController;
use Cylancer\TaskManagement\Controller\SettingsController;

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin( //
    'Cylancer.TaskManagement', //
    'TaskBoard', //
    [
        TaskBoardController::class => 'show, create, done, remove, duplicate'
    ], 
        // non-cacheable actions
        [
            TaskBoardController::class => 'show, create, done, remove, duplicate'
        ]);
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin( //
    'Cylancer.TaskManagement', //
    'Settings', //
    [
        SettingsController::class => 'show, save'
    ], 
        // non-cacheable actions
        [
            SettingsController::class => 'show,save'
        ]);

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    taskmanagement-plugin-taskboard {
                        iconIdentifier = taskmanagement-plugin-taskboard
                        title = LLL:EXT:task_management/Resources/Private/Language/locallang_be_taskboard.xlf:plugin.name
                        description = LLL:EXT:task_management/Resources/Private/Language/locallang_be_taskboard.xlf:plugin.description
                        tt_content_defValues {
                            CType = list
                            list_type = taskmanagement_taskboard
                        }
                    }
                    taskmanagement-plugin-settings {
                        iconIdentifier = taskmanagement-plugin-settings
                        title = LLL:EXT:task_management/Resources/Private/Language/locallang_be_settings.xlf:plugin.name
                        description = LLL:EXT:task_management/Resources/Private/Language/locallang_be_settings.xlf:plugin.description
                        tt_content_defValues {
                            CType = list
                            list_type = taskmanagement_settings
                        }
                    }
                }
                show = *
            }
       }');

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon('taskmanagement-plugin-taskboard', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:task_management/Resources/Public/Icons/taskmanagement_plugin_taskboard.svg'
    ]);
    $iconRegistry->registerIcon('taskmanagement-plugin-settings', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:task_management/Resources/Public/Icons/taskmanagement_plugin_settings.svg'
    ]);
});

// Add task for optimizing database tables
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Cylancer\TaskManagement\Task\TaskManagementInformerTask::class] = [
    'extension' => 'taskmanagement',
    'title' => 'LLL:EXT:task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.title',
    'description' => 'LLL:EXT:task_management/Resources/Private/Language/locallang.xlf:task.taskManagementInformer.description',
    'additionalFields' => \Cylancer\TaskManagement\Task\TaskManagementInformerAdditionalFieldProvider::class
];
    

    


