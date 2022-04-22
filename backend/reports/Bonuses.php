<?php

namespace backend\reports;
use modules\profiles\common\models\Profile;
use ms\loyalty\catalog\common\components\CertificatesCatalogPartner;
use \marketingsolutions\finance\models\Transaction;
use ms\loyalty\reports\contracts\base\ReportInterface;
use ms\loyalty\reports\contracts\types\WidgetReportInterface;
use ms\loyalty\reports\support\WidgetConfig;
use marketingsolutions\finance\models\Purse;
use yii\db\ActiveQuery;
use yz\icons\Icons;


/**
 * Class Bonuses
 */
class Bonuses implements ReportInterface, WidgetReportInterface
{

    /**
     * Returns title of the report
     * @return string
     */
    public function title()
    {
        return 'Баллы участников';
    }

    /**
     * @return WidgetConfig[]
     */
    public function widgets()
    {
        $in = Transaction::find()
            ->joinWith(['purse' => function(ActiveQuery $query) {
                $query->from(['purse' => Purse::tableName()]);
            }])
            ->where([
                'purse.owner_type' => Profile::className(),
                'type' => Transaction::INCOMING,
            ])
            ->andWhere(['!=', 'partner_type', CertificatesCatalogPartner::class])
            ->sum('amount');

        $returned = Transaction::find()
            ->joinWith(['purse' => function(ActiveQuery $query) {
                $query->from(['purse' => Purse::tableName()]);
            }])
            ->where([
                'purse.owner_type' => Profile::className(),
                'type' => Transaction::INCOMING,
            ])
            ->andWhere(['=', 'partner_type', CertificatesCatalogPartner::class])
            ->sum('amount');

        $returned = $returned ? $returned : 0;

        $out = Transaction::find()
            ->joinWith(['purse' => function(ActiveQuery $query) {
                $query->from(['purse' => Purse::tableName()]);
            }])
            ->where([
                'purse.owner_type' => Profile::className(),
                'type' => Transaction::OUTBOUND,
            ])
            ->sum('amount');

        $out -= $returned;

        return [
            [
                (new WidgetConfig())
                    ->title('Баллов на счетах участников')
                    ->icon(Icons::i('rub'))
                    ->style(WidgetConfig::STYLE_AQUA)
                    ->format(WidgetConfig::FORMAT_DECIMAL)
                    ->value(
                        Purse::find()
                            ->where(['owner_type' => Profile::className()])
                            ->sum('balance')
                    ),
                (new WidgetConfig())
                    ->title('Начисленно баллов')
                    ->icon(Icons::i('rub'))
                    ->style(WidgetConfig::STYLE_GREEN)
                    ->format(WidgetConfig::FORMAT_DECIMAL)
                    ->value($in),
                (new WidgetConfig())
                    ->title('Потрачено баллов')
                    ->icon(Icons::i('rub'))
                    ->style(WidgetConfig::STYLE_RED)
                    ->format(WidgetConfig::FORMAT_DECIMAL)
                    ->value($out),
                (new WidgetConfig())
                    ->title('Возвращено баллов')
                    ->icon(Icons::i('rub'))
                    ->style(WidgetConfig::STYLE_YELLOW)
                    ->format(WidgetConfig::FORMAT_DECIMAL)
                    ->value($returned),
            ],
            [
                (new WidgetConfig())
                    ->title('Среднее число баллов у участников')
                    ->icon(Icons::i('rub'))
                    ->style(WidgetConfig::STYLE_YELLOW)
                    ->format(WidgetConfig::FORMAT_DECIMAL)
                    ->value(
                        round(Purse::find()
                            ->where(['owner_type' => Profile::className()])
                            ->average('balance'), 0)
                    ),
                (new WidgetConfig())
                    ->title('Максимальное количество баллов у участника')
                    ->icon(Icons::i('rub'))
                    ->style(WidgetConfig::STYLE_YELLOW)
                    ->format(WidgetConfig::FORMAT_DECIMAL)
                    ->value(
                        Purse::find()
                            ->where(['owner_type' => Profile::className()])
                            ->max('balance')
                    ),
                (new WidgetConfig())
                    ->title('Минимальное количество баллов у участника')
                    ->icon(Icons::i('rub'))
                    ->style(WidgetConfig::STYLE_YELLOW)
                    ->format(WidgetConfig::FORMAT_DECIMAL)
                    ->value(
                        Purse::find()
                            ->where(['owner_type' => Profile::className()])
                            ->min('balance')
                    ),
            ]
        ];
    }
}