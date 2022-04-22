<?php

namespace backend\models;
use modules\profiles\backend\models\ProfileSearch;
use yii\db\ActiveQuery;
use ms\loyalty\identity\phones\common\models\Identity;


/**
 * Class ProfileWithIdentitySearch
 */
class ProfileWithIdentitySearch extends ProfileSearch
{
    protected static function extraColumns()
    {
        return array_merge(parent::extraColumns(), [
//            'identity__is_registered',
            'identity__created_at',
        ]);
    }

    public static function find()
    {
        return parent::find()
            ->leftJoin(['identity' => Identity::tableName()], 'identity.id = profile.identity_id');
    }

    protected function getQuery()
    {
        return static::find();
    }


}