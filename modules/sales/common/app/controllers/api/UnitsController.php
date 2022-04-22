<?php

namespace modules\sales\common\app\controllers\api;

use modules\sales\common\models\Unit;
use modules\sales\frontend\base\ApiController;


/**
 * Class UnitsController
 */
class UnitsController extends ApiController
{
    public function actionIndex()
    {
        return Unit::find()->all();
    }

    public function actionView($id)
    {
        return Unit::findOne($id);
    }
}