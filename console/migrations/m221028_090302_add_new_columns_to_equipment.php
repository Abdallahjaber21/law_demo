<?php

use common\models\Equipment;
use yii\db\Migration;

/**
 * Class m221028_090302_add_new_columns_to_equipment
 */
class m221028_090302_add_new_columns_to_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Equipment::tableName(), "floor", $this->string());
        $this->addColumn(Equipment::tableName(), "zone", $this->string());
        $this->addColumn(Equipment::tableName(), "place", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Equipment::tableName(), "floor");
        $this->dropColumn(Equipment::tableName(), "zone");
        $this->dropColumn(Equipment::tableName(), "place");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221028_090302_add_new_columns_to_equipment cannot be reverted.\n";

        return false;
    }
    */
}
