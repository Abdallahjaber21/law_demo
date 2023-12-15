<?php

use common\models\LocationEquipments;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m231213_135658_add_safety_status_to_location_equipments_table
 */
class m231213_135658_add_safety_status_to_location_equipments_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(LocationEquipments::tableName(), 'safety_status', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(LocationEquipments::tableName(), 'safety_status');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231213_135658_add_safety_status_to_location_equipments_table cannot be reverted.\n";

        return false;
    }
    */
}
