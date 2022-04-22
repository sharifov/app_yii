<?php

use modules\spent\backend\models\SpentOrder;
use yii\helpers\Html;
use yii\web\View;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\FormBox;

/**
 * @var View $this
 * @var SpentOrder $model
 * @var array $transactions
 */

$this->title = 'Отчет по израсходованным средствам';
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;

$summaryAmount = 0;
$summaryTotalBalance = 0;
$summaryNominal = 0;
$summaryTotalPlusNominal = 0;
$summaryProfileCommission = 0;
$summaryNdfl = 0;
$summaryTotalSpent = 0;
$summaryEndBalance = 0;
$summaryNds = 0;
$summaryTotal = 0;

$currentPurse = null;

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
    <?= Html::a('Экспорт', ['/spent/spent/export', 'year' => $model->year, 'month' => $model->month], ['class' => 'btn btn-info']) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<div class="row">

  <?php
  $box = FormBox::begin(['cssClass' => 'mailer-form box-primary', 'title' => '']);
  $box->beginBody();
  ?>

  <table class="table table-hover table-condensed table-bordered">
    <thead>
      <tr>
        <td colspan="4"></td>
        <td colspan="13" class="text-center"><?= SpentOrder::getMonth($model->month) ?>, <?= $model->year ?>г.</td>
      </tr>
      <tr>
        <td colspan="4"></td>
        <td colspan="10" class="text-center">Для участника акции</td>
        <td colspan="3" class="text-center">Для ЯК</td>
      </tr>
      <tr>
        <th scope="col" class="text-center">Дилер</th>
        <th scope="col" class="text-center">ФИО участника</th>
        <th scope="col" class="text-center">Тип приза</th>
        <th scope="col" class="text-center">Остаток начисленных баллов у участника на начало периода</th>
        <th scope="col" class="text-center">Сумма начисленных баллов за отчетный месяц</th>
        <th scope="col" class="text-center" style="background-color: #fcd5b5;">ИТОГО баллов у участника</th>
        <th scope="col" class="text-center">Номинал приза</th>
        <th scope="col" class="text-center">Количество</th>
        <th scope="col" class="text-center">Номинал + Количество</th>
        <th scope="col" class="text-center">% процент комиссии за предоставление приза</th>
        <th scope="col" class="text-center">Комиссия для участника</th>
        <th scope="col" class="text-center">НДФЛ c участника</th>
        <th scope="col" class="text-center">Сумма списанных баллов с участника</th>
        <th scope="col" class="text-center" style="background-color: #c6d9f1;">Сумма оставшихся баллов у участника на конец периода</th>
        <?php if($model->year <= 2019): ?><th scope="col" class="text-center">НДС за оказанную услугу с клиента</th><?php endif; ?>
        <th scope="col" class="text-center">Общая сумма со счета клиента<?php if($model->year <= 2019): ?>, в т.ч. НДС 20%<?php endif; ?></th>
        <th scope="col" class="text-center">Дата выплаты в 1С</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($transactions as $purse => $purseData):
        $summaryAmount += $purseData['incoming'] ?? 0;
        $summaryEndBalance += $purseData['balance_end'] ?? 0;
        $summaryTotalBalance += $purseData['total_balance'] ?? 0;
        $nominal = 0;

        if (!empty($purseData['transactions'])):
          foreach ($purseData['transactions'] as $key => $transaction):
            if ($transaction['qty']):
              $nominal = $transaction['nominal'] * $transaction['qty'];
              $commissionAmount = $transaction['commission_amount'];

              if ((int)$model->month < 6 && (int)$model->year < 2020) {
                  $ndfl = $transaction['company_tax'];
              } else {
                  $ndfl = $transaction['profile_tax'];
              }

              if ($transaction['is_nds']) {
                  $nds = 0;
                  if($model->year <= 2019) {
                      $nds = ($nominal / 1.2 + $commissionAmount / 1.2 + $ndfl) * 0.2;
                      $total = $nominal / 1.2 + $commissionAmount / 1.2 + $ndfl + $nds;
                  }
                  else {
                      $total = $nominal + $commissionAmount + $ndfl + $nds;
                  }
              } else {
                if ($commissionAmount) {
                    $nds = 0;
                    if($model->year <= 2019) {
                        $nds = ($nominal + $ndfl) * 0.2 + $commissionAmount / 1.2 * 0.2;
                        $total = $nominal + $commissionAmount / 1.2 + $ndfl + $nds;
                    }
                    else {
                        $total = $nominal + $commissionAmount + $ndfl + $nds;
                    }
                  } else {
                    $nds = 0;
                    if($model->year <= 2019) {
                        $nds = ($nominal + $commissionAmount + $ndfl) * 0.2;
                    }
                    $total = $nominal + $commissionAmount + $ndfl + $nds;
                  }
              }

              $summaryNominal += $nominal;
              $summaryProfileCommission += $commissionAmount;
              $summaryNdfl += $transaction['profile_tax'];
              $summaryTotalSpent += $transaction['total_amount'];
              $summaryNds += $nds;
              $summaryTotal += $total;

            else:
              $commissionAmount = '';
              $nds = null;
              $total = null;
            endif;
            ?>
            <tr>
              <td><?= $transaction['dealer'] ?></td>
              <td><?= $transaction['full_name'] ?></td>
              <td><?= $transaction['title'] ?></td>
              <td class="text-right"><?= $key === 0 ? $purseData['balance_start'] ?? '' : '' ?></td>
              <td class="text-right" style="background-color: #d7e4bd;"><?= $key === 0 ? $purseData['incoming'] ?? '' : '' ?></td>
              <td class="text-right" style="background-color: #fcd5b5;"><?= $key === 0 ? $purseData['total_balance'] ?? 0 : '' ?></td>
              <td class="text-right" style="background-color: #d7e4bd;"><?= $transaction['nominal'] ?></td>
              <td class="text-right" style="background-color: #d7e4bd;"><?= $transaction['qty'] ?></td>
              <td class="text-right" style="background-color: #d7e4bd;"><?= $nominal ? $nominal : ''  ?></td>
              <td class="text-right" style="background-color: #d7e4bd;"><?= $transaction['commission_percent'] ? $transaction['commission_percent'] . '%' : '' ?></td>
              <td class="text-right" style="background-color: #d7e4bd;"><?= $commissionAmount ?></td>
              <td class="text-right"><?= $transaction['profile_tax'] ?></td>
              <td class="text-right"><?= $transaction['total_amount'] ?></td>
              <td class="text-right" style="background-color: #c6d9f1;"><?= $key === 0 ? $purseData['balance_end'] ?? '' : '' ?></td>
              <?php if($model->year <= 2019): ?><td class="text-right"><?= $nds ? number_format($nds, 2, '.', '') : '' ?></td><?php endif; ?>
              <td class="text-right"><?= $total ? number_format($total, 2, '.', '') : '' ?></td>
              <td class="text-center"><?= $transaction['paid'] ?? ''  ?></td>
            </tr>
            <?php
          endforeach;
        endif;
      endforeach; ?>
    </tbody>
    <thead>
    <tr style="background-color: #dce6f2;">
      <th class="text-center" scope="row" colspan="4">ИТОГО в расчетном периоде:</th>
      <th class="text-right" scope="col"><?= $summaryAmount ?></th>
      <th class="text-right" scope="col"><?= $summaryTotalBalance ?></th>
      <th scope="col"></th>
      <th scope="col"></th>
      <th class="text-right" scope="col"><?= $summaryNominal ?></th>
      <th scope="col"></th>
      <th class="text-right" scope="col"><?= $summaryProfileCommission ?></th>
      <th class="text-right" scope="col"><?= number_format($summaryNdfl, 0, '.', '') ?></th>
      <th class="text-right" scope="col"><?= number_format($summaryTotalSpent, 0, '.', '') ?></th>
      <th class="text-right" scope="col"><?= number_format($summaryEndBalance, 0, '.', '') ?></th>
        <?php if($model->year <= 2019): ?><th class="text-right" scope="col"><?= number_format($summaryNds, 2, '.', '') ?></th><?php endif; ?>
      <th class="text-right" scope="col"><?= number_format($summaryTotal, 2, '.', '') ?></th>
      <th scope="col"></th>
    </tr>
    </thead>
  </table>

  <?php
  $box->endBody();
  FormBox::end();
  ?>
</div>