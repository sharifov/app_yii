<?php

return [
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'components' => [
		'session' => [
			'name' => 'PHPBACKSESSID',
		],
        'request' => [
            'cookieValidationKey' => 'QRIzuvbodspOIvCE5BBnSNYTlwspdQ4p',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'admin/main/index',
                'login' => 'admin/main/login',
                'logout' => 'admin/main/logout',
                'profile' => 'admin/profile/index',
            ],
        ],
        'user' => [
            'identityClass' => '\yz\admin\models\User',
            'enableAutoLogin' => false,
            'loginUrl' => ['admin/main/login'],
            'on afterLogin' => ['\yz\admin\models\User', 'onAfterLoginHandler'],
        ],
        'authManager' => [
            'class' => \yz\admin\components\AuthManager::class,
        ],
        'errorHandler' => [
            'errorAction' => 'admin/main/error',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@vendor/marketingsolutions/yz2-admin' => '@app/themes/yz2-admin',
                    '@vendor/marketingsolutions/loyalty-prizes-payments' => '@app/themes/prizes-payments',
                ]
            ]
        ]
    ],
    'modules' => [
        'admin' => [
            'class' => \yz\admin\Module::class,
            'allowLoginViaToken' => true,
        ],
        'filemanager' => [
            'class' => \yz\admin\elfinder\Module::class,
            'roots' => [
                [
                    'baseUrl' => '@frontendWeb',
                    'basePath' => '@frontendWebroot',
                    'path' => 'media/uploads',
                    'name' => 'Файлы на сайте',
                ]
            ]
        ],
        'mailing' => [
            'class' => \yz\admin\mailer\backend\Module::class,
        ],
        /**
         * Loyalty modules
         */
        'identity' => [
            'class' => ms\loyalty\identity\phones\backend\Module::class,
        ],
        'sales' => [
            'class' => modules\sales\backend\Module::class,
            'documentsRequired' => false,
            'useValidationRules' => false,
        ],
        'manual' => [
            'class' => \modules\manual\backend\Module::class,
        ],
        'profiles' => [
            'class' => \modules\profiles\backend\Module::class,
        ],
        'catalog' => [
            'class' => \ms\loyalty\catalog\backend\Module::class,
        ],
        'spent' => [
            'class' => \modules\spent\backend\Module::class
        ],
        'interim' => [
            'class' => \modules\interim\backend\Module::class
        ],
        'feedback' => [
            'class' => \ms\loyalty\feedback\backend\Module::class,
        ],
        'payments' => [
            'class' => \ms\loyalty\prizes\payments\backend\Module::class,
        ],
        'sms' => [
            'class' => modules\sms\backend\Module::class,
        ],
        'finances' => [
            'class' => \ms\loyalty\finances\backend\Module::class,
        ],
        'reports' => [
            'class' => \ms\loyalty\reports\backend\Module::class,
            'reports' => [
                \backend\reports\Feedback::class,
                \backend\reports\ProfilesGroup::class,
                \backend\reports\Bonuses::class,
                \ms\loyalty\catalog\backend\reports\OrdersStatGroup::class,
                \backend\reports\SalesStat::class,
                \backend\reports\CommissionStat::class,
            ]
        ],
        'taxes' => [
            'class' => \ms\loyalty\taxes\backend\Module::class,
        ],
        'news' => [
            'class' => \modules\news\backend\Module::class,
        ],
        'api' => [
            'class' => \ms\loyalty\api\backend\Module::class,
        ],
        'checker' => [
            'class' => \ms\loyalty\checker\backend\Module::class,
        ],
    ],
    'params' => [

    ],
];


