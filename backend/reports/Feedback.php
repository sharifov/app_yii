<?php

namespace backend\reports;

use ms\loyalty\feedback\common\models\Message;
use ms\loyalty\reports\contracts\base\ReportInterface;
use ms\loyalty\reports\contracts\ByQueryInterface;
use ms\loyalty\reports\contracts\ReportSearchModelInterface;
use ms\loyalty\reports\contracts\types\TableReportInterface;
use ms\loyalty\reports\support\QueryReport;
use ms\loyalty\reports\support\SelfSearchModel;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;


/**
 * Class Feedback
 */
class Feedback extends Model implements ReportInterface, TableReportInterface,
    ByQueryInterface, ReportSearchModelInterface
{
    use QueryReport, SelfSearchModel;

    /**
     * Returns title of the report
     * @return string
     */
    public function title()
    {
        return 'Статистика обратной связи';
    }

    public function query()
    {
        return (new Query())
            ->select([
                'Тип' => new Expression('"Всего вопросов"'),
                'Количество' => 'count(id)',
            ])
            ->from(Message::tableName())

            ->union(
                (new Query())
                    ->select([
                        'Тип' => new Expression('"Неотвеченные вопросы"'),
                        'Количество' => 'count(id)',
                    ])
                    ->from(Message::tableName())
                    ->where(['is_processed' => 0])
            )

            ->union(
                (new Query())
                    ->select([
                        'Тип' => new Expression('"Отвеченные вопросы"'),
                        'Количество' => 'count(id)',
                    ])
                    ->from(Message::tableName())
                    ->where(['is_processed' => 1])

            );
    }
}