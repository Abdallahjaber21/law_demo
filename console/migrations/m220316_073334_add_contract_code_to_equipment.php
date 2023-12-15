<?php

use yii\db\Migration;

/**
 * Class m220316_073334_add_contract_code_to_equipment
 */
class m220316_073334_add_contract_code_to_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("equipment", "contract_code", $this->string());
        $this->migrateContractCodes();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("equipment", "contract_code");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220316_073334_add_contract_code_to_equipment cannot be reverted.\n";

        return false;
    }
    */

    public function migrateContractCodes()
    {
        $command = $this->db->createCommand("
        UPDATE `equipment` e
            INNER JOIN `contract` c
                on e.contract_id = c.id
        set e.contract_code = c.code
            where e.contract_id IS NOT null
        ");
        $command->execute();
    }
}
