<?php

use yii\db\Schema;
use yii\db\Migration;

class m150615_102459_profiles_create_dealers_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%dealers}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
            'x' => Schema::TYPE_INTEGER,
            'xx' => Schema::TYPE_INTEGER,
            'promotion_id' => Schema::TYPE_INTEGER,
            'manager_commission' => Schema::TYPE_INTEGER,
            'manager_commission_included' => Schema::TYPE_BOOLEAN . ' DEFAULT 0',
        ], $tableOptions);

        $this->createIndex('promotion_id', '{{%dealers}}', 'promotion_id');
    }

    public function down()
    {
        $this->dropTable('{{%dealers}}');
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
