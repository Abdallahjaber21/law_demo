<?php

use common\models\EquipmentMaintenanceBarcode;
use yii\db\Migration;

/**
 * Class m220318_134010_convert_code_in_barcode_to_integer
 */
class m220318_134010_convert_code_in_barcode_to_integer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(EquipmentMaintenanceBarcode::tableName(), 'code', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn(EquipmentMaintenanceBarcode::tableName(), 'code', $this->string());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_134010_convert_code_in_barcode_to_integer cannot be reverted.\n";

        return false;
    }
    */
}
