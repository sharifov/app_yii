<?php

namespace backend\listeners;

use backend\models\OrderedCardWithDealerSearch;
use modules\profiles\common\models\Dealer;
use ms\loyalty\catalog\backend\models\OrderedCardWithProfileSearch;
use Yii;
use yii\base\Event;
use yz\admin\grid\GridView;


/**
 * Class OrderedCards
 */
class OrderedCards
{
    public static function whenBeforeAction(Event $event)
    {
        Yii::$container->set(
            OrderedCardWithProfileSearch::class,
            OrderedCardWithDealerSearch::class
        );

        Event::on(
            GridView::class,
            GridView::EVENT_SETUP_GRID,
            [get_called_class(), 'whenSetupGrid']
        );
    }

    public static function whenSetupGrid(Event $event)
    {
        /**
         * @var GridView $grid
         */
        $grid = $event->sender;
        $grid->columns = array_merge(array_slice($grid->columns, 0, 9), [
            [
                'attribute' => 'dealers_names',
                'value' => function (OrderedCardWithDealerSearch $model) {
                    return $model->dealers_names ?? '';
                },
                'filter' => Dealer::getOptions('name')
            ]
        ], array_slice($grid->columns, 9));
    }
}