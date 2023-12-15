<?php

use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%mall_ppm_frequency}}`.
 */
class m230926_095732_create_mall_ppm_frequency_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "mall_ppm_frequency";
    }

    public function columns()
    {
        return [
            'name' => $this->string(),
            'parent_frequency' => $this->foreignKey('mall_ppm_frequency', 'id', $this->integer()),
        ];
    }
}
