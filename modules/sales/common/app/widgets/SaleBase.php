<?php

namespace modules\sales\common\app\widgets;
use yii\base\Widget;


/**
 * Class SaleBase
 */
class SaleBase extends Widget
{
    public $view = null;

    /**
     * Sale id
     * @var int
     */
    public $id = null;

    /**
     * Dealer id
     * @var int
     */
    public $dealer_id;
    /**
     * @var int
     */
    public $promotion_id;

    /**
     * @var string
     */
    public $promotion_type;

    /**
     * Configuration for sale application
     * @var array
     */
    public $config;

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        $id = $this->id;
        $config = $this->config;
        $dealerId = $this->dealer_id;
        $promotionId = $this->promotion_id;
        $promotionType = $this->promotion_type;

        return $this->render($this->view, compact('id', 'dealerId', 'promotionId', 'promotionType', 'config'));
    }
}