<?php
define('YZ_BASE_DIR',  __DIR__.'/../../../');
define('YZ_APP_DIR', YZ_BASE_DIR . '/app');
define('YZ_VENDOR_DIR', YZ_BASE_DIR . '/app/vendor');

require(YZ_VENDOR_DIR . '/autoload.php');
require(YZ_APP_DIR . '/common/config/env.php');

defined('YII_ENV') || define('YII_ENV', getenv('YII_ENV'));
defined('YII_DEBUG') || define('YII_DEBUG', getenv('YII_DEBUG') == 'true');

require(YZ_VENDOR_DIR . '/yiisoft/yii2/Yii.php');
require(YZ_APP_DIR . '/common/config/bootstrap.php');
require(YZ_APP_DIR . '/frontend/config/bootstrap.php');

$config = \yii\helpers\ArrayHelper::merge(
    require(YZ_APP_DIR . '/common/config/main.php'),
	require(YZ_APP_DIR . '/common/config/main-'.YII_ENV.'.php'),
	require(YZ_APP_DIR . '/frontend/config/main.php'),
	require(YZ_APP_DIR . '/frontend/config/main-'.YII_ENV.'.php')
);

$application = new \frontend\base\Application($config);
$application->run();