<?php


namespace modules\sales\console\controllers;


use console\base\Controller;
use DateTime;
use marketingsolutions\finance\models\Transaction;
use ms\loyalty\catalog\common\models\CatalogOrder;
use ms\loyalty\catalog\common\models\OrderedCard;
use ms\loyalty\finances\common\components\CompanyAccount;
use ms\loyalty\prizes\payments\common\finances\PaymentPartner;
use ms\loyalty\prizes\payments\common\models\Payment;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;

class FixController extends Controller
{
    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionMay()
    {
        $paymentsIds = [];
        $paymentTransactionsData = [];

        $orderIds = [];
        $orderTransactionsData = [];


        $dateStart = new DateTime(date('Y-m-01', strtotime("2019-05-01")));
        $dateEnd = new DateTime(date("Y-m-t", strtotime("2019-05-01")));

        $transactions = Transaction::find()
            ->where([
                'type' => Transaction::OUTBOUND,
                'partner_type' => [
                    PaymentPartner::class,
                    CatalogOrder::class
                ]
            ])
            ->andWhere(['<>', 'purse_id', CompanyAccount::getPurse()->id])
            ->andWhere([
                'between', 'DATE(created_at)',
                Yii::$app->formatter->asDate($dateStart, 'YYYY-MM-dd 00:00:00'),
                Yii::$app->formatter->asDate($dateEnd, 'YYYY-MM-dd 23:59:59')
            ])
            ->all();

        foreach ($transactions as $transaction) {
            /** @var Transaction $transaction */
            switch ($transaction->partner_type) {
                case PaymentPartner::class:
                    $paymentsIds[] = $transaction->partner_id;
                    $paymentTransactionsData[$transaction->partner_id] = $transaction->amount;
                    break;
                case CatalogOrder::class:
                    $orderIds[] = $transaction->partner_id;
                    $orderTransactionsData[$transaction->partner_id] = $transaction->amount;
                    break;
            }
        }

        if (count($paymentsIds)) {
            $this->fixPayments($paymentsIds, $paymentTransactionsData);
        }

        if (count($orderIds)) {
            $this->fixOrders($orderIds, $orderTransactionsData);
        }

    }

    /**
     * @param array $ids
     * @param array $data
     * @throws Exception
     */
    protected function fixPayments($ids, $data)
    {
        $payments = Payment::find()
            ->where(['id' => $ids])
            ->all();

        foreach ($payments as $payment) {
            /** @var Payment $payment */
            $transactionAmount = $data[$payment->id];
            if (($difference = $payment->amount - $transactionAmount) !== 0) {

                echo 'payment: ' . $payment->id . "\n";

                $commissionDifference = $payment->commission_amount - $difference;
                $amountDifference = $payment->amount - $difference;

                Yii::$app->db->createCommand("
                    update {{%payments}}
                    set `commission_amount` = {$commissionDifference},
                    `amount` = {$amountDifference}
                    where `id` = {$payment->id}
                ")->execute();
            }
        }
    }


    /**
     * @param array $ids
     * @param array $data
     * @throws Exception
     */
    protected function fixOrders($ids, $data)
    {
        $orders = CatalogOrder::find()
            ->where(['id' => $ids])
            ->all();

        foreach ($orders as $order) {
            /** @var CatalogOrder $order */
            $transactionAmount = $data[$order->id];
            if (($difference = $order->profile_amount - $transactionAmount) !== 0) {

                echo 'order: ' . $order->id . "\n";

                $commissionDifference = $order->profile_commission_amount - $difference;
                $amountDifference = $order->profile_amount - $difference;

                Yii::$app->db->createCommand("
                    update {{%catalog_orders}}
                    set `profile_commission_amount` = {$commissionDifference},
                    `profile_amount` = {$amountDifference}
                    where `id` = {$order->id}
                ")->execute();
            }
        }
    }

    public function actionFixDetails()
    {
        $dateStart = new DateTime(date('Y-m-01', strtotime("2019-05-01")));
        $dateEnd = new DateTime(date("Y-m-t", strtotime("2019-05-01")));

        $orders = CatalogOrder::find()
            ->andWhere([
                'between', 'DATE(created_at)',
                Yii::$app->formatter->asDate($dateStart, 'YYYY-MM-dd 00:00:00'),
                Yii::$app->formatter->asDate($dateEnd, 'YYYY-MM-dd 23:59:59')
            ])
            ->all();

        foreach ($orders as $order) {
            /** @var CatalogOrder $order */

            echo 'cards by order: ' . $order->id . "\n";

            $details = OrderedCard::find()
                ->where(['catalog_order_id' => $order->id])
                ->all();

            $detailsData = [];
            $totalAmount = 0;
            $totalCommission = 0;
            foreach ($details as $detail) {
                /** @var OrderedCard $detail */
                $totalAmount += $detail->profile_amount;
                $totalCommission += $detail->profile_commission_amount;

                $detailsData[$detail->id][] = [
                    'id' => $detail->id,
                    'amount' => $detail->profile_amount,
                    'commission' => $detail->profile_commission_amount
                ];
            }

            $amountDifference = $totalAmount - $order->profile_amount;
            $commissionDifference = $totalCommission - $order->profile_commission_amount;

            if ($amountDifference === $commissionDifference) {
                if ($totalCommission === $commissionDifference) {
                    foreach ($details as $detail) {
                        $amount = $detail->profile_amount - $detail->profile_commission_amount;
                        Yii::$app->db->createCommand("
                            update {{%ordered_cards}}
                            set `profile_commission_amount` = 0,
                            `profile_amount` = {$amount}
                            where `id` = {$detail->id}
                        ")->execute();
                    }
                }
            }
        }
    }
}