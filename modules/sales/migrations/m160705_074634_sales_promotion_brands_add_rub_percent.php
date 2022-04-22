<?php

use yii\db\Migration;

class m160705_074634_sales_promotion_brands_add_rub_percent extends Migration
{
    public function up()
    {
        $this->addColumn('{{%sales_promotion_brands}}', 'rub_percent', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%sales_promotion_brands}}', 'rub_percent');

        return false;
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
