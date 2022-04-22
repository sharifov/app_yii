<?php

use yii\db\Migration;
use yii\db\Schema;

class m160626_114946_sales_create_promotion_products_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sales_promotion_products}}', [
            'id' => $this->primaryKey(),
            'promotion_id' => $this->integer(),
            'product_id' => $this->integer(),
            'x' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('promotion_id', '{{%sales_promotion_products}}', 'promotion_id');
        $this->createIndex('product_id', '{{%sales_promotion_products}}', 'product_id');
    }

    public function down()
    {
        $this->dropTable('{{%sales_promotion_products}}');
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
