<?php

namespace modules\sales\frontend\controllers;

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\DealerPayment;
use modules\profiles\common\models\Profile;
use modules\profiles\common\models\PromotionProfile;
use modules\sales\common\finances\DealerPaymentPartner;
use modules\sales\common\models\Promotion;
use modules\sales\frontend\base\Controller;
use marketingsolutions\finance\models\Purse;
use marketingsolutions\finance\models\Transaction;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SellersController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $profile = $this->findManager();

                            return $profile && $profile->isManager();
                        }
                    ],
                    [
                        'allow' => false,
                    ]
                ],
            ],
        ];
    }

    /**
     * @param $dealer_id
     * @param $promotion_id
     * @return string
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionIndex($dealer_id, $promotion_id)
    {
        /** @var Dealer $dealer */
        $dealer = $this->findDealer($dealer_id);

        $query = $dealer->getProfiles()
            ->leftJoin(['pp' => PromotionProfile::tableName()], 'pp.profile_id = {{%profiles}}.id')
            ->leftJoin(['p' => Purse::tableName()], 'p.owner_id = {{%profiles}}.id')
            ->where([
                Profile::tableName() . '.role' => Profile::ROLE_SALES,
                'pp.promotion_id' => $promotion_id
            ])
            ->orderBy('p.balance DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => 100),
        ]);

        $promotion = $this->findPromotion($promotion_id);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'dealer' => $dealer,
            'promotion' => $promotion,
        ]);
    }

    /**
     * @param $dealer_id
     * @param $promotion_id
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function actionView($dealer_id, $promotion_id, $id)
    {
        /** @var Profile $manager */
        $manager = $this->findManager();
        $dealer = $this->findDealer($dealer_id);
        $promotion = $this->findPromotion($promotion_id);
        $purse = $dealer->findPurseByPromotion($promotion_id);
        $recipient = $this->findRecipient($id);

        $model = new DealerPayment();

        $model->manager_id = $manager->id;
        $model->dealer_id = $dealer->id;
        $model->recipient_id = $recipient->id;
        if ($model->load(\Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();

            if ($model->save()) {
                if ($model->bonuses <= $purse->balance) {

                    $purse->addTransaction(Transaction::factory(
                        Transaction::OUTBOUND,
                        $model->bonuses,
                        new DealerPaymentPartner($model->id),
                        'Списание при переводе уч-ку от дилера "' . $dealer->name . '"'
                        . ' по акции"'
                    ));

                    $recipient->purse->addTransaction(Transaction::factory(
                        Transaction::INCOMING,
                        $model->bonuses,
                        new DealerPaymentPartner($model->id),
                        'Перевод уч-ку от дилера "' . $dealer->name . '"'
                        . ' по акции"'
                    ));

                    $transaction->commit();
                    \Yii::$app->session->setFlash(\yz\Yz::FLASH_SUCCESS, "На счет участника было переведено {$model->bonuses} бонусных баллов");

                    return $this->redirect(['view', 'id' => $id, 'dealer_id' => $dealer_id, 'promotion_id' => $promotion_id]);
                }
                else {
                    $model->addError('bonuses', 'Указанная сумма превышает баланс диллера по этой акции');
                    $transaction->rollBack();
                }
            }
            else {
                $transaction->rollBack();
            }
        }

        $query = DealerPayment::find()->where(['recipient_id' => $recipient->id])->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => 20),
        ]);

        $promotion = $this->findPromotion($promotion_id);

        return $this->render('view', [
            'model' => $model,
            'dealer' => $dealer,
            'promotion' => $promotion,
            'dataProvider' => $dataProvider,
            'recipient' => $recipient,
        ]);
    }

    /**
     * @param $promotion_id
     * @return Promotion
     * @throws NotFoundHttpException
     */
    private function findPromotion($promotion_id)
    {
        $promotion = Promotion::findOne($promotion_id);

        if (null === $promotion) {
            throw new NotFoundHttpException();
        }

        return $promotion;
    }

    private function checkDealerBalance(Dealer $dealer, DealerPayment $payment)
    {
        return $dealer->purse->balance >= $payment->bonuses;
    }

    private function validateDealer($dealer_id)
    {
        /** @var Profile $manager */
        $manager = \Yii::$app->user->identity->profile;
        $profileDealers = ArrayHelper::getColumn($manager->dealers, 'id');

        $validator = DynamicModel::validateData(compact('dealer_id'), [
            ['dealer_id', 'required'],
            ['dealer_id', 'in', 'range' => $profileDealers]
        ]);

        if ($validator->hasErrors()) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @param $dealer_id
     * @return Dealer
     * @throws NotFoundHttpException
     */
    private function findDealer($dealer_id)
    {
        $this->validateDealer($dealer_id);

        $dealer = Dealer::findOne($dealer_id);
        if ($dealer === null) {
            throw new NotFoundHttpException();
        }

        return $dealer;
    }

    /**
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    private function findRecipient($id)
    {
        $recipient = Profile::findOne($id);

        if (!$recipient) {
            throw new NotFoundHttpException();
        }

        return $recipient;
    }

    /**
     * @return Profile
     * @throws NotFoundHttpException
     */
    private function findManager()
    {
        /** @var Profile $profile */
        $profile = \Yii::$app->user->identity->profile;
        if ($profile === null) {
            throw new NotFoundHttpException();
        }
        if ($profile->isManager() == false) {
            throw new NotFoundHttpException();
        }

        return $profile;
    }
}
