<?php

namespace modules\profiles\frontend\controllers;

use modules\profiles\frontend\models\RegistrationAndLoginForm;
use ms\loyalty\contracts\identities\IdentityRegistrarInterface;
use ms\loyalty\contracts\identities\RegistrationTokenProviderInterface;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yz\Yz;


/**
 * Class RegistrationController
 */
class RegistrationController extends Controller
{
    /**
     * @var RegistrationTokenProviderInterface
     */
    private $registrationTokenProvider;
    /**
     * @var IdentityRegistrarInterface
     */
    private $registrar;

    public function __construct($id, $module,
                                RegistrationTokenProviderInterface $registrationTokenProvider,
                                IdentityRegistrarInterface $registrar,
                                $config = [])
    {
        $this->registrationTokenProvider = $registrationTokenProvider;
        $this->registrar = $registrar;

        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => false,
                        'matchCallback' => function ($rule, $action) {
                            $tokenManager = $this->registrationTokenProvider->findFromRequest(\Yii::$app->request);
                            return $tokenManager === null;
                        },
                        'denyCallback' => function ($rule, $action) {
                            \Yii::$app->session->setFlash(Yz::FLASH_ERROR, 'Извините, Вы должны подтвердить свои данные');
                            \Yii::$app->response->redirect(Url::home());
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => false,
                    ]
                ]
            ]
        ];
    }


    public function actionIndex()
    {
        $tokenManager = $this->registrationTokenProvider->findFromRequest(\Yii::$app->request);
        $model = new RegistrationAndLoginForm($tokenManager, $this->registrar);

        if ($model->loadAll(\Yii::$app->request->post()) && $model->process()) {
            return $this->redirect(['/dashboard/index']);
        }

        return $this->render('index', compact('model'));
    }
}