<?php

namespace frontend\controllers;

use frontend\base\Controller;
use marketingsolutions\captcha\algorithms\Numbers;
use marketingsolutions\captcha\CaptchaAction;
use yii\web\ErrorAction;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction;
use modules\profiles\common\models\Profile;
use ms\loyalty\identity\phones\common\models\Identity;
use Yii;


/**
 * Class SiteController
 * @package \frontend\controllers
 */
class SiteController extends Controller
{
    public function getAccessRules()
    {
        return array_merge([
            [
                'allow' => false,
                'actions' => ['index'],
                'roles' => ['@'],
                'denyCallback' => function() {
                    $this->redirect(['/dashboard/index']);
                }
            ],
            [
                'allow' => true,
                'actions' => ['blocked', 'access-denied'],
                'roles' => ['?']
            ],
            [
                'allow' => true,
            ]
        ], parent::getAccessRules());
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::className()
            ],
            'page' => [
                'class' => ViewAction::className(),
            ],
            'captcha' => [
                'class' => CaptchaAction::className(),
                'algorithm' => Numbers::className(),
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAccessDenied()
    {
        return $this->render('accessDenied');
    }

    public function actionBlocked()
    {
        return $this->render('blocked');
    }

    public function actionLogin($id, $hash)
    {

        Yii::$app->cache->flush();
        /** @var Profile $profile */
        $profile = Profile::findOne($id);

        $session = Yii::$app->session;

        if (!$profile || md5($id) != $hash) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_INFO, 'Невозможно войти под участником, такой участник не найден');
            return $this->redirect('/');
        }

        /** @var Identity $identity */
        $identity = Identity::findOne(['login' => $profile->phone_mobile]);

        if ($identity === null) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_INFO, 'Невозможно войти под участником, который не прошел регистрацию');
            return $this->redirect('/');
        }

        @\Yii::$app->user->logout();
        \Yii::$app->user->login($identity, 0);

        return $this->redirect('/dashboard/index');
    }
} 