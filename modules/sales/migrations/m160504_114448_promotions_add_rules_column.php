<?php

use yii\db\Migration;

class m160504_114448_promotions_add_rules_column extends Migration
{
    public function up()
    {
        $this->addColumn('{{%sales_promotions}}', 'rules', $this->string());
    }

    public function down()
    {
        $this->dropColumn('{{%sales_promotions}}', 'rules');
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
