<?php

namespace console\models;

use yii\db\Migration;

/**
 * Description of AccountMigrationHelper
 *
 * @author Tarek K. Ajaj
 */
class AccountMigrationHelper extends Migration {

    public function getColumns() {
        return [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'name' => $this->string(),
            'email' => $this->string(),
            'password' => $this->string(),
            'status' => $this->integer(),
            'phone_number' => $this->string(),
            'address' => $this->string(),
            'image' => $this->string(),
            'auth_key' => $this->string(),
            'access_token' => $this->string(),
            'random_token' => $this->string(),
            'password_reset_token' => $this->string(),
            'mobile_registration_id' => $this->text(),
            'web_registration_id' => $this->text(),
            'enable_notification' => $this->boolean(),
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
