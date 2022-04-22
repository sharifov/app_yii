<?php

namespace backend\models;
use ms\loyalty\catalog\backend\models\CatalogOrderSearch;
use yz\admin\search\WithExtraColumns;
use ms\loyalty\catalog\common\models\CatalogOrder;
use modules\profiles\common\models\Profile;


/**
 * Class CatalogOrderWithProfileSearch
 */
class CatalogOrderWithProfileSearch extends CatalogOrderSearch
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
            ->select(self::selectWithExtraColumns([
                'catalogOrder.*',
            ]))
            ->leftJoin(['profile' => Profile::tableName()], 'profile.id = catalogOrder.user_id');

        return $query;
    }

    protected function prepareFilters($query)
    {
        parent::prepareFilters($query);

        self::filtersForExtraColumns($query);
    }
}