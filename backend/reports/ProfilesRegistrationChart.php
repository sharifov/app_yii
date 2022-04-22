<?php

namespace backend\reports;

use ms\loyalty\identity\phones\common\models\Identity;
use ms\loyalty\reports\contracts\base\ReportInterface;
use ms\loyalty\reports\contracts\types\ChartReportInterface;


/**
 * Class ProfilesRegistrationChart
 */
class ProfilesRegistrationChart implements ReportInterface, ChartReportInterface
{

    /**
     * Returns array of the chart config
     * @return array
     */
    public function config()
    {
        return [
            'chart' => [
                'type' => 'line',
            ],
            'title' => [
                'text' => '',
            ],
            'yAxis' => [
                'title' => [
                    'text' => 'Количество регистраций',
                ],
                'min' => 0,
            ],
            'xAxis' => [
                'type' => 'category',
                'title' => [
                    'text' => 'Дата',
                ]
            ]
        ];
    }

    /**
     * Returns data for the chart
     * @return array
     */
    public function data()
    {
        return [
            [
                'name' => 'Количество регистраций в день',
                'data' => array_map(function($value) {
                    $value['y'] = (int)$value['y'];
                    return $value;
                }, Identity::find()
                    ->select([
                        'y' => 'COUNT(id)',
                        'name' => 'DATE(created_at)',
                    ])
                    ->groupBy('DATE(created_at)')
                    ->orderBy('DATE(created_at)')
                    ->asArray()
                    ->all()
                )
            ],
        ];
    }

    /**
     * Returns title of the report
     * @return string
     */
    public function title()
    {
        return 'Регистрация участников';
    }
}