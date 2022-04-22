<?php

use yii\db\Migration;

class m160705_101119_sales_add_rub_column extends Migration
{
    public function up()
    {
        $this->addColumn('{{%sales}}', 'rub', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%sales}}', 'rub');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
