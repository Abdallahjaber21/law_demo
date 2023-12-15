<?php

use yii\db\Migration;

/**
 * Class m230824_055721_add_category_to_equipmenttype_table
 */
class m230824_055721_add_category_to_equipmenttype_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn("equipment_type", "category_id", $this->integer()->after('id'));
        $this->createIndex("fk_equipment_type_category_id_idx", "equipment_type", "category_id");
        $this->addForeignKey(
            'fk_equipment_type_category_id',
            'equipment_type',
            'category_id',
            'category',
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
        $this->dropForeignKey("fk_equipment_type_category_id", "equipment_type");
        $this->dropIndex("fk_equipment_type_category_id_idx", "equipment_type");
        $this->dropColumn("equipment_type", "category_id");
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230824_055721_add_category_to_equipmenttype_table cannot be reverted.\n";

        return false;
    }
    */
}
