<?php

namespace modules\sales\common\models;

use marketingsolutions\finance\models\Transaction;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\sales\statuses\Statuses;
use modules\sales\common\sales\statuses\StatusManager;
use marketingsolutions\datetime\DateTimeBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Html;
use yii\rbac\Assignment;
use yz\admin\models\AuthAssignment;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales".
 *
 * @property integer $id
 * @property string $status
 * @property integer $recipient_id
 * @property integer $kg умноженное на 100
 * @property integer $kg_real
 * @property string $created_at
 * @property string $updated_at
 * @property string $sold_on
 * @property string $bonuses_paid_at
 * @property string $approved_by_admin_at
 * @property integer $dealer_id
 *
 * @property integer $rub
 * @property integer $bonuses
 * @property integer $dealer_bonuses
 * @property integer $manager_bonuses
 *
 * @property integer $promotion_id
 * @property integer $previous_kg
 * @property integer $previous_kg_real
 * @property integer $x
 * @property integer $xx
 * @property integer $x_real
 * @property integer $xx_real
 * @property string $rule PromotionRule used to calculate bonuses for this sale
 * @property integer $manager_commission
 * @property boolean $manager_commission_included
 *
 * @property SalePosition[] $positions
 * @property SaleBrandPosition[] $brandPositions
 * @property StatusManager $statusManager
 * @property SaleDocument[] $documents
 * @property SalePreviousDocument[] $previous_documents
 * @property Promotion $promotion
 * @property Dealer $dealer
 *
 * @property string $sold_on_local
 */
class Sale extends \yz\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @var StatusManager
     */
    private $_statusManager;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     *
     * @return string
     */
    public static function modelTitle()
    {
        return 'Продажа';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     *
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Продажи продукции';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            'datetime' => [
                'class' => DateTimeBehavior::className(),
                'attributes' => [
                    'sold_on' => [
                        'targetAttribute' => 'sold_on_local',
                        'originalFormat' => ['date', 'yyyy-MM-dd'],
                        'targetFormat' => ['date', 'dd.MM.yyyy'],
                    ]
                ]
            ]
        ];
    }

    public static function getStatusValues()
    {
        return Statuses::statusesValues();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['sold_on_local', 'required'],
            ['previous_kg', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Статус',
            'recipient_id' => 'Получатель приза',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'sold_on' => 'Дата продажи',
            'dealer_id' => 'Дилер',
            'bonuses_paid_at' => 'Дата начисления бонусов',
            'approved_by_admin_at' => 'Дата одобрения администратором',

            'x' => 'x * 100',
            'x_real' => 'x',
            'xx' => 'xx * 100',
            'xx_real' => 'xx',

            'rub' => 'Проданно на сумму, руб.',
            'rub_real' => 'Проданно на сумму, руб.',

            'kg' => 'Продано единиц (шт или кг) * 100',
            'kg_real' => 'Продано единиц (шт или кг)',
            'promotion_id' => 'Акция',
            'previous_kg' => 'Продажа за 2014 год, кг * 100',
            'previous_kg_real' => 'Продажа за 2014 год, кг',
            'rule' => 'Правило бонусов',

            'manager_commission' => 'Бонусы руководителю ДЦ в %',
            'manager_commission_included' => 'Бонусы руководителю ДЦ включается в бонусы от продажи',
            'bonuses' => 'Бонусы за продажу',
            'dealer_bonuses' => 'Бонусы дилеру',
            'manager_bonuses' => 'Бонусы руководителю ДЦ',
            'sold_on_local' => 'Дата продажи',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPositions()
    {
        return $this->hasMany(SalePosition::className(), ['sale_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrandPositions()
    {
        return $this->hasMany(SaleBrandPosition::className(), ['sale_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealer::className(), ['id' => 'dealer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotion::class, ['id' => 'promotion_id']);
    }

    public function updateBonuses()
    {
        foreach ($this->getPositions()->all() as $position) {
            /** @var SalePosition $position */

            if ($this->promotion->isGold()) {
                $factor = Factor::findOne(['brand_id' => $position->product->brand->id, 'dealer_id' => $this->dealer_id]);
                $position->bonuses = $factor ? intval(round($position->kg_real * $factor->x_real)) : 0;
            }
            elseif ($this->promotion->isProf()) {
                $position->bonuses = $position->product->brand->xprof
                    ? intval(round($position->kg_real / $position->product->brand->xprof))
                    : 0;
            }
            elseif ($this->promotion->type == Promotion::TYPE_SKU) {
                /** @var PromotionProduct $promotionProduct */
                $promotionProduct = PromotionProduct::findOne([
                    'promotion_id' => $this->promotion_id,
                    'product_id' => $position->product_id,
                ]);
                $position->bonuses = $promotionProduct
                    ? intval(round($promotionProduct->x_real * $position->kg_real))
                    : 0;
            }
            else {
                $brand = $position->product->brand;
                $promotionBrand = PromotionBrand::findOne([
                    'promotion_id' => $this->promotion_id,
                    'brand_id' => $brand->id,
                ]);

                if ($promotionBrand) {
                    $position->bonuses = intval(round($position->kg_real * $promotionBrand->x_real));
                }
            }

            $position->updateAttributes(['bonuses']);
        }

        foreach ($this->getBrandPositions()->all() as $brandPosition) {
            /** @var SaleBrandPosition $brandPosition */
            /** @var PromotionBrand $promotionBrand */
            $promotionBrand = PromotionBrand::findOne([
                'promotion_id' => $this->promotion_id,
                'brand_id' => $brandPosition->brand_id,
            ]);

            if ($promotionBrand) {
                $brandPosition->bonuses = !empty($brandPosition->kg)
                    ? intval(round($brandPosition->kg_real * $promotionBrand->x_real))
                    : intval($brandPosition->rub * $promotionBrand->rub_percent_real / 100);
            }

            $brandPosition->updateAttributes(['bonuses']);
        }

        $this->bonuses = intval($this->getPositions()->sum('bonuses'))
            + intval($this->getBrandPositions()->sum('bonuses'));

        $this->updateAttributes(['bonuses']);
    }

    public function updateKg()
    {
        $this->kg = $this->getPositions()->sum('kg') + $this->getBrandPositions()->sum('kg');
        $this->updateAttributes(['kg']);
    }

    public function updateRub()
    {
        $this->rub = $this->getBrandPositions()->sum('rub');
        $this->updateAttributes(['rub']);
    }

    public function getStatusManager()
    {
        if ($this->_statusManager === null) {
            $this->_statusManager = new StatusManager($this);
        }

        return $this->_statusManager;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(SaleDocument::className(), ['sale_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrevious_documents()
    {
        return $this->hasMany(SalePreviousDocument::className(), ['sale_id' => 'id']);
    }

    public function fields()
    {
        return parent::fields() + ['sold_on_local', 'previous_kg'];
    }

    public function getKg_real()
    {
        return $this->kg / 100;
    }

    public function getX_real()
    {
        return $this->x / 100;
    }

    public function getXx_real()
    {
        return $this->xx / 100;
    }

    public function getPrevious_kg_real()
    {
        return $this->previous_kg / 100;
    }

    public function getPromotionRules()
    {
        return PromotionRule::find()
            ->where(['promotion_id' => $this->promotion_id])
            ->orderBy(['priority' => SORT_DESC])
            ->all();
    }

    public function isPaid()
    {
        return $this->status === Statuses::PAID;
    }

    public function getManager()
    {
        return $this->hasOne(Profile::className(), ['dealer_id' => $this->dealer_id, 'role' => 'manager']);
    }

    public static function getStatusOptions()
    {
        return [
            Statuses::ADMIN_REVIEW => 'новая',
            Statuses::APPROVED => 'подтверждена',
            Statuses::DECLINED => 'отклонена',
            Statuses::DRAFT => 'черновик',
            Statuses::PAID => 'баллы начислены',
        ];
    }

    public function renderStatusButton()
    {
        switch ($this->status) {
            case Statuses::ADMIN_REVIEW:
                return Html::tag('span', self::getStatusOptions()[$this->status], ['class' => 'label label-info']);

            case Statuses::APPROVED:
                return Html::tag('span', self::getStatusOptions()[$this->status], ['class' => 'label label-success']);

            case Statuses::DECLINED:
                return Html::tag('span', self::getStatusOptions()[$this->status], ['class' => 'label label-danger']);

            case Statuses::DRAFT:
                return Html::tag('span', self::getStatusOptions()[$this->status], ['class' => 'label label-default']);

            case Statuses::PAID:
                return Html::tag('span', self::getStatusOptions()[$this->status], ['class' => 'label label-primary']);

            default:
                return null;
        }
    }

    public function getRub_real()
    {
        $this->updateRub();

        return $this->rub;
    }

    /**
     * Добавляем ID админнистратора в транзакции
     * @param $saleId
     * @return bool
     */
    public static function addAdminId($saleId, $purseId){
        if (Transaction::updateAll(['admin_id'=>\Yii::$app->user->identity->id], ['partner_id' => $saleId , 'purse_id' => $purseId ]))
            return true;
		return false;
    }

    /**
     * @param $id
     * @return int
     */
    public static function getSumRub($id){
        $model = Sale::findOne($id);
        if($model){
            $sum=0;
            foreach ($model->brandPositions as $brandPosition){
                if(empty($brandPosition->kg_real)){
                    $sum=$sum+$brandPosition->rub;
                }
            }
            return $sum;
        }

    }

    /**
     * @return mixed
     */
    public static function getRoleNameForAdmin(){
       return  AuthAssignment::find()->where(['user_id' => \Yii::$app->user->id])->asArray()->one()['item_name'];
    }
}
