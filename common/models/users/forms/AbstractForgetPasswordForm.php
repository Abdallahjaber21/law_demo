<?php

namespace common\models\users\forms;

use common\models\users\AbstractAccount;
use Yii;
use yii\base\Model;

/**
 * Description of AbstractForgetPasswordForm
 *
 * @author Tarek K. Ajaj
 */
class AbstractForgetPasswordForm extends Model
{

  public $UserClass;
  public $UserClassFilter;
  public $email;
  public $useCode = false;

  /**
   *
   * @var AbstractAccount
   */
  protected $_user;

  public function rules()
  {
    return [
      [['email'], 'required'],
      [['email'], 'string', 'max' => 255],
      // [['email'], 'email'],
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return [
      'email' => Yii::t("app", "Email"),
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
        $length = strlen($this->email);
        if (is_numeric($this->email)) {
          if ($length <= 7) {
            $this->addError('email', \Yii::t("app", 'Invalid Phone Number Length'));
            return;
          }
          $numberAsString = (string)$this->email;

          if ($numberAsString[0] === '0') {
            $numberAsString = substr($numberAsString, 1);
          }

          $this->email = $numberAsString;
          $length = strlen($this->email);
        }
        $where = [
          'AND',
          [
            'OR',
            ['RIGHT(phone_number , ' . $length . ')' => $this->email],
            ['email' => $this->email],
          ],            ['status' => AbstractAccount::STATUS_ENABLED]
        ];
        if (!empty($this->UserClassFilter)) {
          $where = array_merge($where, $this->UserClassFilter);
        }
        $this->_user = $userClass::find()
          ->where($where)
          ->one();
      }
    }
    return $this->_user;
  }

  public function forgetPassword()
  {
    if ($this->validate()) {
      $user = $this->getUser();
      if (!empty($user)) {
        $user->generatePasswordResetToken($this->useCode);
        if ($user->save()) {
          $this->sendPasswordResetCode();
          return true;
        }
      } else {
        $this->addError('email', \Yii::t("app", "Account not found"));
      }
    }
    return false;
  }

  private function sendPasswordResetCode()
  {
    Yii::$app->mailer->compose($this->useCode ? 'password-reset-code' : 'password-reset-link', [
      'link' => $this->useCode ? $this->_user->password_reset_token : $this->generateResetLink()
    ])
      ->setFrom(\Yii::$app->params['passwordResetEmail'])
      ->setTo($this->getUser()->email)
      ->setSubject(\Yii::$app->params['project-name'] . ' - ' . \Yii::t("app", 'Password Reset'))
      ->send();
  }

  public function generateResetLink()
  {
    return Yii::$app->urlManager->createAbsoluteUrl(['/site/reset-password', 't' => $this->_user->password_reset_token]);
  }
}
