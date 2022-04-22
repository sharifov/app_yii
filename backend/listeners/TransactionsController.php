<?php
namespace backend\listeners;

use ms\loyalty\finances\common\models\Transaction;
use yii\base\Event;
use yz\admin\grid\GridView;
use yz\admin\grid\filters\DateRangeFilter;

/**
 * Class TransactionsController
 */
class TransactionsController
{
    public static function whenBeforeAction(Event $event)
    {
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
        $grid->columns = array_merge(array_slice($grid->columns, 0, 11), [
            [
                'attribute' => 'created_at',
                'format' => 'html',
                'value' => function (Transaction $transaction) {
                    return (new \DateTime($transaction->created_at))->format('d.m.Y H:i:s');
                },
                'filter' => DateRangeFilter::instance(),
            ],
        ], array_slice($grid->columns, 12));
    }
}