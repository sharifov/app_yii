<?php

namespace modules\profiles\backend\models;

use marketingsolutions\datetime\DateTimeAttributeFinder;
use marketingsolutions\datetime\DateTimeRangeBehaviorFinder;
use marketingsolutions\widgets\DateRangePicker;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\FormatConverter;


/**
 * Class DateRangeConfig
 */
class DateRangeConfig
{
    use DateTimeAttributeFinder, DateTimeRangeBehaviorFinder;

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @param string $datePickerClass
     * @return array
     */
    public static function get(Model $model, $attribute, $options = [], $datePickerClass = DateRangePicker::class)
    {
        $defaults = [];

        return ArrayHelper::merge($defaults, $options);
    }
}