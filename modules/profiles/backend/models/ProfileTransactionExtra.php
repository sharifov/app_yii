<?php


namespace modules\profiles\backend\models;


use marketingsolutions\finance\models\Purse;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\DealerProfile;
use modules\profiles\common\models\Profile;
use yii\db\Query;
use yz\admin\search\WithExtraColumns;

class ProfileTransactionExtra extends ProfileTransactionSearch
{
    use WithExtraColumns;

    /**
     * @var string
     */
    public $dealerNames;


    protected static function extraColumns()
    {
        return [
            'purse__balance',
            'purse__owner_id',
            'profile__full_name',
            'profile__phone_mobile',
        ];
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [self::extraColumns(), 'safe'],
            ['dealerNames', 'safe'],
        ]);
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), static::extraColumns());
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'purse__balance' => 'Баланс',
            'profile__full_name' => 'Участник',
            'profile__phone_mobile' => 'Номер телефона',
            'profile__partner_division' => 'Подразделение',
            'dealerNames' => 'Дилеры'
        ]);
    }

    protected function prepareQuery()
    {
        $query = static::find()
            ->select(self::selectWithExtraColumns(['transaction.*'])
                + [
                    'dealerNames' => (new Query())
                        ->select("GROUP_CONCAT(DISTINCT d.name ORDER BY d.name ASC SEPARATOR '; ')")
                        ->from(['prs' => Purse::tableName()])
                        ->leftJoin(['p' => Profile::tableName()], "prs.owner_id = p.id")
                        ->leftJoin(['dp' => DealerProfile::tableName()], "p.id = dp.profile_id")
                        ->leftJoin(['d' => Dealer::tableName()], "dp.dealer_id = d.id")
                        ->where("transaction.purse_id = prs.id")
                        ->andWhere(['prs.owner_type' => Profile::class])
                        ->groupBy("prs.id")
                ]
            )
            ->from(['transaction' => ProfileTransaction::tableName()])
            ->leftJoin(['purse' => Purse::tableName()], 'transaction.purse_id = purse.id')
            ->leftJoin(['profile' => Profile::tableName()], 'purse.owner_id = profile.id')
            ->where(['purse.owner_type' => Profile::class])
        ;

        return $query;
    }

    protected function prepareFilters($query)
    {
        parent::prepareFilters($query);

        $query->andFilterHaving(['like', 'dealerNames', $this->dealerNames]);

        self::filtersForExtraColumns($query);
    }
}