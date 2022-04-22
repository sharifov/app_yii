<?php

namespace modules\profiles\backend\models;

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\sales\statuses\StatusManager;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yz\admin\models\User;
use yz\admin\search\SearchModelEvent;
use yz\admin\search\SearchModelInterface;
use modules\profiles\common\models\Report;

/**
 * ReportSearch represents the model behind the search form about `modules\profiles\common\models\Report`.
 */
class ReportSearch extends Report implements SearchModelInterface

{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'file_size', 'profile_id'], 'integer'],
            [['original_name', 'name', 'created_at', 'updated_at'], 'safe'],
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

        foreach (\Yii::$app->user->identity->roles as $role) {
            if ($role->name == StatusManager::ROLE_REGIONAL_MANAGER) {
                $profileIds = (new Query())
                    ->select('p.id')
                    ->from(['p' => Profile::tableName()])
                    ->leftJoin(['d' => Dealer::tableName()], 'p.dealer_id = d.id')
                    ->where(['d.admin_user_id' => \Yii::$app->user->identity->id])
                    ->column();

                $query->andWhere(['in', 'profile_id', $profileIds]);
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
    protected function getQuery()
    {
        return Report::find();
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
        ]);
    }
}
