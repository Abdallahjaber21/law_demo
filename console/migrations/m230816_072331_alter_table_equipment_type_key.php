<?php

use yii\db\Migration;

/**
 * Class m230816_072331_alter_table_equipment_type_key
 */
class m230816_072331_alter_table_equipment_type_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('equipment_type', 'key', 'code');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('equipment_type', 'code', 'key');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230816_072331_alter_table_equipment_type_key cannot be reverted.\n";

        return false;
    }
    */
}
