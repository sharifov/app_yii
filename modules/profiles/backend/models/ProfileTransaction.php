<?php

namespace modules\profiles\backend\models;
use modules\profiles\common\models\Profile;
use marketingsolutions\finance\models\Transaction;
use yii\db\ActiveQuery;


/**
 * Class ProfileTransaction
 * @property Profile $profile
 */
class ProfileTransaction extends Transaction
{
    /**
     * @return ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['id' => 'owner_id'])
            ->via('purse');
    }
}