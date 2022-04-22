<?php

use yii\db\Schema;
use yii\db\Migration;

class m151208_114627_sales_create_sale_validation_rules_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

		$this->createTable('{{%sale_validation_rules}}', [
			'id' => Schema::TYPE_PK,
			'name' => Schema::TYPE_STRING,
			'is_enabled' => Schema::TYPE_BOOLEAN,
			'rule' => Schema::TYPE_STRING,
			'error' => Schema::TYPE_STRING,
			'promotion_id' => Schema::TYPE_INTEGER,
		], $tableOptions);

		$this->createIndex('promotion_id', '{{%sale_validation_rules}}', 'promotion_id');
		$this->addForeignKey('{{%fk-validation_rules-promotions}}', '{{%sale_validation_rules}}', 'promotion_id', '{{%sales_promotions}}', 'id', 'CASCADE', 'CASCADE');
	}

    public function down()
    {
		$this->dropForeignKey('{{%fk-validation_rules-promotions}}', '{{%sale_validation_rules}}');
        $this->dropTable('{{%sale_validation_rules}}');
    }
}
