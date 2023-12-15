<?php

namespace common\models\users\forms;

use common\models\users\AbstractAccount;
use common\models\users\AbstractUser;
use kartik\password\StrengthValidator;
use Yii;
use yii\base\Model;
use lavrentiev\widgets\toastr\Notification;

/**
 * Description of AbstractResetPasswordForm
 *
 * @author Tarek K. Ajaj
 * May 2, 2017 10:12:18 AM
 * 
 * AbstractResetPasswordForm.php
 * UTF-8
 * 
 */
class AbstractResetPasswordForm extends Model
{

  public $UserClass;
  public $UserClassFilter;
  public $password;
  public $password_repeat;
  public $token;
  public $email = null;

  /**
   *
   * @var AbstractUser
   */
  protected $_user;

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      //--- Password
      ['password', 'required'], //is required on create only
      ['password', StrengthValidator::className(), 'preset' => 'normal', 'usernameValue' => ""],
      //--- Password repeat
      ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => \Yii::t("app", 'Passwords does not match')],
      //--- Token
      ['email', 'string'],
      ['token', 'string'],
      ['token', 'validateToken'],
    ];
  }

  /**
   * Validate if password reset token is valid
   * 
   * @param string $attribute
   * @param array $params
   * @param type $validator
   */
  public function validateToken($attribute, $params, $validator)
  {
    $user = $this->getUser();
    if (empty($user)) {
      $this->addError($attribute, \Yii::t("app", 'Reset token is invalid.'));
    }
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return [
      'email' => Yii::t("app", 'Email'),
      'password' => Yii::t("app", 'New password'),
      'password_repeat' => Yii::t("app", 'Repeat new password'),
    ];
  }

  /**
   * Finds user by [[email]]
   *
   * @return AbstractUser|null
   */
  public function getUser()
  {
    if (!empty($this->UserClass)) {
      if ($this->_user === null) {
        $userClass = $this->UserClass;
        $where = [];
        if ($userClass::isPasswordResetTokenValid($this->token)) {
          if ($this->email == null) {
            //$this->_user = $userClass::findByPasswordResetToken($this->token);
            $where = [
              'and',
              ['status' => AbstractAccount::STATUS_ENABLED],
              ['password_reset_token' => $this->token],
            ];
          } else {
            $where = [
              'and',
              ['status' => AbstractAccount::STATUS_ENABLED],
              ['email' => $this->email],
              ['password_reset_token' => $this->token],
            ];
          }
          if (!empty($this->UserClassFilter)) {
            $where = array_merge($where, $this->UserClassFilter);
          }
          $this->_user = $userClass::find()
            ->where($where)
            ->one();
        }
      }
    }
    return $this->_user;
  }

  /**
   * change users password to new one
   * 
   * @return boolean
   */
  public function resetPassword()
  {
    if ($this->validate()) {
      $user = $this->getUser();
      if (!empty($user)) {
        $user->setPassword($this->password);
        $user->password_reset_token = null;
        $user->save(false);

        return true;
      }
      $this->addError('email', \Yii::t("app", "Invalid reset password link."));
    } else {
      $errors = $this->getErrors();
      foreach ($errors as $attr) {
        foreach ($attr as $error) {
          Yii::$app->getSession()->addFlash("error", $error);
        }
      }
      return false;
    }
  }
}
