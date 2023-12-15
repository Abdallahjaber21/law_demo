<?php
namespace console\migrations\namespaced;

use console\models\AccountMigrationHelper;
use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m180104_124340_create_admin_table extends Migration {

    /**
     * @inheritdoc
     */
    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('admin', (new AccountMigrationHelper())->getColumns(), $tableOptions);
        $this->createIndex("fk_admin_account_idx", "admin", "account_id");
        $this->addForeignKey("fk_admin_account", "admin", "account_id", "account", "id", "CASCADE", "CASCADE");
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropForeignKey("fk_admin_account", "admin");
        $this->dropIndex("fk_admin_account_idx", "admin");
        $this->dropTable('admin');
    }

}
