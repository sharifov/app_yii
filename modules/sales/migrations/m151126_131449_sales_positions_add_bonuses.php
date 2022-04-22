<?php

use yii\db\Migration;
use yii\db\Schema;

class m151126_131449_sales_positions_add_bonuses extends Migration
{
    public function up()
    {
        $this->addColumn('{{%sales_positions}}', 'bonuses', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%sales_positions}}', 'bonuses');
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
