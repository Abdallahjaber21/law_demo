<?php

/**
 * Handles the creation of table `{{%admin_sector}}`.
 */
class m210916_082724_create_admin_sectors_table extends \console\models\CreateTableMigration
{

    public function getTableName()
    {
        return "admin_sector";
    }

    public function columns()
    {
        return [
            "admin_id"  => $this->foreignKey("admin", "id", $this->integer()),
            "sector_id" => $this->foreignKey("sector", "id", $this->integer()),
        ];
    }

    public function safeUp()
    {
        parent::safeUp();
        $this->createIndex("idx_admin_id_sector_id_unique", "admin_sector",
            ["admin_id", "sector_id"], true);
    }
}
