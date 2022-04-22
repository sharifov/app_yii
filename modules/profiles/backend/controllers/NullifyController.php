<?php

namespace modules\profiles\backend\controllers;

use modules\profiles\common\models\Profile;
use Yii;
use modules\profiles\common\models\Nullify;
use modules\profiles\backend\models\NullifySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yz\admin\actions\ExportAction;
use yz\admin\traits\CheckAccessTrait;
use yz\admin\traits\CrudTrait;
use yz\admin\contracts\AccessControlInterface;

/**
 * NullifyController implements the CRUD actions for Nullify model.
 */
class NullifyController extends Controller implements AccessControlInterface
{
    use CrudTrait, CheckAccessTrait;

    public function actions()
    {
        return array_merge(parent::actions(), [
            'export' => [
                'class' => ExportAction::className(),
                'searchModel' => function($params) {
                    /** @var NullifySearch $searchModel */
                    return Yii::createObject(NullifySearch::className());
                },
                'dataProvider' => function($params, NullifySearch $searchModel) {
                        $dataProvider = $searchModel->search($params);
                        return $dataProvider;
                    },
            ]
        ]);
    }

    /**
     * Lists all Nullify models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var NullifySearch $searchModel */
        $searchModel = Yii::createObject(NullifySearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(NullifySearch $searchModel)
    {
        return [
            [
                'attribute' => 'profile_id',
                'label' => 'ID профиля участника',
            ],
            [
                'attribute' => 'profilePhone',
                'label' => 'Телефон участника',
            ],
            [
                'attribute' => 'profileName',
                'label' => 'ФИО участника',
            ],
			[
                'attribute' => 'profileRole',
                'label' => 'Роль участника',
                'filter' => Profile::getRoleValues(),
                'value' => function($model){
                    return Profile::getRoleValues()[$model->profileRole];
                }
            ],
            [
                'attribute' => 'sum',
                'label' => 'Сумма обнуления',
            ],
			'created_at:datetime',
        ];
    }

    /**
     * Creates a new Nullify model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Nullify;

		if ($model->load(\Yii::$app->request->post()) && $model->save()) {
			\Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));
			return $this->getCreateUpdateResponse($model);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Nullify model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

		if ($model->load(\Yii::$app->request->post()) && $model->save()) {
			\Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully updated'));
			return $this->getCreateUpdateResponse($model);
		}

        return $this->render('update', [
            'model' => $model,
        ]);
	}


    /**
     * Deletes an existing Nullify model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $id)
    {
        $message = is_array($id) ?
            \Yii::t('admin/t', 'Records were successfully deleted') : \Yii::t('admin/t', 'Record was successfully deleted');
        $id = (array)$id;

        foreach ($id as $id_)
            $this->findModel($id_)->delete();

        \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, $message);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Nullify model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nullify the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Nullify::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
