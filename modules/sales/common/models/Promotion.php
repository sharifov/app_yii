<?php

namespace modules\sales\common\models;

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\DealerPromotion;
use modules\profiles\common\models\Profile;
use modules\profiles\common\models\ProfilePromotion;
use marketingsolutions\finance\models\Purse;
use marketingsolutions\finance\models\PurseOwnerInterface;
use marketingsolutions\finance\models\PurseOwnerTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sales_promotions".
 *
 * @property integer $id
 * @property string $name
 * @property string $rules
 * @property string $type
 * @property string $type_label
 * @property integer $x
 * @property float $x_real
 *
 * @property Sale[] $sales
 * @property Dealer[] $dealers
 * @property Profile[] $profiles
 * @property PromotionBrand[] $promotion_brands
 * @property PromotionProduct[] $promotion_products
 * @property Brand[] $brandItems
 * @property string $brand_names
 */
class Promotion extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    const TYPE_SKU = 'sku';
    const TYPE_BRAND_KG = 'brand_kg';
    const TYPE_BRAND_RUB = 'brand_rub';

    /** @var string */
    protected $x_real;

    /** @var array */
    protected $brands = [];

    /** @var array */
    protected $products = [];

    /**
     * @var UploadedFile
     */
    public $rulesFile;

    public $promotionBrands = [];
    public $promotionProducts = [];
    public $promotionBrandsRub = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales_promotions}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     *
     * @return string
     */
    public static function modelTitle()
    {
        return 'Акция';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     *
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Акции';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => 255],
            ['name', 'required'],
            ['type', 'string', 'max' => 255],
            ['brands', 'safe'],
            ['x', 'integer'],
            ['x_real', 'safe'],
            ['promotionBrands', 'safe'],
            ['promotionBrandsRub', 'safe'],
            ['promotionProducts', 'safe'],
            ['rules', 'safe'],
            ['rulesFile', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf, gif'],
        ];
    }

    public static function getTypeOptions()
    {
        return [
            self::TYPE_BRAND_KG => 'По Брендам, кг',
            self::TYPE_BRAND_RUB => 'По Брендам, руб.',
            self::TYPE_SKU => 'По SKU с коэфициентом для каждой позиции',
        ];
    }

    public function getType_label()
    {
        if ($this->type == null) {
            return '';
        }

        return self::getTypeOptions()[$this->type];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Акция',
            'brands' => 'Участвующие бренды',
            'brand_names' => 'Участвующие бренды',
            'x' => 'Коэффициент Умножения * 100',
            'x_real' => 'Коэффициент Умножения',
            'rules' => 'Правила акции',
            'rulesFile' => 'Правила акции',
            'type' => 'Вид механики продаж',
            'type_label' => 'Вид механики продаж',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasMany(Sale::className(), ['promotion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealers()
    {
        return $this->hasMany(Dealer::className(), ['promotion_id' => 'id']);
    }

    public function getPromotion_brands()
    {
        return $this->hasMany(PromotionBrand::className(), ['promotion_id' => 'id']);
    }

    public function getPromotion_products()
    {
        return $this->hasMany(PromotionProduct::className(), ['promotion_id' => 'id']);
    }

    public static function getOptions()
    {
        return self::find()
            ->where('id != 1')
            ->select('name, id')->indexBy('id')->column();
    }

    public static function getOptionsByDealer($dealer_id = null)
    {
        if (!$dealer_id) {
            return self::getOptions();
        }

        $promotionIds = DealerPromotion::find()->select('promotion_id')->where(['dealer_id' => $dealer_id])->column();

        return self::find()->where(['id' => $promotionIds])->select('name, id')->indexBy('id')->column();
    }

    public function isGold()
    {
        return $this->id == 1;
    }

    public function isIndividual()
    {
        return $this->id == 2;
    }

    public function isTest()
    {
        return $this->id == 3;
    }

    public function isProf()
    {
        return $this->id == 4;
    }

    public function isApp()
    {
        return $this->id <= 36 && !in_array($this->id, [1, 2, 3, 4]);
    }

    public function isApp2()
    {
        return $this->id >= 37;
    }

    public function setBrands($brandIds)
    {
        $this->brands = (array) $brandIds;
    }

    /**
     * Возвращает массив идентификаторов тэгов.
     */
    public function getBrands()
    {
        return ArrayHelper::getColumn($this->getPromotion_brands()->all(), 'brand_id');
    }

    public function getBrandItems()
    {
        return Brand::findAll(['id' => $this->getBrands()]);
    }

    public function getBrand_names()
    {
        $brands = (new Query())
            ->select('b.*')
            ->from(['b' => Brand::tableName()])
            ->innerJoin(['pb' => PromotionBrand::tableName()], 'pb.brand_id = b.id')
            ->where(['pb.promotion_id' => $this->id])
            ->all();

        $names = ArrayHelper::getColumn($brands, 'name');

        return empty($names) ? '' : implode(', ', $names);
    }

    public function getX_real()
    {
        return $this->x / 100;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->x = intval(floatval($this->x_real) * 100);

            if (!empty($this->promotionBrands)) {
                foreach ($this->promotionBrands as $brand_id => $brand_x) {
                    $promotionBrand = PromotionBrand::findOne([
                        'promotion_id' => $this->id,
                        'brand_id' => $brand_id,
                    ]);
                    if ($promotionBrand) {
                        $promotionBrand->x = intval(floatval($brand_x) * 100);
                        $promotionBrand->updateAttributes(['x']);
                    }
                }
            }

            if (!empty($this->promotionBrandsRub)) {
                foreach ($this->promotionBrandsRub as $brand_id => $rub_percent) {
                    /** @var PromotionBrand $promotionBrand */
                    $promotionBrand = PromotionBrand::findOne([
                        'promotion_id' => $this->id,
                        'brand_id' => $brand_id,
                    ]);

                    if ($promotionBrand) {
                        $promotionBrand->rub_percent = intval(floatval($rub_percent) * 100);
                        $promotionBrand->updateAttributes(['rub_percent']);
                    }
                }
            }

            if (!empty($this->promotionProducts)) {
                foreach ($this->promotionProducts as $product_id => $x) {
                    $promotionProduct = PromotionProduct::findOne([
                        'promotion_id' => $this->id,
                        'product_id' => $product_id,
                    ]);

                    if ($promotionProduct) {
                        $promotionProduct->x = intval(floatval($x) * 100);
                        $promotionProduct->updateAttributes(['x']);
                    }
                }
            }

            return true;
        }

        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!empty($this->brands)) {
            $brandIds = implode(',', $this->brands);
            PromotionBrand::deleteAll("promotion_id = {$this->id} AND brand_id NOT IN ({$brandIds})");

            foreach ($this->brands as $brandId) {
                if (false == PromotionBrand::findOne(['promotion_id' => $this->id, 'brand_id' => $brandId])) {
                    $promotionBrand = new PromotionBrand();
                    $promotionBrand->promotion_id = $this->id;
                    $promotionBrand->brand_id = $brandId;
                    $promotionBrand->save();
                }
                $productIds = Product::find()->select('id')->where(['brand_id' => $brandId])->column();

                foreach ($productIds as $productId) {
                    if (false == PromotionProduct::findOne(['promotion_id' => $this->id, 'product_id' => $productId])) {
                        $promotionProduct = new PromotionProduct();
                        $promotionProduct->promotion_id = $this->id;
                        $promotionProduct->product_id = $productId;
                        $promotionProduct->save();
                    }
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::class, ['id' => 'profile_id'])
            ->viaTable(ProfilePromotion::tableName(), ['promotion_id' => 'id']);
    }

    public function upload()
    {
        if ($this->validate()) {
            $fileName = $this->id . '.' . $this->rulesFile->extension;
            $path = \Yii::getAlias('@frontendWebroot/media/uploads/') . $fileName;
            $this->rulesFile->saveAs($path);
            $this->rules = $fileName;
            $this->updateAttributes(['rules']);

            return true;
        }
        else {
            return false;
        }
    }

    public function getRulesPath()
    {
        if (empty($this->rules)) {
            return '';
        }

        return \Yii::getAlias('@frontendWeb/media/uploads/') . $this->rules;
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            Sale::deleteAll(['promotion_id' => $this->id]);

            return true;
        }
        else {
            return false;
        }
    }
}
