<?php

use yii\db\Migration;

class m161028_13299999_add_column_to_profile extends Migration
{
	public function up()
	{
		$this->addColumn('{{%profiles}}', 'fresh_news', $this->integer());
		$this->addColumn('{{%profiles}}', 'push_news', $this->boolean()->defaultValue(true));
	}

	public function down()
	{
		$this->dropColumn('{{%profiles}}', 'fresh_news');
		$this->dropColumn('{{%profiles}}', 'push_news');
	}

}
