<?php

use yii\db\Migration;

/**
 * Class m190121_053820_news_add_column_is_push_sent
 */
class m190121_053820_news_add_column_is_push_sent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%news}}', 'is_push_sent', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%news}}', 'is_push_sent');
    }

}
