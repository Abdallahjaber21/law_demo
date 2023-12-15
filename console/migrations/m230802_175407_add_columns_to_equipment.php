<?php

use yii\db\Migration;

/**
 * Class m230802_175407_add_columns_to_equipment
 */
class m230802_175407_add_columns_to_equipment extends Migration
{
    public function safeUp()
    {
        $this->addColumn("equipment", "division_id", $this->integer());
        $this->createIndex("fk_equipment_division_id_idx", "equipment", "division_id");
        $this->addForeignKey(
            'fk_equipment_division_id',
            'equipment',
            'division_id',
            'division',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("equipment", "equipment_path_id", $this->integer());
        $this->createIndex("fk_equipment_equipment_path_id_idx", "equipment", "equipment_path_id");
        $this->addForeignKey(
            'fk_equipment_equipment_path_id',
            'equipment',
            'equipment_path_id',
            'equipment_path',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("equipment", "category_id", $this->integer());
        $this->createIndex("fk_equipment_category_id_idx", "equipment", "category_id");
        $this->addForeignKey(
            'fk_equipment_category',
            'equipment',
            'category_id',
            'category',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addColumn("equipment", "description", $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("fk_equipment_division_id", "equipment");
        $this->dropIndex("fk_equipment_division_id_idx", "equipment");
        $this->dropColumn("equipment", "division_id");
        $this->dropForeignKey("fk_equipment_category_id", "equipment");
        $this->dropIndex("fk_equipment_category_id_idx", "equipment");
        $this->dropColumn("equipment", "equipment_path_id");
        $this->dropForeignKey("fk_equipment_category_id", "equipment");
        $this->dropIndex("fk_equipment_category_id_idx", "equipment");
        $this->dropColumn("equipment", "category_id");
        $this->dropColumn("equipment", "description");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230802_175407_add_columns_to_equipment cannot be reverted.\n";

        return false;
    }
    */
}
