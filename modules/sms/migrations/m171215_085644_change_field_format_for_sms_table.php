<?php

use yii\db\Migration;

/**
 * Class m171215_085644_change_field_format_for_sms_table
 */
class m171215_085644_change_field_format_for_sms_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('{{%sms}}' , 'to' , $this->text());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('{{%sms}}' , 'to' , $this->string());
    }


}
