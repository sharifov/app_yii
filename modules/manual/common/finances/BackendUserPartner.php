<?php

namespace modules\manual\common\finances;
use marketingsolutions\finance\models\TransactionPartnerInterface;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yz\admin\models\User;


/**
 * Class BackendUserPartner
 */
class BackendUserPartner extends BaseObject implements TransactionPartnerInterface
{
    public $id;
    protected static $_titles = [];

    /**
     * Returns partner for some transaction by partner's id
     * @param int $id
     * @return $this
     */
    public static function findById($id)
    {
        return \Yii::createObject([
            'class' => self::className(),
            'id' => $id,
        ]);
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
            self::$_titles[$this->id] = ArrayHelper::getValue(User::findOne($this->id), 'name', 'Неизвестно');
        }
        return self::$_titles[$this->id];
    }

    /**
     * Returns type of the partner
     * @return string
     */
    public function getTypeForTransaction()
    {
        return 'Администратор';
    }
}