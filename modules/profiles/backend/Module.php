<?php

namespace modules\profiles\backend;
use yz\icons\Icons;


/**
 * Class Module
 */
class Module extends \modules\profiles\common\Module
{
    public function getName()
    {
        return 'Профили участников';
    }

    public function getAdminMenu()
    {
        return [
            [
                'label' => 'Участники программы',
                'icon' => Icons::o('user'),
                'items' => [
                    [
                        'route' => ['/profiles/import-profiles/index'],
                        'label' => 'Импорт участников',
                        'icon' => Icons::o('plus'),
                    ],
                    [
                        'route' => ['/profiles/profiles/index'],
                        'label' => 'Профили участников',
                        'icon' => Icons::o('list'),
                    ],
                    [
                        'route' => ['/profiles/profiles2/index'],
                        'label' => 'Профили участников + НАЧИСЛЕНИЯ/СПИСАНИЯ',
                        'icon' => Icons::o('list'),
                    ],
                    [
                        'route' => ['/profiles/dealers/index'],
                        'label' => 'Дилеры',
                        'icon' => Icons::o('list'),
                    ],
                    [
                        'route' => ['/sales/promotions/index'],
                        'label' => 'Акции',
                        'icon' => Icons::o('list'),
                    ],
					[
						'route' => ['/profiles/reports/index'],
						'label' => 'Отчеты об остатках',
						'icon' => Icons::o('list'),
					],
                    [
                        'route' => ['/profiles/profile-transactions/index'],
                        'label' => 'Движение баллов',
                        'icon' => Icons::o('list'),
                    ],
                    [
                        'route' => ['/profiles/dealer-transactions/index'],
                        'label' => 'Движения баллов по дилерам',
                        'icon' => Icons::o('list')
                    ],
                    [
                        'route' => ['/profiles/nullify/index'],
                        'label' => 'История обнуления баллов по участникам',
                        'icon' => Icons::o('list')
                    ],
                    [
                        'route' => ['/profiles/active-profiles/index'],
                        'label' => 'Отчет об активных участниках',
                        'icon' => Icons::o('list')
                    ],
                ]
            ],
        ];
    }

}