<?php

use yii\db\Migration;

class m150619_084822_sales_add_foreign_keys extends Migration
{
    public function up()
    {
        $this->addForeignKey('{{%fk-sales_positions-products}}',
            '{{%sales_positions}}', 'product_id',
            '{{%sales_products}}', 'id',
            'RESTRICT', 'CASCADE');

        $this->addForeignKey('{{%fk-sales_positions-sales}}',
            '{{%sales_positions}}', 'sale_id',
            '{{%sales}}', 'id',
            'CASCADE', 'CASCADE');

        $this->addForeignKey('{{%fk-sales-dealer}}',
            '{{%sales}}', 'dealer_id',
            '{{%dealers}}', 'id',
            'RESTRICT', 'CASCADE');

        $this->addForeignKey('{{%fk-sales-promotions}}',
            '{{%sales}}', 'promotion_id',
            '{{%sales_promotions}}', 'id',
            'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('{{%fk-sales_positions-products}}', '{{%sales_positions}}');
        $this->dropForeignKey('{{%fk-sales_positions-sales}}', '{{%sales_positions}}');
        $this->dropForeignKey('{{%fk-sales-dealer}}', '{{%sales}}');
        $this->dropForeignKey('{{%fk-sales-promotions}}', '{{%sales}}');
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
