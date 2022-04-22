<?php

use yii\db\Migration;

class m160705_093018_sales_brand_position_add_rub_column extends Migration
{
    public function up()
    {
        $this->addColumn('{{%sales_brand_positions}}', 'rub', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{%sales_brand_positions}}', 'rub');

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
