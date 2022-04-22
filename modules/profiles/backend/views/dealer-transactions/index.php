<?php

use modules\profiles\backend\models\DealerTransactionSearch;
use modules\profiles\common\models\Dealer;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;

/**
 * @var View $this
 * @var ArrayDataProvider $dataProvider
 * @var array $dataTotal
 * @var DealerTransactionSearch $model
 */

$this->title = 'Данные по начислению и расходованию баллов';
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>

<div class="row">
    <?php $form = ActiveForm::begin(); ?>

  <div class="col-md-6">
      <?= $form->field($model, 'dealer')->select2(Dealer::getOptions(), ['prompt' => 'Выберите дилера']) ?>
  </div>

  <div class="col-md-6 text-right">
      <?= Html::submitButton('Сформировать', ['class' => 'btn btn-primary']) ?>
      <?= Html::a('Экспорт', [
          '/profiles/dealer-transactions/export',
          'dealer' => $model->dealer
      ], ['class' => 'btn btn-info']) ?>
  </div>

    <?php ActiveForm::end(); ?>
</div>


<div class="row">

    <?php
    $box = FormBox::begin(['cssClass' => 'mailer-form box-primary', 'title' => '']);
    $box->beginBody();

    $models = $dataProvider->getModels();
    if (count($models)):

      $currentDealer = 0;
      $dealerBalance = 0;
    ?>

      <table class="table table-striped table-bordered">
        <thead>
        <tr>
          <th>ID дилера</th>
          <th>Дилер</th>
          <th>Год</th>
          <th>Месяц</th>
          <th>Начисления</th>
          <th>Списание</th>
          <th>Разница</th>
          <th>Баланс</th>
        </tr>
        </thead>

        <tbody>
        <?php
        foreach ($models as $key => $model):

          if ($currentDealer !== $model['id']) {
              $currentDealer = $model['id'];
              $dealerBalance = 0;
          }

          $arrival = (int)$model['arrival'];
          $withdraw = (int)$model['withdraw'];
          $balance = $arrival - $withdraw;
          $dealerBalance += $balance;
        ?>

          <tr data-key="<?= $key ?>">
            <td><span class="not-set"><?= $model['id'] ?? 'N/A' ?></span></td>
            <td><span class="not-set"><?= $model['name'] ?? '(не определено)' ?></span></td>
            <td><?= $model['data_year'] ?></td>
            <td><?= DealerTransactionSearch::getMonth($model['data_month']) ?></td>
            <td><?= $arrival ?></td>
            <td><?= $withdraw ?></td>
            <td><?= $balance ?></td>
            <th style="color: <?= $dealerBalance < 0 ? 'red' : 'black' ?>"><?= $dealerBalance ?></th>
          </tr>

        <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <th>Итого:</th>
          <th><?= $dataTotal['arrival'] ?></th>
          <th><?= $dataTotal['withdraw'] ?></th>
          <th><?= (int)$dataTotal['arrival'] - (int)$dataTotal['withdraw'] ?></th>
          <td>&nbsp;</td>
        </tr>
        </tfoot>
      </table>

    <?php
    endif;
    $box->endBody();
    FormBox::end();
    ?>

</div>
