<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;
use yz\admin\widgets\ActionButtons;

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
    });
JS;
$this->registerJs($js);

# CSS
$css = <<<CSS
	#profile-grid {
		width: 3000px !important;
	}
CSS;
$this->registerCss($css);

$js = <<<JS
    $(document).ready(function() {
        var jdate = $('#profilesearch-dr');
        var dateValue = jdate.val();

        var timer;

        timer = setInterval(function() {
            var newValue = jdate.val();
            if (dateValue != newValue) {
                dateValue = newValue;
                clearInterval(timer);
                jdate.trigger('change');
            }
        }, 300);
    });
JS;
$this->registerJs($js);
?>
<?php $box = Box::begin(['cssClass' => 'profile-index box-primary']) ?>
<div class="text-right">
    <div class="pull-left">
        <a href="<?= Url::to(['/profiles/profiles/nullify']) ?>"
           class="btn btn-success btn-nullify">Списать баллы участников</a>
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
            'template' => '{update} {delete}',
        ],
    ]),
]); ?>
<?php Box::end() ?>
