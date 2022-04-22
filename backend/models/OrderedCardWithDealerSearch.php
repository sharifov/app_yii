<?php

namespace backend\models;


use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\DealerProfile;
use modules\profiles\common\models\Profile;
use ms\loyalty\catalog\backend\models\OrderedCardWithProfileSearch;
use yii\db\ActiveQuery;
use yii\db\Query;
use yz\admin\search\WithExtraColumns;

class OrderedCardWithDealerSearch extends OrderedCardWithProfileSearch
{
    use WithExtraColumns;

    public $dealers_names;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [self::extraColumns(), 'safe'],
            ['dealers_names', 'safe']
        ]);
    }

    protected static function extraColumns()
    {
        return [
            'profile__full_name',
            'profile__phone_mobile',
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'dealers_names' => 'Дилеры'
        ]);
    }

    /**
     * @inheritDoc
     */
    public function formName()
    {
        return 'OrderedCardSearch';
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), self::extraColumns());
    }

    protected function prepareQuery()
    {
        $query = parent::prepareQuery();

        $query
            ->select(self::selectWithExtraColumns(['ordered_cards.*'])
                + [
                    'dealers_names' => (new Query())
                        ->select("GROUP_CONCAT(DISTINCT dealer.name SEPARATOR '; ')")
                        ->from(['p' => Profile::tableName()])
                        ->leftJoin(['dp' => DealerProfile::tableName()], 'p.id = dp.profile_id')
                        ->leftJoin(['dealer' => Dealer::tableName()], 'dealer.id = dp.dealer_id')
                        ->where("catalogOrder.user_id = p.id")
                        ->groupBy('p.id')
                ]
            )
        ;

        return $query;
    }

    /**
     * @param ActiveQuery $query
     */
    protected function prepareFilters($query)
    {
        parent::prepareFilters($query);

        $query->andFilterHaving(['like', 'dealers_names', $this->dealers_names]);

        self::filtersForExtraColumns($query);
    }
}