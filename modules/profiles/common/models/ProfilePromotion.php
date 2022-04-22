<?php

namespace modules\profiles\common\models;

use modules\sales\common\models\Promotion;
use Yii;
use yii\db\Expression;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "kr_promotions_profiles".
 *
 * @property integer $id
 * @property integer $promotion_id
 * @property integer $profile_id
 *
 * @property Profile $profile
 * @property Promotion $promotion
 */
class ProfilePromotion extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Profile Promotion';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Profile Promotions';
    }

    public static function updatePromotionsForProfile(Profile $profile)
    {
        if ($profile->sync_with_dealers_promotions == false) {
            return;
        }

        $transaction = self::getDb()->beginTransaction();

        self::deleteAll(['profile_id' => $profile->id]);

        $data = DealerPromotion::find()
            ->select([
                'profile_id' => new Expression(':id', [
                    ':id' => $profile->id,
                ]),
                'promotion_id',
            ])
            ->where([
                'dealer_id' => $profile->getDealers()->select('id'),
            ])
            ->asArray()
            ->all();

        self::getDb()->createCommand()->batchInsert(self::tableName(), ['profile_id', 'promotion_id'], $data)->execute();

        $transaction->commit();

        $profile->refresh();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotions_profiles}}';
    }

    public static function updatePromotionsByDealer(Dealer $dealer)
    {
        $idsQuery = $dealer->getProfiles()
            ->andWhere(['sync_with_dealers_promotions' => 1])
            ->select('id');

        $profileIds = $idsQuery->column();

        if (count($profileIds) == 0) {
            return;
        }

        $transaction = self::getDb()->beginTransaction();

        self::deleteAll(['profile_id' => $idsQuery]);

        $promotionIds = $dealer->getPromotions()
            ->select('id')
            ->column();

        $data = [];
        foreach ($profileIds as $profileId) {
            foreach ($promotionIds as $promotionId) {
                $data[] = ['profile_id' => $profileId, 'promotion_id' => $promotionId];
            }
        }

        self::getDb()->createCommand()->batchInsert(self::tableName(), ['profile_id', 'promotion_id'], $data)->execute();

        $transaction->commit();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['promotion_id', 'integer'],
            ['profile_id', 'integer'],
            [['profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profile::className(), 'targetAttribute' => ['profile_id' => 'id']],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotion::className(), 'targetAttribute' => ['promotion_id' => 'id']]
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
    public function getProfile()
    {
        return $this->hasOne(Promotion::className(), ['id' => 'profile_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(SalesPromotions::className(), ['id' => 'promotion_id']);
    }
}
