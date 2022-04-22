<?php

use modules\profiles\common\models\Dealer;
use yii\helpers\Html;
use yz\admin\widgets\ActionButtons;
use yz\admin\widgets\Box;
use yz\admin\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \yz\admin\models\search\UserSearch $searchModel
 */

$this->title = \yz\admin\models\User::modelTitlePlural();
$this->params['breadcrumbs'][] = $this->title;
$this->params['header'] = $this->title;
?>
<?php $box = Box::begin(['cssClass' => 'box-primary']) ?>
<div class="text-right">
    <?php echo ActionButtons::widget([
        'order' => [['create', 'delete', 'return']],
        'gridId' => 'user-grid',
        'searchModel' => $searchModel,
        'modelClass' => '\yz\admin\models\User',
    ]) ?>
</div>
<?php echo GridView::widget([
    'id' => 'user-grid',
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\CheckboxColumn'],

        'id',
        'login',
        'name',
        'email:email',
        [
            'attribute' => 'is_identity',
            'label' => 'Является идентификатором',
            'format' => 'boolean',
            'visible' => \yz\admin\models\User::find()->where(['is_identity' => 1])->exists(),
        ],
        'is_super_admin:boolean',
        'is_active:boolean',
        [
            'label' => Yii::t('admin/t', 'Roles'),
            'value' => function (\yz\admin\models\User $model) {
                return implode('; ', \yii\helpers\ArrayHelper::getColumn($model->roles, 'description'));
            }
        ],
        'logged_at:datetime',
        'created_at:datetime',
        [
            'label' => 'Управляет дилерами',
            'value' => function (\yz\admin\models\User $model) {
                $dealers = Dealer::findAll(['admin_user_id' => $model->id]);
                if (empty($dealers)) {
                    return '';
                }

                return Html::ul($dealers, ['item' => function(Dealer $item, $index) {
                    return Html::tag(
                        'li',
                        $item->name,
                        ['class' => 'post']
                    );
                }]);
            },
            'format' => 'raw',
        ],
        [
            'class' => \yz\admin\grid\columns\ActionColumn::class,
            'template' => '{update} {delete}',
        ],
    ],
]); ?>
<?php Box::end() ?>
