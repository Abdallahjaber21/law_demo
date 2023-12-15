<?php

use yii\db\Migration;

/**
 * Class m220325_075552_add_removed_units_from_location
 */
class m220325_075552_add_removed_units_from_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("user_location", "removed_units", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("user_location", "removed_units");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220325_075552_add_removed_units_from_location cannot be reverted.\n";

        return false;
    }
    */
}
