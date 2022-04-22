<?php

namespace modules\profiles\backend\controllers\api;
use modules\profiles\common\models\SalesPoint;
use yii\rest\Controller;
use modules\profiles\backend\base\ApiController;


/**
 * Class SalesPointsController
 */
class SalesPointsController extends ApiController
{
    public $modelClass = SalesPoint::class;

    public function actions()
    {
        $actions = parent::actions();

        unset(
            $actions['create'],
            $actions['update'],
            $actions['delete']
        );

        return $actions;
    }


}