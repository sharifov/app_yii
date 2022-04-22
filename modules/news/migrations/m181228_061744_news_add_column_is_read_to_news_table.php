<?php

use yii\db\Migration;

/**
 * Class m181228_061726_courses_add_column_brand_id_to_courses_table
 */
class m181228_061744_news_add_column_is_read_to_news_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%news}}', 'is_read', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%news}}', 'is_read');
    }

}
