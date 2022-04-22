<?php

namespace modules\sales\common\models;

use marketingsolutions\files\File;
use Yii;


/**
 * Class SalePreviousDocument
 * @property int $sale_id
 * @property Sale sale
 * @property bool $isImage
 */
class SalePreviousDocument extends File
{
    public static function tableName()
    {
        return '{{%sale_previous_documents}}';
    }

    public function rules()
    {
        $rules = parent::rules();

        $rules['fileRule'] = ['fileUpload', 'file', 'skipOnEmpty' => false, 'on' => self::SCENARIO_FILE_UPLOAD, 'when' => function () {
            return $this->isNewRecord;
        }, 'extensions' => 'gif, jpg, png, pdf, tiff, xls, xlsx', 'checkExtensionByMimeType' => false];

        return $rules;
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
        return Yii::getAlias('@data/sales/documents/' . $this->name);
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
    public function getSale()
    {
        return $this->hasOne(Sale::className(), ['id' => 'sale_id']);
    }

}