<?php

use modules\sales\common\models\Brand;
use modules\sales\common\models\Promotion;
use yii\helpers\Html;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;
use yz\admin\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var modules\sales\common\models\Promotion $model
 * @var yz\admin\widgets\ActiveForm $form
 */

$css = <<<CSS
    .products {
        display: none;
    }
CSS;
$this->registerCss($css);

$js = <<<JS
    $(document).ready(function() {
        $('.show-products').click(function() {
            $('.products').toggle();
        });
    });
JS;
$this->registerJs($js);

?>

<?php $box = FormBox::begin(['cssClass' => 'promotion-form box-primary', 'title' => '']) ?>
<?php $form = ActiveForm::begin(); ?>

<?php $box->beginBody() ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'type')->dropDownList(Promotion::getTypeOptions(), ['prompt' => 'выберите']) ?>
<?= $form->field($model, 'rulesFile')->fileInput() ?>

<?php if (!empty($model->rules)): ?>
    <div class="form-group">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <?= \yii\bootstrap\Html::a('Показать правила', $model->getRulesPath(), ['class' => 'btn btn-default', 'target' => '_blank']) ?>

        </div>
    </div>
<?php endif; ?>

<?= $form->field($model, 'brands')->select2(Brand::getOptions(), [
    'multiple' => 'multiple',
    'size' => 10,
])->hint('Для выставления коэфициентов по бренду сперва добавьте бренд и сохраните<br/><br/>Возможно вводить дробные значения через точку, к примеру, 10.5') ?>

<?php if (!empty($model->promotion_brands)): ?>
    <div class="row">
        <div class="col-md-2" style="text-align:right">
            <label>Коэфициенты по брендам</label>
        </div>
        <div class="col-md-8">
            <?php foreach ($model->promotion_brands as $promotionBrand): ?>
                <div class="row" style="margin-bottom:6px;">
                    <div class="col-md-1">
                        <?= Html::input(
                            'text',
                            "Promotion[promotionBrands][{$promotionBrand->brand_id}]",
                            $promotionBrand->x_real,
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                    <div class="col-md-2">
						<span style="vertical-align:top; margin-top:8px;display:inline-block;">
							<?= $promotionBrand->brand->name ?>
						</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col-md-2"></div>
    </div>

    <div class="row">
        <div class="col-md-2" style="text-align:right">
            <label>Процент от суммы по брендам</label>
        </div>
        <div class="col-md-8">
            <?php foreach ($model->promotion_brands as $promotionBrand): ?>
                <div class="row" style="margin-bottom:6px;">
                    <div class="col-md-1">
                        <?= Html::input(
                            'text',
                            "Promotion[promotionBrandsRub][{$promotionBrand->brand_id}]",
                            $promotionBrand->rub_percent_real,
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                    <div class="col-md-2">
						<span style="vertical-align:top; margin-top:8px;display:inline-block;">%
                            <?= $promotionBrand->brand->name ?>
						</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col-md-2"></div>
    </div>
    <div class="row">
        <div class="col-md-2" style="text-align:right">
            <label>Коэфициенты по SKU</label>
        </div>
        <div class="col-md-8">
            <a href="#" class="show-products btn btn-default">ОТОБРАЗИТЬ | СКРЫТЬ</a>
            <div class="products">
                <?php foreach ($model->promotion_products as $p): ?>
                    <div class="row" style="margin-bottom:6px;">
                        <div class="col-md-1">
                            <?= Html::input(
                                'text',
                                "Promotion[promotionProducts][{$p->product_id}]",
                                $p->x_real,
                                ['class' => 'form-control']
                            ) ?>
                        </div>
                        <div class="col-md-10">
						<span style="vertical-align:top; margin-top:8px;display:inline-block;">
                            <?= $p->product->name ?> (<?= $p->product->brand->name ?>)
						</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
<?php endif; ?>

<?php $box->endBody() ?>

<?php $box->actions([
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
    AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
]) ?>
<?php ActiveForm::end(); ?>

<?php FormBox::end() ?>