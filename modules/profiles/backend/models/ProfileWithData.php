<?php

namespace modules\profiles\backend\models;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use yz\admin\search\WithExtraColumns;
use yii\db\ActiveQuery;
use marketingsolutions\finance\models\Purse;


/**
 * Class ProfileWithData
 */
class ProfileWithData extends Profile
{
    public $dr;
    public $bonuses_in;
    public $bonuses_eps;
    public $bonuses_yandex;
    public $bonuses_kiwi;
    public $bonuses_mobile;
    public $bonuses_ozon;

    use WithExtraColumns;

    protected static function extraColumns()
    {
        return [
            'purse__balance',
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), static::extraColumns());
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'purse__balance' => 'Баланс',
        ]);
    }


    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        return parent::find()
            ->select(static::selectWithExtraColumns(['profile.*']))
            ->from(['profile' => self::tableName()])
            ->joinWith(['purse' => function (ActiveQuery $query) {
                $query->from(['purse' => Purse::tableName()]);
            }]);
    }
}