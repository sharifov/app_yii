<?php

namespace modules\sales\common\sales\statuses;

use modules\sales\common\models\Sale;
use yii\base\InvalidCallException;
use yii\base\BaseObject;
use yii\db\Expression;
use yz\admin\models\User;

/**
 * Class StatusManager
 */
class StatusManager extends BaseObject
{
	const ROLE_REGIONAL_MANAGER = 'REGIONAL_MANAGER';

    /**
     * @var Sale
     */
    private $sale;

    public function __construct(Sale $sale, $config = [])
    {
        $this->sale = $sale;
        parent::__construct($config);
    }

    public function adminCanSetStatus($status)
    {
		if ($this->isRegionalManager() && in_array($status, [Statuses::PAID, Statuses::DECLINED])) {
			return false;
		}

        switch ($this->sale->status) {
            case Statuses::ADMIN_REVIEW:
                return in_array($status, [Statuses::DRAFT, Statuses::APPROVED, Statuses::DECLINED]);
            case Statuses::APPROVED:
                return in_array($status, [Statuses::PAID, Statuses::DECLINED]);
            case Statuses::DECLINED:
                return in_array($status, [Statuses::APPROVED]);
        }

        return false;
    }

    public function recipientCanSetStatus($status)
    {
        switch ($this->sale->status) {
            case Statuses::DRAFT:
                return in_array($status, [Statuses::ADMIN_REVIEW]);
        }
        return false;
    }

    /**
     * @param string $status
     * @return bool
     */
    public function changeStatus($status)
    {
        if ($this->sale->isNewRecord) {
            throw new InvalidCallException('Can not change status of the new sale');
        }
        switch ($status) {
            case Statuses::DRAFT:
                $this->sale->updateAttributes([
                    'status' => $status,
                    'approved_by_admin_at' => null,
                ]);
                break;
            case Statuses::ADMIN_REVIEW:
                $this->sale->updateAttributes([
                    'status' => $status,
                    'approved_by_admin_at' => null,
                ]);
                break;
            case Statuses::APPROVED:
                $this->sale->updateAttributes([
                    'status' => $status,
                    'approved_by_admin_at' => new Expression('NOW()'),
                ]);
                break;
            case Statuses::PAID:
                $this->sale->updateAttributes([
                    'status' => $status,
                ]);
                break;
            case Statuses::DECLINED:
                $this->sale->updateAttributes([
                    'status' => $status,
                    'approved_by_admin_at' => null,
                ]);
                break;
            default:
                return false;
        }
        return true;
    }

    public function canBeDeleted()
    {
        return $this->sale->status == Statuses::DRAFT;
    }

    /**
     * Is user can edit this sale
     * @return bool
     */
    public function recipientCanEdit()
    {
        return $this->sale->status == Statuses::DRAFT;
    }

    /**
     * Is administrator can edit this sale
     * @return bool
     */
    public function adminCanEdit()
    {
        return $this->isRegionalManager() ? false : $this->sale->status == Statuses::ADMIN_REVIEW;
    }

	private function isRegionalManager()
	{
		/** @var User $admin */
		$admin = \Yii::$app->user->identity;

		foreach ($admin->roles as $role) {
			if ($role->name == self::ROLE_REGIONAL_MANAGER) {
				return true;
			}
		}

		return false;
	}
}