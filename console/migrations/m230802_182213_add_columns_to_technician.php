<?php

use yii\db\Migration;

/**
 * Class m230802_182213_add_columns_to_technician
 */
class m230802_182213_add_columns_to_technician extends Migration
{
    public function safeUp()
    {
        $this->addColumn("technician", "profession_id", $this->integer());
        $this->createIndex("fk_technician_profession_id_idx", "technician", "profession_id");
        $this->addForeignKey(
            'fk_technician_profession',
            'technician',
            'profession_id',
            'profession',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("technician", "division_id", $this->integer());
        $this->createIndex("fk_technician_division_id_idx", "technician", "division_id");
        $this->addForeignKey(
            'fk_technician_division',
            'technician',
            'division_id',
            'division',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("technician", "badge_number", $this->string(50)->null());
        $this->addColumn("technician", "description", $this->text()->null());
        $this->addColumn("technician", 'longitude', $this->string());
        $this->addColumn("technician", 'latitude', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_technician_division_id", "technician");
        $this->dropIndex("fk_technician_division_id_idx", "technician");
        $this->dropColumn("technician", "division_id");
        $this->dropForeignKey("fk_technician_profession_id", "technician");
        $this->dropIndex("fk_technician_profession_id_idx", "technician");
        $this->dropColumn("technician", "profession_id");
        $this->dropColumn("technician", "badge_number");
        $this->dropColumn("technician", "description");
        $this->dropColumn("technician", "longitude");
        $this->dropColumn("technician", "latitude");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230802_182213_add_columns_to_technician cannot be reverted.\n";

        return false;
    }
    */
}
