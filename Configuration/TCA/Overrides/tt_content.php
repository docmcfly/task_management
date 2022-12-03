<?php


$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['taskmanagement_taskboard'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
    'taskmanagement_taskboard',
    // Flexform configuration schema file
    'FILE:EXT:task_management/Configuration/FlexForms/TaskBoard.xml'
    );

?>
