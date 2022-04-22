<?php

namespace modules\sms\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yz\interfaces\ModelInfoInterface;

/**
 * This is the model class for table "yz_sms".
 *
 * @property integer $id
 * @property string $to
 * @property string $type
 * @property string $sent_to
 * @property string $status
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 */
class Sms extends \yii\db\ActiveRecord implements ModelInfoInterface
{
    const TYPE_PROFILES = 'profiles';
    const TYPE_INDIVIDUAL = 'individual';

    const STATUS_NEW = 'new';
    const STATUS_PROGRESS = 'progress';
    const STATUS_DONE = 'done';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sms}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     *
     * @return string
     */
    public static function modelTitle()
    {
        return 'СМС-рассылка';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     *
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'СМС-рассылки';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['to' , 'string'],
            ['type', 'string', 'max' => 255],
            ['status', 'string', 'max' => 255],
            ['message', 'string'],
            ['sent_to', 'string'],
            ['created_at', 'safe'],
            ['updated_at', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'to' => 'Индивидуальные получатели',
            'type' => 'Тип рассылки',
            'status' => 'Статус',
            'message' => 'Текст сообщения',
            'sent_to' => 'Разослано по номерам',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }

    public static function getTypeOptions()
    {
        return [
            self::TYPE_INDIVIDUAL => 'Индивидуальные получатели',
            self::TYPE_PROFILES => 'Участники акции с оповещением по SMS',
        ];
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_NEW => 'Не отправлена',
            self::STATUS_PROGRESS => 'Отправляется',
            self::STATUS_DONE => 'Отправлена',
        ];
    }
}
