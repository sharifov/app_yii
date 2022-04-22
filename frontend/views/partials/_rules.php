<?php
/**
 * @var \yii\web\View $this
 * @var Profile $profile
 */
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\models\Promotion;
use yii\helpers\Html;

$promotionIds = [10, 11, 12, 13, 14, 17];
$matchedPromotions = array_intersect($profile->promotionIds, $promotionIds);

?>

<?php if (!empty($matchedPromotions)): ?>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            Правила акции (памятка)
        </div>
        <div class="panel-body">
            <?php foreach ($matchedPromotions as $promotionId): ?>
            <?php $promotion = Promotion::findOne($promotionId) ?>
                <?= Html::a($promotion->name, "@frontendWeb/media/uploads/$promotionId.pdf",
                    ['target' => '_blank', 'class' => 'btn btn-default', 'style' => 'margin-right:15px;']) ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
