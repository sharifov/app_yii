<?php


namespace modules\interim\backend;


use yz\icons\Icons;

class Module extends \modules\interim\common\Module
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Отчет по промежуточным баллам';
    }

    public function getAdminMenu()
    {
        return [
            [
                'label' => 'Отчет по промежуточным баллам',
                'icon' => Icons::o('file'),
                'route' => ['/interim/interim/index']
            ]
        ];
    }
}