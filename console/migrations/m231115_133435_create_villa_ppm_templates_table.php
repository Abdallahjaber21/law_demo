<?php

use common\models\Category;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\Project;
use common\models\Sector;
use console\models\CreateTableMigration;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%villa_ppm_templates}}`.
 */
class m231115_133435_create_villa_ppm_templates_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "villa_ppm_templates";
    }

    public function columns()
    {
        return [
            'name' => $this->string(),
            'sector_id' => $this->foreignKey(Sector::tableName(), 'id', $this->integer()),
            'location_id' => $this->foreignKey(Location::tableName(), 'id', $this->integer()),
            'category_id' => $this->foreignKey(Category::tableName(), 'id', $this->integer()),
            'asset_id' => $this->foreignKey(LocationEquipments::tableName(), 'id', $this->integer()),
            'project_id' => $this->foreignKey(Project::tableName(), 'id', $this->integer()),
            'frequency' => $this->integer(),
            'repeating_condition' => $this->integer(),
            'note' => $this->string(),
            'team_members' => $this->string(),
            'tasks' => $this->string(),
            'next_scheduled_date' => $this->dateTime(),
            'starting_date_time' => $this->dateTime(),
        ];
    }
}
