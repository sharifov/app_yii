<?php

use yii\db\Migration;

/**
 * Class m191204_101818_profiles_add_table_nullify
 */
class m191204_101818_profiles_add_table_nullify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%nullify}}', [
            'id' => $this->primaryKey(),
            'profile_id' => $this->integer(),
            'sum' => $this->integer(),
            'created_at' => $this->dateTime(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%nullify}}');
    }
}
