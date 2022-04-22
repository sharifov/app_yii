<?php

namespace modules\sales\common\app\controllers\api;

use modules\sales\common\models\Brand;
use modules\sales\common\models\Product;
use modules\sales\common\models\PromotionBrand;
use modules\sales\frontend\base\ApiController;
use yii\db\Query;
use yii\web\NotFoundHttpException;

/**
 * Class ProductsController
 */
class ProductsController extends ApiController
{
    public function actionIndex($typeId, $categoryId, $promotionId)
    {
		$brands = (new Query())
			->select('b.id')
			->from(['b' => Brand::tableName()])
			->innerJoin(['pb' => PromotionBrand::tableName()], 'pb.brand_id = b.id')
			->where('pb.promotion_id = ' . $promotionId)
			->all();

        $query = Product::find()->where(['type_id' => $typeId, 'category_id' => $categoryId]);

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

    public function actionView($id)
    {
        return $this->loadModel($id);
    }

    public function actionCalculateBonuses($id, $quantity)
    {
        $product = $this->loadModel($id);

        return $product->bonusesCalculator->calculateForLocalQuantity($quantity);
    }

    /**
     * @param $id
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function loadModel($id)
    {
        if (($model = Product::findOne($id)) === null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}