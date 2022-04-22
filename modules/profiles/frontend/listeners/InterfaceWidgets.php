<?php

namespace modules\profiles\frontend\listeners;

use modules\profiles\frontend\widgets\ProfileDashboard;
use modules\profiles\frontend\widgets\ProfileTopMenu;
use ms\loyalty\contracts\widgets\WidgetsCollectionInterface;
use yii\base\Event;


/**
 * Class InterfaceWidgets
 */
class InterfaceWidgets
{
    public static function whenInitCollection(Event $event)
    {
        /** @var \ms\loyalty\contracts\widgets\WidgetsCollectionInterface $dashboard */
        $dashboard = $event->sender;

        $dashboard->addWidget('profile', ProfileDashboard::class);
        $dashboard->addWidget('top-menu', ProfileTopMenu::class);
    }
}