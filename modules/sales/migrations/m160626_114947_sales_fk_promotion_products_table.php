<?php

use yii\db\Migration;
use yii\db\Schema;

class m160626_114947_sales_fk_promotion_products_table extends Migration
{
    public function up()
    {
        $this->addForeignKey('{{%fk-promotion-id}}', '{{%sales_promotion_products}}', 'promotion_id', '{{%sales_promotions}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%fk-product-id}}', '{{%sales_promotion_products}}', 'product_id', '{{%sales_products}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('{{%fk-promotion-id}}', '{{%sales_promotion_products}}');
        $this->dropForeignKey('{{%fk-product-id}}', '{{%sales_promotion_products}}');
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
