<?php

namespace console\models;

use yii\db\Migration;

/**
 * Description of AccountTableMigration
 *
 * @author Tarek K. Ajaj
 */
abstract class AccountTableMigration extends CreateTableMigration {

  public function columns() {
    return [
        'account_id' => $this->foreignKey("account", "id"),
        'name' => $this->string()->notNull(),
        'email' => $this->string()->notNull()->unique(),
        'password' => $this->string(),
        'phone_number' => $this->string()->notNull()->unique(),
        'address' => $this->string(),
        'image' => $this->string(),
        'auth_key' => $this->string(),
        'access_token' => $this->string(),
        'random_token' => $this->string(),
        'password_reset_token' => $this->string(),
        'mobile_registration_id' => $this->text(),
        'web_registration_id' => $this->text(),
        'enable_notification' => $this->boolean()->defaultValue(true),
        'locked' => $this->boolean()->defaultValue(0),
        'login_attempts' => $this->integer()->defaultValue(0),
        'last_login' => $this->dateTime(),
        'timezone' => $this->string()->defaultValue("UTC"),
        "language" => $this->string()->defaultValue("en-US"),
        'created_at' => $this->dateTime(),
        'updated_at' => $this->dateTime(),
    ];
  }

}
