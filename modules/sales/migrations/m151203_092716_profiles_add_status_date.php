<?php

use yii\db\Migration;
use yii\db\Schema;

class m151203_092716_profiles_add_status_date extends Migration
{
    public function up()
    {
        $this->addColumn('{{%profiles}}', 'status', Schema::TYPE_STRING . ' DEFAULT NULL');
        $this->addColumn('{{%profiles}}', 'status_date', Schema::TYPE_DATE . ' DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('{{%profiles}}', 'status');
        $this->dropColumn('{{%profiles}}', 'status_date');
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
