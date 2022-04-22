<?php

use yii\db\Migration;
use yii\db\Schema;

class m151130_060749_profiles_create_dealer_payments_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%dealer_payments}}', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
            'recipient_id' => Schema::TYPE_INTEGER,
            'bonuses' => Schema::TYPE_INTEGER,
            'dealer_id' => Schema::TYPE_INTEGER,
            'manager_id' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('recipient_id', '{{%dealer_payments}}', 'recipient_id');
        $this->createIndex('dealer_id', '{{%dealer_payments}}', 'dealer_id');
        $this->createIndex('manager_id', '{{%dealer_payments}}', 'manager_id');

        $this->addForeignKey('{{%fk_recipient_id}}', '{{%dealer_payments}}', 'recipient_id', '{{%profiles}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%fk_manager_id}}', '{{%dealer_payments}}', 'manager_id', '{{%profiles}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('{{%fk_dealer_id}}', '{{%dealer_payments}}', 'dealer_id', '{{%dealers}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('{{%fk_recipient_id}}', '{{%dealer_payments}}');
        $this->dropForeignKey('{{%fk_manager_id}}', '{{%dealer_payments}}');
        $this->dropForeignKey('{{%fk_dealer_id}}', '{{%dealer_payments}}');

        $this->dropTable('{{%dealer_payments}}');
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
