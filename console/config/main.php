<?php

return [
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'migrate' => [
            'class' => dmstr\console\controllers\MigrateController::class,
            'migrationTable' => '{{%migrations}}',
            'migrationPath' => '@migrations',
        ],
    ],
    'components' => [
        'schedule' => [
            'class' => marketingsolutions\scheduling\Schedule::class,
        ]
    ],
    'modules' => [
        'mailing' => [
            'class' => \yz\admin\mailer\console\Module::class,
        ],
        /**
         * Loyalty modules
         */
        'catalog' => [
            'class' => \ms\loyalty\catalog\console\Module::class,
        ],
        'sales' => [
            'class' => modules\sales\console\Module::class,
        ],
        'payments' => [
            'class' => ms\loyalty\prizes\payments\console\Module::class,
        ],
        'sms' => [
            'class' => \modules\sms\console\Module::class,
        ],
        'checker' => [
            'class' => \ms\loyalty\checker\console\Module::class,
        ],
        'spent' => [
            'class' => \modules\spent\console\Module::class
        ],
        'interim' => [
            'class' => \modules\interim\console\Module::class
        ]
    ],
    'params' => [
        'yii.migrations' => [
            '@modules/sales/migrations',
            '@modules/profiles/migrations',
            '@modules/sms/migrations',
            '@modules/news/migrations',
        ]
    ],
];
