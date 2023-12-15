<?php

use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Class m231106_080238_create_table_engine_oil_types
 */
class m231106_080238_create_table_engine_oil_types extends CreateTableMigration
{
    public function getTableName()
    {
        return "engine_oil_types";
    }

    public function columns()
    {
        return [
            "oil_viscosity" => $this->string(),
            "motor_fuel_type_id" => $this->integer(),
            "can_weight" => $this->float(),
            "oil_durability" => $this->float(),
        ];
    }
}
