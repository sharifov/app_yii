<?php

namespace modules\sales\backend\models;

use modules\sales\common\models\Type;
use modules\sales\common\models\Category;
use modules\sales\common\models\Brand;
use modules\sales\common\models\Product;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * ProductSearch represents the model behind the search form about `modules\sales\common\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'category_id', 'brand_id'], 'integer'],
            [['name', 'packing'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find();
        $query
            ->joinWith(['type' => function (ActiveQuery $query) {
                $query->from(['type' => Type::tableName()]);
            }])
            ->joinWith(['category' => function (ActiveQuery $query) {
                $query->from(['category' => Category::tableName()]);
            }])
            ->joinWith(['brand' => function (ActiveQuery $query) {
                $query->from(['brand' => Brand::tableName()]);
            }]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%sales_products}}.id' => $this->id,
            '{{%sales_products}}.type_id' => $this->type_id,
            '{{%sales_products}}.category_id' => $this->category_id,
            '{{%sales_products}}.brand_id' => $this->brand_id,
        ]);

        $query->andFilterWhere(['like', '{{%sales_products}}.name', $this->name]);

        return $dataProvider;
    }
}
