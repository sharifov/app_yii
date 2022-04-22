<?php

use yii\db\Migration;
use yii\db\Schema;

class m150618_092306_sales_create_brands_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sales_brands}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%sales_brands}}');
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
