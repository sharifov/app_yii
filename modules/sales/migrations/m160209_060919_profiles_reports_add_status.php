<?php

use yii\db\Migration;
use yii\db\Schema;

class m160209_060919_profiles_reports_add_status extends Migration
{
    public function up()
    {
        $this->addColumn('{{%reports}}', 'status', Schema::TYPE_STRING . " DEFAULT 'new'");
    }

    public function down()
    {
        $this->dropColumn('{{%reports}}', 'status');
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
