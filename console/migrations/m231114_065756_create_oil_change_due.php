<?php

use common\models\LocationEquipments;
use common\models\RepairRequest;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Class m231114_065756_create_oil_change_due
 */
class m231114_065756_create_oil_change_due extends CreateTableMigration
{
    public function getTableName()
    {
        return "oil_change_due";
    }

    public function columns()
    {
        return [
            "repair_request_id" => $this->foreignKey(RepairRequest::tableName(), 'id', $this->integer()),
            "asset_id" => $this->foreignKey(LocationEquipments::tableName(), 'id', $this->integer()),
            "next_oil_change" => $this->float(),
            "datetime" => $this->dateTime()
        ];
    }
}
