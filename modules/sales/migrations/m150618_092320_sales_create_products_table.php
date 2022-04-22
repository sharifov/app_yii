<?php

use yii\db\Migration;
use yii\db\Schema;

class m150618_092320_sales_create_products_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sales_products}}', [
            'id' => Schema::TYPE_PK,
            'category_id' => Schema::TYPE_INTEGER,
            'type_id' => Schema::TYPE_INTEGER,
            'brand_id' => Schema::TYPE_INTEGER,
            'name' => Schema::TYPE_STRING,
            'packing' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('category_id', '{{%sales_products}}', 'category_id');
        $this->createIndex('type_id', '{{%sales_products}}', 'type_id');
        $this->createIndex('brand_id', '{{%sales_products}}', 'brand_id');
    }

    public function down()
    {
        $this->dropTable('{{%sales_products}}');
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
