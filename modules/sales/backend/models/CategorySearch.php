<?php

namespace modules\sales\backend\models;

use modules\sales\common\models\Category;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CategorySearch represents the model behind the search form about `modules\sales\common\models\Category`.
 */
class CategorySearch extends Category
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'created_at', 'updated_at'], 'safe'],
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
        $query = Category::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['between', 'created_at', Yii::$app->formatter->asDate($this->created_at, 'YYYY-MM-d 00:00:00'), Yii::$app->formatter->asDate($this->created_at, 'YYYY-MM-d 23:59:59')])
            ->andFilterWhere(['between', 'updated_at', Yii::$app->formatter->asDate($this->updated_at, 'YYYY-MM-d 00:00:00'), Yii::$app->formatter->asDate($this->updated_at, 'YYYY-MM-d 23:59:59')]);

        return $dataProvider;
    }
}
