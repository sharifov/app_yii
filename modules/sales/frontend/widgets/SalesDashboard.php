<?php

namespace modules\sales\frontend\widgets;

use yii\base\Widget;


/**
 * Class SalesDashboard
 */
class SalesDashboard extends Widget
{
    public function run()
    {
        return $this->render('sales-dashboard');
    }
}