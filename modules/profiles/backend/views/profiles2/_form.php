<?php

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\models\Promotion;
use marketingsolutions\datetime\DatePickerConfig;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\ActiveForm;
use yz\admin\widgets\Box;
use yz\admin\widgets\FormBox;

/**
 * @var yii\web\View $this
 * @var modules\profiles\common\models\Profile $model
 * @var yz\admin\widgets\ActiveForm $form
 */

\marketingsolutions\assets\AngularAsset::register($this);
?>

    <div>

        <div class="row col-md-9">
            <?php $box = FormBox::begin(['cssClass' => 'profile-form box-primary', 'title' => '']) ?>
            <?php $form = ActiveForm::begin(); ?>

            <?php $box->beginBody() ?>

            <?= $form->field($model, 'role')->dropDownList($model->getRoleValues()) ?>
            <?= $form->field($model, 'dealerIds')->select2(Dealer::getOptions(), [
                'multiple' => true,
            ]) ?>

            <?= $form->field($model, 'sync_with_dealers_promotions')->checkbox() ?>

            <div id="promotion_ids_static"
                 style="<?= $model->sync_with_dealers_promotions ? '' : 'display: none;' ?>">
                <?= $form->field($model, 'promotionIds')->staticControl([
                    'value' => implode(', ', ArrayHelper::getColumn($model->promotions, 'name')),
                ]) ?>
            </div>

            <div id="promotion_ids_control"
                 style="<?= $model->sync_with_dealers_promotions ? 'display: none;' : '' ?>">
                <?= $form->field($model, 'promotionIds')->select2(Promotion::getOptions(), [
                    'multiple' => true,
                ]) ?>
            </div>

            <?= $form->field($model, 'sales_point_name')->textInput() ?>
            <?= $form->field($model, 'position')->textInput() ?>


            <?= $form->field($model, 'phone_mobile_local')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '+7 999 999-99-99',
            ]) ?>
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => 32]) ?>
            <?= $form->field($model, 'last_name')->textInput(['maxlength' => 32]) ?>
            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 32]) ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>

            <?= $form->field($model, 'status')->dropDownList(Profile::getStatusValues(), ['prompt' => 'не указан']) ?>
            <?= $form->field($model, 'status_date_local')->datePicker(DatePickerConfig::get($model, 'status_date_local', [],
                \marketingsolutions\widgets\DatePicker::className())) ?>

            <?php $box->endBody() ?>

            <?php $box->actions([
                AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
                AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
                AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
            ]) ?>
            <?php ActiveForm::end(); ?>

            <?php FormBox::end() ?>
        </div>

        <?php if ($model->isNewRecord == false): ?>
            <div class="col-md-3">
                <?php Box::begin() ?>
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        Управление регистрацией участника
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <tr>
                                <th>Участник зарегистрирован</th>
                                <td><?= Yii::$app->formatter->asBoolean($model->identity_id) ?>,
                                    ID #<?= $model->identity_id ?> </td>
                            </tr>
                            <?php if ($model->identity_id): ?>
                                <tr>
                                    <td colspan="2">

                                        <a href="<?= Url::to(['/profiles/identities/delete', 'id' => $model->identity_id]) ?>"
                                           class="btn btn-danger">
                                            Удалить регистрацию участника
                                        </a>

                                    <span class="label label-info">
                                        Все данные участника, включая его баланс, сохранятся
                                    </span>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </table>
                    </div>
                </div>

                <?php Box::end() ?>

                <?php Box::begin() ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Информация
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <tr>
                                <th>Бонусы</th>
                                <td><?= Html::encode($model->purse->balance) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php Box::end() ?>
            </div>
        <?php endif; ?>
    </div>
<?php

$syncId = Html::getInputId($model, 'sync_with_dealers_promotions');

$js = <<<JS
$('#{$syncId}').on('click change', function () {
    if ($('#{$syncId}').is(':checked')) {
        $('#promotion_ids_static').show();
        $('#promotion_ids_control').hide();
    } else {
        $('#promotion_ids_static').hide();
        $('#promotion_ids_control').show();
    }
});
JS;

$this->registerJs($js);


