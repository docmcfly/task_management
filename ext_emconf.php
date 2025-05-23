<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Task management',
    'description' => 'is a simple task management: Create, delete, done, display (open and closed tasks)',
    'category' => 'plugin',
    'author' => 'C. Gogolin',
    'author_email' => 'service@cylancer.net',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'bootstrap_package' => '15.0.00-15.9.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ]
];


/* CHANGLOG: 
      4.0.0 :: Update to TYPO3 13.4 and bootstrap 15.0
      3.0.1 :: Fix the page validation in the task management task.
      3.0.0 :: Update to TYPO3 12.4 and bootstrap 13.0
      2.2.4 :: Fix the sql query in the task management task.
      2.2.3 :: Fix deletion of tasks.
      2.2.2 :: Fix the missing jquery and a translation
      2.2.1 :: Fix the plugin registration/configuration.
      2.2.0 :: Release the bootsrap version 
      2.0.x :: full migration to TYPO3 11.5  
      1.3.0 :: update: make this plugin TYPO3 11.5 LTS ready
      1.2.0 :: update: a user group is informed if a repeated task is added to the open tasks. 
      1.1.0 :: update: you can repeat task define and an background batch job repeats the task.  
      1.0.1 :: first productive version  
 */