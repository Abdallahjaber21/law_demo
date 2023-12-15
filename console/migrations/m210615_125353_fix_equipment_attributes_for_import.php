<?php

use yii\db\Migration;

/**
 * Class m210615_125353_fix_equipment_attributes_for_import
 */
class m210615_125353_fix_equipment_attributes_for_import extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment", "unit_type", $this->string());
        $this->alterColumn("equipment", "manufacturer", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("equipment", "unit_type");
        $this->alterColumn("equipment", "manufacturer", $this->integer());
    }
}
