<?php

use yii\db\Migration;
use yii\db\Schema;

class m151208_104626_sales_promotion_brands extends Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
		}

		$this->createTable('{{%sales_promotion_brands}}', [
			'promotion_id' => Schema::TYPE_INTEGER,
			'brand_id' => Schema::TYPE_INTEGER,
		], $tableOptions);

		$this->createIndex('promotion_id', '{{%sales_promotion_brands}}', 'promotion_id');
		$this->createIndex('brand_id', '{{%sales_promotion_brands}}', 'brand_id');
	}

	public function down()
	{
		$this->dropTable('{{%sales_promotion_brands}}');
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
