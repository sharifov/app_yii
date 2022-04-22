<?php

namespace modules\sales\common\finances;

use modules\sales\common\models\Sale;
use marketingsolutions\finance\models\TransactionPartnerInterface;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;


/**
 * Class ManagerPartner
 */
class ManagerPartner extends BaseObject implements TransactionPartnerInterface
{
    public $id;
    protected static $_titles = [];

    public function __construct($id, $config = [])
    {
        $this->id = $id;
        parent::__construct($config);
    }


    /**
     * Returns partner for some transaction by partner's id
     * @param int $id
     * @return $this
     */
    public static function findById($id)
    {
        return \Yii::createObject([
            'class' => self::className(),
        ], [$id]);
    }

    /**
     * Returns id of the partner. Could NULL if partner does not support id
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns title for partner
     * @return string
     */
    public function getTitleForTransaction()
    {
        if (!array_key_exists($this->id, self::$_titles)) {
            self::$_titles[$this->id] = ArrayHelper::getValue(Sale::findOne($this->id), 'id', 'Неизвестно');
        }
        return self::$_titles[$this->id];
    }

    /**
     * Returns type of the partner
     * @return string
     */
    public function getTypeForTransaction()
    {
        return 'Продажа товара';
    }
}