<?php

namespace modules\sales\common\sales\statuses;


/**
 * Class Statuses
 */
class Statuses
{
    /**
     * Initial status for the sale, when user decided to save in the draft. This sale can be edited
     */
    const DRAFT = 'draft';
    /**
     * Sale is waiting review by the admin
     */
    const ADMIN_REVIEW = 'adminReview';
    /**
     * Sale is approved by administrator and ready to be paid for user
     */
    const APPROVED = 'approved';
    /**
     * Sale in paid status will charge user's purse soon
     */
    const PAID = 'paid';
    /**
     * Sale was declined by admin
     */
    const DECLINED = 'declined';

    public static function statusesValues()
    {
        return [
            self::DRAFT => 'Черновик',
            self::ADMIN_REVIEW => 'Новая',
            self::APPROVED => 'Обработана',
            self::DECLINED => 'Отказано',
            self::PAID => 'Баллы начислены',
        ];
    }
}