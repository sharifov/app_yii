<?php

use yii\db\Migration;

class m160304_153947_sales_add_profile_fields extends Migration
{
    public function up()
    {
        $this->addColumn('{{%profiles}}', 'sales_point_name', $this->string());
        $this->addColumn('{{%profiles}}', 'position', $this->string());
    }

    public function down()
    {
        $this->dropColumn('{{%profiles}}', 'position');
        $this->dropColumn('{{%profiles}}', 'sales_point_name');
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
