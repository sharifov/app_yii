<?php

namespace backend\listeners;
use modules\profiles\common\models\Profile;
use yii\base\Event;
use modules\profiles\backend\models\ProfileSearch;
use backend\models\ProfileWithIdentitySearch;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yz\admin\grid\GridView;
use yz\icons\Icons;
use Yii;


/**
 * Class ProfilesController
 */
class ProfilesController 
{
    public static function whenBeforeAction(Event $event)
    {
        \Yii::$container->set(
            ProfileSearch::class,
            ProfileWithIdentitySearch::class
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

        $yarAdminNew=\yz\admin\models\AuthAssignment::findOne(['user_id' => Yii::$app->user->identity->id]);
        $grid = $event->sender;
        if(($yarAdminNew && $yarAdminNew->item_name == 'yar_admin_new') || (Yii::$app->user->identity->login) == 'admin') {
            $grid->columns = array_merge(array_slice($grid->columns, 0, -1), [
                [
                    'attribute' => 'identity__created_at',
                    'format' => 'date',
                    'label' => 'Дата регистрации',
                ],
                [
                    'class' => ActionColumn::class,
                    'template' => '{change-balance}',
                    'buttons' => [
                        'change-balance' => function ($url, Profile $model, $key) {
                            return Html::a(Icons::i('rub'), ['/manual/manage-bonuses/index', 'id' => $model->id], [
                                'class' => 'btn btn-default btn-sm',
                                'title' => 'Изменить баланс участника',
                            ]);
                        }
                    ]
                ]
            ], array_slice($grid->columns, -1));
        }else{
            $grid->columns = array_merge(array_slice($grid->columns, 0, -1), [
                [
                    'attribute' => 'identity__created_at',
                    'format' => 'date',
                    'label' => 'Дата регистрации',
                ],

            ], array_slice($grid->columns, -1));
        }
    }
}