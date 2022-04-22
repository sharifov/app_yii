<?php

namespace modules\sales\common\app\controllers\api;

use modules\sales\common\models\SaleDocument;
use modules\sales\common\models\SalePreviousDocument;
use modules\sales\frontend\base\ApiController;
use yii\web\NotAcceptableHttpException;
use yii\web\UploadedFile;


/**
 * Class FilesController
 */
class FilesController extends ApiController
{
    protected function verbs()
    {
        return [
            'post' => ['upload']
        ];
    }

    public function actionUpload()
    {
		/** @var UploadedFile $uploadedFile */
        $uploadedFile = UploadedFile::getInstanceByName('file');

        if ($uploadedFile === null) {
            throw new NotAcceptableHttpException();
        }

        $model = new SaleDocument();
        $model->scenario = SaleDocument::SCENARIO_FILE_UPLOAD;
        $model->fileUpload = $uploadedFile;

        $model->save();

        return $model;
    }

    public function actionUploadPrevious()
    {
        $uploadedFile = UploadedFile::getInstanceByName('file');

        if ($uploadedFile === null) {
            throw new NotAcceptableHttpException();
        }

        $model = new SalePreviousDocument();
        $model->scenario = SaleDocument::SCENARIO_FILE_UPLOAD;
        $model->fileUpload = $uploadedFile;

        $model->save();

        return $model;
    }
}