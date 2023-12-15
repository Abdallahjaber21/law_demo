<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%location}}`.
 */
class m230919_124122_drop_columns_from_location_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Customer ID
        $this->dropForeignKey("fk_location_customer_id__customer_id", "location");
        $this->dropIndex("fk_location_customer_id__customer_id_idx", "location");
        $this->dropColumn("location", "customer_id");

        // Country ID
        $this->dropForeignKey("fk_location_country_id", "location");
        $this->dropIndex("fk_location_country_id_idx", "location");
        $this->dropColumn("location", "country_id");

        // State ID
        $this->dropForeignKey("fk_location_state_id", "location");
        $this->dropIndex("fk_location_state_id_idx", "location");
        $this->dropColumn("location", "state_id");

        // City ID
        $this->dropForeignKey("fk_location_city_id", "location");
        $this->dropIndex("fk_location_city_id_idx", "location");
        $this->dropColumn("location", "city_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Customer ID
        $this->addColumn("location", "customer_id", $this->integer()->after('id'));
        $this->createIndex("fk_location_customer_id__customer_id_idx", "location", "customer_id");
        $this->addForeignKey(
            'fk_location_customer_id__customer_id',
            'location',
            'customer_id',
            'customer',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Country ID
        $this->addColumn("location", "country_id", $this->integer()->after('id'));
        $this->createIndex("fk_location_country_id_idx", "location", "country_id");
        $this->addForeignKey(
            'fk_location_country_id',
            'location',
            'country_id',
            'country',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // State ID
        $this->addColumn("location", "state_id", $this->integer()->after('id'));
        $this->createIndex("fk_location_state_id_idx", "location", "state_id");
        $this->addForeignKey(
            'fk_location_state_id',
            'location',
            'state_id',
            'state',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // City ID
        $this->addColumn("location", "city_id", $this->integer()->after('id'));
        $this->createIndex("fk_location_city_id_idx", "location", "city_id");
        $this->addForeignKey(
            'fk_location_city_id',
            'location',
            'city_id',
            'city',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}
