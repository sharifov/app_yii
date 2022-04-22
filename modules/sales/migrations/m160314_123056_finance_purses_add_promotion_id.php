<?php

use yii\db\Migration;
use yii\db\Schema;

class m160314_123056_finance_purses_add_promotion_id extends Migration
{
    public function up()
    {
        $this->addColumn('{{%finance_purses}}', 'promotion_id', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%finance_purses}}', 'promotion_id');
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
