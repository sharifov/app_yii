<?php

namespace modules\sales\backend\controllers;

use backend\base\Controller;
use modules\profiles\backend\models\DealerSearch;
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\backend\models\SaleSearch;
use modules\sales\backend\models\SaleWithDataSearch;
use modules\sales\common\actions\DownloadDocumentAction;
use modules\sales\common\actions\DownloadPreviousDocumentAction;
use modules\sales\common\finances\DealerPartner;
use modules\sales\common\finances\ManagerPartner;
use modules\sales\common\models\CreateSale;
use modules\sales\common\models\Promotion;
use modules\sales\common\models\PromotionRule;
use modules\sales\common\models\Sale;
use modules\sales\common\sales\statuses\Statuses;
use marketingsolutions\finance\models\Transaction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Yii;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yz\admin\actions\ExportAction;
use yz\admin\grid\columns\DataColumn;
use yz\admin\grid\filters\DateRangeFilter;
use yz\Yz;

/**
 * SalesController implements the CRUD actions for Sale model.
 */
class SalesController extends Controller
{
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'verbs' => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		]);
	}

	public function actions()
	{
		return array_merge(parent::actions(), [
			'export'                     => [
				'class'        => ExportAction::className(),
				'dataProvider' => function ($params) {
					$searchModel  = Yii::createObject(SaleWithDataSearch::className());
					$dataProvider = $searchModel->search($params);

					return $dataProvider;
				},
			],
			'download-document'          => [
				'class' => DownloadDocumentAction::className(),
			],
			'download-previous-document' => [
				'class' => DownloadPreviousDocumentAction::className(),
			]
		]);
	}

	/**
	 * Lists all Sale models.
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel  = Yii::createObject(SaleWithDataSearch::className());
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->setSort([
            'defaultOrder' => ['created_at' => SORT_DESC],
        ]);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel'  => $searchModel,
			'columns'      => $this->getGridColumns(),
		]);
	}

	public function getGridColumns()
	{
		return [
			'id',
			[
				'attribute' => 'status',
				'titles'    => Sale::getStatusValues(),
				'filter'    => Sale::getStatusValues(),
				'labels'    => [
					Statuses::DRAFT        => DataColumn::LABEL_DEFAULT,
					Statuses::ADMIN_REVIEW => DataColumn::LABEL_INFO,
					Statuses::APPROVED     => DataColumn::LABEL_SUCCESS,
					Statuses::PAID         => DataColumn::LABEL_SUCCESS,
					Statuses::DECLINED     => DataColumn::LABEL_DANGER,
				]
			],
			[
				'label'  => 'Продажа',
				'format' => 'html',
				'value'  => function (Sale $model) {
//					if ($model->promotion->isProf()) {
//						return $model->kg_real . ' руб';
//					}
//					elseif ($model->promotion->isGold()) {
//						$str = $model->kg_real . ' кг за 2015';
//						if ($model->previous_kg_real) {
//							$str .= '</br>' . $model->previous_kg_real . ' кг за 2014';
//						}
//						return $str;
//					}
//					else {
//						return $model->kg_real . ' кг';
//					}
                    return Sale::getSumRub($model->id);

				}
			],
			['attribute' => 'promotion__name', 'label' => 'Акция'],
			['attribute' => 'dealer__name', 'label' => 'Дилер'],
			['attribute' => 'profile__full_name', 'label' => 'Руководитель ДЦ'],
			'bonuses',
			[
				'attribute' => 'sold_on',
				'format'    => 'date',
				'filter'    => DateRangeFilter::instance(),
			],
            [
                'attribute' => 'bonuses_paid_at',
                'format'    => 'date',
                'filter'    => DateRangeFilter::instance(),

            ],
			[
				'attribute' => 'created_at',
				'format'    => 'date',
				'filter'    => DateRangeFilter::instance(),
			],
			// 'updated_at:datetime',
		];
	}

	/**
	 * Updates an existing Sale model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		$model = $this->findModel($id);

		return $this->render('view', [
			'model' => $model,
		]);
	}

	public function actionSelectDealer()
	{
		$dealers      = Dealer::find()->orderBy(['name' => SORT_ASC])->all();
		$searchModel  = Yii::createObject(DealerSearch::className());
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('select_dealer', [
			'dealers'      => $dealers,
			'dataProvider' => $dataProvider,
			'searchModel'  => $searchModel,
			'columns'      => [
				'name',
				[
					'label' => 'Акция',
					'format' => 'raw',
					'value' => function (Dealer $dealer) {
						$value = '';
						foreach ($dealer->promotions as $promotion) {
							$value .= Html::a($promotion->name, Url::to(['create', 'dealer_id' => $dealer->id, 'promotion_id' => $promotion->id]), [
								'title' => 'Добавить продажу по акции',
								'class' => 'btn btn-primary btn-xs',
							]);
							$value .= ', ';
						}
						return $value;
					}
				],
			]
		]);
	}

	public function actionCreate($dealer_id, $promotion_id)
	{
		$dealer = Dealer::findOne($dealer_id);
		$promotion = Promotion::findOne($promotion_id);

		if ($dealer === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}

        $id = \Yii::$app->request->get('id');

		return $this->render('create', [
			'dealer'    => $dealer,
			'promotion' => $promotion,
            'id' => $id,
		]);
	}

	public function actionApp($id)
	{
		$model = $this->findModel($id);

		if ($model->statusManager->adminCanEdit() == false) {
			return $this->redirect(['view', 'id' => $id]);
		}

		return $this->render('app', compact('model'));
	}

	/**
	 * Deletes an existing Sale model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete(array $id)
	{
		if(is_array($id)){
		    $ids = array_flip($id);
		    unset($ids[1]);
		    $id = array_flip($ids);
        }
	    $status  = Yz::FLASH_SUCCESS;
		$message = is_array($id) ?
			\Yii::t('admin/t', 'Records were successfully deleted') : \Yii::t('admin/t', 'Record was successfully deleted');
		$id      = (array) $id;

		foreach ($id as $id_) {
			$model = $this->findModel($id_);
			if ($model->statusManager->canBeDeleted()) {
				$this->findModel($id_)->delete();
			}
			else {
				$status  = Yz::FLASH_WARNING;
				$message = 'Некоторые (или все) из выбранных продаж невозможно удалить, т.к. их статус не равен "Черновик"';
			}
		}

		\Yii::$app->session->setFlash($status, $message);

		return $this->redirect(['index']);
	}

	public function actionChangeStatus($id, $status)
	{
		$model = $this->findModel($id);

		if ($model->statusManager->adminCanSetStatus($status)) {
			switch ($status) {
				case Statuses::APPROVED:
					if ($this->approved($model)) {
						$model->statusManager->changeStatus($status);
						\Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, 'Статус успешно изменен, баллы за продажу подсчитаны');
					}
					else {
						\Yii::$app->session->setFlash(Yz::FLASH_ERROR, 'Произошла ошибка при подсчете баллов');
					}
					break;
				case Statuses::PAID:
					if ($this->paid($model)) {
						$model->statusManager->changeStatus($status);
						\Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, 'Статус успешно изменен, баллы за продажу начислены');
					}
					else {
						\Yii::$app->session->setFlash(Yz::FLASH_ERROR, 'Произошла ошибка при начислении баллов');
					}
					break;
				case Statuses::DECLINED:
					if ($this->declined($model)) {
						$model->statusManager->changeStatus($status);
						\Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, 'Статус успешно изменен, баллы за продажу сброшены');
					}
					else {
						\Yii::$app->session->setFlash(Yz::FLASH_ERROR, 'Произошла ошибка при сбросе баллов');
					}
					break;
				default:
					$model->statusManager->changeStatus($status);
					\Yii::$app->session->setFlash(Yz::FLASH_SUCCESS, 'Статус успешно изменен');
			}
		}
		else {
			\Yii::$app->session->setFlash(Yz::FLASH_ERROR, 'Статус не может быть изменен');
		}

		return $this->redirect(\Yii::$app->request->referrer ?: Url::home());
	}

	/**
	 * Finds the Sale model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Sale the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Sale::findOne($id)) !== null) {
			return $model;
		}
		else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * @param Sale $sale
	 * @return bool
	 */
	public function declined(Sale $sale)
	{
		$sale->updateAttributes(['bonuses' => null, 'manager_bonuses' => null, 'dealer_bonuses' => null]);

		return true;
	}

	/**
	 * @param Sale $sale
	 * @return bool
	 * @throws \yii\db\Exception
	 */
	public function approved(Sale $sale)
	{
		$promotion   = $sale->promotion;
		$dealer      = $sale->dealer;
		$transaction = \Yii::$app->db->beginTransaction();

		$sale->x                           = $dealer->x;
		$sale->xx                          = $dealer->xx;
		$sale->manager_commission          = $dealer->manager_commission;
		$sale->manager_commission_included = $dealer->manager_commission_included;

		if ($promotion->isGold()) {
			$language      = new ExpressionLanguage();
			$promotionRule = null;

			foreach ($sale->getPromotionRules() as $rule) {
				/** @var PromotionRule $rule */
				$conditionArgs = [
					'previous' => $sale->previous_kg_real,
					'kg'       => $sale->kg_real
				];

				if ($language->evaluate($rule->condition, $conditionArgs)) {
					$promotionRule = $rule;
					break;
				}
			}

			if ($promotionRule === null) {
				return false; # we have to find rule to calculate bonuses
			}

			$ruleArgs = [
				'previous' => $sale->previous_kg_real,
				'kg'       => $sale->kg_real,
				'x'        => $sale->x_real,
				'xx'       => $sale->xx_real,
			];

			$bonuses = intval($language->evaluate($promotionRule->rule, $ruleArgs));

			if (!$bonuses) {
				return false; # failed bonuses calculation
			}

			$sale->bonuses = $bonuses;
			$sale->rule    = $promotionRule->rule;
		}
		else {
			$sale->updateKg();
			$sale->updateBonuses();
		}

		# count bonuses to manager and dealer
		if ($sale->promotion->isProf()) {
			# there is no reward to manager for this action
			$sale->manager_bonuses = 0;
			$sale->dealer_bonuses  = $sale->bonuses;
		}
		else {
			$sale->manager_bonuses = intval(round($sale->bonuses * $sale->manager_commission / 100));

			if ($sale->manager_commission_included) {
				$sale->dealer_bonuses = $sale->bonuses - $sale->manager_bonuses;
			}
			else {
				$sale->dealer_bonuses = $sale->bonuses;
			}
		}

		$sale->save(false);

		$transaction->commit();

		return true;
	}

	/**
	 * @param Sale $sale
	 * @throws \yii\base\Exception
	 * @throws \yii\db\Exception
	 *
	 * @return bool
	 */
	public function paid(Sale $sale)
	{
		$promotion = $sale->promotion;
		$dealer    = $sale->dealer;
        $purse = $dealer->findPurseByPromotion($promotion->id);

		/** @var Profile $manager */
		$manager = $dealer->manager;

		$transaction = \Yii::$app->db->beginTransaction();

		$purse->addTransaction(Transaction::factory(
			Transaction::INCOMING,
			$sale->dealer_bonuses,
			new DealerPartner($sale->id),
			'Бонусы дилеру "' . $dealer->name . '" за продажу #' . $sale->id
		));
        Sale::addAdminId($sale->id , $purse->id);

		$manager->purse->addTransaction(Transaction::factory(
			Transaction::INCOMING,
			$sale->manager_bonuses,
			new ManagerPartner($sale->id),
			'Бонусы участнику "' . $manager->full_name . '" за продажу #' . $sale->id
		));

        Sale::addAdminId($sale->id , $manager->purse->id);

		$sale->bonuses_paid_at = new Expression('NOW()');
		$sale->save(false);

		$transaction->commit();

		return true;
	}

    /**
     * @return string|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     */
	public function actionCreateSale()
    {
        $model = new CreateSale();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));

            return $this->getCreateUpdateResponse($model);
        } else {
            return $this->render('create-sale', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionUpdate($id)
    {
        $model = CreateSale::findOne($id);

        if (null === $model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, \Yii::t('admin/t', 'Record was successfully created'));

            return $this->getCreateUpdateResponse($model);
        } else {
            return $this->render('create-sale', [
                'model' => $model,
            ]);
        }
    }
}
