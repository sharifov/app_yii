<?php

use yii\db\Migration;

class m160304_134317_sales_add_dealer_id_to_reports extends Migration
{
    public function up()
    {
        $this->addColumn('{{%reports}}', 'dealer_id', $this->integer());

        $profiles = (new \yii\db\Query())
            ->select('*')
            ->from('{{%dealers_profiles}}')
            ->each();
        foreach ($profiles as $profile) {
            $this->update('{{%reports}}', [
                'dealer_id' => $profile['dealer_id'],
            ], [
                'profile_id' => $profile['profile_id'],
            ]);
        }
    }

    public function down()
    {
        $this->dropColumn('{{%reports}}', 'dealer_id');
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
