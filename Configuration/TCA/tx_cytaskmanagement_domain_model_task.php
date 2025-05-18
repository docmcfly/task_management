<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_task',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
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
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        - 1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    [
                        '',
                        0
                    ]
                ],
                'foreign_table' => 'tx_cytaskmanagement_domain_model_task',
                'foreign_table_where' => 'AND {#tx_cytaskmanagement_domain_model_task}.{#pid}=###CURRENT_PID### AND {#tx_cytaskmanagement_domain_model_task}.{#sys_language_uid} IN (-1,0)'
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ]
            ]
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],

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
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 10,
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
                'type' => 'input',
                'size' => 6,
                'eval' => 'int',
                'default' => 'weeks',
                'readOnly' => true,
            ]
        ],
        'next_repetition' => [
            'exclude' => true,
            'label' => 'LLL:EXT:cy_task_management/Resources/Private/Language/locallang_db.xlf:tx_cytaskmanagement_domain_model_task.next_repetition',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'dbType' => 'date',
                'default' => time(),
                'readOnly' => false,
            ]
        ],
        
    ]
];
