<?php

namespace ms\loyalty\bonuses\manual;

use yii\base\Application;
use yii\base\BootstrapInterface;


/**
 * Class Bootstrap
 */
class Bootstrap implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        #\Yii::$app->params['yii.migrations'][] = '@ms/loyalty/bonuses/manual/migrations';
    }
}