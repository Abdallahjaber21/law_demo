<?php

use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `cause_code`.
 */
class m191025_074125_create_cause_code_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "cause_code";
    }

    public function columns()
    {
        return [
            "code" => $this->string(),
            "name" => $this->string(),
        ];
    }
}
