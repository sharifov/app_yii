<?php

namespace modules\sales\common\app\controllers\api;

use modules\sales\common\models\Brand;
use modules\sales\common\models\Category;
use modules\sales\common\models\Product;
use modules\sales\common\models\PromotionBrand;
use modules\sales\frontend\base\ApiController;
use yii\db\Query;

/**
 * Class CategoriesController
 */
class CategoriesController extends ApiController
{
    public function actionIndex($typeId, $promotionId)
    {
		$brands = (new Query())
			->select('b.id')
			->from(['b' => Brand::tableName()])
			->innerJoin(['pb' => PromotionBrand::tableName()], 'pb.brand_id = b.id')
			->where('pb.promotion_id = ' . $promotionId)
			->all();

        $query = (new Query())
            ->select('c.*')
            ->from(['c' => Category::tableName()])
            ->innerJoin(['p' => Product::tableName()], 'p.category_id = c.id')
            ->where(['p.type_id' => $typeId])
            ->groupBy('c.id');

		if (!empty($brands)) {
			$query->andWhere(['p.brand_id' => (new Query())
				->select('b.id')
				->from(['b' => Brand::tableName()])
				->innerJoin(['pb' => PromotionBrand::tableName()], 'pb.brand_id = b.id')
				->where('pb.promotion_id = ' . $promotionId)
			]);
		}

		return $query->all();
    }
}