<?php

namespace modules\profiles\frontend\models;
use modules\profiles\common\models\Profile;
use modules\profiles\common\models\SalesPoint;


/**
 * Class RegistrationProfile
 */
class RegistrationProfile extends Profile
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['first_name', 'required'],
            ['last_name', 'required'],
            ['email', 'required'],
            ['position', 'required'],
            ['sales_point_name', 'required'],
        ]);
    }

    public static function getSalesPointIdValues()
    {
        return SalesPoint::find()->indexBy('id')->select(['name' => 'CONCAT(name, " (", address, ")")', 'id'])->column();
    }


}