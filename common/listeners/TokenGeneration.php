<?php

namespace common\listeners;
use modules\profiles\common\models\Profile;
use ms\loyalty\identity\phones\frontend\forms\TokenGenerationForm;
use yii\base\Event;


/**
 * Class TokenGeneration
 */
class TokenGeneration
{
    public static function whenAfterValidate(Event $event)
    {
        /** @var TokenGenerationForm $sender */
        $sender = $event->sender;

        $profile = Profile::findOne(['phone_mobile' => $sender->phoneMobile]);

        if ($profile === null) {
            $sender->addError('phone_mobile', 'Данный номер телефона не зарегистрирован в акции. '.
                'Просим отправить заявку по адресу market7@grasaro.ru или воспользоваться разделом "Помощь"');
        }
    }
}