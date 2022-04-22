<?php

// Dependency injection

use ms\loyalty\theme\widgets\InterfaceWidgets;

$listener = new \marketingsolutions\events\Listener(
    new \marketingsolutions\events\PatternEventsProvider(),
    new \marketingsolutions\events\PrefixMethodFinder()
);

\Yii::$container->set(
    \ms\loyalty\contracts\prizes\PrizeRecipientInterface::class,
    function() {
        return Yii::$app->user->identity->profile;
    }
);

// Events

// Identity dashboard
\yii\base\Event::on(
    InterfaceWidgets::class,
    \ms\loyalty\contracts\widgets\WidgetsCollectionInterface::EVENT_INIT_COLLECTION,
    [\ms\loyalty\identity\phones\frontend\listeners\InterfaceWidgets::class, 'whenInitCollection']
);

// Profile dashboard
\yii\base\Event::on(
    InterfaceWidgets::class,
    \ms\loyalty\contracts\widgets\WidgetsCollectionInterface::EVENT_INIT_COLLECTION,
    [\modules\profiles\frontend\listeners\InterfaceWidgets::class, 'whenInitCollection']
);

// Electronic catalog dashboard
\yii\base\Event::on(
    InterfaceWidgets::class,
    \ms\loyalty\contracts\widgets\WidgetsCollectionInterface::EVENT_INIT_COLLECTION,
    [\ms\loyalty\catalog\frontend\listeners\InterfaceWidgets::class, 'whenInitCollection']
);

// Sales bonuses dashboard
\yii\base\Event::on(
    InterfaceWidgets::class,
    \ms\loyalty\contracts\widgets\WidgetsCollectionInterface::EVENT_INIT_COLLECTION,
    [\modules\sales\frontend\listeners\InterfaceWidgets::class, 'whenInitCollection']
);

// Feedback dashboard
\yii\base\Event::on(
    InterfaceWidgets::class,
    \ms\loyalty\contracts\widgets\WidgetsCollectionInterface::EVENT_INIT_COLLECTION,
    [\ms\loyalty\feedback\frontend\listeners\InterfaceWidgets::class, 'whenInitCollection']
);

// Payments dashboard
\yii\base\Event::on(
    InterfaceWidgets::class,
    \ms\loyalty\contracts\widgets\WidgetsCollectionInterface::EVENT_INIT_COLLECTION,
    [\ms\loyalty\prizes\payments\frontend\listeners\InterfaceWidgets::class, 'whenInitCollection']
);


Yii::$container->set(
    InterfaceWidgets::class,
    [
        'groupOrdering' => [
            'top-menu' => [
                \ms\loyalty\identity\phones\frontend\widgets\AuthTopMenu::class,
                \ms\loyalty\feedback\frontend\widgets\FeedbackTopMenu::class,
                \modules\profiles\frontend\widgets\ProfileTopMenu::class,
            ]
        ]
    ]
);

\yii\base\Event::on(
    \ms\loyalty\prizes\payments\common\models\Payment::class,
    \ms\loyalty\prizes\payments\common\models\Payment::EVENT_BEFORE_INSERT,
    [frontend\listeners\PaymentListener::class, 'beforePaymentInsert']
);
