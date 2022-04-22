<?php


namespace modules\profiles\backend\models;


use modules\profiles\common\models\Profile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\db\Exception;
use yii\helpers\FileHelper;

class DealerTransactionSearch extends Model
{
    /**
     * @var null|integer
     */
    public $dealer = null;


    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['dealer', 'integer']
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'dealer' => 'Дилер'
        ];
    }

    /**
     * @return array
     */
    public static function getYearList()
    {
        $months = range(2015, (int)date("Y", strtotime('now')));
        return array_combine($months, $months);
    }

    /**
     * @return array
     */
    public static function getMonthsList()
    {
        return [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь',
        ];
    }

    /**
     * @param $month
     * @return mixed
     */
    public static function getMonth($month)
    {
        $month = (int)$month;
        $month = $month > 9 ? (string)$month : '0' . $month;

        return self::getMonthsList()[$month];
    }

    /**
     * @return ArrayDataProvider
     * @throws Exception
     */
    public function data()
    {
        $condition = $this->dealer ? ' and d.id = ' . $this->dealer : '';
        $select = 'd.id, d.name, max(YEAR(ft.created_at)) data_year, max(MONTH(ft.created_at)) data_month,';
        $dataProvider = new ArrayDataProvider([
            'allModels' => Yii::$app->getDb()->createCommand($this->requestText($condition, $select) . "
                group by d.id, EXTRACT(YEAR_MONTH FROM ft.created_at)
            ")->queryAll(),
            'pagination' => [
                'pageSize' => 0
            ]
        ]);

        return $dataProvider;
    }

    /**
     * @return ArrayDataProvider
     * @throws Exception
     */
    public function total()
    {
        $condition = $this->dealer ? ' and d.id = ' . $this->dealer : '';

        $dataProvider = new ArrayDataProvider([
            'allModels' => Yii::$app->getDb()->createCommand($this->requestText($condition) . " limit 1")->queryAll(),
            'pagination' => [
                'pageSize' => 0
            ]
        ]);

        return $dataProvider;
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \yii\base\Exception
     */
    public function excel()
    {
        $dataProvider = $this->data();
        $models = $dataProvider->getModels();

        if (count($models)) {
            $dataTotal = $this->total()->getModels()[0];

            $phpExcel = new Spreadsheet();

            /* header */
            $phpExcel->setActiveSheetIndex(0)->setCellValue('A1', 'ID дилера');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('B1', 'Дилер');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('C1', 'Год');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('D1', 'Месяц');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('E1', 'Начисления');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('F1', 'Списания');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('G1', 'Разница');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('H1', 'Баланс');
            $phpExcel->setActiveSheetIndex(0)->getStyle('A1:H1')->getFont()->setBold(true);

            $rowNumber = 2;
            $currentDealer = 0;
            $dealerBalance = 0;
            foreach ($models as $model) {
                if ($currentDealer !== $model['id']) {
                    $currentDealer = $model['id'];
                    $dealerBalance = 0;
                }

                $arrival = (int)$model['arrival'];
                $withdraw = (int)$model['withdraw'];
                $balance = $arrival - $withdraw;
                $dealerBalance += $balance;

                $phpExcel->setActiveSheetIndex(0)->setCellValue('A' . $rowNumber, $model['id'] ?? 'N/A');
                $phpExcel->setActiveSheetIndex(0)->setCellValue('B' . $rowNumber, $model['name'] ?? '(не определено)');
                $phpExcel->setActiveSheetIndex(0)->setCellValue('C' . $rowNumber, $model['data_year']);
                $phpExcel->setActiveSheetIndex(0)->setCellValue('D' . $rowNumber, self::getMonth($model['data_month']));
                $phpExcel->setActiveSheetIndex(0)->setCellValue('E' . $rowNumber, $arrival);
                $phpExcel->setActiveSheetIndex(0)->setCellValue('F' . $rowNumber, $withdraw);
                $phpExcel->setActiveSheetIndex(0)->setCellValue('G' . $rowNumber, $balance);
                $phpExcel->setActiveSheetIndex(0)->setCellValue('H' . $rowNumber, $dealerBalance);
                $phpExcel->setActiveSheetIndex(0)->getStyle('H' . $rowNumber)->getFont()->setBold(true);

                $rowNumber++;
            }

            /* footer */
            $phpExcel->setActiveSheetIndex(0)->setCellValue('D' . $rowNumber, 'Итого:');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('D' . $rowNumber, $dataTotal['arrival']);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('D' . $rowNumber, $dataTotal['withdraw']);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('D' . $rowNumber, (int)$dataTotal['arrival'] - (int)$dataTotal['withdraw']);
            $phpExcel->setActiveSheetIndex(0)->getStyle('D' .  $rowNumber . ':G' . $rowNumber)->getFont()->setBold(true);

            // write to file
            $dir = Yii::getAlias('@data') . '/reports/';
            FileHelper::createDirectory($dir);
            $filePath = $dir . 'dealers_transactions.xls';
            $phpExcelWriter = IOFactory::createWriter($phpExcel, 'Xlsx');
            $phpExcelWriter->save($filePath);

            return $filePath;
        }

        return null;
    }

    /**
     * @param $condition
     * @param string $select
     * @return string
     */
    private function requestText($condition, $select = '')
    {
        return "select {$select}
                sum(
                    case
                        when ft.type = 'in' then ft.amount
                        else 0
                        end
                ) arrival,
                sum(
                   case
                       when ft.type = 'out' then ft.amount
                       else 0
                       end
               ) withdraw
                from kr_finance_transactions ft
                    left join kr_finance_purses fp on ft.purse_id = fp.id
                    left join kr_profiles p on fp.owner_id = p.id
                    left join kr_dealers_profiles dp on p.id = dp.profile_id
                    left join kr_dealers d on dp.dealer_id = d.id
                where fp.owner_type = 'modules\\\profiles\\\common\\\models\\\Profile'{$condition}";
    }
}