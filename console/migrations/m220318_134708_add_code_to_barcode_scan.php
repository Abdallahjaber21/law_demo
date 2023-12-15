<?php

use common\models\BarcodeScan;
use yii\db\Migration;

/**
 * Class m220318_134708_add_code_to_barcode_scan
 */
class m220318_134708_add_code_to_barcode_scan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(BarcodeScan::tableName(), "code", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(BarcodeScan::tableName(), "code");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_134708_add_code_to_barcode_scan cannot be reverted.\n";

        return false;
    }
    */
}
