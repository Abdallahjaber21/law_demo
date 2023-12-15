<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%maintenance}}`.
 */
class m210705_110406_add_report_generated_column_to_maintenance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("maintenance", "report_generated", $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("maintenance", "report_generated");
    }
}
