<?php

// Events

/**
 * Catalog module
 */

use yii\base\Event;

Event::on(
    \ms\loyalty\catalog\backend\controllers\CatalogOrdersController::class,
    \ms\loyalty\catalog\backend\controllers\CatalogOrdersController::EVENT_BEFORE_ACTION,
    [\backend\listeners\CatalogOrders::class, 'whenBeforeAction']
);


/**
 * Payments module
 */
Event::on(
    \ms\loyalty\prizes\payments\backend\controllers\PaymentsController::class,
    \ms\loyalty\prizes\payments\backend\controllers\PaymentsController::EVENT_BEFORE_ACTION,
    [\backend\listeners\PaymentsController::class, 'whenBeforeAction']
);


/**
 * Electronic certificates
 */
Event::on(
    \ms\loyalty\catalog\backend\controllers\OrderedCardsController::class,
    \ms\loyalty\catalog\backend\controllers\OrderedCardsController::EVENT_BEFORE_ACTION,
    [\backend\listeners\OrderedCards::class, 'whenBeforeAction']
);


/**
 * Feedback module
 */
Event::on(
    \ms\loyalty\feedback\backend\controllers\MessagesController::class,
    \ms\loyalty\feedback\backend\controllers\MessagesController::EVENT_BEFORE_ACTION,
    [\backend\listeners\MessagesController::class, 'whenBeforeAction']
);


/**
 * Bonus lists module
 */
Event::on(
    \ms\loyalty\bonuses\lists\backend\forms\UploadBonusesForm::class,
    \ms\loyalty\bonuses\lists\backend\forms\UploadBonusesForm::EVENT_FIND_PROFILE,
    [\backend\listeners\UploadBonusesForm::class, 'whenFindProfile']
);


/**
 * Profiles module
 */
Event::on(
    \modules\profiles\backend\controllers\ProfilesController::class,
    \modules\profiles\backend\controllers\ProfilesController::EVENT_BEFORE_ACTION,
    [\backend\listeners\ProfilesController::class, 'whenBeforeAction']
);


/**
 * Payments module
 */
Event::on(
    \ms\loyalty\finances\backend\controllers\TransactionsController::class,
    \ms\loyalty\finances\backend\controllers\TransactionsController::EVENT_BEFORE_ACTION,
    [\backend\listeners\TransactionsController::class, 'whenBeforeAction']
);