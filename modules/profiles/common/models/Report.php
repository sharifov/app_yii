<?php

namespace modules\profiles\common\models;

use modules\sales\common\sales\statuses\StatusManager;
use marketingsolutions\files\File;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\web\UploadedFile;
use yz\admin\models\User;
use yz\interfaces\ModelInfoInterface;

/**
 * Class Report
 *
 * @property int $profile_id
 * @property profile $profile
 * @property bool $isImage
 * @property string $status
 * @property integer $dealer_id
 */
class Report extends File implements ModelInfoInterface
{
    const STATUS_NEW = 'new';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_APPROVED = 'approved';

    public static function tableName()
    {
        return '{{%reports}}';
    }

    /**
     * Returns model title, ex.: 'Person', 'Book'
     *
     * @return string
     */
    public static function modelTitle()
    {
        return 'Отчет об остатках';
    }

    /**
     * Returns plural form of the model title, ex.: 'Persons', 'Books'
     *
     * @return string
     */
    public static function modelTitlePlural()
    {
        return 'Отчеты об остатках';
    }

    public function rules()
    {
        $rules = parent::rules();

        $rules['fileRule'] = ['fileUpload', 'file', 'skipOnEmpty' => false, 'on' => self::SCENARIO_FILE_UPLOAD, 'when' => function () {
            return $this->isNewRecord;
        }, 'extensions' => 'gif, jpg, png, pdf, tiff, xls, xlsx, doc, docx', 'checkExtensionByMimeType' => false];

        $rules[] = ['status', 'safe'];
        $rules[] = ['dealer_id', 'required'];
        $rules[] = ['dealer_id', 'exist', 'targetClass' => Dealer::class, 'targetAttribute' => 'id'];

        return $rules;
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_NEW => 'новый',
            self::STATUS_CONFIRMED => 'согласован',
            self::STATUS_REJECTED => 'отклонен',
            self::STATUS_APPROVED => 'подтвержден',
        ];
    }

    public function getStatus_label()
    {
        return self::getStatusOptions()[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'original_name' => 'Исходное название',
            'name' => 'Название',
            'file_size' => 'Размер файла',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'fileUpload' => 'Загружен',
            'status' => 'Статус',
            'dealer_id' => 'Дилер',
        ];
    }

    public function getIsImage()
    {
        return in_array($this->getFileExtension(), ['gif', 'jpg', 'png']);
    }

    public function fields()
    {
        return parent::fields() + ['isImage'];
    }

    public function getFileName()
    {
        return Yii::getAlias('@data/sales/reports/' . $this->name);
    }

    /**
     * @return bool|string
     */
    public function getUrl()
    {
        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'profile_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->fileUpload instanceof UploadedFile) {
            $this->uploadFile($this->fileUpload);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function uploadFile(UploadedFile $fileUpload)
    {
        $this->original_name = $fileUpload->name;
        $this->name = $this->generateName();

        $this->updateAttributes(['original_name', 'name']);

        $fileName = $this->getFileName();
        FileHelper::createDirectory(dirname($fileName));
        $this->fileUpload->saveAs($fileName);
    }

    /**
     * @return string
     */
    protected function generateName()
    {
        return $this->sanitize($this->profile->dealer->name, false)
        . '_' . (new \DateTime('now'))->format('d.m.Y_H.i.s')
        . '.' . $this->getOriginalExtension();
    }

    private function sanitize($string, $force_lowercase = true, $anal = false)
    {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "_", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Zа-яА-Я0-9]/", "", $clean) : $clean;

        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }

    /**
     * @return string
     */
    public function getOriginalExtension()
    {
        return strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    }

    public function adminCan($status)
    {
        /** @var User $admin */
        $admin = \Yii::$app->user->identity;
        $isRegionalManager = false;

        foreach ($admin->roles as $role) {
            if ($role->name == StatusManager::ROLE_REGIONAL_MANAGER) {
                $isRegionalManager = true;
                break;
            }
        }

        switch ($status) {
            case self::STATUS_CONFIRMED:
                return in_array($this->status, [self::STATUS_NEW, self::STATUS_REJECTED]);

            case self::STATUS_REJECTED:
                return $isRegionalManager
                    ? in_array($this->status, [self::STATUS_NEW, self::STATUS_CONFIRMED])
                    : in_array($this->status, [self::STATUS_NEW, self::STATUS_CONFIRMED, self::STATUS_APPROVED]);

            case self::STATUS_APPROVED:
                return $isRegionalManager
                    ? false
                    : in_array($this->status, [self::STATUS_CONFIRMED]);

            default:
                return false;
        }
    }

    public function renderStatusButton()
    {
        switch ($this->status) {
            case self::STATUS_NEW:
                return Html::tag('span', self::getStatusOptions()[$this->status], ['class' => 'label label-info']);

            case self::STATUS_CONFIRMED:
                return Html::tag('span', self::getStatusOptions()[$this->status], ['class' => 'label label-primary']);

            case self::STATUS_REJECTED:
                return Html::tag('span', self::getStatusOptions()[$this->status], ['class' => 'label label-danger']);

            case self::STATUS_APPROVED:
                return Html::tag('span', self::getStatusOptions()[$this->status], ['class' => 'label label-success']);

            default:
                return null;
        }
    }

    public function getDealer()
    {
        return $this->hasOne(Dealer::class, ['id' => 'dealer_id']);
    }
}