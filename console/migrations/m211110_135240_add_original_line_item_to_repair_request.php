<?php

use common\models\LineItem;
use common\models\RepairRequest;
use yii\db\Migration;

/**
 * Class m211110_135240_add_original_line_item_to_repair_request
 */
class m211110_135240_add_original_line_item_to_repair_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(LineItem::tableName(), 'type', $this->integer()->defaultValue(LineItem::TYPE_TECHNICIAN));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(LineItem::tableName(), 'type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211110_135240_add_original_line_item_to_repair_request cannot be reverted.\n";

        return false;
    }
    */
}
