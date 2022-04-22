<?php

use yii\db\Migration;

class m160705_124742_sales_promotions_add_type_column extends Migration
{
    public function up()
    {
        $this->addColumn('{{%sales_promotions}}', 'type', $this->string());
    }

    public function down()
    {
        $this->dropColumn('{{%sales_promotions}}', 'type');
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
