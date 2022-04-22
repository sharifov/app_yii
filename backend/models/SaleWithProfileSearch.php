<?php

namespace backend\models;

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\backend\models\SaleSearch;
use modules\sales\common\models\Sale;
use yz\admin\search\WithExtraColumns;


/**
 * Class SaleWithProfileSearch
 */
class SaleWithProfileSearch extends SaleSearch
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


    protected function prepareQuery()
    {
        $query = static::find();

        $query
            ->select(self::selectWithExtraColumns([
                'sale.*',
            ]))
            ->from(['sale' => Sale::tableName()])
            ->leftJoin(['dealer' => Dealer::tableName()], 'dealer.id = sale.dealer_id')
			->leftJoin(['profile' => Profile::tableName()], "profile.role = 'manager' AND profile.dealer_id = sale.dealer_id");

        return $query;
    }

    protected function prepareFilters($query)
    {
        parent::prepareFilters($query);

        self::filtersForExtraColumns($query);
    }
}