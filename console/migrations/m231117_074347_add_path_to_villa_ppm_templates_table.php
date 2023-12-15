<?php

use common\models\VillaPpmTemplates;
use console\models\UpdateTableMigration;
use yii\db\Migration;

/**
 * Class m231117_074347_add_path_to_villa_ppm_templates_table
 */
class m231117_074347_add_path_to_villa_ppm_templates_table extends UpdateTableMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(VillaPpmTemplates::tableName(), 'path', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(VillaPpmTemplates::tableName(), 'path');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231117_074347_add_path_to_villa_ppm_templates_table cannot be reverted.\n";

        return false;
    }
    */
}
