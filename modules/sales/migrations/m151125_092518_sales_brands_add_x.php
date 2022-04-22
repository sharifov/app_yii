<?php

use yii\db\Migration;
use yii\db\Schema;

class m151125_092518_sales_brands_add_x extends Migration
{
    public function up()
    {
        $this->addColumn('{{%sales_brands}}', 'x', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%sales_brands}}', 'x');
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
