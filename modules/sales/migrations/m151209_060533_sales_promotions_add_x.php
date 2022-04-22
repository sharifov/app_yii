<?php

use yii\db\Migration;
use yii\db\mssql\Schema;

class m151209_060533_sales_promotions_add_x extends Migration
{
    public function up()
    {
		$this->addColumn('{{%sales_promotions}}', 'x', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%sales_promotions}}', 'x');
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
