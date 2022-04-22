<?php

return [
    'components' => [
        'cache' => [
            'class' => 'yii\caching\DummyCache',
            //'class' => 'yii\caching\FileCache',
            //'class' => 'yii\caching\MemCache',
        ],
		'mailer'   => [
			'class'            => 'yii\swiftmailer\Mailer',
			'useFileTransport' => false,
			'transport'        => [
				'class'      => 'Swift_SmtpTransport',
				'host'       => 'smtp.gmail.com',
				'username'   => 'kraskibonus@gmail.com',
				'password'   => 'ijoijoj123',
				'port'       => '587',
				'encryption' => 'tls',
			],
		],
    ]
];