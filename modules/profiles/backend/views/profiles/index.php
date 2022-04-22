<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;
use modules\profiles\common\models\Profile;
use yz\icons\Icons;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var modules\profiles\backend\models\ProfileSearch $searchModel
 * @var array $columns
 */

$this->title = modules\profiles\common\models\Profile::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;

$js = <<<JS
    $(document).ready(function() {
      $('.btn-nullify').click(function() {
        return confirm('Вы уверены, что хотите обнулить баллы?');
      });
      
      /*Поведение кнопки списания баллов*/
      $( "#with_sales" ).change(function() {
            if($("#with_sales").prop("checked") == true){
                $("#with").hide();
                $("#without").show();
            }else{
                $("#with").show();
                $("#without").hide();
            }
        });
    });
JS;
$this->registerJs($js);
?>
<?php $box = Box::begin(['cssClass' => 'profile-index box-primary']) ?>
<div class="text-right">
    <div class="pull-left">
        <a id="with" href="<?= Url::to(['/profiles/profiles/nullify']) ?>"
           class="btn btn-success btn-nullify">Списать баллы участников</a>
        <a style="display: none;" id="without" href="<?= Url::to(['/profiles/profiles/nullify-hard']) ?>"
           class="btn btn-success btn-nullify">Списать баллы участников</a>
        &nbsp;&nbsp;&nbsp;
        <label for="with_sales">Списать баллы без учета статусов продаж</label>
        <input type="checkbox" id="with_sales" value="1">
    </div>
    <?php echo ActionButtons::widget([
        'order' => [['export', 'create', 'delete', 'return']],
        'gridId' => 'profile-grid',
        'searchModel' => $searchModel,
        'modelClass' => 'modules\profiles\common\models\Profile',
    ]) ?>
</div>

<?= GridView::widget([
    'id' => 'profile-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => array_merge([
        ['class' => 'yii\grid\CheckboxColumn'],
    ], $columns, [
        [
            'class' => 'yz\admin\widgets\ActionColumn',
            'template' => '{update} {login} {delete}',
            'buttons' => [
                'login' => function ($url, Profile $model) {
                    return Html::a(Icons::i('key'), '/profiles/profiles/login?id=' . $model->id, [
                        'target' => '_blank',
                        'title' => 'Войти под участником',
                        'data-method' => 'post',
                        'class' => 'btn btn-warning btn-sm',
                        'data-pjax' => '0',
                    ]);
                },
            ],
        ],
    ]),
]); ?>
<?php Box::end() ?>
