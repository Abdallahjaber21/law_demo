<?php

use yii\db\Migration;

/**
 * Class m230802_185157_add_columns_to_technician_location
 */
class m230802_185157_add_columns_to_technician_location extends Migration
{
    public function safeUp()
    {
        $this->addColumn("technician_location", "repair_request_id", $this->integer());
        $this->createIndex("fk_technician_location_repair_request_id_idx", "technician_location", "repair_request_id");
        $this->addForeignKey(
            'fk_technician_location_repair_request_id',
            'technician_location',
            'repair_request_id',
            'repair_request',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("technician_location", "date_time", $this->dateTime());
        $this->addColumn("technician_location", "description", $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_technician_location_repair_request_id", "technician_location");
        $this->dropIndex("fk_technician_location_repair_request_id_idx", "technician_location");
        $this->dropColumn("technician_location", "repair_request_id");
        $this->dropColumn("technician_location", "date_time");
        $this->dropColumn("technician_location", "description");
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230802_185157_add_columns_to_technician_location cannot be reverted.\n";

        return false;
    }
    */
}
