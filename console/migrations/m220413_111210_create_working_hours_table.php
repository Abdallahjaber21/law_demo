<?php

/**
 * Handles the creation of table `{{%working_hours}}`.
 */
class m220413_111210_create_working_hours_table extends \console\models\CreateTableMigration
{
    public function getTableName()
    {
        return "working_hours";
    }

    public function columns()
    {
        return [
            'year_month'  => $this->string()->unique()->notNull(),
            'daily_hours' => $this->text(),
            'holidays'    => $this->text(),
            'total_hours' => $this->double()
        ];
    }

}
