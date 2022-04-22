<?php

namespace modules\profiles\common\models;

use Yii;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "kr_dealers_profiles".
 *
 * @property integer $id
 * @property integer $dealer_id
 * @property integer $profile_id
 *
 * @property Profile $profile
 * @property Dealer $dealer
 */
class DealerProfile extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dealers_profiles}}';
    }

	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Dealer Profile';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Dealer Profiles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['dealer_id', 'integer'],
            ['profile_id', 'integer'],
            [['profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profile::className(), 'targetAttribute' => ['profile_id' => 'id']],
            [['dealer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dealer::className(), 'targetAttribute' => ['dealer_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dealer_id' => 'Dealer ID',
            'profile_id' => 'Profile ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealer::className(), ['id' => 'dealer_id']);
    }
}
