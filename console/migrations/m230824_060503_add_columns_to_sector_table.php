<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%sector}}`.
 */
class m230824_060503_add_columns_to_sector_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("sector", "country_id", $this->integer()->after('id'));
        $this->createIndex("fk_sector_country_id_idx", "sector", "country_id");
        $this->addForeignKey(
            'fk_sector_country_id',
            'sector',
            'country_id',
            'country',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("sector", "state_id", $this->integer()->after('country_id'));
        $this->createIndex("fk_sector_state_id_idx", "sector", "state_id");
        $this->addForeignKey(
            'fk_sector_state_id',
            'sector',
            'state_id',
            'state',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("sector", "city_id", $this->integer()->after('state_id'));
        $this->createIndex("fk_sector_city_id_idx", "sector", "city_id");
        $this->addForeignKey(
            'fk_sector_city_id',
            'sector',
            'city_id',
            'city',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_sector_country_id", "sector");
        $this->dropIndex("fk_sector_country_id_idx", "sector");
        $this->dropColumn("sector", "country_id");

        $this->dropForeignKey("fk_sector_state_id", "sector");
        $this->dropIndex("fk_sector_state_id_idx", "sector");
        $this->dropColumn("sector", "state_id");

        $this->dropForeignKey("fk_sector_city_id", "sector");
        $this->dropIndex("fk_sector_city_id_idx", "sector");
        $this->dropColumn("sector", "city_id");
    }
}
