<?php

namespace modules\profiles\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yz\admin\search\SearchModelEvent;
use yz\admin\search\SearchModelInterface;
use modules\profiles\common\models\Nullify;

/**
 * NullifySearch represents the model behind the search form about `modules\profiles\common\models\Nullify`.
 */
class NullifySearch extends Nullify implements SearchModelInterface

{
    /**
     * @var
     */
    public $profilePhone;

    /**
     * @var
     */
    public $profileName;

    /**
     * @var
     */
    public $profileRole;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'profile_id', 'sum'], 'integer'],
            [['created_at', 'profileRole', 'profileName', 'profilePhone'], 'safe'],
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
        $query = $this->prepareQuery();
        $this->trigger(self::EVENT_AFTER_PREPARE_QUERY, new SearchModelEvent([
            'query' => $query,
        ]));

        $dataProvider = $this->prepareDataProvider($query);
        $this->trigger(self::EVENT_AFTER_PREPARE_DATA_PROVIDER, new SearchModelEvent([
            'query' => $query,
            'dataProvider' => $dataProvider,
        ]));

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $this->prepareFilters($query);
        $this->trigger(self::EVENT_AFTER_PREPARE_FILTERS, new SearchModelEvent([
            'query' => $query,
            'dataProvider' => $dataProvider,
        ]));

        return $dataProvider;
    }

    /**
     * @return ActiveQuery
     */
    protected function getQuery()
    {
        return Nullify::find()
            ->joinWith('profile');
    }

    /**
     * @return ActiveQuery
     */
    protected function prepareQuery()
    {
        $query = $this->getQuery();
        return $query;
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider($query)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder'=>[
                    'id'=>SORT_DESC
                ]
            ]
        ]);
        $sort = $dataProvider->getSort();

        $sort->attributes = array_merge($sort->attributes, [
            'profilePhone' => [
                'asc' => ['{{%profiles}}.phone_mobile' => SORT_ASC],
                'desc' => ['{{%profiles}}.phone_mobile' => SORT_DESC]
            ],
            'profileName' => [
                'asc' => ['{{%profiles}}.full_name' => SORT_ASC],
                'desc' => ['{{%profiles}}.full_name' => SORT_DESC]
            ],
            'profileRole' => [
                'asc' => ['{{%profiles}}.role' => SORT_ASC],
                'desc' => ['{{%profiles}}.role' => SORT_DESC]
            ],
        ]);
        $dataProvider->setSort($sort);

        return $dataProvider;
    }

    /**
     * @param ActiveQuery $query
     */
    protected function prepareFilters($query)
    {
        $query->andFilterWhere([
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'sum' => $this->sum,
        ]);

        $query->andFilterWhere(['like', '{{%profiles}}.phone_mobile', $this->profilePhone]);
        $query->andFilterWhere(['like', '{{%profiles}}.full_name', $this->profileName]);
        $query->andFilterWhere(['like', '{{%profiles}}.role', $this->profileRole]);

    }
}
