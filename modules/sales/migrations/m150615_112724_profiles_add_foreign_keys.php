<?php

use yii\db\Migration;

class m150615_112724_profiles_add_foreign_keys extends Migration
{
    public function up()
    {
        $this->addForeignKey('{{%fk-profiles-dealers}}',
            '{{%profiles}}', 'dealer_id',
            '{{%dealers}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey('{{%fk-dealers-promotions}}',
            '{{%dealers}}', 'promotion_id',
            '{{%sales_promotions}}', 'id',
            'CASCADE', 'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('{{%fk-profiles-dealers}}', '{{%profiles}}');
        $this->dropForeignKey('{{%fk-dealers-promotions}}', '{{%dealers}}');
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
