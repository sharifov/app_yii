<?php

namespace backend\reports;

use modules\sales\common\models\Brand;
use modules\sales\common\models\Sale;
use ms\loyalty\reports\contracts\base\ReportInterface;
use ms\loyalty\reports\contracts\ByQueryInterface;
use ms\loyalty\reports\contracts\types\TableReportInterface;
use ms\loyalty\reports\support\SelfSearchModel;
use yii\base\Model;
use yii\db\Query;

/**
 * Class SalesStat
 */
class SalesStat extends Model implements ReportInterface, TableReportInterface, ByQueryInterface
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
		return 'Продажи по месяцам';
	}

	/**
	 * @return array
	 */
	public function gridColumns()
	{
		return [
			[
				'attribute' => 'monthYear',
				'label'     => 'Год и месяц',
				'format'    => 'html',
				'value'     => function ($data) {
					$parts = explode(' ', $data['monthYear']);

					return $parts[1] . ' &ndash; ' . $this->months[$parts[0] - 1];
				}
			],
			[
				'attribute' => 'kg',
				'label'     => 'Продано кг',
				'value'     => function ($data) {
					return $data['kg'] / 100;
				}
			],
			[
				'attribute' => 'bonuses',
				'label'     => 'Бонусов начислено',
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
				'monthYear' => 'sales.monthYear',
				'kg'        => 'sales.kg',
				'bonuses'   => 'sales.bonuses',
			])
			->from([
				'sales' => (new Query())
					->select([
						'monthYear' => "DATE_FORMAT(s.sold_on, '%m %Y')",
						'kg'        => 'SUM(s.kg)',
						'bonuses'   => 'SUM(s.bonuses)'])
					->from(['s' => Sale::tableName()])
					->groupBy('monthYear')
			]);

		return $query;
	}
}