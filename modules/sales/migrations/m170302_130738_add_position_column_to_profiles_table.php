<?php

use yii\db\Migration;

class m170302_130738_add_position_column_to_profiles_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%profiles}}', 'resolve_phone', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%profiles}}', 'resolve_purse', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        $this->dropColumn('{{%profiles}}', 'resolve_phone');
        $this->dropColumn('{{%profiles}}', 'resolve_purse');
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
