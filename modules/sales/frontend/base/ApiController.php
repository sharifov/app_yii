<?php

namespace modules\sales\frontend\base;

use yii\filters\AccessControl;
use yii\rest\Controller;


/**
 * Class ApiController
 */
class ApiController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors = array_merge($behaviors, [
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
        ]);

        unset($behaviors['authenticator']);

        return $behaviors;
    }


    public function beforeAction($action)
    {
        \Yii::$app->request->parsers['application/json'] = 'yii\web\JsonParser';
        return parent::beforeAction($action);
    }
}