<?php

use yii\db\Migration;
use yii\db\Schema;

class m151210_072924_sales_brands_add_xprof extends Migration
{
    public function up()
    {
		$this->addColumn('{{%sales_brands}}', 'xprof', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%sales_brands}}', 'xprof');
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
