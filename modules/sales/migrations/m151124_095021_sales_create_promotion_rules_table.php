<?php

use yii\db\Migration;
use yii\db\Schema;

class m151124_095021_sales_create_promotion_rules_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sales_promotion_rules}}', [
            'id' => Schema::TYPE_PK,
            'promotion_id' => Schema::TYPE_INTEGER,
            'priority' => Schema::TYPE_INTEGER,
            'condition' => Schema::TYPE_STRING,
            'rule' => Schema::TYPE_STRING,
            'description' => Schema::TYPE_TEXT,
        ], $tableOptions);

        $this->createIndex('promotion_id', '{{%sales_promotion_rules}}', 'promotion_id');
    }

    public function down()
    {
        $this->dropTable('{{%sales_promotion_rules}}');
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
