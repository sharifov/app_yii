<?php

namespace backend\listeners;

use backend\models\CatalogOrderWithProfileSearch;
use ms\loyalty\catalog\backend\models\CatalogOrderSearch;
use yii\base\Event;
use yz\admin\grid\GridView;


/**
 * Class CatalogOrders
 */
class CatalogOrders
{
    public static function whenBeforeAction(Event $event)
    {
        \Yii::$container->set(
            CatalogOrderSearch::class,
            CatalogOrderWithProfileSearch::class
        );

        Event::on(
            GridView::class,
            GridView::EVENT_SETUP_GRID,
            [get_called_class(), 'whenSetupGrid']
        );
    }

    public static function whenSetupGrid(Event $event)
    {
        /** @var GridView $grid */
        $grid = $event->sender;
        $grid->columns = array_merge(array_slice($grid->columns, 0, 2), [
            [
                'attribute' => 'profile__full_name',
                'label' => 'Участник',
            ],
        ], array_slice($grid->columns, 2));
    }
}