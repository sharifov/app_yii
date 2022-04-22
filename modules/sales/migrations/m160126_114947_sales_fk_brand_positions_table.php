<?php

use yii\db\Migration;
use yii\db\Schema;

class m160126_114947_sales_fk_brand_positions_table extends Migration
{
	public function up()
	{
		$this->addForeignKey('{{%fk-brands}}',
			'{{%sales_brand_positions}}', 'brand_id',
			'{{%sales_brands}}', 'id',
			'RESTRICT', 'CASCADE');

		$this->addForeignKey('{{%fk-sales}}',
			'{{%sales_brand_positions}}', 'sale_id',
			'{{%sales}}', 'id',
			'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('{{%fk-brands}}', '{{%sales_brand_positions}}');
		$this->dropForeignKey('{{%fk-sales}}', '{{%sales_brand_positions}}');
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
