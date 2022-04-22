<?php


namespace modules\spent\backend\models;


use Exception;
use marketingsolutions\finance\models\Transaction;
use modules\profiles\common\models\Profile;
use modules\sales\common\models\FinanceTransactions;
use ms\loyalty\catalog\common\models\CatalogOrder;
use ms\loyalty\catalog\common\models\OrderedCard;
use ms\loyalty\finances\common\components\CompanyAccount;
use ms\loyalty\prizes\payments\common\finances\PaymentPartner;
use ms\loyalty\prizes\payments\common\models\Payment;
use ms\loyalty\prizes\payments\common\models\Settings;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * Class SpentOrder
 *
 * @property string $month
 * @property integer $year
 *
 * @package modules\spent\backend\models
 */
class SpentOrder extends Model
{
    /** @var string */
    public $month = null;

    /** @var integer */
    public $year = null;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['year', 'integer'],
            ['month', 'string']
        ];
    }

    public function __construct($config = [])
    {
        $this->month = date("m");
        $this->year = date("Y");

        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'month' => 'Месяц',
            'year' => 'Год'
        ];
    }

    /**
     * @return array
     */
    public static function getYearList()
    {
        $months = range(2018, (int)date("Y", strtotime('now')));
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
        return self::getMonthsList()[$month];
    }

    /**
     * @return array
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function transaction()
    {
        $dates = $this->getDate();
        $data = [];

        $profiles = Profile::find()
            ->all();

        foreach ($profiles as $profile) {
            /** @var Profile $profile */
            $currentPurse = $profile->purse->id;
            $transactions = FinanceTransactions::find()
                ->where(['purse_id' => $currentPurse])
                ->andWhere(['between', 'DATE(created_at)', $dates['start'], $dates['end']])
                ->all();

            if (count($transactions)) {
                $incomingAmount = 0;
                $endBalance = 0;

                $paymentTypes = [];
                foreach (Settings::find()->all() as $paymentSetting) {
                    /** @var Settings $paymentSetting */
                    $paymentTypes[$paymentSetting->type] = $paymentSetting->profile_commission_real;
                }

                foreach ($transactions as $key => $transaction) {
                    /** @var FinanceTransactions $transaction */

                    if ($key === 0) {
                        $data[$currentPurse]['balance_start'] = $transaction->balance_before;
                        $data[$currentPurse]['total_balance'] = $transaction->balance_before;
                    }
                    $endBalance = $transaction->balance_after;

                    if ($transaction->type === Transaction::INCOMING) {

                        $incomingAmount += $transaction->amount;

                    } else {

                        switch ($transaction->partner_type) {
                            case PaymentPartner::class:
                                if (($payment = $this->findPayment($transaction->partner_id)) !== null) {
                                    /** @var Payment $payment */
                                    $data[$currentPurse]['transactions'][] = [
                                        'dealer' => $transaction->pursesOwner->owner->dealers[0]->name,
                                        'full_name' => $transaction->pursesOwner->owner->full_name,
                                        'title' => $transaction->title,
                                        'balance_start' => $transaction->balance_before,
                                        'amount' => $transaction->amount,
                                        'balance_end' => $transaction->balance_after,
                                        'nominal' => $payment->payment_amount,
                                        'qty' => 1,
                                        'commission_percent' => $paymentTypes[$payment->type] ?? 0,
                                        'commission_amount' => $payment->commission_amount,
                                        'profile_tax' => $payment->taxes_profile,
                                        'company_tax' => $payment->taxes_company / 100,
                                        'is_nds' => 0,
                                        'total_amount' => $payment->amount,
                                        'paid' => date("d.m.Y", strtotime($payment->paid_at)) ?? ''
                                    ];
                                }
                                break;
                            case CatalogOrder::class:
                                if ((count($orders = $this->findOrders($transaction->partner_id)))) {
                                    foreach ($orders as $order) {
                                        /** @var OrderedCard $order */
                                        $data[$currentPurse]['transactions'][] = [
                                            'dealer' => $transaction->pursesOwner->owner->dealers[0]->name,
                                            'full_name' => $transaction->pursesOwner->owner->full_name,
                                            'title' => $order->card->title,
                                            'balance_start' => $transaction->balance_before,
                                            'amount' => $transaction->amount,
                                            'balance_end' => $transaction->balance_after,
                                            'nominal' => $order->nominal,
                                            'qty' => $order->quantity,
                                            'commission_percent' => $order->profile_commission_percent_real,
                                            'commission_amount' => $order->profile_commission_amount,
                                            'profile_tax' => $order->taxes_profile,
                                            'company_tax' => $order->taxes_company / 100,
                                            'is_nds' => $order->include_nds,
                                            'total_amount' => $order->profile_amount,
                                            'paid' => date("d.m.Y", strtotime($order->paid_at)) ?? ''
                                        ];
                                    }
                                }
                                break;
                        }
                    }
                }
                $data[$currentPurse]['incoming'] = $incomingAmount;
                $data[$currentPurse]['balance_end'] = $endBalance;
                $data[$currentPurse]['total_balance'] = $data[$currentPurse]['balance_start'] + $incomingAmount;

            } else {

                $purseBalanceStart = $profile->purse->balanceByDate($dates['start']);
                $purseBalanceEnd = $profile->purse->balanceByDate($dates['end']);

                $data[$currentPurse]['balance_start'] = $purseBalanceStart;
                $data[$currentPurse]['incoming'] = 0;
                $data[$currentPurse]['balance_end'] = $purseBalanceEnd;
                $data[$currentPurse]['total_balance'] = $purseBalanceEnd;

            }

            if (empty($data[$currentPurse]['transactions'])) {
                $data[$currentPurse]['transactions'][] = [
                    'dealer' => $profile->dealers[0]->name,
                    'full_name' => $profile->full_name,
                    'title' => '',
                    'balance_start' => '',
                    'amount' => '',
                    'balance_end' => '',
                    'nominal' => '',
                    'qty' => '',
                    'commission_percent' => '',
                    'commission_amount' => '',
                    'profile_tax' => '',
                    'company_tax' => '',
                    'is_nds' => '',
                    'total_amount' => '',
                    'paid' => ''
                ];
            }
        }

        return $data;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getDate()
    {
        $month = $this->month ?? date("m");
        $year = $this->year ?? date("Y");

        $dateStart = new \DateTime(date('Y-m-01 00:00:00', strtotime("{$year}-{$month}-01")));
        $dateEnd = new \DateTime(date("Y-m-t 23:59:59", strtotime("{$year}-{$month}-01")));

        return [
            'start' => $dateStart->format("Y-m-d H:i:s"),
            'end' => $dateEnd->format("Y-m-d H:i:s")
        ];
    }

    /**
     * @param $id
     * @return ActiveRecord[]|null
     */
    protected function findOrders($id)
    {
        return OrderedCard::find()
            ->where([
                'status' => OrderedCard::STATUS_READY,
                'catalog_order_id' => $id
            ])
            ->all();
    }

    /**
     * @param $id
     * @return ActiveRecord|null
     */
    protected function findPayment($id)
    {
        return Payment::find()
            ->where([
                'status' => [Payment::STATUS_SUCCESS],
                'id' => $id
            ])
            ->limit(1)
            ->one();
    }

    /**
     * @param string $year
     * @return string
     * @throws InvalidConfigException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function excel($year)
    {
        $transactions = $this->transaction();
        if (count($transactions)) {
            $phpExcel = new Spreadsheet();
            $all_summ_client_title = "Общая сумма со счета клиента";

            $last_column_letter = $year <= 2019 ? "Q": "P";

            /* header */
            // style
            $this->setCellsFont($phpExcel, 2);
            $this->setCellsFont($phpExcel, 3);
            $this->setCellsFont($phpExcel, 4);
            $this->setCellBorder($phpExcel, 'E2');
            $this->setCellBorder($phpExcel, 'E3');
            $this->setCellBorder($phpExcel, 'N3');
            $this->setCellsBorder($phpExcel, 4);

            $phpExcel->setActiveSheetIndex(0)->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $phpExcel->setActiveSheetIndex(0)->getStyle('E2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $phpExcel->setActiveSheetIndex(0)->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $phpExcel->setActiveSheetIndex(0)->getStyle('E3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $phpExcel->setActiveSheetIndex(0)->getStyle('O3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $phpExcel->setActiveSheetIndex(0)->getStyle('O3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $phpExcel->setActiveSheetIndex(0)->getRowDimension(4)->setRowHeight(72);
            $phpExcel->setActiveSheetIndex(0)->getStyle('A4:Q4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $phpExcel->setActiveSheetIndex(0)->getStyle('A4:Q4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $phpExcel->setActiveSheetIndex(0)->getStyle('A4:Q4')->getAlignment()->setWrapText(true);
            $phpExcel->setActiveSheetIndex(0)->getStyle('A4:Q4')->getAlignment()->setShrinkToFit(false);
            $phpExcel->setActiveSheetIndex(0)->getStyle('A4:Q4')->getFont()->setBold(true);

            $phpExcel->setActiveSheetIndex(0)->getStyle('F4')->getFill()->setFillType(Fill::FILL_SOLID);
            $phpExcel->setActiveSheetIndex(0)->getStyle('F4')->getFill()->getStartColor()->setRGB('FCD5B5');

            // content
            $phpExcel->setActiveSheetIndex(0)->mergeCells('A2:D2');
            $phpExcel->setActiveSheetIndex(0)->mergeCells('E2:Q2');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('E2', SpentOrder::getMonth($this->month) . ', ' . $this->year . 'г.');

            $phpExcel->setActiveSheetIndex(0)->mergeCells('A3:D3');
            $phpExcel->setActiveSheetIndex(0)->mergeCells('E3:N3');
            if($year <= 2019) {
                $phpExcel->setActiveSheetIndex(0)->mergeCells('O3:Q3');
            }
            else {
                $phpExcel->setActiveSheetIndex(0)->mergeCells('O3:P3');
            }
            $phpExcel->setActiveSheetIndex(0)->setCellValue('E3', 'Для участника акции');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('O3', 'Для ЯК');

            $phpExcel->setActiveSheetIndex(0)->getStyle('F4')->getFill()->setFillType(Fill::FILL_SOLID);
            $phpExcel->setActiveSheetIndex(0)->getStyle('F4')->getFill()->getStartColor()->setRGB('FCD5B5');

            $phpExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Дилер');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('B4', 'ФИО участника');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('C4', 'Тип приза');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('D4', 'Остаток начисленных баллов у участника на начало периода');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('E4', 'Сумма начисленных баллов за отчетный месяц');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('F4', 'ИТОГО баллов у участника');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('G4', 'Номинал приза');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('H4', 'Количество');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('I4', 'Номинал + Количество');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('J4', '% процент комиссии за предоставление приза');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('K4', 'Комиссия для участника');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('L4', 'НДФЛ с участника');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('M4', 'Сумма списанных баллов с участника');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('N4', 'Сумма оставшихся баллов у участника на конец периода');
            if($year <= 2019) {
                $phpExcel->setActiveSheetIndex(0)->setCellValue('O4', 'НДС за оказанную услугу с клиента');
                $all_summ_client_title .= ", в т.ч. НДС 20%";
                $phpExcel->setActiveSheetIndex(0)->setCellValue('P4', $all_summ_client_title);
                $phpExcel->setActiveSheetIndex(0)->setCellValue('Q4', 'Дата выплаты в 1С');
            }
            else {
                $phpExcel->setActiveSheetIndex(0)->setCellValue('O4', $all_summ_client_title);
                $phpExcel->setActiveSheetIndex(0)->setCellValue('P4', 'Дата выплаты в 1С');
            }



            /* content */
            $row = 5;
            $summaryAmount = 0;
            $summaryTotalBalance = 0;
            $summaryNominal = 0;
            $summaryProfileCommission = 0;
            $summaryNdfl = 0;
            $summaryTotalSpent = 0;
            $summaryEndBalance = 0;
            $summaryNds = 0;
            $summaryTotal = 0;

            foreach ($transactions as $purse => $purseData) {

                $summaryAmount += $purseData['incoming'] ?? 0;
                $summaryEndBalance += $purseData['balance_end'] ?? 0;
                $summaryTotalBalance += $purseData['total_balance'] ?? 0;
                $nominal = 0;

                if (!empty($purseData['transactions'])) {

                    $needStartData = true;
                    foreach ($purseData['transactions'] as $transaction) {

                        if ($transaction['qty']) {

                            $nominal = $transaction['nominal'] * $transaction['qty'];
                            $commissionAmount = $transaction['commission_amount'];

                            if ((int)$this->month < 6 && (int)$this->year < 2020) {
                                $ndfl = $transaction['company_tax'];
                            } else {
                                $ndfl = $transaction['profile_tax'];
                            }

                            if ($transaction['is_nds']) {
                                $nds = 0;
                                if($year <= 2019) {
                                    $nds = ($nominal / 1.2 + $commissionAmount / 1.2 + $ndfl) * 0.2;
                                    $total = $nominal / 1.2 + $commissionAmount / 1.2 + $ndfl + $nds;
                                }
                                else {
                                    $total = $nominal + $commissionAmount + $ndfl + $nds;
                                }
                            } else {
                                if ($commissionAmount) {
                                    $nds = 0;
                                    if($year <= 2019) {
                                        $nds = ($nominal + $ndfl) * 0.2 + $commissionAmount / 1.2 * 0.2;
                                        $total = $nominal + $commissionAmount / 1.2 + $ndfl + $nds;
                                    }
                                    else {
                                        $total = $nominal + $commissionAmount + $ndfl + $nds;
                                    }
                                } else {
                                    $nds = 0;
                                    if($year <= 2019) {
                                        $nds = ($nominal + $commissionAmount + $ndfl) * 0.2;
                                    }
                                    $total = $nominal + $commissionAmount + $ndfl + $nds;
                                }
                            }

                            $summaryNominal += $nominal;
                            $summaryProfileCommission += $commissionAmount;
                            $summaryNdfl += $transaction['profile_tax'];
                            $summaryTotalSpent += $transaction['total_amount'];
                            $summaryNds += $nds;
                            $summaryTotal += $total;
                        } else {
                            $commissionAmount = '';
                            $nds = null;
                            $total = null;
                        }

                        // style
                        $this->setCellsFont($phpExcel, $row);
                        $this->setCellsBorder($phpExcel, $row);
                        $phpExcel->setActiveSheetIndex(0)->getStyle('E' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                        $phpExcel->setActiveSheetIndex(0)->getStyle('E' . $row)->getFill()->getStartColor()->setRGB('D7E4BD');
                        $phpExcel->setActiveSheetIndex(0)->getStyle('F' . $row)->getFill()->getStartColor()->setRGB('FCD5B5');
                        $phpExcel->setActiveSheetIndex(0)->getStyle('G' . $row . ':K' . $row)->getFill()->getStartColor()->setRGB('D7E4BD');
                        $phpExcel->setActiveSheetIndex(0)->getStyle('A' . $row . ':Q' . $row)->getBorders()->getLeft()->applyFromArray(['style' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]);
                        $phpExcel->setActiveSheetIndex(0)->getStyle('A' . $row . ':Q' . $row)->getBorders()->getTop()->applyFromArray(['style' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]);
                        $phpExcel->setActiveSheetIndex(0)->getStyle('A' . $row . ':Q' . $row)->getBorders()->getRight()->applyFromArray(['style' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]);

                        $phpExcel->setActiveSheetIndex(0)->setCellValue('A' . $row, $transaction['dealer']);
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('B' . $row, $transaction['full_name']);
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('C' . $row, $transaction['title']);
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('D' . $row, $needStartData ? $purseData['balance_start'] ?? '' : '');
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('E' . $row, $needStartData ? $purseData['incoming'] ?? '' : '');
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('F' . $row, $needStartData ? $purseData['total_balance'] ?? 0 : '');
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('G' . $row, $transaction['nominal']);
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('H' . $row, $transaction['qty']);
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('I' . $row, $nominal ? $nominal : '');
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('J' . $row, $transaction['commission_percent'] ? $transaction['commission_percent'] . '%' : '');
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('K' . $row, $commissionAmount);
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('L' . $row, $transaction['profile_tax']);
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('M' . $row, $transaction['total_amount']);
                        $phpExcel->setActiveSheetIndex(0)->setCellValue('N' . $row, $needStartData ? $purseData['balance_end'] ?? '' : '');
                        if($year <= 2019) {
                            $phpExcel->setActiveSheetIndex(0)->setCellValue('O' . $row, $nds ? number_format($nds, 2, '.', '') : '');
                            $phpExcel->setActiveSheetIndex(0)->setCellValue('P' . $row, $total ? number_format($total, 2, '.', '') : '');
                            $phpExcel->setActiveSheetIndex(0)->setCellValue('Q' . $row, $transaction['paid']);
                        }
                        else {
                            $phpExcel->setActiveSheetIndex(0)->setCellValue('O' . $row, $total ? number_format($total, 2, '.', '') : '');
                            $phpExcel->setActiveSheetIndex(0)->setCellValue('P' . $row, $transaction['paid']);
                        }

                        $row++;
                        $needStartData = false;
                    }
                    $summaryEndBalance += $purseData['balance_end'] ?? 0;
                }
            }

            /* footer */
            // style
            $this->setCellsFont($phpExcel, $row);
            $this->setCellsBorder($phpExcel, $row, true);
            $phpExcel->setActiveSheetIndex(0)->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $phpExcel->setActiveSheetIndex(0)->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
            $phpExcel->setActiveSheetIndex(0)->getStyle('A' . $row . ':' . $last_column_letter . $row)->getFont()->setBold(true);

            // content
            $phpExcel->setActiveSheetIndex(0)->getStyle('A' . $row . ':' . $last_column_letter . $row)->getFill()->setFillType(Fill::FILL_SOLID);
            $phpExcel->setActiveSheetIndex(0)->getStyle('A' . $row . ':' . $last_column_letter . $row)->getFill()->getStartColor()->setRGB('DCE6F2');
            $phpExcel->setActiveSheetIndex(0)->mergeCells('A' . $row . ':D' . $row);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('A' . $row, 'ИТОГО в расчетном периоде:');
            $phpExcel->setActiveSheetIndex(0)->setCellValue('E' . $row, $summaryAmount);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('F' . $row, $summaryTotalBalance);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('I' . $row, $summaryNominal);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('K' . $row, $summaryProfileCommission);
            $phpExcel->setActiveSheetIndex(0)->setCellValue('L' . $row, number_format($summaryNdfl, 0, '.', ''));
            $phpExcel->setActiveSheetIndex(0)->setCellValue('M' . $row, number_format($summaryTotalSpent, 0, '.', ''));
            $phpExcel->setActiveSheetIndex(0)->setCellValue('N' . $row, number_format($summaryEndBalance, 0, '.', ''));
            if($year <= 2019) {
                $phpExcel->setActiveSheetIndex(0)->setCellValue('O' . $row, number_format($summaryNds, 2, '.', ''));
                $phpExcel->setActiveSheetIndex(0)->setCellValue('P' . $row, number_format($summaryTotal, 2, '.', ''));
            }
            else {
                $phpExcel->setActiveSheetIndex(0)->setCellValue('O' . $row, number_format($summaryTotal, 2, '.', ''));
            }



            // write to file
            $fileFolder = Yii::getAlias('@data') . '/reports/';
            if (!file_exists($fileFolder)) {
                mkdir($fileFolder, 0777);
            }
            $filePath = $fileFolder . 'spent.xls';
            $phpExcelWriter = IOFactory::createWriter($phpExcel, 'Xlsx');
            $phpExcelWriter->save($filePath);

            return $filePath;
        }

        return null;
    }

    /**
     * @param Spreadsheet $phpExcel
     * @param $cell
     * @param bool $bottom
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function setCellBorder(Spreadsheet $phpExcel, $cell, $bottom = false)
    {
        $phpExcel->setActiveSheetIndex(0)->getStyle($cell)->getBorders()->getLeft()->applyFromArray(['style' => Border::BORDER_THIN,'color' => ['rgb' => '000000']]);
        $phpExcel->setActiveSheetIndex(0)->getStyle($cell)->getBorders()->getTop()->applyFromArray(['style' => Border::BORDER_THIN,'color' => ['rgb' => '000000']]);
        $phpExcel->setActiveSheetIndex(0)->getStyle($cell)->getBorders()->getRight()->applyFromArray(['style' => Border::BORDER_THIN,'color' => ['rgb' => '000000']]);
        if ($bottom === true) {
            $phpExcel->setActiveSheetIndex(0)->getStyle($cell)->getBorders()->getBottom()->applyFromArray(['style' => Border::BORDER_THIN,'color' => ['rgb' => '000000']]);
        }
    }

    /**
     * @param Spreadsheet $phpExcel
     * @param $row
     * @param bool $bottom
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function setCellsBorder(Spreadsheet $phpExcel, $row, $bottom = false)
    {
        $this->setCellBorder($phpExcel, 'A' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'B' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'C' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'D' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'E' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'F' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'G' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'H' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'I' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'J' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'K' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'L' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'M' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'N' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'O' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'P' . $row, $bottom);
        $this->setCellBorder($phpExcel, 'Q' . $row, $bottom);
    }

    /**
     * @param Spreadsheet $phpExcel
     * @param $row
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function setCellsFont(Spreadsheet $phpExcel, $row)
    {
        $phpExcel->setActiveSheetIndex(0)->getStyle('A' . $row . ':Q' . $row)->getFont()->setSize(8);
    }
}