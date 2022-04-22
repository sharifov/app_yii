<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var modules\sms\common\models\Sms $model
 */
$this->title = \Yii::t('admin/t', 'Update {item}', ['item' => modules\sms\common\models\Sms::modelTitle()]);
$this->params['breadcrumbs'][] = ['label' => modules\sms\common\models\Sms::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<div class="sms-update">

    <div class="text-right">
        <?php  Box::begin() ?>
        <?=  ActionButtons::widget([
            'order' => [['index', 'return']],
            'addReturnUrl' => false,
        ]) ?>
        <?php  Box::end() ?>
    </div>

    <?php echo $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>
