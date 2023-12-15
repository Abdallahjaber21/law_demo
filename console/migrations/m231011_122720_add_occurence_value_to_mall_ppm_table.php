<?php

use common\models\MallPpmTasks;
use yii\db\Migration;

/**
 * Class m231011_122720_add_occurence_value_to_mall_ppm_table
 */
class m231011_122720_add_occurence_value_to_mall_ppm_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(MallPpmTasks::tableName(), 'occurence_value', $this->integer()->after('equipment_type_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(MallPpmTasks::tableName(), 'occurence_value');
    }
}
