<?php

use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `manufacturer`.
 */
class m191025_074222_create_manufacturer_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "manufacturer";
    }

    public function columns()
    {
        return [
            "code" => $this->string(),
            "name" => $this->string(),
        ];
    }
}
