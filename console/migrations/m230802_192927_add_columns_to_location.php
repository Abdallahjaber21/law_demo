<?php

use yii\db\Migration;

/**
 * Class m230802_192927_add_columns_to_location
 */
class m230802_192927_add_columns_to_location extends Migration
{
    public function safeUp()
    {
        $this->addColumn("location", "division_id", $this->integer());
        $this->createIndex("fk_location_division_id_idx", "location", "division_id");
        $this->addForeignKey(
            'fk_location_division_id',
            'location',
            'division_id',
            'division',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn("location", "country_id", $this->integer());
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
        $this->addColumn("location", "state_id", $this->integer());
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
        $this->addColumn("location", "city_id", $this->integer());
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
        $this->addColumn("location", "owner", $this->string(100));
        $this->addColumn("location", "owner_phone", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_location_division_id", "location");
        $this->dropIndex("fk_location_division_id_idx", "location");
        $this->dropColumn("location", "division_id");
        $this->dropForeignKey("fk_location_country_id", "location");
        $this->dropIndex("fk_location_country_id_idx", "location");
        $this->dropColumn("location", "country_id");
        $this->dropForeignKey("fk_location_state_id", "location");
        $this->dropIndex("fk_location_state_id_idx", "location");
        $this->dropColumn("location", "state_id");
        $this->dropForeignKey("fk_location_city_id", "location");
        $this->dropIndex("fk_location_city_id_idx", "location");
        $this->dropColumn("location", "city_id");
        $this->dropColumn("location", "owner");
        $this->dropColumn("location", "owner_phone");
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230802_192927_add_columns_to_location cannot be reverted.\n";

        return false;
    }
    */
}
