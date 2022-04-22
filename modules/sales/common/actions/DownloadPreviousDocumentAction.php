<?php

namespace modules\sales\common\actions;

use modules\sales\common\models\SaleDocument;
use modules\sales\common\models\SalePreviousDocument;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;


/**
 * Class DownloadPreviousDocumentAction
 */
class DownloadPreviousDocumentAction extends Action
{
    /**
     * @var callable Callback format is:
     * ```php
     * function (SaleDocument $document) {
     *  return true;
     * }
     * ```
     */
    public $checkAccess;

    public function run($id)
    {
        $document = $this->findModel($id);

        $this->checkAccess($document);

        \Yii::$app->response->sendFile($document->getFileName(), $document->original_name, [
            'inline' => true,
        ]);
    }

    private function checkAccess($document)
    {
        if ($this->checkAccess === null) {
            return;
        }

        if (call_user_func($this->checkAccess, $document) == false) {
            throw new UnauthorizedHttpException();
        }
    }

    /**
     * @param $id
     * @return SaleDocument
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = SalePreviousDocument::findOne($id)) === null) {
            throw new NotFoundHttpException();
        };
        return $model;
    }
}