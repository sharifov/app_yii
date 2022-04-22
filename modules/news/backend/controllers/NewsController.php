<?php

namespace modules\news\backend\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yz\Yz;
use yz\admin\actions\ExportAction;
use yz\admin\traits\CheckAccessTrait;
use yz\admin\traits\CrudTrait;
use yz\admin\contracts\AccessControlInterface;
//use modules\profiles\common\managers\NotifyManager;
use modules\profiles\common\models\Profile;
use modules\news\common\models\News;
use modules\news\backend\models\NewsSearch;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller implements AccessControlInterface
{
    use CrudTrait, CheckAccessTrait;

    public function actions()
    {
        return array_merge(parent::actions(), [
            'export' => [
                'class' => ExportAction::class,
                'searchModel' => function($params) {
                    /** @var NewsSearch $searchModel */
                    return Yii::createObject(NewsSearch::class);
                },
                'dataProvider' => function($params, NewsSearch $searchModel) {
                        $dataProvider = $searchModel->search($params);
                        return $dataProvider;
                    },
            ]
        ]);
    }

    /**
     * Lists all News models.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        /** @var NewsSearch $searchModel */
        $searchModel = Yii::createObject(NewsSearch::class);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'columns' => $this->getGridColumns($searchModel),
        ]);
    }

    public function getGridColumns(NewsSearch $searchModel)
    {
        return [
			'id',
			'title',
            'teaser:ntext',
			'enabled:boolean',
           // 'is_push_sent:boolean',
            'created_at:datetime',
        ];
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionCreate()
    {
        $model = new News;
        $model->enabled = true;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, "Новость '{$model->title}' успешно создана.");

            return $this->getCreateUpdateResponse($model);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, Yii::t('admin/t', 'Record was successfully updated'));

			return $this->getCreateUpdateResponse($model);
		}

        return $this->render('update', [
            'model' => $model,
        ]);
	}

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete(array $id)
    {
        $message = is_array($id)
            ? Yii::t('admin/t', 'Records were successfully deleted')
            : Yii::t('admin/t', 'Record was successfully deleted');
        $id = (array)$id;

        foreach ($id as $id_) {
            $this->findModel($id_)->delete();
        }

        Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, $message);

        return $this->redirect(['index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param News $publication
     * @return bool
     * @throws \yii\base\Exception
     */
    protected function sendProfileNewsCreatedPush(News $publication)
    {
        if ($publication->is_push_sent || $publication->enabled == false) {
            return false;
        }

        $profileIds = [];
        if ($publication->brand_id) {
            $profileIds = Profile::find()
                ->select(['id'])
                ->column();
        }

        $title = "Появилась новая публикация";
        $body = strtr("\":title\"\n\n:teaser", [
            ':title' => $publication->title,
            ':teaser' => $publication->teaser,
        ]);

        $data = [
            "url" => "news/news/index",
            "menu" => "/news/",
        ];

        $publication->updateAttributes(['is_push_sent' => true]);

        return (new NotifyManager())->createPush($title, $body, $data, $profileIds);
    }
}
