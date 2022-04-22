<?php

namespace modules\sales\common\app\controllers\api;

use modules\profiles\common\models\Dealer;
use modules\sales\frontend\base\ApiController;
use yii\web\NotFoundHttpException;


/**
 * Class DealersController
 */
class DealersController extends ApiController
{
    public function actionIndex()
    {
        return Dealer::find()->orderBy('name')->all();
    }
}