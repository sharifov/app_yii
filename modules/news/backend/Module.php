<?php

namespace modules\news\backend;

use yz\icons\Icons;

/**
 * Class Module
 */
class Module extends \modules\news\common\Module
{
    public function getAdminMenu()
    {
        return [
            [
                'route' => ['/news/news/index'],
                'label' => 'Новости',
                'icon' => Icons::o('list'),
            ],
        ];
    }

}