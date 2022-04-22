<?php

namespace modules\sales\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yz\admin\search\SearchModelEvent;
use yz\admin\search\SearchModelInterface;
use modules\sales\common\models\FinanceTransactions;

/**
 * FinanceTransactionsSearch represents the model behind the search form about `modules\sales\common\models\FinanceTransactions`.
 */
class FinanceTransactionsSearch extends FinanceTransactions implements SearchModelInterface

{
    public $adminName;
    public $profileTableName;
    public $profileTablePhone;
    public $dealerTableName;
    public $promoTableName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'purse_id', 'balance_before', 'amount', 'balance_after', 'partner_id'], 'integer'],
            [['type', 'partner_type', 'title', 'comment', 'created_at', 'adminName' , 'profileTableName' , 'profileTablePhone', 'dealerTableName' , 'promoTableName'], 'safe'],
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
        return FinanceTransactions::find()
            ->leftJoin('kr_admin_users' , 'kr_finance_transactions.admin_id=kr_admin_users.id')
            ->leftJoin('kr_finance_purses' , 'kr_finance_purses.id=kr_finance_transactions.purse_id')
            ->leftJoin('kr_profiles' , 'kr_profiles.id=kr_finance_purses.owner_id')
            ->leftJoin('kr_dealers' , 'kr_dealers.id=kr_finance_purses.owner_id')
            ->leftJoin('kr_sales_promotions' , 'kr_sales_promotions.id=kr_finance_purses.promotion_id')
            ->where('kr_finance_transactions.admin_id is not NULL')
            ->andWhere("kr_finance_transactions.created_at >'2017-05-01 00:00:00'")
            ->andWhere("kr_finance_transactions.amount <> 0");
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
        ]);
        return $dataProvider;
    }

    /**
     * @param ActiveQuery $query
     */
    protected function prepareFilters($query)
    {
        $query->andFilterWhere([
            'id' => $this->id,
            'purse_id' => $this->purse_id,
            'balance_before' => $this->balance_before,
            'amount' => $this->amount,
            'balance_after' => $this->balance_after,
            'partner_id' => $this->partner_id,
        ]);

        $query->andFilterWhere(['like', 'kr_finance_transactions.type', $this->type])
            ->andFilterWhere(['like', 'partner_type', $this->partner_type])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'comment', $this->comment]);
            //->andFilterWhere(['between', 'created_at', Yii::$app->formatter->asDate($this->created_at, 'YYYY-MM-d 00:00:00'), Yii::$app->formatter->asDate($this->created_at, 'YYYY-MM-d 23:59:59')]);
        if($this->adminName)
            $query->andFilterWhere(['like', 'kr_admin_users.name', $this->adminName]);
        if($this->profileTableName)
            $query->andFilterWhere(['like', 'kr_profiles.full_name', $this->profileTableName]);
        if($this->profileTablePhone)
            $query->andFilterWhere(['like', 'kr_profiles.phone_mobile', $this->profileTablePhone]);
        if($this->dealerTableName)
            $query->andFilterWhere(['like', 'kr_dealers.name', $this->dealerTableName]);
        if($this->promoTableName)
            $query->andFilterWhere(['like', 'kr_sales_promotions.name', $this->promoTableName]);




    }
}
