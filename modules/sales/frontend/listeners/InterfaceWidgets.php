<?php

namespace modules\sales\frontend\listeners;

use modules\sales\frontend\widgets\SalesDashboard;
use ms\loyalty\contracts\widgets\WidgetsCollectionInterface;
use yii\base\Event;


/**
 * Class InterfaceWidgets
 */
class InterfaceWidgets
{
    public static function whenInitCollection(Event $event)
    {
        /** @var WidgetsCollectionInterface $dashboard */
        $dashboard = $event->sender;

        $dashboard->addWidget('bonuses', SalesDashboard::class);
    }
}