<?php

namespace modules\manual\backend\controllers;

use backend\base\Controller;
use modules\manual\backend\forms\ChangeBalanceForm;
use ms\loyalty\contracts\profiles\ProfileFinderInterface;
use yii\web\NotFoundHttpException;
use yz\Yz;


/**
 * Class ManageBonusesController
 */
class ManageBonusesController extends Controller
{
    /**
     * @var ProfileFinderInterface
     */
    private $profileFinder;

    public function __construct($id, $module, ProfileFinderInterface $profileFinder, $config = [])
    {
        $this->profileFinder = $profileFinder;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex($id)
    {
        $profile = $this->findProfile($id);

        /** @noinspection PhpParamsInspection */
        $model = new ChangeBalanceForm($profile, \Yii::$app->user->identity);

        if ($model->load(\Yii::$app->request->post()) && $model->change()) {
            \Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, 'Баланс участника успешно изменен');
            return $this->refresh();
        }

        return $this->render('index', compact('model'));
    }

    /**
     * @param $id
     * @return \ms\loyalty\contracts\profiles\ProfileInterface|null
     * @throws NotFoundHttpException
     */
    protected function findProfile($id)
    {
        if (($profile = $this->profileFinder->findByProfileId($id)) === null) {
            throw new NotFoundHttpException('Участник не найден');
        }

        return $profile;
    }
}