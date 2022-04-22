<?php

namespace modules\profiles\backend\base;
use yii\filters\AccessControl;
use yii\rest\ActiveController;


/**
 * Class ApiController
 */
class ApiController extends ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ]
            ]
        ];

        unset($behaviors['authenticator']);

        return $behaviors;
    }

}