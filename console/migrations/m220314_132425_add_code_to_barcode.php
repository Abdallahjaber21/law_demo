<?php

use common\models\EquipmentMaintenanceBarcode;
use yii\db\Migration;

/**
 * Class m220314_132425_add_code_to_barcode
 */
class m220314_132425_add_code_to_barcode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(EquipmentMaintenanceBarcode::tableName(), 'code', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(EquipmentMaintenanceBarcode::tableName(), 'code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220314_132425_add_code_to_barcode cannot be reverted.\n";

        return false;
    }
    */
}
