<?php

namespace backend\reports;

use ms\loyalty\prizes\payments\common\models\Payment;
use ms\loyalty\reports\contracts\base\ReportInterface;
use ms\loyalty\reports\contracts\ByQueryInterface;
use ms\loyalty\reports\contracts\types\TableReportInterface;
use ms\loyalty\reports\support\SelfSearchModel;
use yii\base\Model;
use yii\db\Query;

/**
 * Class CommissionStat
 */
class CommissionStat extends Model implements ReportInterface, TableReportInterface, ByQueryInterface
{
    use SelfSearchModel;

    protected $months;

    public function __construct()
    {
        parent::__construct();

        $this->months = explode(' ', 'январь февраль март апрель май июнь июль август сентябрь октябрь ноябрь декабрь');
    }

    /**
     * Returns title of the report
     *
     * @return string
     */
    public function title()
    {
        return 'Платежи по месяцам';
    }

    /**
     * @return array
     */
    public function gridColumns()
    {
        return [
            [
                'attribute' => 'monthYear',
                'label' => 'Год и месяц',
                'format' => 'html',
                'value' => function ($data) {
                    $parts = explode(' ', $data['monthYear']);

                    return $parts[1] . ' &ndash; ' . $this->months[$parts[0] - 1];
                }
            ],
            [
                'attribute' => 'amount',
                'label' => 'Платежи за месяц',
            ],
            [
                'attribute' => 'commission',
                'label' => 'Коммиссия за месяц',
            ],
            [
                'attribute' => 'total',
                'label' => 'Всего за месяц',
            ],
        ];
    }

    /**
     * @return Query
     */
    public function query()
    {
        $query = (new Query())
            ->select([
                'monthYear' => 'payment.monthYear',
                'commission' => 'payment.commission',
                'amount' => 'payment.amount',
                'total' => 'payment.total',
            ])
            ->from([
                'payment' => (new Query())
                    ->select([
                        'monthYear' => "DATE_FORMAT(p.created_at, '%m %Y')",
                        'commission' => 'SUM(p.company_commission_money_amount) / 100',
                        'amount' => 'SUM(p.company_payment_money_amount) / 100',
                        'total' => 'SUM(p.company_money_amount) / 100'
                    ])
                    ->from(['p' => Payment::tableName()])
                    ->groupBy('monthYear')
            ]);

        return $query;
    }
}