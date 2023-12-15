<?php

use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%account_type}}`.
 */
class m230913_113926_create_account_type_table extends CreateTableMigration
{
    // public $skipTimeStamps = true;
    // public $skipBleameables = true;
    // public $skipStatus = true;

    public function getTableName()
    {
        return "account_type";
    }

    public function columns()
    {
        return [
            "name" => $this->string(255),
            "role_id" => $this->foreignKey('auth_item', 'name', $this->string()),
            "parent_id" => $this->foreignKey('account_type', 'id', $this->integer()),
        ];
    }
}
