<?php

use yii\db\Migration;
use yii\db\Schema;

class m151203_132410_sales_create_factor_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sales_factors}}', [
            'id' => Schema::TYPE_PK,
            'x' => Schema::TYPE_INTEGER . ' DEFAULT 0',
            'dealer_id' => Schema::TYPE_INTEGER,
            'brand_id' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('dealer_id', '{{%sales_factors}}', 'dealer_id');
        $this->addForeignKey('{{%fk-dealer_id}}', '{{%sales_factors}}', 'dealer_id', '{{%dealers}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('brand_id', '{{%sales_factors}}', 'brand_id');
        $this->addForeignKey('{{%fk-brand_id}}', '{{%sales_factors}}', 'brand_id', '{{%sales_brands}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('{{%fk-dealer_id}}', '{{%sales_factors}}');
        $this->dropForeignKey('{{%fk-brand_id}}', '{{%sales_factors}}');
        $this->dropTable('{{%sales_factors}}');
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
