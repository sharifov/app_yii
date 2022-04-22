<?php

use yii\db\Migration;

class m151013_095744_dealers_fk_admin_user_id extends Migration
{
    public function up()
    {
        $this->createIndex('admin_user_id', '{{%dealers}}', 'admin_user_id');
        $this->addForeignKey('{{%fk_admin_user_id}}', '{{%dealers}}', 'admin_user_id', '{{%admin_users}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('{{%fk_admin_user_id}}', '{{%dealers}}');
        $this->dropIndex('admin_user_id', '{{%dealers}}');

        return false;
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
