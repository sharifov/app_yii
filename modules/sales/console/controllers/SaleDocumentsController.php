<?php

namespace modules\sales\console\controllers;

use console\base\Controller;
use modules\sales\common\models\SaleDocument;


/**
 * Class SaleDocumentsController
 */
class SaleDocumentsController extends Controller
{
    /**
     * Removes all orphaned documents
     * @throws \Exception
     */
    public function actionRemoveOrphaned()
    {
        $query = SaleDocument::find()->where('sale_id IS NULL and DATEDIFF(NOW(), "created_at") > 1');

        foreach ($query->each() as $document) {
            /** @var SaleDocument $document */
            $document->delete();
        }
    }
}