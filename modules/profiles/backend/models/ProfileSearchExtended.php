<?php

namespace modules\profiles\backend\models;

use modules\profiles\common\models\DealerProfile;
use modules\profiles\common\models\Profile;
use modules\profiles\common\models\ProfilePromotion;
use ms\loyalty\finances\common\models\Transaction;
use marketingsolutions\finance\models\Purse;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yz\admin\search\SearchModelEvent;
use yz\admin\search\SearchModelInterface;

class ProfileSearchExtended extends ProfileWithData implements SearchModelInterface
{
    public $dealer_id;
    public $promotion_id;
    public $dr;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id'], 'integer'],
            [['first_name', 'last_name', 'middle_name', 'full_name', 'dr', 'phone_mobile'], 'safe'],
            [['phone_mobile', 'email', 'created_at', 'updated_at', 'role', 'status', 'sales_point_name', 'position'], 'safe'],
            [['dealer_id', 'promotion_id'], 'integer'],
            [static::extraColumns(), 'safe'],
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

        $data = \Yii::$app->request->get('ProfileSearchExtended');
        $dateRange = empty($data['dr']) ? null : $data['dr'];

        if ($dateRange) {
            list($from, $to) = explode(' - ', $dateRange);
            $to = (new \DateTime($to))->format('Y-m-d 23:59:59');
            $from = (new \DateTime($from))->format('Y-m-d 00:00:00');

            $query->select(
                static::selectWithExtraColumns(['profile.*'])
                + ['bonuses_in' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'in'")
                    ->andWhere("t.created_at > '$from'")
                    ->andWhere("t.created_at < '$to'")
                ]
                + ['bonuses_eps' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'out'")
                    ->andWhere("t.title LIKE 'Заказ ЭПС%'")
                    ->andWhere("t.created_at > '$from'")
                    ->andWhere("t.created_at < '$to'")
                ]
                + ['bonuses_yandex' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'out'")
                    ->andWhere("t.title LIKE '%Яндекс%'")
                    ->andWhere("t.created_at > '$from'")
                    ->andWhere("t.created_at < '$to'")
                ]
                + ['bonuses_mobile' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'out'")
                    ->andWhere("t.title LIKE '%Мобильный%'")
                    ->andWhere("t.created_at > '$from'")
                    ->andWhere("t.created_at < '$to'")
                ]
                + ['bonuses_ozon' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'out'")
                    ->andWhere("t.partner_type LIKE '%ozon%'")
                    ->andWhere("t.created_at > '$from'")
                    ->andWhere("t.created_at < '$to'")
                ]
            );
        }
        else {
            $query->select(
                static::selectWithExtraColumns(['profile.*'])
                + ['bonuses_in' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'in'")
                ]
                + ['bonuses_eps' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'out'")
                    ->andWhere("t.title LIKE 'Заказ ЭПС%'")
                ]
                + ['bonuses_yandex' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'out'")
                    ->andWhere("t.title LIKE '%Яндекс%'")
                ]
                + ['bonuses_mobile' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'out'")
                    ->andWhere("t.title LIKE '%Мобильный%'")
                ]
                + ['bonuses_ozon' => (new Query)
                    ->select('SUM(t.amount)')
                    ->from(['p' => Purse::tableName()])
                    ->leftJoin(['t' => Transaction::tableName()], 't.purse_id = p.id')
                    ->where(['p.owner_type' => Profile::class])
                    ->andWhere('p.owner_id = profile.id')
                    ->andWhere("t.type = 'out'")
                    ->andWhere("t.partner_type LIKE '%ozon%'")
                ]
            );
        }

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
