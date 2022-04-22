<?php

namespace modules\sms\console\controllers;

use console\base\Controller;
use modules\profiles\common\models\Profile;
use modules\sms\common\models\Sms;
use modules\sms\common\models\SmsLog;


class SendController extends Controller
{
    public function actionRun()
    {
        $smsQuery = Sms::find()->where(['status' => Sms::STATUS_NEW]);

        foreach ($smsQuery->each() as $sms) {
            /** @var $sms Sms */
            $sms->status = Sms::STATUS_PROGRESS;
            $sms->updateAttributes(['status']);

            $message = $sms->message;
            $phoneNumbers = [];

            if ($sms->type == Sms::TYPE_INDIVIDUAL) {
                $phoneNumbers = explode(';', $sms->to);
            }
            elseif ($sms->type == Sms::TYPE_PROFILES) {
                $phoneNumbers = Profile::find()
					->where(['like', 'notify', 'sms'])
					->select('phone_mobile')->column();
            }

            foreach ($phoneNumbers as $phoneNumber) {
                $phoneNumber = trim($phoneNumber);

                $result = \Yii::$app->sms->send($phoneNumber, $message);

				SmsLog::add([
					'sms_id' => $sms->id,
					'type' => SmsLog::TYPE_BROADCAST,
					'phone_mobile' => $phoneNumber,
					'message' => $message,
					'status' => $result,
				]);

                $sms->sent_to = empty($sms->sent_to) ? $phoneNumber : $sms->sent_to . ' ' . $phoneNumber;
                $sms->updateAttributes(['sent_to']);

                sleep(1);
            }

            $sms->status = Sms::STATUS_DONE;
            $sms->updateAttributes(['status']);
        }
    }
}