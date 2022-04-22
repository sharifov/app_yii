<?php

/**
 * Aliases configuration
 *
 * All aliases that are used in application are placed here
 */

Yii::setAlias('common', dirname(dirname(__DIR__)) . '/common');
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');

// Modules
Yii::setAlias('modules', dirname(dirname(__DIR__)) . '/modules');

// Application migrations
Yii::setAlias('migrations', dirname(dirname(__DIR__)) . '/migrations');

// Data
Yii::setAlias('data',  dirname(dirname(dirname(__DIR__))) . '/data');

// Web
Yii::setAlias('frontendWebroot', '@frontend/web');
Yii::setAlias('backendWebroot', '@backend/web');
Yii::setAlias('frontendWeb', getenv('FRONTEND_WEB'));
Yii::setAlias('backendWeb', getenv('BACKEND_WEB'));

/**
 * Configuration of the dependency injector container (DI)
 */

// Profile class information
Yii::$container->set(
    \ms\loyalty\contracts\profiles\ProfileFinderInterface::class,
    \modules\profiles\common\models\ProfileFinder::class
);

// Identity registrar
Yii::$container->set(
    \ms\loyalty\contracts\identities\IdentityRegistrarInterface::class,
    \ms\loyalty\identity\phones\common\models\IdentityRegistrar::class
);

// Registration token provider
Yii::$container->set(
    \ms\loyalty\contracts\identities\RegistrationTokenProviderInterface::class,
    \ms\loyalty\identity\phones\common\registration\RegistrationTokenProvider::class
);

Yii::$container->set(
	\ms\loyalty\contracts\identities\RegistrationTokenManagerInterface::class,
	\ms\loyalty\contracts\identities\TokenProvidesPhoneMobileInterface::class
);

/**
 * Event handlers
 */

\yii\base\Event::on(
    \ms\loyalty\identity\phones\common\forms\TokenGenerationForm::class,
    \ms\loyalty\identity\phones\common\forms\TokenGenerationForm::EVENT_AFTER_VALIDATE,
    [\common\listeners\TokenGeneration::class, 'whenAfterValidate']
);