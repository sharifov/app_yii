<?php

namespace modules\sales\frontend;
use modules\sales\common\app\SaleApplicationModule;
use modules\sales\common\models\Sale;
use yii\helpers\Url;


/**
 * Class Module
 */
class Module extends \modules\sales\common\Module
{
    public function init()
    {
        \Yii::configure($this, [
            'modules' => [
                'sale-app' => [
                    'class' => SaleApplicationModule::class,
                    'allowCreation' => true,
                    'afterSaleProcess' => function(Sale $sale) {
                        return Url::to(['/sales/sales/view', 'id' => $sale->id]);
                    }
                ]
            ]
        ]);

        parent::init();
    }

}