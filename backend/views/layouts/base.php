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

$this->beginContent('@ms/loyalty/theme/backend/views/layouts/base.php');
echo $content;

?>

<style>
	body {
		background: url(/images/bg_shake.jpg) no-repeat top left !important;
	}
</style>

<?php
$this->endContent();
?>