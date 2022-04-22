<?php

use yii\db\Migration;

class m160304_083904_sales_change_tables_structure_and_relations extends Migration
{
    public function safeUp()
    {
        /**
         * Order of functions is important, because they change table structure
         */

        $this->createDealersPromotionsJunction();

        $this->createDealersProfilesJunction();

        $this->createPromotionsProfilesJunction();

        $this->createSalesProfilesJunction();
    }

    private function createDealersPromotionsJunction()
    {
        $this->createTable('{{%dealers_promotions}}', [
            'id' => $this->primaryKey(),
            'dealer_id' => $this->integer(),
            'promotion_id' => $this->integer(),
        ], $this->getTableOptions());

        $this->createIndex('dealer_id', '{{%dealers_promotions}}', 'dealer_id');
        $this->createIndex('promotion_id', '{{%dealers_promotions}}', 'promotion_id');

        $this->addForeignKey(
            '{{%fk-dealers_promotions-dealers}}',
            '{{%dealers_promotions}}', 'dealer_id',
            '{{%dealers}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-dealers_promotions-promotions}}',
            '{{%dealers_promotions}}', 'promotion_id',
            '{{%sales_promotions}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $dealersQuery = (new \yii\db\Query())
            ->select('*')
            ->from('{{%dealers}}')
            ->each();

        foreach ($dealersQuery as $dealer) {
            $this->insert('{{%dealers_promotions}}', [
                'dealer_id' => $dealer['id'],
                'promotion_id' => $dealer['promotion_id'],
            ]);
        }

        $this->dropForeignKey('{{%fk-dealers-promotions}}', '{{%dealers}}');
        $this->dropColumn('{{%dealers}}', 'promotion_id');
    }

    /**
     * @return null|string
     */
    private function getTableOptions()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
            return $tableOptions;
        }
        return $tableOptions;
    }

    private function createDealersProfilesJunction()
    {
        $this->createTable('{{%dealers_profiles}}', [
            'id' => $this->primaryKey(),
            'dealer_id' => $this->integer(),
            'profile_id' => $this->integer(),
        ], $this->getTableOptions());

        $this->createIndex('dealer_id', '{{%dealers_profiles}}', 'dealer_id');
        $this->createIndex('profile_id', '{{%dealers_profiles}}', 'profile_id');

        $this->addForeignKey(
            '{{%fk-dealers_profiles-dealers}}',
            '{{%dealers_profiles}}', 'dealer_id',
            '{{%dealers}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-dealers_profiles-promotions}}',
            '{{%dealers_profiles}}', 'profile_id',
            '{{%profiles}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $profilesQuery = (new \yii\db\Query())
            ->select('*')
            ->from('{{%profiles}}')
            ->each();

        foreach ($profilesQuery as $profile) {
            $this->insert('{{%dealers_profiles}}', [
                'dealer_id' => $profile['dealer_id'],
                'profile_id' => $profile['id'],
            ]);
        }
//
        $this->dropForeignKey('{{%fk-profiles-dealers}}', '{{%profiles}}');
        $this->dropColumn('{{%profiles}}', 'dealer_id');
    }

    private function createPromotionsProfilesJunction()
    {
        $this->addColumn('{{%profiles}}', 'sync_with_dealers_promotions', $this->boolean()->defaultValue(1));

        $this->createTable('{{%promotions_profiles}}', [
            'id' => $this->primaryKey(),
            'promotion_id' => $this->integer(),
            'profile_id' => $this->integer(),
        ], $this->getTableOptions());

        $this->createIndex('promotion_id', '{{%promotions_profiles}}', 'promotion_id');
        $this->createIndex('profile_id', '{{%promotions_profiles}}', 'profile_id');

        $this->addForeignKey(
            '{{%fk-promotions_profiles-dealers}}',
            '{{%promotions_profiles}}', 'promotion_id',
            '{{%sales_promotions}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-promotions_profiles-promotions}}',
            '{{%promotions_profiles}}', 'profile_id',
            '{{%profiles}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $profilePromotionsQuery = (new \yii\db\Query())
            ->select([
                'profile_id' => 'profile.id',
                'promotion_id' => 'dealerPromotion.promotion_id',
            ])
            ->from([
                'profile' => '{{%profiles}}',
                'dealerProfile' => '{{%dealers_profiles}}',
                'dealerPromotion' => '{{%dealers_promotions}}',
            ])
            ->where('profile.id = dealerProfile.profile_id and dealerPromotion.dealer_id = dealerProfile.dealer_id')
            ->each();

        foreach ($profilePromotionsQuery as $profilePromotion) {
            $this->insert('{{%promotions_profiles}}', [
                'profile_id' => $profilePromotion['profile_id'],
                'promotion_id' => $profilePromotion['promotion_id'],
            ]);
        }
    }

    private function createSalesProfilesJunction()
    {
        $this->createTable('{{%sales_profiles}}', [
            'id' => $this->primaryKey(),
            'sale_id' => $this->integer(),
            'profile_id' => $this->integer(),
        ], $this->getTableOptions());

        $this->createIndex('sale_id', '{{%sales_profiles}}', 'sale_id');
        $this->createIndex('profile_id', '{{%sales_profiles}}', 'profile_id');

        $this->addForeignKey(
            '{{%fk-sales_profiles-dealers}}',
            '{{%sales_profiles}}', 'sale_id',
            '{{%sales}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-sales_profiles-promotions}}',
            '{{%sales_profiles}}', 'profile_id',
            '{{%profiles}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $profilesSalesQuery = (new \yii\db\Query())
            ->select([
                'profile_id' => 'dealerProfile.profile_id',
                'sale_id' => 'sale.id',
            ])
            ->from([
                'sale' => '{{%sales}}',
                'dealerProfile' => '{{%dealers_profiles}}',
            ])
            ->where('sale.dealer_id = dealerProfile.dealer_id')
            ->each();

        foreach ($profilesSalesQuery as $profilesSales) {
            $this->insert('{{%sales_profiles}}', [
                'sale_id' => $profilesSales['sale_id'],
                'profile_id' => $profilesSales['profile_id'],
            ]);
        }
    }

    public function down()
    {
        echo "m160304_083904_sales_change_tables_structure_and_relations cannot be reverted.\n";

        return false;
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
