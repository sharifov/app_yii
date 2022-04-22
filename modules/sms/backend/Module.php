<?php

namespace modules\sms\backend;

use yz\icons\Icons;

/**
 * Class Module
 */
class Module extends \modules\sms\common\Module
{
    public function getAdminMenu()
    {
        return [
            [
                'label' => 'СМС-рассылка',
                'icon' => Icons::o('list'),
				'items' => [
					[
						'route' => ['/sms/sms/index'],
						'label' => 'Рассылка',
						'icon' => Icons::o('list'),
					],
					[
						'route' => ['/sms/sms-logs/index'],
						'label' => 'Отчет',
						'icon' => Icons::o('list'),
					],
				]
            ],
        ];
    }

}