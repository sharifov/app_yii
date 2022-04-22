<?php

namespace modules\sales\common\app\controllers\api;

use modules\sales\common\models\Brand;
use modules\sales\common\models\PromotionBrand;
use modules\sales\frontend\base\ApiController;
use yii\db\Query;

/**
 * Class BrandsController
 */
class BrandsController extends ApiController
{
    public function actionIndex($promotionId)
    {
		$brands = (new Query())
			->select('b.id, b.name')
			->from(['b' => Brand::tableName()])
			->innerJoin(['pb' => PromotionBrand::tableName()], 'pb.brand_id = b.id')
			->where(['pb.promotion_id' => $promotionId])
			->orderBy(['b.name' => SORT_ASC])
			->all();

		return $brands;
    }
}