<?php

use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `damage_code`.
 */
class m191025_074150_create_damage_code_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "damage_code";
    }

    public function columns()
    {
        return [
            "code" => $this->string(),
            "name" => $this->string(),
        ];
    }
}
