<?php

use yii\db\Migration;
use yii\db\Schema;

class m141123_084337_sales_create_promotions_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sales_promotions}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%sales_promotions}}');
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
