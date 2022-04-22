<?php

namespace backend\models;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\DealerProfile;
use modules\profiles\common\models\Profile;
use ms\loyalty\catalog\backend\models\OrderedCardSearch;
use yii\db\ActiveQuery;
use yii\helpers\StringHelper;
use yz\admin\search\WithExtraColumns;
use ms\loyalty\catalog\common\models\OrderedCard;


/**
 * Class OrderedCardWithProfileSearch
 */
class OrderedCardWithProfileSearch extends OrderedCardSearch
{
    use WithExtraColumns;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [self::extraColumns(), 'safe']
        ]);
    }

    protected static function extraColumns()
    {
        return [
			'dealer__name',
            'profile__full_name',
            'profile__phone_mobile',
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), self::extraColumns());
    }

    protected function getQuery()
    {
        return static::find();
    }

    protected function prepareQuery()
    {
        $query = parent::prepareQuery();

        $query
            ->select(self::selectWithExtraColumns([
                'ordered_cards.*',
            ]))
            ->leftJoin(['profile' => Profile::tableName()], 'profile.id = catalogOrder.user_id')
            ->leftJoin(['dp' => DealerProfile::tableName()], 'dp.profile_id = profile.id')
            ->leftJoin(['dealer' => Dealer::tableName()], 'dealer.id = dp.dealer_id')
        ;

        return $query;
    }

    protected function prepareFilters($query)
    {
        $this->prepareFiltersQuery($query);

        self::filtersForExtraColumns($query);
    }

    /**
     * @param ActiveQuery $query
     */
    protected function prepareFiltersQuery($query)
    {
        $query->andFilterWhere([
            'ordered_cards.id' => $this->id,
            'ordered_cards.card_id' => $this->card_id,
            'ordered_cards.catalog_order_id' => $this->catalog_order_id,
            'zakazpodarka_order_id' => $this->zakazpodarka_order_id,
            'ordered_cards.nominal' => $this->nominal,
            'ordered_cards.quantity' => $this->quantity,
        ]);


        $query->andFilterWhere([
            'ordered_cards.status' => StringHelper::explode($this->status, ',', true, true)
        ]);

        $query
            ->andFilterWhere(['like', 'ordered_cards.type', $this->type])
            ->andFilterWhere(['like', 'zakazpodarkaOrder.zp_order_id', $this->getAttribute('zakazpodarkaOrder.zp_order_id')]);

        $query->andFilterWhere(['between', 'DATE(ordered_cards.created_at)', $this->startDate, $this->endDate]);
    }
}