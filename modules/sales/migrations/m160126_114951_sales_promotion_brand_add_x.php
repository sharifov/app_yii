<?php

use yii\db\Migration;
use yii\db\Schema;

class m160126_114951_sales_promotion_brand_add_x extends Migration
{
	public function up()
	{
		$this->addColumn('{{%sales_promotion_brands}}', 'x', Schema::TYPE_INTEGER);
	}

	public function down()
	{
		$this->dropColumn('{{%sales_promotion_brands}}', 'x');
	}
}
