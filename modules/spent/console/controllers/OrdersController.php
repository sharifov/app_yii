<?php


namespace modules\spent\console\controllers;


use console\base\Controller;
use marketingsolutions\finance\models\Transaction;
use ms\loyalty\catalog\common\models\CatalogOrder;
use ms\loyalty\prizes\payments\common\finances\PaymentPartner;
use ms\loyalty\prizes\payments\common\models\Payment;
use Yii;
use yii\base\InvalidConfigException;

class OrdersController extends Controller
{
    /**
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function actionReSave()
    {
        $badSum = 0;

        $dateStart = Yii::$app->formatter->asDate(new \DateTime(date('Y-m-01', strtotime("2019-03-01"))), 'YYYY-MM-dd 00:00:00');
        $dateEnd = Yii::$app->formatter->asDate(new \DateTime(date('Y-m-t', strtotime("2019-05-01"))), 'YYYY-MM-dd 23:59:59');

        $orders = CatalogOrder::find()
            ->where(['status' => [
                CatalogOrder::STATUS_WAITING,
                CatalogOrder::STATUS_NEW,
                CatalogOrder::STATUS_READY,

            ]])
            ->andWhere(['between', 'DATE(created_at)', $dateStart, $dateEnd])
            ->all();

        foreach ($orders as $order) {
            /** @var CatalogOrder $order */
            echo "[order [#{$order->id}]\n";
            $order->recalculate(true);

            foreach ($order->orderedCards as $card) {
                $card->include_nds = $card->card->is_nds;
                $card->save(false);
            }

            $transaction = Transaction::findOne([
                'type' => Transaction::OUTBOUND,
                'partner_type' => CatalogOrder::class,
                'partner_id' => $order->id
            ]);

            $badSum += ($order->profile_amount - $transaction->amount);
        }

        //---

        $payments = Payment::find()
            ->where(['status' => [
                Payment::STATUS_WAITING,
                Payment::STATUS_NEW,
                Payment::STATUS_PROCESSING,
                Payment::STATUS_SUCCESS,
            ]])
            ->andWhere(['between', 'DATE(created_at)', $dateStart, $dateEnd])
            ->all();

        foreach ($payments as $payment) {
            /** @var Payment $payment */
            echo "[payment #{$payment->id}]\n";
            $payment->save(false);

            $transaction = Transaction::findOne([
                'type' => Transaction::OUTBOUND,
                'partner_type' => PaymentPartner::class,
                'partner_id' => $payment->id
            ]);

            $badSum += ($payment->amount - $transaction->amount);
        }

        //---

        echo "sum: " . $badSum . "\n";

        echo "done\n";
    }
}