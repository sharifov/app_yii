<?php

namespace backend\reports;

use modules\profiles\common\models\Profile;
use ms\loyalty\reports\contracts\base\ReportInterface;
use ms\loyalty\reports\contracts\types\WidgetReportInterface;
use ms\loyalty\reports\support\WidgetConfig;
use marketingsolutions\finance\models\Purse;
use marketingsolutions\finance\models\Transaction;
use yz\icons\Icons;


/**
 * Class ProfilesStat
 */
class ProfilesStat implements ReportInterface, WidgetReportInterface
{

    /**
     * Returns title of the report
     * @return string
     */
    public function title()
    {
        return 'Статистика участников';
    }


    /**
     * @return WidgetConfig[]
     */
    public function widgets()
    {
        return [
            (new WidgetConfig())
                ->title('Всего участников')
                ->icon(Icons::i('user'))
                ->style(WidgetConfig::STYLE_AQUA)
                ->value(
                    Profile::find()
                        ->count()
                ),
            (new WidgetConfig())
                ->title('Зарегистрированные участники')
                ->icon(Icons::i('user'))
                ->style(WidgetConfig::STYLE_GREEN)
                ->value(
                    Profile::find()
                        ->where('identity_id IS NOT NULL')
                        ->count()
                ),
            (new WidgetConfig())
                ->title('Незарегистрированные участники')
                ->icon(Icons::i('user'))
                ->style(WidgetConfig::STYLE_RED)
                ->value(
                    Profile::find()
                        ->where('identity_id IS NULL')
                        ->count()
                ),

            (new WidgetConfig())
                ->title('Заработали, но не потратили ни одного балла')
                ->icon(Icons::i('rub'))
                ->style(WidgetConfig::STYLE_RED)
                ->value(
                    Profile::find()
                        ->where([
                            'id' => Purse::find()
                                ->select(['owner_id'])
                                ->where([
                                    'owner_type' => Profile::class,
                                ])
                                ->andWhere(['not in', 'id',
                                    Transaction::find()
                                        ->distinct()
                                        ->select('purse_id')
                                        ->where(['type' => Transaction::OUTBOUND])
                                ])
                                ->andWhere(['in', 'id',
                                    Transaction::find()
                                        ->distinct()
                                        ->select('purse_id')
                                        ->where(['type' => Transaction::INCOMING])
                                ])
                        ])
                        ->count()
                ),

        ];
    }
}