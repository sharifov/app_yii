<?php

use yii\db\Migration;
use yii\db\Schema;

class m160220_125025_news_create_news_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%news}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING,
            'content' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
            'enabled' => Schema::TYPE_BOOLEAN . ' DEFAULT 1',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%news}}');
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
