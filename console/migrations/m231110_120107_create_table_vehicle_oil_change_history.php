<?php

use common\models\EngineOilTypes;
use common\models\LocationEquipments;
use common\models\RepairRequest;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Class m231110_120107_create_table_vehicle_oil_change_history
 */
class m231110_120107_create_table_vehicle_oil_change_history extends CreateTableMigration
{
    public function getTableName()
    {
        return "vehicle_oil_change_history";
    }

    public function columns()
    {
        return [
            "repair_request_id" => $this->foreignKey(RepairRequest::tableName(), 'id', $this->integer()),
            "asset_id" => $this->foreignKey(LocationEquipments::tableName(), 'id', $this->integer()),
            "oil_id" => $this->foreignKey(EngineOilTypes::tableName(), 'id', $this->integer()),
            "meter_value" => $this->float(),
            "next_oil_change" => $this->float(),
            // meter_value + kam kilo bimachik l oil id 
            "datetime" => $this->dateTime()
        ];
    }
}
