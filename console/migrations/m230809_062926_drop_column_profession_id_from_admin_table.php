<?php

use yii\db\Migration;

class m230809_062926_drop_column_profession_id_from_admin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_admin_profession', 'admin');
        $this->dropIndex('fk_admin_profession_id_idx', 'admin');
        $this->dropColumn('admin', 'profession_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn("admin", "profession_id", $this->integer());
        $this->createIndex("fk_admin_profession_id_idx", "admin", "profession_id");
        $this->addForeignKey(
            'fk_admin_profession_id',
            'admin',
            'profession_id',
            'profession',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}
