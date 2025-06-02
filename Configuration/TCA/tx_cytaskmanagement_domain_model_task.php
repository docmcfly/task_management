<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_task',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime'
        ],
        'searchFields' => 'title',
        'iconfile' => 'EXT:cy_task_management/Resources/Public/Icons/tx_taskmanagement_domain_model_task.gif'
    ],
    'types' => [
        '1' => [
            'showitem' => 'title, done_at, user, repeat_period_count, repeat_period_unit, next_repetition '
        ]
    ],
    'columns' => [
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_task.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => '',
                'readOnly' => true,
            ]
        ],
        'done_at' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_task.done_at',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'eval' => 'datetime',
                'default' => time(),
                'readOnly' => true,
            ]
        ],
        'user' => [
            'label' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_task.user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'minitems' => 1,
                'maxitems' => 1,
                'readOnly' => true,
            ]
        ],
        'repeat_period_unit' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_task.repeat_period_unit',
            'config' => [
                'type' => 'input',
                'size' => 6,
                'eval' => 'trim',
                'default' => 'weeks',
                'readOnly' => true,
            ]
        ],
        'repeat_period_count' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_task.repeat_period_count',
            'config' => [
                'type' => 'number',
                'size' => 6,
                'default' => '1',
                'readOnly' => true,
            ]
        ],
        'next_repetition' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_task.next_repetition',
            'config' => [
                'type' => 'datetime',
                'format' => 'date',
                'eval' => 'date',
                'dbType' => 'date',
                'default' => time(),
                'readOnly' => true,
            ]
        ],
        
    ]
];
