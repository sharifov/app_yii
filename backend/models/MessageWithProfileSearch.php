<?php

namespace backend\models;
use modules\profiles\common\models\Profile;
use ms\loyalty\feedback\backend\models\MessageSearch;
use ms\loyalty\feedback\common\models\Message;
use yz\admin\search\WithExtraColumns;


/**
 * Class MessageWithProfileSearch
 */
class MessageWithProfileSearch extends MessageSearch
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


    protected function prepareQuery()
    {
        $query = static::find();

        $query
            ->select(self::selectWithExtraColumns([
                'message.*',
            ]))
            ->from(['message' => Message::tableName()])
            ->leftJoin(['profile' => Profile::tableName()], 'profile.id = message.user_id');

        return $query;
    }

    protected function prepareFilters($query)
    {
        parent::prepareFilters($query);

        self::filtersForExtraColumns($query);
    }
}