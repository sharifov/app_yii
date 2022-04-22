<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\Brand $model
 */
$this->title = 'Создать продажу';
$this->params['breadcrumbs'][] = ['label' => modules\sales\common\models\Sale::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<div class="brand-create">
    <div class="text-right">
        <?php  Box::begin() ?>
        <?=  ActionButtons::widget([
            'order' => [['return']],
            'addReturnUrl' => false,
        ]) ?>
        <?php  Box::end() ?>
    </div>
    <?php echo $this->render('_form-create-sale', [
        'model' => $model,
    ]); ?>
</div>
