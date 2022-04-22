<?php

use yii\db\Migration;

class m170606_085938_add_column_admin_id_to_finance_transactions_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%finance_transactions}}', 'admin_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%finance_transactions}}', 'admin_id');
    }
}
