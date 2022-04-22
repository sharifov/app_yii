<?php

use yii\db\Migration;
use yii\db\Schema;

class m150618_114946_sales_add_sales_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sales}}', [
            'id' => Schema::TYPE_PK,
            'status' => Schema::TYPE_STRING . '(16)',
            'kg' => Schema::TYPE_INTEGER,
            'bonuses' => Schema::TYPE_INTEGER,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
            'sold_on' => Schema::TYPE_DATETIME,
            'approved_by_admin_at' => Schema::TYPE_DATETIME,
            'bonuses_paid_at' => Schema::TYPE_DATETIME,
            'dealer_id' => Schema::TYPE_INTEGER,
            'previous_kg' => Schema::TYPE_INTEGER,
            'x' => Schema::TYPE_INTEGER,
            'xx' => Schema::TYPE_INTEGER,
            'rule' => Schema::TYPE_STRING,
            'manager_commission' => Schema::TYPE_INTEGER,
            'manager_commission_included' => Schema::TYPE_BOOLEAN,
            'manager_bonuses' => Schema::TYPE_INTEGER,
            'dealer_bonuses' => Schema::TYPE_INTEGER,
            'promotion_id' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createTable('{{%sales_positions}}', [
            'id' => Schema::TYPE_PK,
            'sale_id' => Schema::TYPE_INTEGER,
            'product_id' => Schema::TYPE_INTEGER,
            'kg' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('sale_id', '{{%sales_positions}}', 'sale_id');
        $this->createIndex('product_id', '{{%sales_positions}}', 'product_id');
        $this->createIndex('dealer_id', '{{%sales}}', 'dealer_id');
        $this->createIndex('promotion_id', '{{%sales}}', 'promotion_id');
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
