<?php

namespace common\models\users\forms;

use common\models\users\AbstractAccount;
use common\models\users\AbstractUser;
use Yii;
use yii\base\Model;

/**
 * Description of AbstractPasswordCodeForm
 *
 * @author Tarek K. Ajaj
 * May 2, 2017 10:12:18 AM
 * 
 * AbstractPasswordCodeForm.php
 * UTF-8
 * 
 */
class AbstractPasswordCodeForm extends Model
{

  public $UserClass;
  public $token;
  public $email;
  public $UserClassFilter;

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
      [['token', 'email'], 'required'], //is required on create only
      ['token', 'validateCode'],
    ];
  }

  /**
   * Validate if password reset token is valid
   * 
   * @param string $attribute
   * @param array $params
   * @param type $validator
   */
  public function validateCode($attribute, $params, $validator)
  {
    $user = $this->getUser();
    if (empty($user)) {
      $this->addError($attribute, \Yii::t("app", 'Reset code is invalid.'));
    }
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return [
      'email' => Yii::t("app", 'Email'),
      'token' => Yii::t("app", 'Reset Code'),
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
          'and',
          ['status' => AbstractAccount::STATUS_ENABLED],
          [
            'OR',
            ['RIGHT(phone_number , ' . $length . ')' => $this->email],
            ['email' => $this->email],
          ],                       ['password_reset_token' => $this->token],
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
}
