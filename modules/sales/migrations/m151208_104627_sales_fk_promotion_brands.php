<?php

use yii\db\Migration;
use yii\db\Schema;

class m151208_104627_sales_fk_promotion_brands extends Migration
{
	public function up()
	{
		$this->addForeignKey('{{%fk_promotion_id}}', '{{%sales_promotion_brands}}', 'promotion_id', '{{%sales_promotions}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_brand_id}}', '{{%sales_promotion_brands}}', 'brand_id', '{{%sales_brands}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('{{%fk_dealer_id}}', '{{%sales_promotion_brands}}');
		$this->dropForeignKey('{{%fk_brand_id}}', '{{%sales_promotion_brands}}');
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
