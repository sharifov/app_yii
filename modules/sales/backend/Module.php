<?php

namespace modules\sales\backend;

use modules\sales\common\app\SaleApplicationModule;
use modules\sales\common\models\Sale;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yz\icons\Icons;

/**
 * Class Module
 */
class Module extends \modules\sales\common\Module
{
    public function getName()
    {
        return 'Бонусы за продажи';
    }

    public function getAdminMenu()
    {
        return [
            [
                'label' => 'Продукция',
                'icon' => Icons::o('archive'),
                'items' => [
                    [
                        'route' => ['/sales/import-catalog/index'],
                        'label' => 'Импорт каталога',
                        'icon' => Icons::o('plus'),
                    ],
                    [
                        'route' => ['/sales/types/index'],
                        'label' => 'Типы продукции',
                        'icon' => Icons::o('bars'),
                    ],
                    [
                        'route' => ['/sales/categories/index'],
                        'label' => 'Виды продукции',
                        'icon' => Icons::o('bars'),
                    ],
                    [
                        'route' => ['/sales/brands/index'],
                        'label' => 'Бренды продукции',
                        'icon' => Icons::o('bars'),
                    ],
					[
						'route' => ['/sales/factors/index'],
						'label' => 'Коэффициенты продукции',
						'icon' => Icons::o('bars'),
					],
                    [
                        'route' => ['/sales/products/index'],
                        'label' => 'Продукция',
                        'icon' => Icons::o('bars'),
                    ],
                ]
            ],
            [
                'label' => 'Продажи',
                'icon' => Icons::o('dollar'),
                'items' => [
                    [
                        'route' => ['/sales/promotion-rules/index'],
                        'label' => 'Правила начислений баллов по акции "Золотые кг для участников дилера"',
                        'icon' => Icons::o('check-square-o'),
                    ],
                    [
                        'route' => ['/sales/sale-validation-rules/index'],
                        'label' => 'Правила проверки продаж',
                        'icon' => Icons::o('check-square-o'),
                    ],
					[
						'route' => ['/sales/sales/select-dealer'],
						'label' => 'Внести продажу по дилеру',
						'icon' => Icons::o('plus'),
					],
                    [
                        'route' => ['/sales/sales/index'],
                        'label' => 'Список продаж',
                        'icon' => Icons::o('bars'),
                    ],
                ]
            ],
            [
                'label' => 'Начисления / списания',
                'icon' => Icons::o('dollar'),
                'items' => [
                    [
                        'route' => ['/sales/finance-transactions/index'],
                        'label' => 'Начисления / списания',
                        'icon' => Icons::o('bars'),
                    ],
                 ]
             ],
        ];
    }

    public function init()
    {
        \Yii::configure($this, [
            'modules' => [
                'sale-app' => [
                    'class' => SaleApplicationModule::class,
                    'allowCreation' => false,
                    'afterSaleProcess' => function (Sale $sale) {
                        return Url::to(['/sales/sales/view', 'id' => $sale->id]);
                    },
                    'findSale' => function ($id) {
                        if (($sale = Sale::findOne($id)) === null) {
                            throw new NotFoundHttpException();
                        }

                        return $sale;
                    },
                    'useValidationRules' => false, // Admin can create any sale
                ]
            ]
        ]);

        parent::init();
    }
}