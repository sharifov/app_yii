<?php

use yii\db\Migration;

/**
 * Class m190123_105058_news_add_columns_brand_id_and_teaser
 */
class m190123_105058_news_add_columns_brand_id_and_teaser extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%news}}', 'teaser', $this->text()->after('title'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%news}}', 'teaser');
    }

}
