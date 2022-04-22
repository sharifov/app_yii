<?php

namespace modules\profiles\frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class RulesController extends Controller
{
    public function actionIndex($dealer_id, $promotion_id)
    {
        return $this->createResponsePdf("{$promotion_id}.pdf");
    }

    public function actionTerms()
    {
        return $this->render('terms');

        //return $this->createResponsePdf('terms.pdf');
    }

    private function createResponsePdf($filename)
    {
        $response = new Response();
        $path = \Yii::getAlias('@frontendWebroot/media/uploads/' . $filename);
        $content = file_get_contents($path);

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Length', strlen($content));
        $response->headers->set('Content-Disposition', "inline; filename=\"{$path}\"");
        $response->format = Response::FORMAT_RAW;
        $response->content = $content;

        return $response;
    }
}
