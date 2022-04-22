<?php
use ms\loyalty\identity\phones\frontend\assets\PhoneValidationAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 */

$settings = [
    'generateTokenUrl' => Url::to(['/identity/api/registration-validation/generate-token']),
    'validateTokenUrl' => Url::to(['/identity/api/registration-validation/validate-token']),
];

$js =<<<JS
phoneValidation.settings.generateTokenUrl = '{$settings['generateTokenUrl']}';
phoneValidation.settings.validateTokenUrl = '{$settings['validateTokenUrl']}';
JS;

?>

<?= $this->render('partials/_app', ['validateCodeText' => 'Проверить код и перейти к регистрации']) ?>