<?php

/**
 * Class m210531_075005_add_r_code_and_d_code_to_location
 */
class m210531_075005_add_r_code_and_d_code_to_location extends \console\models\CreateTableMigration
{

    public function getTableName()
    {
        return "location_code";
    }

    public function columns()
    {
        return [
            'location_id'  => $this->foreignKey("location", "id", $this->integer()),
            'code'         => $this->string(),
            'usages_limit' => $this->integer()->defaultValue(100),
            'usages_count' => $this->integer()->defaultValue(0),
            'type'         => $this->integer()
        ];
    }
}
