<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `{{%media}}`.
 */
class m230802_191308_create_media_table extends CreateTableMigration
{
    public function getTableName()
    {
        return "media";
    }
    public function columns()
    {
        return [
            'gallery_id' => $this->foreignKey("gallery", "id"),
            'type' => $this->string(),
            'image' => $this->string(),
            'voice' => $this->string(),
            'description' => $this->text(),


        ];
    }
}
