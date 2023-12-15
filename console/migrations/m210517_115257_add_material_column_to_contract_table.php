<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%contract}}`.
 */
class m210517_115257_add_material_column_to_contract_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("contract", "material", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("contract", "material");
    }
}
