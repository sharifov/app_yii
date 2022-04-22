<?php

use yii\db\Migration;

class m170302_130718_add_position_column_to_dealers_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%dealers}}', 'resolve_phone', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%dealers}}', 'resolve_purse', $this->boolean()->defaultValue(false));
    }

    public function down()
    {

        $this->dropColumn('{{%dealers}}', 'resolve_phone');
        $this->dropColumn('{{%dealers}}', 'resolve_purse');
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
