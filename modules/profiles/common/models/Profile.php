<?php

namespace modules\profiles\common\models;

use modules\sales\common\models\Promotion;
use ms\loyalty\contracts\identities\IdentityRegistrarInterface;
use ms\loyalty\contracts\identities\IdentityRoleInterface;
use ms\loyalty\contracts\prizes\PrizeRecipientInterface;
use ms\loyalty\contracts\profiles\HasEmail;
use ms\loyalty\contracts\profiles\HasEmailInterface;
use ms\loyalty\contracts\profiles\HasPhoneMobile;
use ms\loyalty\contracts\profiles\HasPhoneMobileInterface;
use ms\loyalty\contracts\profiles\ProfileInterface;
use ms\loyalty\contracts\profiles\UserName;
use ms\loyalty\contracts\profiles\UserNameInterface;
use ms\loyalty\identity\phones\common\models\Identity;
use marketingsolutions\datetime\DateTimeBehavior;
use marketingsolutions\finance\models\Purse;
use marketingsolutions\finance\models\PurseInterface;
use marketingsolutions\finance\models\PurseOwnerInterface;
use marketingsolutions\finance\models\PurseOwnerTrait;
use marketingsolutions\phonenumbers\PhoneNumberBehavior;
use ms\loyalty\taxes\common\models\Account;
use ms\loyalty\taxes\common\models\AccountProfile;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii2tech\ar\linkmany\LinkManyBehavior;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_profiles".
 *
 * @property integer $id
 * @property integer $identity_id
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $full_name
 * @property string $phone_mobile
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 * @property integer $dealer_id @deprecated
 * @property boolean $resolve_phone
 * @property boolean $resolve_purse
 * @property string $role
 * @property string $phone_mobile_local
 * @property string $status
 * @property string $status_date
 * @property bool $sync_with_dealers_promotions
 * @property string $position
 * @property string $sales_point_name
 *
 * @property Dealer $dealer @deprecated
 * @property Identity $identity
 * @property Report $activeReport
 *
 * @property Promotion[] $promotions
 * @property Dealer[] $dealers
 *
 * @property array $dealerIds
 * @property array $promotionIds
 */
class Profile extends \yz\db\ActiveRecord implements ModelInfoInterface,
    HasEmailInterface, HasPhoneMobileInterface, UserNameInterface, IdentityRoleInterface,
    PurseOwnerInterface, PrizeRecipientInterface, ProfileInterface
{
    use HasEmail, HasPhoneMobile, UserName, PurseOwnerTrait;

    const ROLE_MANAGER = 'manager';
    const ROLE_SALES = 'sales';
    const ROLE_RTT = 'rtt';

    const STATUS_EMPLOYEE = 'employee';
    const STATUS_FIRED = 'fired';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profiles}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Профиль участника';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Профили участников';
    }

    public function __toString()
    {
        return $this->full_name;
    }

    /**
     * @return array
     */
    public static function getDealerIdValues()
    {
        return Dealer::find()->indexBy('id')->select('name, id')->column();
    }

    /**
     * Returns purse's owner by owner's id
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

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            'phonenumber' => [
                'class' => PhoneNumberBehavior::className(),
                'attributes' => [
                    'phone_mobile_local' => 'phone_mobile',
                ],
                'defaultRegion' => 'RU',
            ],
            'datetime' => [
                'class' => DateTimeBehavior::className(),
                'attributes' => ['status_date'],
                'performValidation' => false,
            ],
            'linkDealers' => [
                'class' => LinkManyBehavior::class,
                'relation' => 'dealers',
                'relationReferenceAttribute' => 'dealerIds',
            ],
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
            [['created_at', 'updated_at', 'status', 'status_date', 'status_date_local'], 'safe'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 32],
            [['full_name'], 'string', 'max' => 255],
            [['phone_mobile'], 'string', 'max' => 16],
            [['email'], 'string', 'max' => 64],
            ['role', 'required'],
            ['role', 'validateManagerRole'],
            ['dealerIds', 'required'],
            ['promotionIds', 'safe', 'when' => function () {
                return $this->sync_with_dealers_promotions == false;
            }],
            ['phone_mobile_local', 'required'],
            ['phone_mobile_local', 'unique', 'targetAttribute' => ['phone_mobile' => 'phone_mobile']],
            ['email', 'email'],
            ['status_date_local', 'safe'],
            ['sync_with_dealers_promotions', 'boolean'],
            ['position', 'string'],
            ['sales_point_name', 'string'],
            ['resolve_phone', 'integer'],
            ['resolve_purse', 'integer'],
//            ['email', 'unique'],
        ];
    }

    public function validateManagerRole()
    {
        if ($this->role != self::ROLE_MANAGER) {
            return;
        }

        $query = (new Query())
            ->from([
                'profile' => self::tableName(),
                'dealerProfile' => DealerProfile::tableName(),
            ])
            ->where('profile.id = dealerProfile.profile_id')
            ->andWhere([
                'dealerProfile.dealer_id' => $this->dealerIds,
            ])
            ->andWhere([
                'profile.role' => self::ROLE_MANAGER,
            ]);
        if ($this->isNewRecord == false) {
            $query->andWhere('profile.id != :id', [
                ':id' => $this->id,
            ]);
        }

        if ($query->exists()) {
            $this->addError('role', 'Один или несколько дилеров уже имеют другого менеджера');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'full_name' => 'Полное имя',
            'phone_mobile' => 'Номер телефона',
            'phone_mobile_local' => 'Номер телефона',
            'email' => 'Email',
            'role' => 'Роль',
            'dealerIds' => 'Дилер',
            'promotionIds' => 'Акции',
            'created_at' => 'Дата добавления',
            'updated_at' => 'Дата изменения',
            'status' => 'Статус',
            'status_date' => 'Дата статуса',
            'status_date_local' => 'Дата статуса',
            'sync_with_dealers_promotions' => 'Акции участника совпадают с акциями его дилеров',
            'position' => 'Должность',
            'sales_point_name' => 'Наименование РТТ',
            'resolve_phone' => 'Разрешить перевод денежных средств на телефон',
            'resolve_purse' => 'Разрешить перевод денежных средств на кошелек',
        ];
    }
    /**
     * @deprecated
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealer::className(), ['id' => 'dealer_id'])
            ->viaTable(DealerProfile::tableName(), ['profile_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        $this->full_name = $this->first_name . ' ' . $this->last_name;
        if ($this->status_date == '1970-01-01 00:00:00') {
            $this->status_date = null;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdentity()
    {
        return $this->hasOne(Identity::className(), ['id' => 'identity_id']);
    }

    /**
     * @return string
     */
    public function getIdentityRole()
    {
        return 'profile';
    }

    /**
     * Returns id of the recipient
     * @return integer
     */
    public function getRecipientId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns purce for the recipient, that should contain enough money
     * @return PurseInterface
     */
    public function getRecipientPurse()
    {
        return $this->purse;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->createPurse();
        }

        if (!$insert && $this->isAttributeChanged('full_name')) {
            $this->updatePurse();
        }

        parent::afterSave($insert, $changedAttributes);

        ProfilePromotion::updatePromotionsForProfile($this);
    }

    protected function createPurse()
    {
        Purse::create(self::class, $this->id, strtr('Счет пользователя #{id} ({name})', [
            '{id}' => $this->id,
            '{name}' => $this->full_name,
        ]));
    }

    protected function updatePurse()
    {
        $this->purse->updateAttributes([
            'title' => strtr('Счет пользователя #{id} ({name})', [
                '{id}' => $this->id,
                '{name}' => $this->full_name,
            ]),
        ]);
    }

    public function afterDelete()
    {
        $this->removeIdentity();
        Purse::remove(self::class, $this->id);

        parent::afterDelete();
    }

    private function removeIdentity()
    {
        /** @var IdentityRegistrarInterface $registrar */
        $registrar = Yii::$container->get(IdentityRegistrarInterface::class);
        $registrar->removeIdentity($this->identity_id);
    }    public static function getRoleValues()
    {
        return [
            self::ROLE_SALES => 'Участник',
            self::ROLE_MANAGER => 'Руководитель',
            self::ROLE_RTT => 'РТТ',
        ];
    }

    /**
     * @return integer
     */
    public function getProfileId()
    {
        return $this->getPrimaryKey();
    }    public static function getStatusValues()
    {
        return [
            self::STATUS_EMPLOYEE => 'Работает',
            self::STATUS_FIRED => 'Не работает',
        ];
    }

    public function isSales()
    {
        return $this->role == self::ROLE_SALES;
    }

    public function isManager()
    {
        return $this->role == self::ROLE_MANAGER;
    }

    public function getActiveReport(Dealer $dealer = null)
    {
        /** @var Report $report */
        $reportQuery = Report::find()
            ->where(['profile_id' => $this->id])
            ->orderBy(['created_at' => SORT_DESC]);

        if ($dealer !== null) {
            $reportQuery->andWhere([
                'dealer_id' => $dealer->id,
            ]);
        }

        $report = $reportQuery->one();

        return $report && (new \DateTime($report->created_at))->format('m') == (new \DateTime('now'))->format('m')
            ? $report
            : null;
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getDealers()
    {
        return $this->hasMany(Dealer::class, ['id' => 'dealer_id'])
            ->viaTable(DealerProfile::tableName(), ['profile_id' => 'id']);
    }

    public function getDealersList()
    {
        $dealersNames = '';

        foreach ($this->dealers as $key => $dealer) {
            if ($key > 0) {
                $dealersNames .= '; ';
            }

            $dealersNames .= $dealer->name;
        }

        return $dealersNames;
    }


    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getPromotions()
    {
        return $this->hasMany(Promotion::class, ['id' => 'promotion_id'])
            ->viaTable(ProfilePromotion::tableName(), ['profile_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function resolveForbidPay(){
        $model=self::find()
            ->select('resolve_phone , resolve_purse')
            ->where(['identity_id' => \Yii::$app->user->identity->id])
            ->asArray()
            ->one();
        $typeResolve = [];
        $typeResolve['phone'] = $model['resolve_phone'];
        $typeResolve['purse'] = $model['resolve_purse'];
        return $typeResolve;
    }

    /**
     * @param null $profileId
     * @return bool
     */
    public static function isNdflRecord($profileId = null)
    {
        if ($profileId == null) {
            return false;
        }
        $account = Account::findOne(['profile_id' => $profileId]);

        if (null === $account) {
            return false;
        }

        $accountProfile = AccountProfile::findOne(['account_id' => $account->id]);

        if (null === $accountProfile) {
            return false;
        }

        return $accountProfile->is_fulfilled;
    }

    public function getDealersBalance()
    {
        $totalBalance = '';

        foreach ($this->dealers as $key => $dealer) {
            /** @var Dealer $dealer */
            $dealerTotal = 0;
            foreach ($dealer->purses as $purse) {
                $dealerTotal += $purse->balance;
            }
            if ($key) {
                $totalBalance .= '<br>';
            }
            $totalBalance .= $dealer->name . ': <strong>' . $dealerTotal . '</strong>';
        }

        return $totalBalance;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status !== self::STATUS_FIRED;
    }

    /**
     * @return array
     */
    public static function getOptions()
    {
        $query = (new Query)
            ->select(['p.id', 'p.phone_mobile', 'p.full_name'])
            ->from(['p' => Profile::tableName()])
            ->orderBy(['p.full_name' => SORT_ASC]);

        $raw = $query->all();
        $options = [];

        foreach ($raw as $r) {
            $key = $r['id'];
            $options[$key] = $r['full_name'] . '  ' . $r['phone_mobile'];
        }

        return $options;
    }
}
