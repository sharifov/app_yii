<?php

namespace modules\profiles\frontend\widgets;
use yii\base\Widget;


/**
 * Class ProfileTopMenu
 */
class ProfileTopMenu extends Widget
{
    public function run()
    {
        return $this->render('profile-top-menu');
    }

}