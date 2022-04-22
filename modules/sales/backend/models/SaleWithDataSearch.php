<?php

namespace modules\sales\backend\models;

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\DealerProfile;
use modules\profiles\common\models\Profile;
use modules\sales\backend\models\SaleSearch;
use modules\sales\common\models\Promotion;
use modules\sales\common\models\Sale;
use modules\sales\common\sales\statuses\Statuses;
use yz\admin\search\WithExtraColumns;


/**
 * Class SaleWithDataSearch
 */
class SaleWithDataSearch extends SaleSearch
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
            'promotion__name',
            'profile__full_name',
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
            ->joinWith('dealer dealer', true)
            ->joinWith('promotion promotion', true)
            ->joinWith('dealer.manager profile', true, 'inner join')
            ->where("sale.status <>'".Statuses::DECLINED."'");

        return $query;
    }

    protected function prepareFilters($query)
    {
        parent::prepareFilters($query);

        self::filtersForExtraColumns($query);
    }
}