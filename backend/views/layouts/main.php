<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var string $content
 */

/**
 * By default this layout simply renders layout
 * of the admin module. But if you need some customizations
 * you can overwrite this file
 */
?>
<?php $this->beginBlock('header-extra-logo') ?>
    <!-- Client logo here -->
<?php $this->endBlock() ?>
<?php $this->beginContent('@ms/loyalty/theme/backend/views/layouts/main.php'); ?>
<?= $content ?>
<?php $this->endContent() ?>
