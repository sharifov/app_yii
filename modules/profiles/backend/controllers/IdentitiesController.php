<?php

namespace modules\profiles\backend\controllers;
use backend\base\Controller;
use modules\profiles\common\models\Profile;
use ms\loyalty\contracts\identities\IdentityRegistrarInterface;


/**
 * Class IdentitiesController
 */
class IdentitiesController extends Controller
{
    public function actionDelete($id)
    {
        /** @var IdentityRegistrarInterface $registrar */
        $registrar = \Yii::$container->get(IdentityRegistrarInterface::class);
        $registrar->removeIdentity($id);

        Profile::updateAll([
            'identity_id' => null,
        ], [
            'identity_id' => $id
        ]);

        return $this->redirect(\Yii::$app->request->referrer);
    }
}