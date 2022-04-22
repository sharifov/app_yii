<?php


namespace modules\spent\backend;


use yz\icons\Icons;

class Module extends \modules\spent\common\Module
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Отчет по израсходованным средствам';
    }

    /**
     * @return array
     */
    public function getAdminMenu()
    {
        return [
            [
                'label' => 'Отчет по израсходованным средствам',
                'icon' => Icons::o('file'),
                'route' => ['/spent/spent/index']
            ]
        ];
    }
}