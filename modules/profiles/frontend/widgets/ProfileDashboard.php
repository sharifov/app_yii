<?php

namespace modules\profiles\frontend\widgets;
use ms\loyalty\contracts\identities\IdentityInterface as loyaltyIdentityInterface;
use yii\base\Widget;
use yii\web\IdentityInterface;


/**
 * Class ProfileDashboard
 */
class ProfileDashboard extends Widget
{
    public function run()
    {
        $user = \Yii::$app->user;
        /** @var IdentityInterface|loyaltyIdentityInterface $identity */
        $identity = $user->identity;
        $profile = $identity->profile;
        return $this->render('profile-dashboard', compact('user', 'identity', 'profile'));
    }

}