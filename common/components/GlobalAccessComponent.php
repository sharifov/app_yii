<?php


namespace common\components;


use modules\profiles\common\models\Profile;
use ms\loyalty\identity\phones\common\models\Identity;
use Yii;
use yii\base\Component;

class GlobalAccessComponent extends Component
{
    public function init()
    {
        /**
         * @var Identity $identity
         * @var Profile $profile
         */
        if (Yii::$app->user->isGuest == false && $identity = Yii::$app->user->identity ) {
            if ($profile = $identity->profile) {
                if ($profile->isActive() === false) {
                    $allowedUrls = [
                        'feedback/messages/add',
                        'site/blocked',
                        'identity/auth/logout'
                    ];
                    if (!in_array(Yii::$app->request->pathInfo, $allowedUrls)) {
                        Yii::$app->response->redirect(['/site/blocked']);
                    }
                }
            }
        }

        parent::init();
    }
}