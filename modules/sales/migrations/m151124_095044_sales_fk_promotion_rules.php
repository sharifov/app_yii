<?php

use yii\db\Migration;

class m151124_095044_sales_fk_promotion_rules extends Migration
{
    public function up()
    {
        $this->addForeignKey('{{%sales-promotion-rules-promotions}}',
            '{{%sales_promotion_rules}}', 'promotion_id',
            '{{%sales_promotions}}', 'id',
            'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('{{%sales-promotion-rules-promotions}}', '{{%sales_promotion_rules}}');
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
