<?php

namespace backend\listeners;
use ms\loyalty\feedback\backend\models\MessageSearch;
use yii\base\Event;
use yz\admin\grid\GridView;
use backend\models\MessageWithProfileSearch;


/**
 * Class MessagesController
 */
class MessagesController
{
    public static function whenBeforeAction()
    {
        \Yii::$container->set(
            MessageSearch::class,
            MessageWithProfileSearch::class
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