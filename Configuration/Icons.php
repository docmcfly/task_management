<?php

$icons = [];
foreach (['userSettings','taskboard'] as $key) {
    $icons['cyTaskManagement-' . $key] = [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:cy_task_management/Resources/Public/Icons/Plugins/' . $key . '.svg',
    ];

}
return $icons;
