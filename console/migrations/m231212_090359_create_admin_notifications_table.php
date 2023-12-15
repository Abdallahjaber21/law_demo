<?php

use common\models\RepairRequest;
use common\models\Technician;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%admin_notifications}}`.
 */
class m231212_090359_create_admin_notifications_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "admin_notifications";
    }

    public function columns()
    {
        return [
            'request_id' => $this->foreignKey(RepairRequest::tableName(), 'id', $this->integer()),
            'technician_id' => $this->foreignKey(Technician::tableName(), 'id', $this->integer()),
            'seen' => $this->boolean(),
            'type' => $this->integer(),
        ];
    }
}
