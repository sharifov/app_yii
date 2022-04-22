<?php

use yii\db\Migration;
use yii\db\Schema;

class m151126_140000_profiles_create_reports_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%reports}}', [
            'id' => Schema::TYPE_PK,
            'original_name' => Schema::TYPE_STRING,
            'name' => Schema::TYPE_STRING,
            'file_size' => Schema::TYPE_INTEGER,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
            'profile_id' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('profile_id', '{{%reports}}', 'profile_id');
        $this->addForeignKey('{{%fk-reports-profiles}}', '{{%reports}}', 'profile_id', '{{%profiles}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('{{%fk-reports-profiles}}', '{{%reports}}');
        $this->dropColumn('{{%reports}}', 'profile_id');
        $this->dropTable('{{%reports}}');
    }
}
