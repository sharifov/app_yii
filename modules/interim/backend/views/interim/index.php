<?php

use modules\interim\backend\models\Interim;
use modules\spent\backend\models\SpentOrder;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;

/**
 * @var View $this
 * @var ArrayDataProvider $dataProvider
 * @var array $columns
 * @var Interim $model
 */

$this->title = 'Баллы участников';
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;

?>

<div class="row">
  <?php $form = ActiveForm::begin(); ?>

  <div class="col-md-4">
    <?= $form->field($model, 'year')->select2(SpentOrder::getYearList()) ?>
  </div>

  <div class="col-md-4">
    <?= $form->field($model, 'month')->select2(SpentOrder::getMonthsList()) ?>
  </div>

  <div class="col-md-4">
    <?= Html::submitButton('Сформировать', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Экспорт', ['/interim/interim/export', 'year' => $model->year, 'month' => $model->month], ['class' => 'btn btn-info']) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>


<div class="row">

  <?php
  $box = FormBox::begin(['cssClass' => 'mailer-form box-primary', 'title' => '']);
  $box->beginBody();
  ?>

  <?= GridView::widget([
      'id' => 'interim-grid',
      'dataProvider' => $dataProvider,
      'columns' => $columns,
      'showFooter' => true
  ]); ?>

  <?php
  $box->endBody();
  FormBox::end();
  ?>

</div>
