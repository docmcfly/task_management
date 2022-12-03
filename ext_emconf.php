<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Task management',
    'description' => 'is a simple task management: Create, delete, done, display (open and closed tasks)
      changelog:   
      2.0.x :: full migration to TYPO3 11.5  
      1.3.0 :: update: make this plugin TYPO3 11.5 LTS ready
      1.2.0 :: update: a user group is informed if a repeated task is added to the open tasks. 
      1.1.0 :: update: you can repeat task define and an background batch job repeats the task.  
      1.0.1 :: first productive version  
',
    'category' => 'plugin',
    'author' => 'Clemens Gogolin',
    'author_email' => 'service@cylancer.net',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
            'bootstrap_package' => '11.0.2-12.9.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ]
];
