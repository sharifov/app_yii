<?php


namespace modules\interim\backend\models;


use Exception;
use marketingsolutions\finance\models\Purse;
use marketingsolutions\finance\models\Transaction;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\DealerProfile;
use modules\profiles\common\models\Profile;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;


class Interim extends Model
{
    /** @var string */
    public $month = null;

    /** @var integer */
    public $year = null;

    /** @var Purse[] */
    private $_purses = [];

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['year', 'integer'],
            ['month', 'string']
        ];
    }

    public function __construct($config = [])
    {
        $this->month = date("m");
        $this->year = date("Y");

        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'month' => 'Месяц',
            'year' => 'Год'
        ];
    }

    /**
     * @return array
     */
    public static function getYearList()
    {
        $months = range(2018, (int)date("Y", strtotime('now')));
        return array_combine($months, $months);
    }

    /**
     * @return array
     */
    public static function getMonthsList()
    {
        return [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь',
        ];
    }

    /**
     * @param $month
     * @return mixed
     */
    public static function getMonth($month)
    {
        return self::getMonthsList()[$month];
    }

    /**
     * @return ArrayDataProvider
     * @throws Exception
     */
    public function purses()
    {
        $pursesQuery = Purse::find()
            ->where(['owner_type' => [
                Profile::class,
                Dealer::class
            ]]);

        $this->setPurses($pursesQuery);

        $dataProvider = new ArrayDataProvider([
            'allModels' => \Yii::$app->getDb()->createCommand(
                "
                select max(dealers.`id`) dealer_id,
                       max(dealers.`name`) dealer_name,
                       max(manager.`full_name`) manager_name,
                       sum((
                         select balance.balance_after
                         from " . Transaction::tableName() . " balance
                         where DATE(balance.created_at) <= :select_date
                           and balance.purse_id = purses.id
                         order by balance.created_at desc
                         limit 1
                       )) dealer_balance,
                       '' profile_name,
                       0 profile_balance
                from " . Dealer::tableName() . " dealers
                left join " . Purse::tableName() . " purses on purses.owner_id = dealers.id
                left join " . DealerProfile::tableName() . " dealer_profiles on dealers.id = dealer_profiles.dealer_id
                left join " . Profile::tableName() . " manager on dealer_profiles.profile_id = manager.id
                where purses.owner_type = :dealer_class
                    and manager.role = :role_manager
                group by purses.owner_id
                
                union
                
                select dealer.`id` dealer_id,
                       dealer.`name` dealer_name,
                       '' manager_name,
                       0 dealer_balance,
                       profile.`full_name` profile_name,
                       (
                           select balance.balance_after
                           from " . Transaction::tableName() . " balance
                           where DATE(balance.created_at) <= :select_date
                             and balance.purse_id = purses.id
                           order by balance.created_at desc
                           limit 1
                       ) profile_balance
                from " . Profile::tableName() . " profile
                left join " . Purse::tableName() . " purses on purses.owner_id = profile.id
                left join " . DealerProfile::tableName() . " dealer_profiles on profile.id = dealer_profiles.profile_id
                left join " . Dealer::tableName() . " dealer on dealer_profiles.dealer_id = dealer.id
                where purses.owner_type = :profile_class
                    and profile.role = :role_sales
                
                order by dealer_id asc, manager_name desc;
                ", [
                    ':select_date' => $this->getDate(),
                    ':dealer_class' => Dealer::class,
                    ':profile_class' => Profile::class,
                    ':role_manager' => Profile::ROLE_MANAGER,
                    ':role_sales' => Profile::ROLE_SALES
                ]
            )->queryAll(),
            'pagination' => [
                'pageSize' => 0
            ]
        ]);

        return $dataProvider;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getDate()
    {
        $month = $this->month ?? date("m");
        $year = $this->year ?? date("Y");

        return date("Y-m-01 00:00:00", strtotime("{$year}-{$month}-01"));
    }

    /**
     * @param ActiveQuery $pursesQuery
     */
    private function setPurses(ActiveQuery $pursesQuery)
    {
        $purses = $pursesQuery->all();
        foreach ($purses as $purse) {
            /** @var Purse $purse */
            $this->_purses[$purse->id] = $purse;
        }
    }

    /**
     * @param $id
     * @return Purse
     */
    public function getPurse($id)
    {
        return $this->_purses[$id];
    }
}