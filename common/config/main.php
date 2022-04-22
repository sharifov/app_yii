<?php

return [
    'id' => 'yz2-app-standard',
    'language' => 'ru',
    'sourceLanguage' => 'en-US',
    'extensions' => require(YZ_VENDOR_DIR . '/yiisoft/extensions.php'),
    'vendorPath' => YZ_VENDOR_DIR,
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'bootstrap' => [
        'log',
    ],
    'components' => [
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
            'dsn' => getenv('DB_DSN'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'tablePrefix' => getenv('DB_TABLE_PREFIX'),
            'attributes' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));",
            ],
        ],
        'i18n' => [
            'translations' => [
                'common' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                ],
                'frontend' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@frontend/messages',
                    'sourceLanguage' => 'en-US',
                ],
                'backend' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en-US',
                ],
                'console' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@console/messages',
                    'sourceLanguage' => 'en-US',
                ],
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace'],
                ],
            ],
        ],
        'taxesManager' => [
            'class' => \ms\loyalty\taxes\common\components\TaxesManager::class
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'messageConfig' => [
                'from' => [getenv('MAIL_FROM') => getenv('MAIL_NAME')],
            ]
        ],
        'sms' => [
            'class' => \marketingsolutions\sms\Sms::class,
            'services' => [
                'service' => [
                    'class' => \marketingsolutions\sms\services\Smsc::class,
                    'login' => getenv('SMSC_LOGIN'),
                    'password' => getenv('SMSC_PASSWORD'),
                    'from' => getenv('SMS_FROM'),
                ]
            ]
        ],
        'financeChecker' => [
            'class' => \ms\loyalty\finances\common\components\FinanceChecker::class,
            'moneyDifferenceThreshold' => 50000,
            'emailNotificationThreshold' => 100000,
            'email' => 'alex@zakazpodarka.ru',
        ],
        'zp1c' => [
            'class' => \ms\zp1c\client\Client::class,
            'url' => 'http://5.189.160.142',
            'login' => 'WebService',
            'password' => '2Cthnbabrfnf',
        ],
        'globalAccessComponent' => [
            'class' => \common\components\GlobalAccessComponent::class
        ]
    ],
    'modules' => [
        'mailing' => [
            'class' => \yz\admin\mailer\common\Module::class,
            'mailLists' => [
                \modules\profiles\common\mailing\ProfileMailingList::class,
            ]
        ],
        /**
         * Loyalty modules
         */
        'profiles' => [
            'class' => modules\profiles\common\Module::class,
        ],
        'sales' => [
            'class' => modules\sales\common\Module::class,
        ],
        'catalog' => [
            'class' => ms\loyalty\catalog\common\Module::class,
            'classMap' => [
                'prizeRecipient' => \modules\profiles\common\models\Profile::class,
            ],
            'loyaltyName' => getenv('LOYALTY_1C_NAME'),
            'zakazpodarkaOrderDelay' => 24*60*60,
            'disableOrderingForNoTaxAccount' => false,
        ],
        'spent' => [
            'class' => \modules\spent\common\Module::class,
        ],
        'payments' => [
            'class' => ms\loyalty\prizes\payments\common\Module::class,
            'loyaltyName' => getenv('LOYALTY_1C_NAME'),
//            'commissionsForUser' => [
//                'phone' => 0.05,
//				'yandex' => 0.05,
//				'card' => 0.05,
//				'qiwi' => 0.05,
//				'webmoney' => 0.05,
//            ],
//			'commissionsForCompany' => [
//				'phone' => 0.18,
//				'yandex' => 0.18,
//				'card' => 0.18,
//				'qiwi' => 0.18,
//				'webmoney' => 0.18,
//			],
//            'companyCommissions' => [
//                new \ms\loyalty\prizes\payments\common\commission\PercentCommission(['value' => 0.18]),
//            ],
        ],
        'feedback' => [
            'class' => ms\loyalty\feedback\common\Module::class,
        ],
		'taxes' => [
			'class' => \ms\loyalty\taxes\common\Module::class,
			'documentImageUploadRequired' => false,
			'incomeTaxPaymentMethod' => \ms\loyalty\taxes\common\Module::INCOME_TAX_PAYMENT_METHOD_PROFILE,
		],
        'api' => [
            'class' => \ms\loyalty\api\common\Module::class,
            'authType' => \ms\loyalty\api\common\Module::AUTH_TOKEN,
            'hashSecret' => getenv('API_HASH_SECRET'),
            'tokenLiveMinutes' => getenv('API_TOKEN_LIVE_MINUTES'),
        ],
        'news' => [
            'class' => \modules\news\common\Module::class,
        ],
        'sms' => [
            'class' => modules\sms\common\Module::class,
        ],
        'checker' => [
            'class' => \ms\loyalty\checker\common\Module::class,
            'emails' => 'dk@msforyou.ru'
        ],
    ],
];
