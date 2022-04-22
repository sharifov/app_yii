<?php

namespace backend\listeners;

use backend\models\CatalogOrderWithProfileSearch;
use backend\models\PaymentWithProfileSearch;
use ms\loyalty\catalog\backend\models\CatalogOrderSearch;
use ms\loyalty\prizes\payments\backend\models\PaymentSearch;
use yii\base\Event;
use yz\admin\grid\GridView;


/**
 * Class CatalogOrders
 */
class PaymentsController
{
    public static function whenBeforeAction(Event $event)
    {
        \Yii::$container->set(
            PaymentSearch::class,
            PaymentWithProfileSearch::class
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
            [
                'attribute' => 'profile__phone_mobile',
                'label' => 'Номер телефона',
            ],
            [
                'attribute' => 'dealer__name',
                'label' => 'Дилер',
            ],
        ], array_slice($grid->columns, 2));
    }
}