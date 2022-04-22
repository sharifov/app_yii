<?php

namespace modules\sms\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yz\interfaces\ModelInfoInterface;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "yz_sms_log".
 *
 * @property integer $id
 * @property integer $sms_id
 * @property string $phone_mobile
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 */
class SmsLog extends \yii\db\ActiveRecord implements ModelInfoInterface
{


	const TYPE_LOGIN = 'login';
	const TYPE_REGISTRATION = 'registration';
	const TYPE_REGISTRATION_BONUSES = 'registration_bonuses';
	const TYPE_INVITE = 'invite';
	const TYPE_BROADCAST = 'broadcast';
	const TYPE_BONUS_ADD = 'bonus-add';

	const STATUS_NONE = 0;
	const STATUS_SUCCESS = 1;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sms_log}}';
    }


	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::className(),
				'value' => new Expression('NOW()'),
			]
		];
	}


	/**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'СМС-отчет';
    }


    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'СМС-отчеты';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['sms_id', 'integer'],

			['type', 'string', 'max' => 30],

			['service', 'string', 'max' => 30],

			['phone_mobile', 'required'],
			['phone_mobile', 'string', 'max' => 16],

			['status', 'in', 'range' => [self::STATUS_NONE, self::STATUS_SUCCESS]],
			['status', 'default', 'value' => 0],

			['message', 'string'],

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
            'sms_id' => 'SMS ID',
			'type' => 'Тип сообщения',
			'service' => 'Сервис',
			'phone_mobile' => 'Номер телефона',
			'message' => 'Текст сообщения',
			'status' => 'Статус',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }


	public static function getTypes()
	{
		return [
			self::TYPE_LOGIN => 'Вход в систему',
			self::TYPE_REGISTRATION => 'Регистрация',
			self::TYPE_BROADCAST => 'Рассылка',
			self::TYPE_INVITE => 'Приглашение',
			self::TYPE_BONUS_ADD => 'Начисление бонусов',
		];
	}


	public static function getStatuses()
	{
		return [
			self::STATUS_NONE => 'Нет',
			self::STATUS_SUCCESS => 'Доставлено',
		];
	}


	/**
	 * Добавляет данные об отправленной СМС<br>
	 * @param array $params - массив значений полей модели SmsLog
	 *
	 * $params = [
	 *		'sms_id' => $sms->id
	 *		'type' => SmsLog::TYPE_INVITE,
	 *		'phone_mobile' => $profile->phone_mobile,
	 *		'message' => $message,
	 *		'status' => $result,
	 * ]
	 *`
	 * @return boolean
	 */
	public static function add($params = [])
	{
		$service = '';

		if(isset(Yii::$app->sms->services['service'])) {
			$service = Yii::$app->sms->services['service'];
			$service = StringHelper::basename($service::className());
		}

		$sms = new SmsLog;
		$sms->setAttributes($params);
		$sms->service = $service;

		return $sms->save();
	}


}
