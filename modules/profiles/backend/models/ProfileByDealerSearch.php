<?php

namespace modules\profiles\backend\models;

use Yii;


/**
 * ProfileSearch represents the model behind the search form about `modules\profiles\common\models\Profile`.
 */
class ProfileByDealerSearch extends ProfileSearch
{
    public function getQuery()
    {
        return ProfileWithData::find()->where(['dealer_id' => Yii::$app->user->profile->dealer_id]);
    }
}
