<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Task management',
    'description' => 'is a simple task management: Create, delete, done, display (open and closed tasks)',
    'category' => 'plugin',
    'author' => 'C. Gogolin',
    'author_email' => 'service@cylancer.net',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '4.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'bootstrap_package' => '15.0.00-15.9.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ]
];
