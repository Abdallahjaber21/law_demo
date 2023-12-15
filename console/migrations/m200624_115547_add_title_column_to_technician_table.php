<?php

use yii\db\Migration;

/**
 * Handles adding title to table `technician`.
 */
class m200624_115547_add_title_column_to_technician_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('technician', 'title', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('technician', 'title');
    }
}
