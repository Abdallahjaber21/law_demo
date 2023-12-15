<?php

use yii\db\Migration;

/**
 * Class m230830_073159_drop_column_location_from_equipment
 */
class m230830_073159_drop_column_location_from_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey("fk_equipment_location_id__location_id", "equipment");
        $this->dropIndex("fk_equipment_location_id__location_id_idx", "equipment");
        $this->dropColumn("equipment", "location_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn("equipment", "location_id", $this->integer()->after('id'));
        $this->createIndex("fk_equipment_path_location_id_idx", "equipment", "location_id");
        $this->addForeignKey(
            'fk_equipment_path_location_id',
            'equipment',
            'location_id',
            'location',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230830_073159_drop_column_location_from_equipment cannot be reverted.\n";

        return false;
    }
    */
}
