<?php

use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%location_equipments}}`.
 */
class m230925_060701_add_columns_to_location_equipments_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumnWithForeignKey('location_equipments', 'division_id', $this->integer()->after('id'), 'division', 'id');
        $this->addColumnWithForeignKey('location_equipments', 'driver_id', $this->integer()->after('equipment_id'), 'technician', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumnWithForeignKey('location_equipments', 'division_id', 'division', 'id');
        $this->dropColumnWithForeignKey('location_equipments', 'driver_id', 'technician', 'id');
    }
}
