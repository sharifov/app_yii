<?php

namespace modules\news\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Html;
use yz\interfaces\ModelInfoInterface;
use modules\profiles\common\models\Brand;

/**
 * This is the model class for table "yz_news".
 *
 * @property integer $id
 * @property string $title
 * @property string $teaser
 * @property string $content
 * @property string $created_at
 * @property string $is_read
 * @property string $updated_at
 * @property integer $enabled
 * @property boolean $is_push_sent
 *
 * @property Brand $brand
 */
class News extends \yz\db\ActiveRecord implements ModelInfoInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     * @return string
     */
    public static function modelTitle()
    {
        return 'Новость';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Новости';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title', 'filter', 'filter' => 'trim'],
            ['title', 'string', 'max' => 255],
            ['title', 'required'],

            ['teaser', 'string'],

            ['content', 'required'],
            ['content', 'string'],

            ['is_read', 'safe'],

            ['enabled', 'integer'],

            ['is_push_sent', 'boolean'],
            ['is_push_sent', 'default', 'value' => false],

            ['created_at', 'safe'],
            ['updated_at', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'teaser' => 'Короткое содержимое',
            'content' => 'Содержимое',
            'created_at' => 'Размещена',
            'updated_at' => 'Обновлена',
            'enabled' => 'Активна',
            'is_read' => 'Прочитано',
            'is_push_sent' => 'Push сообщение отправлено'
        ];
    }

    /**
     * Добавление ID админов в массив админов, просмотревших сообщение
     */
    public static function isReadMessage($userId, $menu = false)
    {
        if(!$userId){
            return '';
        }
        $model = self::find()
            ->where(['enabled' => true])
            ->asArray()
            ->all();
        $countIsRead = 0;
        foreach ($model as $item) {
            $arrIsRead = [];
            if ($item['is_read'] != null) {
                $arrIsRead = unserialize($item['is_read']);
            }

            if (!in_array($userId, $arrIsRead)) {
                $countIsRead++;
                if ($menu == false) {
                    $isReadPlus = [$userId];
                    $arrPlus = array_merge($arrIsRead, $isReadPlus);
                    $newIdPlus = self::findOne(['id' => $item['id']]);
                    $newIdPlus->is_read = serialize($arrPlus);
                    $newIdPlus->update(false);
                }
            }
        }
        $count = Html::tag('div', Html::encode($countIsRead), ['class' => 'wid_count']);
        return $countIsRead ? $count : '';
    }
}
