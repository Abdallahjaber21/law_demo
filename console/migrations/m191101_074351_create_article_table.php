<?php

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `article`.
 */
class m191101_074351_create_article_table extends CreateTableMigration
{

    public function getTableName()
    {
        return "article";
    }

    public function columns()
    {
        return [
            "title" => $this->string(),
            "subtitle" => $this->string(),
            "content" => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),
            "image" => $this->string(),

        ];
    }
}
