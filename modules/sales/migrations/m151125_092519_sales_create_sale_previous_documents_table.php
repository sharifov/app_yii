<?php

use yii\db\Migration;
use yii\db\Schema;

class m151125_092519_sales_create_sale_previous_documents_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable('{{%sale_previous_documents}}', [
            'id' => Schema::TYPE_PK,
            'original_name' => Schema::TYPE_STRING,
            'name' => Schema::TYPE_STRING,
            'file_size' => Schema::TYPE_INTEGER,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
            'sale_id' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('sale_id', '{{%sale_previous_documents}}', 'sale_id');
        $this->addForeignKey('{{%fk-sale_previous_documents-sales}}', '{{%sale_previous_documents}}', 'sale_id', '{{%sales}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('{{%fk-sale_previous_documents-sales}}', '{{%sale_documents}}');
        $this->dropTable('{{%sale_previous_documents}}');
    }
}
