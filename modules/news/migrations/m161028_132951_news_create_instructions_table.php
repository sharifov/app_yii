<?php

use yii\db\Migration;

class m161028_132951_news_create_instructions_table extends Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
		}

		$this->createTable('{{%instructions}}', [
			'id' => $this->primaryKey(),
			'image_preview' => $this->string(),
			'title' => $this->string(),
			'comment' => $this->text(),
			'pdf_file' => $this->string(),
		], $tableOptions);
		$this->addColumn('{{%profiles}}', 'fresh_news', $this->integer());
		$this->addColumn('{{%profiles}}', 'push_news', $this->boolean()->defaultValue(true));
	}

	public function down()
	{
		$this->dropTable('{{%instructions}}');
		$this->dropColumn('{{%profiles}}', 'fresh_news');
		$this->dropColumn('{{%profiles}}', 'push_news');
	}

}
