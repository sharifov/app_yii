<?php

namespace modules\sales\common;

/**
 * Class Module
 */
class Module extends \yz\Module
{
	/**
	 * Allows creation of the new sale or not
	 *
	 * @var bool
	 */
	public $allowCreation = true;
	/**
	 * If true, validation rules will be applied to the sale before save
	 *
	 * @var bool
	 */
	public $useValidationRules = true;
	/**
	 * Result of witch will be returned to the sale application
	 * ```php
	 * function (Sale $sale) {
	 *  return ['url' => Url::to(['/sale/sale', 'id' => $sale->id])];
	 * }
	 * ```
	 *
	 * @var callable
	 */
	public $afterSaleProcess;
	/**
	 * Find sale model by given ID. If not set, internal implementation will be used. Example:
	 * ```php
	 * function ($id) {
	 *  return Sale::findOne($id);
	 * }
	 *
	 * @var callable
	 */
	public $findSale;
	/**
	 * @var bool
	 */
	public $documentsRequired = true;
}