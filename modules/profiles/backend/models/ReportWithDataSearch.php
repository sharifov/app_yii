<?php

namespace modules\profiles\backend\models;

use modules\profiles\common\models\Dealer;
use modules\profiles\common\models\DealerProfile;
use modules\profiles\common\models\Report;
use modules\sales\common\models\Promotion;
use yz\admin\search\WithExtraColumns;

/**
 * Class ReportWithDataSearch
 */
class ReportWithDataSearch extends ReportSearch
{
	use WithExtraColumns;

	public function rules()
	{
		return array_merge(parent::rules(), [
			[self::extraColumns(), 'safe']
		]);
	}

	protected static function extraColumns()
	{
		return [
			'dealer__name',
			'profile__full_name',
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return array_merge(parent::attributeLabels(), [
			'profile__full_name' => 'Руководитель ДЦ',
			'dealer__name' => 'Дилер',
		]);
	}

	public function attributes()
	{
		return array_merge(parent::attributes(), self::extraColumns());
	}

	protected function prepareQuery()
	{
		$query = static::find();

		$query
			->select(self::selectWithExtraColumns([
				'report.*',
			]))
			->orderBy(['created_at' => SORT_DESC])
			->from(['report' => Report::tableName()])
			->leftJoin('{{%profiles}} profile', "profile.id = report.profile_id")
			->leftJoin(['dealer' => Dealer::tableName()], 'dealer.id = report.dealer_id');
		return $query;
	}

	protected function prepareFilters($query)
	{
		parent::prepareFilters($query);

		self::filtersForExtraColumns($query);
	}
}