<?php

namespace modules\profiles\backend\models;

use marketingsolutions\finance\models\Transaction;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yz\admin\search\SearchModelEvent;
use yz\admin\search\SearchModelInterface;

/**
 * ActiveProfilesSearch represents the model behind the search form about `modules\profiles\common\models\Profile`.
 */
class ActiveProfilesSearch extends ProfileWithData implements SearchModelInterface
{
    protected $types = ['ms\loyalty\prizes\payments\common\finances\PaymentPartner', 'ms\loyalty\catalog\common\models\CatalogOrder'];

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
     * @return Query
     */
    protected function prepareQuery()
    {
        return $this->getQuery();
    }

    /**
     * @return Query
     */
    protected function getQuery()
    {
            return (new Query())
                ->select([
                    'monthYear' => 'transactions.monthYear',
                    'users_count'        => 'transactions.users_count',
                    'bonuses'   => 'transactions.bonuses',
                ])
                ->from([
                    'transactions' => (new Query())
                        ->select([
                            'monthYear' => "DATE_FORMAT(t.created_at, '%m %Y')",
                            'users_count'        => 'SUM(t.amount)',
                            'bonuses'   => 'SUM(t.amount)'])
                        ->from(['t' => Transaction::tableName()])
                        ->where(['t.partner_type' => $this->types, 't.type' => Transaction::OUTBOUND])
                        ->andWhere(['not', ['t.purse_id' => 4]])
                        ->groupBy('monthYear')
                ]);
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
        static::filtersForExtraColumns($query);
    }
}
