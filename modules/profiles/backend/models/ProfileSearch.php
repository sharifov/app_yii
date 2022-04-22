<?php

namespace modules\profiles\backend\models;

use modules\profiles\common\models\DealerProfile;
use modules\profiles\common\models\ProfilePromotion;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yz\admin\search\SearchModelEvent;
use yz\admin\search\SearchModelInterface;

/**
 * ProfileSearch represents the model behind the search form about `modules\profiles\common\models\Profile`.
 */
class ProfileSearch extends ProfileWithData implements SearchModelInterface
{
    public $dealer_id;
    public $promotion_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['first_name', 'last_name', 'middle_name', 'full_name'], 'safe'],
            [['phone_mobile', 'email', 'created_at', 'updated_at', 'role', 'status', 'sales_point_name', 'position'], 'safe'],
            [['dealer_id', 'promotion_id'], 'integer'],
            [static::extraColumns(), 'safe'],
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
    protected function prepareQuery()
    {
        $query = $this->getQuery();
        return $query;
    }

    /**
     * @return ActiveQuery
     */
    protected function getQuery()
    {
        return ProfileWithData::find();
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider($query)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }

    /**
     * @param ActiveQuery $query
     */
    protected function prepareFilters($query)
    {
        $query->andFilterWhere([
            'profile.id' => $this->id,
        ]);

        if (!empty($this->dealer_id)) {
            $query->andFilterWhere([
                'profile.id' => DealerProfile::find()
                    ->select('profile_id')
                    ->andFilterWhere([
                        'dealer_id' => $this->dealer_id,
                    ])
            ]);
        }

        if (!empty($this->promotion_id)) {
            $query->andFilterWhere([
                'profile.id' => ProfilePromotion::find()
                    ->select('profile_id')
                    ->andFilterWhere([
                        'promotion_id' => $this->promotion_id,
                    ])
            ]);
        }

        $query->andFilterWhere(['like', 'profile.first_name', $this->first_name])
            ->andFilterWhere(['like', 'profile.last_name', $this->last_name])
            ->andFilterWhere(['like', 'profile.sales_point_name', $this->sales_point_name])
            ->andFilterWhere(['like', 'profile.position', $this->position])
            ->andFilterWhere(['like', 'profile.role', $this->role])
            ->andFilterWhere(['like', 'profile.status', $this->status])
            ->andFilterWhere(['like', 'profile.middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'profile.full_name', $this->full_name])
            ->andFilterWhere(['like', 'profile.phone_mobile', $this->phone_mobile])
            ->andFilterWhere(['like', 'profile.email', $this->email]);

        static::filtersForExtraColumns($query);
    }
}
