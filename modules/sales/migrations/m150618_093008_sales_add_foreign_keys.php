<?php

use yii\db\Migration;

class m150618_093008_sales_add_foreign_keys extends Migration
{
    public function up()
    {
        $this->addForeignKey('{{%fk-sales-products-categories}}', '{{%sales_products}}', 'category_id', '{{%sales_categories}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('{{%fk-sales-products-types}}', '{{%sales_products}}', 'type_id', '{{%sales_types}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('{{%fk-sales-products-brands}}', '{{%sales_products}}', 'brand_id', '{{%sales_brands}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('{{%fk-sales-products-categories}}', '{{%sales_products}}');
        $this->dropForeignKey('{{%fk-sales-products-types}}', '{{%sales_products}}');
        $this->dropForeignKey('{{%fk-sales-products-brands}}', '{{%sales_products}}');
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
