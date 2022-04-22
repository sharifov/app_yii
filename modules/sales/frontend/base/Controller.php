<?php

namespace modules\sales\frontend\base;

use yii\filters\AccessControl;


/**
 * Class Controller
 */
class Controller extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                    ]
                ]
            ]
        ];
    }

}