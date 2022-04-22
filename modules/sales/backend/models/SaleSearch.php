<?php

namespace modules\sales\backend\models;

use modules\profiles\common\models\Dealer;
use modules\sales\common\models\Sale;
use modules\sales\common\sales\statuses\StatusManager;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yz\admin\behaviors\DateRangeFilteringBehavior;
use yz\admin\search\SearchModelEvent;
use yz\admin\search\SearchModelInterface;

/**
 * SaleSearch represents the model behind the search form about `modules\sales\common\models\Sale`.
 */
class SaleSearch extends Sale implements SearchModelInterface
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bonuses'], 'integer'],
            [['status', 'created_at', 'updated_at', 'sold_on', 'approved_by_admin_at', 'bonuses_paid_at'], 'safe'],
            [['created_at_range', 'updated_at_range', 'sold_on_range', 'dealer', ' bonuses_paid_at_range'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => DateRangeFilteringBehavior::class,
                'attributes' => [
                    'created_at' => 'sale.created_at',
                    'updated_at' => 'sale.updated_at',
                    'bonuses_paid_at' => 'sale.bonuses_paid_at',
                    'sold_on' => 'sale.sold_on',
                ]
            ]
        ]);
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

        foreach (\Yii::$app->user->identity->roles as $role) {
            if ($role->name == StatusManager::ROLE_REGIONAL_MANAGER) {
                $dealerIds = (new Query())
                    ->select('d.id')
                    ->from(['d' => Dealer::tableName()])
                    ->where(['d.admin_user_id' => \Yii::$app->user->identity->id])
                    ->column();

                $query->andWhere(['in', 'sale.dealer_id', $dealerIds]);
            }
        }

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
    protected function prepareQuery()
    {
        $query = Sale::find();
        $query->from(['sale' => self::tableName()]);

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
        ]);

        return $dataProvider;
    }

    /**
     * @param ActiveQuery $query
     */
    protected function prepareFilters($query)
    {
        $query->andFilterWhere([
            'sale.id' => $this->id,
            'sale.bonuses' => $this->bonuses,
        ]);

        $query->andFilterWhere(['like', 'sale.status', $this->status]);
    }
}
