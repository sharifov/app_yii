<?php

return [
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'bootstrap' => ['globalAccessComponent'],
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
            ],
        ],
        'user' => [
            'identityClass' => \ms\loyalty\identity\phones\common\models\Identity::class,
            'loginUrl' => ['/identity/auth/login'],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
		'view' => [
			'theme' => [
				'pathMap' => [
					'@vendor/marketingsolutions/loyalty-identity-phones' => '@frontend/themes/identity',
                    '@vendor/marketingsolutions/loyalty-certificates-catalog' => '@frontend/themes/loyalty-certificates-catalog',
				]
			]
		]
    ],
    'modules' => [
        /**
         * Loyalty modules
         */
        'profiles' => [
            'class' => modules\profiles\frontend\Module::class,
        ],
        'identity' => [
            'class' => ms\loyalty\identity\phones\frontend\Module::class,
        ],
        'sales' => [
            'class' => modules\sales\frontend\Module::class,
			'documentsRequired' => true,
			'useValidationRules' => true,
        ],
        'catalog' => [
            'class' => ms\loyalty\catalog\frontend\Module::class,
        ],
        'feedback' => [
            'class' => ms\loyalty\feedback\frontend\Module::class,
        ],
        'taxes' => [
            'class' => \ms\loyalty\taxes\frontend\Module::class,
        ],
        'news' => [
            'class' => \modules\news\frontend\Module::class,
        ],
        'payments' => [
            'class' => ms\loyalty\prizes\payments\frontend\Module::class,
        ],
    ],
    'params' => [

    ],
];
