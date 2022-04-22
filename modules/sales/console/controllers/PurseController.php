<?php

namespace modules\sales\console\controllers;

use console\base\Controller;
use modules\profiles\common\models\Dealer;
use modules\sales\common\models\Promotion;
use modules\sales\common\models\SaleDocument;
use yii\db\Query;

/**
 * Class PurseController
 */
class PurseController extends Controller
{
    public function actionAddPromotion()
    {
        $query = Dealer::find();

        foreach ($query->each() as $dealer) {
            /** @var Dealer $dealer */
            $purse = $dealer->purse;

            $promotion_id = (new Query())
                ->select('promotion_id')
                ->from('{{%dealers_promotions}}')
                ->where(['{{%dealers_promotions}}.dealer_id' => $dealer->id]);

            /** @var Promotion $promotion */
            $promotion = Promotion::findOne($promotion_id);

            $purse->promotion_id = $promotion->id;
            $purse->title = "Счет дилера #{$dealer->id} ({$dealer->name}) по акции #{$promotion->id} ({$promotion->name})";
            $purse->updateAttributes(['promotion_id', 'title']);
        }
    }
}