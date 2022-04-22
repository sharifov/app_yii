<?php

use yii\db\Migration;
use yii\db\Schema;

class m160126_114946_sales_create_brand_positions_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sales_brand_positions}}', [
            'id' => Schema::TYPE_PK,
            'sale_id' => Schema::TYPE_INTEGER,
            'brand_id' => Schema::TYPE_INTEGER,
            'kg' => Schema::TYPE_INTEGER,
			'bonuses' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('sale_id', '{{%sales_brand_positions}}', 'sale_id');
        $this->createIndex('brand_id', '{{%sales_brand_positions}}', 'brand_id');
    }

    public function down()
    {
        $this->dropTable('{{%sales}}');
        $this->dropTable('{{%sales_positions}}');
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
