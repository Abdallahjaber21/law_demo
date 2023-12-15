<?php
namespace console\migrations\namespaced;

use console\models\CreateTableMigration;

/**
 * Handles the creation of table `notification`.
 */
class m190507_151300_create_notification_table extends CreateTableMigration {

  public function columns() {
    return [
        "account_id" => $this->foreignKey("account", "id", $this->integer()->notNull()),
        "type"=> $this->integer(),
        "message"=> $this->string()->notNull(),
        "params"=> $this->text(),
        "url"=> $this->string(),
        "data"=> $this->text(),
        "seen" => $this->boolean(),
        "mobile_action" =>  $this->text(),
    ];
  }

  public function getTableName() {
    return "notification";
  }

}
