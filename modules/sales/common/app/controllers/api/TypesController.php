<?php

namespace modules\sales\common\app\controllers\api;

use modules\sales\common\models\Brand;
use modules\sales\common\models\Product;
use modules\sales\common\models\PromotionBrand;
use modules\sales\common\models\Type;
use modules\sales\frontend\base\ApiController;
use yii\db\Query;

/**
 * Class TypesController
 */
class TypesController extends ApiController
{
    public function actionIndex($promotionId)
    {
		$brands = (new Query())
			->select('b.id')
			->from(['b' => Brand::tableName()])
			->innerJoin(['pb' => PromotionBrand::tableName()], 'pb.brand_id = b.id')
			->where('pb.promotion_id = ' . $promotionId)
			->all();

		$query = (new Query())
			->select('t.*')
			->from(['t' => Type::tableName()])
			->innerJoin(['p' => Product::tableName()], 'p.type_id = t.id')
			->groupBy('t.id');

		if (!empty($brands)) {
			$query->andWhere(['brand_id' => (new Query())
				->select('b.id')
				->from(['b' => Brand::tableName()])
				->innerJoin(['pb' => PromotionBrand::tableName()], 'pb.brand_id = b.id')
				->where('pb.promotion_id = ' . $promotionId)
			]);
		}

		return $query->all();
    }
}