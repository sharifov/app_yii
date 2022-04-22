<?php

namespace backend\models;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\DealerProfile;
use ms\loyalty\catalog\backend\models\CatalogOrderSearch;
use ms\loyalty\prizes\payments\backend\models\PaymentSearch;
use yz\admin\search\WithExtraColumns;
use ms\loyalty\catalog\common\models\CatalogOrder;
use modules\profiles\common\models\Profile;


/**
 * Class PaymentWithProfileSearch
 */
class PaymentWithProfileSearch extends PaymentSearch
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
            'profile__full_name',
            'profile__phone_mobile',
            'dealer__name',
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), self::extraColumns());
    }

    public function getQuery()
    {
        return static::find();
    }


    protected function prepareQuery()
    {
        $query = parent::prepareQuery();

        $query
            ->select(self::selectWithExtraColumns(['payment.*']))
            ->leftJoin(['profile' => Profile::tableName()], 'profile.id = payment.recipient_id')
            ->leftJoin(['dp' => DealerProfile::tableName()], 'dp.profile_id = profile.id')
            ->leftJoin(['dealer' => Dealer::tableName()], 'dealer.id = dp.dealer_id')
        ;
        return $query;
    }

    protected function prepareFilters($query)
    {
        parent::prepareFilters($query);

        self::filtersForExtraColumns($query);
    }
}