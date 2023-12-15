<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `image`.
 */
class m191203_131444_create_image_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "image";
    }

    public function columns()
    {
        return [
            'gallery_id' => $this->foreignKey("gallery", "id"),
            "image" => $this->string()
        ];
    }
}
