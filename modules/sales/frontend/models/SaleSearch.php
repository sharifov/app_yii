<?php

namespace modules\sales\frontend\models;

use modules\sales\common\models\Sale;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SaleSearch represents the model behind the search form about `modules\sales\common\models\Sale`.
 */
class SaleSearch extends Sale
{
    /**
     * @var int
     */
    public $dealer_id;
    /**
     * @var int
     */
    public $promotion_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bonuses'], 'integer'],
            [['status', 'created_at', 'updated_at'], 'safe'],
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
        $query = Sale::find();
        $query->andWhere([
            'dealer_id' => $this->dealer_id,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'bonuses' => $this->bonuses,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
