<?php


namespace modules\interim\console\controllers;


use console\base\Controller;
use marketingsolutions\finance\models\Purse;
use marketingsolutions\finance\models\Transaction;
use modules\profiles\common\models\Dealer;
use modules\sales\common\finances\DealerPartner;
use yii\console\ExitCode;

class FixController extends Controller
{
    public function actionDealerPurses()
    {
        $dealers = Dealer::find()->all();
        foreach ($dealers as $dealer) {
            /** @var Dealer $dealer */
            echo "{$dealer->name}...\n";
            foreach ($dealer->purses as $purse) {
                /** @var Purse $purse */
                echo "{$purse->id}...\n";
                $this->fixDealerPurseBalance($purse);
            }
        }

        echo "done\n";
        ExitCode::OK;
    }

    private function fixDealerPurseBalance(Purse $purse)
    {
        $balanceByTransaction = 0;
        $purseBalance = $purse->getBalance();

        $lastTransaction = Transaction::find()
            ->where(['purse_id' => $purse->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1)
            ->one();

        if ($lastTransaction !== null) {
            /** @var Transaction $lastTransaction */
            $balanceByTransaction = $lastTransaction->balance_after;
        }

        $amount = 0;
        if ($balanceByTransaction > $purseBalance) {
            $type = Transaction::OUTBOUND;
            $amount = $balanceByTransaction - $purseBalance;
        } elseif ($purseBalance > $balanceByTransaction) {
            $type = Transaction::INCOMING;
            $amount = $purseBalance - $balanceByTransaction;
        }

        if ($amount) {
            /** @var Transaction $transaction */
            $purse->addTransaction(Transaction::factory(
                $type,
                $amount,
                DealerPartner::findById($purse->id),
                'Корректировка баланса',
                'Выявление расхождений по итоговому балансу с движением транзакций'
            ));

            $transaction = Transaction::find()
                ->where(['purse_id' => $purse->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(1)
                ->one();

            $transaction->updateAttributes(['balance_after' => $purseBalance]);
            $purse->updateAttributes(['balance' => $purseBalance]);

            echo "fixed: {$type} - {$amount}\n";
        }
    }
}