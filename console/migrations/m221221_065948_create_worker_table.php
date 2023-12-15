<?php

/**
 * Handles the creation of table `{{%worker}}`.
 */
class m221221_065948_create_worker_table extends \console\models\CreateTableMigration
{
    public function getTableName()
    {
        return "worker";
    }

    public function columns()
    {
        return [
            'name'         => $this->string()->notNull(),
            'title'        => $this->string(),
            'phone_number' => $this->string(),
            'image'        => $this->string(),
        ];
    }
}
