<?php

namespace console\models;

use yii\db\Migration;

abstract class CreateTableMigration extends Migration
{

    const FK = "FK";
    const FK_TABLE = "FK_TABLE";
    const FK_COLUMN = "FK_COLUMN";
    const FK_UPDATE = "FK_UPDATE";
    const FK_DELETE = "FK_DELETE";
    const TR = "TR";

    public $tableName;
    private $relations = [];
    private $translates = [];

    public $skipStatus = false;
    public $skipTimeStamps = false;
    public $skipBleameables = false;

    public abstract function getTableName();

    public abstract function columns();

    private function processColumns($columns)
    {
        $processed = [];
        foreach ($columns as $column => $type) {
            if (is_array($type)) {
                if (key_exists(self::FK, $type)) {
                    $this->relations[$column] = $type[self::FK];
                    $processed[$column] = $type[0];
                }
                if (key_exists(self::TR, $type) || in_array(self::TR, $type)) {
                    $this->translates[$column] = $type[0];
                }
            } else {
                $processed[$column] = $type;
            }
        }
        return $processed;
    }

    public function addRelations()
    {
        if (!empty($this->relations)) {
            foreach ($this->relations as $column => $relation) {
                $this->createIndex(
                    "fk_"
                        . substr($this->tableName . '_'
                            . $column . '__'
                            . $relation[self::FK_TABLE] . '_'
                            . $relation[self::FK_COLUMN], 0, 64 - 7)
                        . "_idx",
                    $this->tableName,
                    $column
                );
                $this->addForeignKey(
                    "fk_"
                        . substr($this->tableName . '_'
                            . $column . '__'
                            . $relation[self::FK_TABLE] . '_'
                            . $relation[self::FK_COLUMN], 0, 64 - 7),
                    $this->tableName,
                    $column,
                    $relation[self::FK_TABLE],
                    $relation[self::FK_COLUMN],
                    !empty($relation[self::FK_DELETE]) ? $relation[self::FK_DELETE] : "CASCADE",
                    !empty($relation[self::FK_UPDATE]) ? $relation[self::FK_UPDATE] : "CASCADE"
                );
            }
        }
    }

    public function deleteRelations()
    {
        if (!empty($this->relations)) {
            foreach ($this->relations as $column => $relation) {
                $this->dropForeignKey(
                    "fk_"
                        . substr($this->tableName . '_'
                            . $column . '__'
                            . $relation[self::FK_TABLE] . '_'
                            . $relation[self::FK_COLUMN], 0, 64 - 7),
                    $this->tableName
                );
                $this->dropIndex(
                    "fk_"
                        . substr($this->tableName . '_'
                            . $column . '__'
                            . $relation[self::FK_TABLE] . '_'
                            . $relation[self::FK_COLUMN], 0, 64 - 7)
                        . "_idx",
                    $this->tableName
                );
            }
        }
    }

    public function addTranslation()
    {
        if (!empty($this->translates)) {
            $columns['id'] = $this->primaryKey();
            $columns["owner_id"] = $this->integer();
            $columns['language'] = $this->string();
            $columns = array_merge($columns, $this->translates);

            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            }
            $this->createTable("{$this->tableName}_lang", $columns, $tableOptions);

            $this->createIndex(
                "fk_" . substr("{$this->tableName}_lang_{$this->tableName}", 0, 64 - 7) . "_idx",
                "{$this->tableName}_lang",
                "owner_id"
            );
            $this->addForeignKey(
                "fk_" . substr("{$this->tableName}_lang_{$this->tableName}", 0, 64 - 7),
                "{$this->tableName}_lang",
                "owner_id",
                $this->tableName,
                'id',
                'CASCADE',
                'CASCADE'
            );
        }
    }

    public function deleteTranslation()
    {
        if (!empty($this->translates)) {
            $this->dropForeignKey(
                "fk_" . substr("{$this->tableName}_lang_{$this->tableName}", 0, 64 - 7),
                "{$this->tableName}_lang"
            );
            $this->dropIndex(
                "fk_" . substr("{$this->tableName}_lang_{$this->tableName}", 0, 64 - 7) . "_idx",
                "{$this->tableName}_lang"
            );

            $this->dropTable("{$this->tableName}_lang");
        }
    }

    public function safeUp()
    {
        $this->tableName = $this->getTableName();
        $columns['id'] = $this->primaryKey();
        $columns = array_merge($columns, $this->processColumns($this->columns()));
        if (!$this->skipStatus) {
            $columns['status'] = $this->integer()->defaultValue(10);
        }
        if (!$this->skipTimeStamps) {
            $columns['created_at'] = $this->dateTime();
            $columns['updated_at'] = $this->dateTime();
        }
        if (!$this->skipBleameables) {
            $columns['created_by'] = $this->integer();
            $columns['updated_by'] = $this->integer();
        }
        if (key_exists("image", $columns)) {
            $columns['random_token'] = $this->string();
        }


        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, $columns, $tableOptions);

        $this->addRelations();

        $this->addTranslation();
    }

    public function safeDown()
    {
        $this->tableName = $this->getTableName();
        $this->processColumns($this->columns());
        $this->deleteTranslation();
        $this->deleteRelations();
        $this->dropTable($this->tableName);
    }

    public function translatable($type)
    {
        return [$type, self::TR];
    }

    public function foreignKey($table, $column, $type = null)
    {
        return [
            empty($type) ? $this->integer() : $type, self::FK => [
                self::FK_TABLE  => $table,
                self::FK_COLUMN => $column,
            ]
        ];
    }
}
