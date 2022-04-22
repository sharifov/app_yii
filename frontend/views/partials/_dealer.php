<?php
/** @var Dealer $dealer */
/** @var Promotion $promotion */
use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\Profile;
use modules\sales\common\models\Promotion;
use yii\helpers\ArrayHelper;

if (!$dealer || !$promotion) {
    return;
}

/** @var Profile $profile */
$profile = \Yii::$app->user->identity->profile;
$purse = $dealer->findPurseByPromotion($promotion->id);
?>

<div class="container" style="margin-bottom: 20px;">
    <div class="row pharmacy-panel">
        <div class="col-md-3 name">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Дилер</span>
                <span type="text" class="form-control" aria-describedby="basic-addon1">
                    <?= $dealer->name ?>
                </span>
            </div>
        </div>
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Акция</span>
                <span type="text" class="form-control" aria-describedby="basic-addon1">
                    <?= $promotion->name ?>
                </span>
            </div>
        </div>
        <div class="col-md-2">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">
                    <i class="fa fa-dollar" style="width:30px;"></i>
                </span>
                <span type="text" class="form-control" aria-describedby="basic-addon1" purse-id="<?= $purse->id ?>">
                    <?= $purse->balance ?>
                </span>
            </div>
        </div>
        <div class="col-md-2">
            <span type="text" class="form-control" aria-describedby="basic-addon1" style="border-radius:4px">
                <?= Profile::getRoleValues()[$profile->role] ?></span>
        </div>
    </div>
</div>