<?php

use console\models\CreateTableMigration;

class m230829_091602_create_login_audit_table extends CreateTableMigration
{

    public $skipTimeStamps = true;
    public $skipBleameables = true;
    public $skipStatus = true;

    public function getTableName()
    {
        return "login_audit";
    }

    public function columns()
    {
        return [
            "ip_address" => $this->string(),
            "login_credential" => $this->string(),
            "login_status" => $this->boolean(),
            "datetime" => $this->dateTime(),
            "logout" => $this->dateTime(),
            "user_id" => $this->foreignKey('admin', 'id'),
        ];
    }
}
