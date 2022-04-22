<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

class m151013_095716_dealers_add_admin_user_id extends Migration
{
    public function up()
    {
        $this->addColumn('{{%dealers}}', 'admin_user_id', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%dealers}}', 'admin_user_id');

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
