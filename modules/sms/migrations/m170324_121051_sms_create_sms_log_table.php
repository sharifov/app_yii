<?php

use yii\db\Migration;


class m170324_121051_sms_create_sms_log_table extends Migration
{

	public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sms_log}}', [
            'id' => $this->primaryKey(),
            'sms_id' => $this->integer(),
			'type' => $this->string(30),
			'service' => $this->string(30),
			'phone_mobile' => $this->string(16)->notNull(),
			'message' => $this->text(),
			'status' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('fk_sms_id', '{{%sms_log}}', 'sms_id');
    }


    public function down()
    {
        $this->dropTable('{{%sms_log}}');
    }

}
