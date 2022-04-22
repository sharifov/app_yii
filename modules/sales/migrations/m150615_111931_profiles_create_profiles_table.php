<?php

use yii\db\Schema;
use yii\db\Migration;

class m150615_111931_profiles_create_profiles_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%profiles}}', [
            'id' => Schema::TYPE_PK,
            'dealer_id' => Schema::TYPE_INTEGER,
            'first_name' => Schema::TYPE_STRING . '(32)',
            'last_name' => Schema::TYPE_STRING . '(32)',
            'middle_name' => Schema::TYPE_STRING . '(32)',
            'full_name' => Schema::TYPE_STRING,
            'phone_mobile' => Schema::TYPE_STRING . '(16)',
            'email' => Schema::TYPE_STRING . '(64)',
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
            'role' => Schema::TYPE_STRING . '(16)',
            'identity_id' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('dealer_id', '{{%profiles}}', 'dealer_id');
        $this->createIndex('identity_id', '{{%profiles}}', 'identity_id');
    }

    public function down()
    {
        $this->dropTable('{{%profiles}}');
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
