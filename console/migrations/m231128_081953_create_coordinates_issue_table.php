<?php

use common\models\Location;
use common\models\Technician;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%coordinates_issue}}`.
 */
class m231128_081953_create_coordinates_issue_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "coordinates_issue";
    }

    public function columns()
    {
        return [
            'location_id' => $this->foreignKey(Location::tableName(), 'id', $this->integer()),
            'reported_by' => $this->foreignKey(Technician::tableName(), 'id', $this->integer()),
            'old_latitude' => $this->string(),
            'old_longitude' => $this->string(),
            'new_latitude' => $this->string(),
            'new_longitude' => $this->string(),
        ];
    }
}
