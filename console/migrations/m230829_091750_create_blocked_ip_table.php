<?php

use console\models\CreateTableMigration;

class m230829_091750_create_blocked_ip_table extends CreateTableMigration
{

    public $skipTimeStamps = true;
    public $skipBleameables = true;
    public $skipStatus = true;

    public function getTableName()
    {
        return "blocked_ip";
    }

    public function columns()
    {
        return [
            "ip_address" => $this->string(),
        ];
    }
}
