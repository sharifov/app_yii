<?php

use yii\helpers\Html;
use yz\admin\widgets\Box;
use yz\admin\widgets\ActionButtons;

/**
 * @var yii\web\View $this
 * @var modules\news\common\models\News $model
 */
$this->title = \Yii::t('admin/t', 'Update {item}', ['item' => modules\news\common\models\News::modelTitle()]);
$this->params['breadcrumbs'][] = ['label' => modules\news\common\models\News::modelTitlePlural(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<div class="news-update">

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
