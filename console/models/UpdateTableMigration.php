<?php

namespace console\models;

use yii\db\Migration;

abstract class UpdateTableMigration extends Migration
{

    public function addColumnWithForeignKey($table, $column, $type, $refTable, $refColumn, $onDelete = "CASCADE", $onUpdate = "CASCADE")
    {
        $this->addColumn($table, $column, $type);
        $this->createIndex(
            "fk_"
                . substr($table . '_'
                    . $column . '__'
                    . $refTable . '_'
                    . $refColumn, 0, 64 - 7)
                . "_idx",
            $table,
            $column
        );
        $this->addForeignKey(
            "fk_"
                . substr($table . '_'
                    . $column . '__'
                    . $refTable . '_'
                    . $refColumn, 0, 64 - 7),
            $table,
            $column,
            $refTable,
            $refColumn,
            $onDelete,
            $onUpdate
        );
    }

    public function dropColumnWithForeignKey($table, $column, $refTable, $refColumn)
    {
        $this->dropForeignKey(
            "fk_"
                . substr($table . '_'
                    . $column . '__'
                    . $refTable . '_'
                    . $refColumn, 0, 64 - 7),
            $table
        );
        $this->dropIndex(
            "fk_"
                . substr($table . '_'
                    . $column . '__'
                    . $refTable . '_'
                    . $refColumn, 0, 64 - 7)
                . "_idx",
            $table
        );
        $this->dropColumn($table, $column);
    }
}
