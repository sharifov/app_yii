<?php

use yz\admin\helpers\AdminHtml;
use yz\admin\widgets\FormBox;
use yz\admin\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use modules\profiles\common\models\Brand;

$dir = Yii::getAlias(getenv('YII_ENV') == 'dev' ? '@frontendWebroot/data/filemanager/source/' : '@data/filemanager/source/');
FileHelper::createDirectory($dir);
$thumbsDir = $dir = Yii::getAlias(getenv('YII_ENV') == 'dev' ? '@frontendWebroot/data/filemanager/thumbs/' : '@data/filemanager/thumbs/');
FileHelper::createDirectory($thumbsDir);

/**
 * @var yii\web\View $this
 * @var modules\news\common\models\News $model
 * @var yz\admin\widgets\ActiveForm $form
 */
?>

<?php  $box = FormBox::begin(['cssClass' => 'news-form box-primary', 'title' => '']) ?>
    <?php $form = ActiveForm::begin(); ?>

    <?php $box->beginBody() ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'content')->widget(\xvs32x\tinymce\Tinymce::class, [
            'pluginOptions' => [
                'plugins' => [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                    "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
                ],
                'toolbar1' => "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
                'toolbar2' => "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor ",
                'image_advtab' => true,
                'filemanager_title' => "Filemanager",
                'language' => ArrayHelper::getValue(explode('_', Yii::$app->language), '0', Yii::$app->language),
            ],
            'fileManagerOptions' => [
                'configPath' => [
                    'upload_dir' => '/data/filemanager/source/',
                    'current_path' => '../../../../../frontend/web/data/filemanager/source/',
                    'thumbs_base_path' => '../../../../../frontend/web/data/filemanager/thumbs/',
                    'base_url' => Yii::getAlias('@frontendWeb'), // <-- uploads/filemanager path must be saved in frontend
                ]
            ]
        ]); ?>

        <?= $form->field($model, 'enabled')->checkbox() ?>

    <?php $box->endBody() ?>

    <?php $box->actions([
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_STAY, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_LEAVE, $model->isNewRecord),
        AdminHtml::actionButton(AdminHtml::ACTION_SAVE_AND_CREATE, $model->isNewRecord),
    ]) ?>
    <?php ActiveForm::end(); ?>

<?php  FormBox::end() ?>
