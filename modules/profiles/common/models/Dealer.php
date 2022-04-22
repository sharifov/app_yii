<?php

namespace modules\profiles\common\models;

use modules\sales\common\models\Factor;
use modules\sales\common\models\Promotion;
use modules\sales\common\models\Sale;
use marketingsolutions\finance\models\Purse;
use marketingsolutions\finance\models\PurseOwnerInterface;
use marketingsolutions\finance\models\PurseOwnerTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii2tech\ar\linkmany\LinkManyBehavior;
use yz\admin\models\User;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_dealers".
 *
 * @property integer $id
 * @property string $name
 * @property integer $manager_commission
 * @property boolean $manager_commission_included
 * @property integer promotion_id @deprecated
 * @property integer $x
 * @property integer $xx
 * @property boolean $resolve_phone
 * @property boolean $resolve_purse
 * @property float $x_real
 * @property float $xx_real
 * @property integer $admin_user_id
 *
 * @property Promotion $promotion @deprecated
 * @property Promotion[] $promotions
 * @property Profile[] $profiles
 * @property Sale[] $sales
 * @property Profile $manager
 * @property Factor[] $factors
 * @property User $adminUser
 * @property Purse[] $purses
 *
 * @property array $promotionIds
 */
class Dealer extends \yii\db\ActiveRecord implements ModelInfoInterface, PurseOwnerInterface
{
    use PurseOwnerTrait;

    /** @var string */
    protected $x_real;
    /** @var  string */
    protected $xx_real;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dealers}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     *
     * @return string
     */
    public static function modelTitle()
    {
        return 'Дилер';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     *
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Дилеры';
    }

    public function behaviors()
    {
        return [
            'linkPromotions' => [
                'class' => LinkManyBehavior::class,
                'relation' => 'promotions',
                'relationReferenceAttribute' => 'promotionIds',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => 255],
            ['name', 'required'],
            ['promotionIds', 'required'],
            ['x', 'integer'],
            ['xx', 'integer'],
            ['resolve_phone', 'integer'],
            ['resolve_purse', 'integer'],
            ['x_real', 'safe'],
            ['xx_real', 'safe'],
            [['manager_commission', 'manager_commission_included'], 'safe'],
            ['admin_user_id', 'safe'],
            ['admin_user_id', 'in', 'range' => array_keys(Dealer::getManagerOptions())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Дилер',
            'manager_commission' => 'Вознаграждение руководителю в %',
            'manager_commission_included' => 'Вознаграждение будет браться из суммы',
            'promotionIds' => 'Акции',
            'x' => 'Коэффициент X * 100',
            'xx' => 'Коэффициент XX * 100',
            'x_real' => 'Коэффициент X',
            'xx_real' => 'Коэффициент XX',
            'admin_user_id' => 'Территориальный управляющий',
            'resolve_phone' => 'Разрешить всем моим участникам перевод денежных средств на телефон',
            'resolve_purse' => 'Разрешить всем моим участникам перевод денежных средств перевод на кошелек',
        ];
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::class, ['id' => 'profile_id'])
            ->viaTable(DealerProfile::tableName(), ['dealer_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAdminUser()
    {
        return $this->hasOne(User::className(), ['id' => 'admin_user_id']);
    }

    public function getFactors()
    {
        return $this->hasMany(Factor::className(), ['dealer_id' => 'id']);
    }

    /**
     * Returns purse's owner by owner's id
     *
     * @param int $id
     * @return $this
     */
    public static function findPurseOwnerById($id)
    {
        return static::findOne($id);
    }

    protected static function purseOwnerType()
    {
        return self::class;
    }

    /**
     * @param $promotion_id
     * @return Purse
     */
    public function findPurseByPromotion($promotion_id)
    {
        $purse = Purse::findOne(['promotion_id' => $promotion_id, 'owner_id' => $this->id, 'owner_type' => Dealer::class]);

        if ($purse === null) {
            $purse = new Purse();
            $purse->owner_type = Dealer::class;
            $purse->owner_id = $this->id;
            $purse->promotion_id = $promotion_id;
            $purse->balance = 0;
            $purse->save();
        }

        return $purse;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->createPurse();
        }

        if (!$insert && $this->isAttributeChanged('name')) {
            $this->updatePurse();
        }

        ProfilePromotion::updatePromotionsByDealer($this);

        $this->addPurses();

        parent::afterSave($insert, $changedAttributes);
    }

    protected function addPurses()
    {
        $promotions = $this->promotions;

        foreach ($promotions as $promotion) {
            $purse = Purse::findOne(['promotion_id' => $promotion->id, 'owner_id' => $this->id, 'owner_type' => Dealer::class]);

            if (null === $purse) {
                $purse = new Purse();
                $purse->balance = 0;
                $purse->owner_id = $this->id;
                $purse->promotion_id = $promotion->id;
                $purse->title = "Счет дилера #{$this->id} ({$this->name}) по акции #{$promotion->id} ({$promotion->name})";
                $purse->owner_type = Dealer::class;
                $purse->save(false);
            }
        }
    }

    public static function updateProfilesId($id){
        $model = DealerProfile::find()
            ->select('profile_id')
            ->where(['dealer_id' => $id])
            ->asArray()
            ->all();
        print_r($model);
        foreach ($model as $profile_id){
            $profile = Profile::findOne(['id' => $profile_id['profile_id']]);
                $profile->resolve_phone = Yii::$app->request->post('Dealer')['resolve_phone'];
                $profile->resolve_purse =  Yii::$app->request->post('Dealer')['resolve_purse'];
                $profile->update(false);
        }
    }

    protected function createPurse()
    {
        Purse::create(self::class, $this->id, strtr('Счет дилера #{id} ({name})', [
            '{id}' => $this->id,
            '{name}' => $this->name,
        ]));
    }

    protected function updatePurse()
    {
        $this->purse->updateAttributes([
            'title' => strtr('Счет дилера #{id} ({name})', [
                '{id}' => $this->id,
                '{name}' => $this->name,
            ]),
        ]);
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            Sale::deleteAll(['dealer_id' => $this->id]);

            return true;
        }
        else {
            return false;
        }
    }

    public function afterDelete()
    {
        Purse::remove(self::class, $this->id);

        parent::afterDelete();
    }

    /**
     * @param string $index
     * @return array
     */
    public static function getOptions($index = 'id')
    {
        return self::find()
            ->indexBy($index)
            ->select('name, id')
            ->column();
    }

    /**
     * @return array
     */
    public static function getOptionsByProfileId($profile_id)
    {
        $raw = (new Query())
            ->select('dealer.name, dealer.id')
            ->from(['dealer' => Dealer::tableName()])
            ->innerJoin(['dp' => DealerProfile::tableName()], 'dp.dealer_id = dealer.id')
            ->where(['dp.profile_id' => $profile_id])
            ->all();

        $options = [];

        foreach ($raw as $r) {
            $key = $r['id'];
            $options[$key] = $r['name'];
        }

        return $options;
    }

    /**
     * @return ActiveQuery
     */
    public function getSales()
    {
        return $this->hasMany(Sale::className(), ['dealer_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @deprecated
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotion::className(), ['id' => 'promotion_id'])
            ->viaTable(DealerPromotion::tableName(), ['dealer_id' => 'id']);
    }

    public function getX_real()
    {
        return $this->x / 100;
    }

    public function getXx_real()
    {
        return $this->xx / 100;
    }

    /**
     * @return ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(Profile::class, ['id' => 'profile_id'])
            ->viaTable(DealerProfile::tableName(), ['dealer_id' => 'id'])
            ->onCondition(['role' => Profile::ROLE_MANAGER]);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->x = intval(floatval($this->x_real) * 100);
            $this->xx = intval(floatval($this->xx_real) * 100);

            return true;
        }

        return false;
    }

    public static function getManagerOptions()
    {
        $query = User::find();

        $users = [];

        foreach ($query->each() as $admin) {
            $users[$admin->id] = $admin->name . ' (#' . $admin->id . ')';
        }

        return $users;
    }

    /**
     * @return ActiveQuery
     */
    public function getPromotions()
    {
        return $this->hasMany(Promotion::class, ['id' => 'promotion_id'])
            ->viaTable(DealerPromotion::tableName(), ['dealer_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPurses()
    {
        return $this->hasMany(Purse::class, ['owner_id' => 'id'])
            ->onCondition('owner_type = :type', [':type' => self::class]);
    }

}
