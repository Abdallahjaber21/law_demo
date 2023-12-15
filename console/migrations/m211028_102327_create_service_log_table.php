<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_log}}`.
 */
class m211028_102327_create_service_log_table extends \console\models\CreateTableMigration
{
    public function getTableName()
    {
        return 'service_log';
    }

    public function columns()
    {
        return [
            'service_id'  => $this->foreignKey("repair_request", "id", $this->integer()),
            'user_name'   => $this->string(),
            'log_message' => $this->string(),
        ];
    }
}
