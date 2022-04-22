<?php

use yii\db\Migration;
use yii\db\Schema;

class m160406_134250_sms_create_sms_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sms}}', [
            'id' => Schema::TYPE_PK,
            'to' => Schema::TYPE_STRING,
            'type' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_STRING,
            'message' => Schema::TYPE_TEXT,
            'sent_to' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%sms}}');
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
