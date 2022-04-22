<?php

namespace modules\profiles\common\models;

use modules\sales\common\models\Promotion;
use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_promotions_profiles".
 *
 * @property integer $id
 * @property integer $promotion_id
 * @property integer $profile_id
 *
 * @property Promotion $promotion
 * @property Profile $profile
 */
class PromotionProfile extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotions_profiles}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Promotion Profile';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Promotion Profiles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['promotion_id', 'integer'],
            ['profile_id', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'promotion_id' => 'Promotion ID',
            'profile_id' => 'Profile ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotion::className(), ['id' => 'promotion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }
}
