<?php
namespace frontend\listeners;

use modules\profiles\common\models\Profile;
use ms\loyalty\prizes\payments\common\models\Payment;
use yii\base\Event;
use yz\Yz;

class PaymentListener
{
    public function beforePaymentInsert(Event $event) {
        /** @var Payment $payment */
        $payment = $event->sender;

        if ($payment->payment_amount > 14500) {
            \Yii::$app->session->setFlash(
                Yz::FLASH_SUCCESS,
                'Сумма одного перевода не может превышать 14500 руб.'
            );

            exit;
        }

        return true;
    }
}