<?php

namespace common\models\users\forms;

use common\components\notification\Notification;
use common\components\notification\NotificationMessages;
use common\components\settings\Setting;
use common\models\users\AbstractAccount;
use common\models\users\AbstractUser;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class AbstractLoginForm extends Model
{

    public $UserClass;
    public $UserClassFilter;
    public $email;
    public $password;
    public $rememberMe = true;
    public $code;


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
            // email and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // Validate login attempts
            ['email', 'validateAttempts'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['code', 'string', 'max' => 4, 'min' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t("app", "Phone Number"),
            'password' => Yii::t("app", "Password"),
            'code' => Yii::t("app", "Verification Code"),
            'rememberMe' => Yii::t("app", "Remember Me"),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (empty($user)) {
                $this->addError('password', \Yii::t("app", 'Incorrect Email, Phone or Password.'));
                // $this->addError($attribute, \Yii::t("app", 'Account will be locked after 4 failed attempts.'));
            } else if (!$user->validatePassword($this->password)) {
                // $lockAttempts = Yii::$app->settings->getValue('max_login_attempts') - $user->login_attempts;
                // $this->addError('email', \Yii::t("app", "Account will be locked after {attempts} failed attempts.", [
                //     'attempts' => $lockAttempts
                // ]));
                $this->addError('password', \Yii::t("app", 'Incorrect Email, Phone or Password.'));
            }
        }
    }

    /**
     * Finds user by [[email]]
     *
     * @return AbstractAccount|null
     */
    public function getUser()
    {
        if (!empty($this->UserClass)) {
            if ($this->_user === null) {
                $userClass = $this->UserClass;

                $length = strlen($this->email);

                // Validate Phone Number
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
                        ['email' => $this->email],
                        ['RIGHT(phone_number , ' . $length . ')' => $this->email],
                    ],
                    ['status' => AbstractAccount::STATUS_ENABLED]
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

    /**
     * Validate if user exceeded number of attempts (5 max)
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateAttempts($attribute, $params)
    {

        $user = $this->getUser();
        $lockActive = true;
        $lockAttempts = (int)Setting::getValue('max_login_attempts');
        $lockDuration = (int)Setting::getValue('user_lock_duration');
        if ($user && $lockActive) {
            $user->login_attempts = $user->login_attempts + 1;
            $diff = $lockDuration - ceil((time() - strtotime($user->last_login)) / 60.0);
            if ($diff <= 0) { // last login was more than $lockDuration minutes ago
                $user->login_attempts = 1; //reset login counter
            }
            if ($user->login_attempts > $lockAttempts) { // lock after $lockAttempts attempts
                $user->locked = true;
                $this->addError($attribute, \Yii::t("app", "Too many failed attempts, this account is locked.")
                    . " "
                    . \Yii::t("app", "Please wait {minutes} minutes before trying again", [
                        'minutes' => $diff
                    ]));
            } else {
                $user->last_login = date('Y-m-d H:i:s');
                $user->locked = false;
            }
            $user->save(false);
        }
    }

    /**
     * Logs in a user using the provided email and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if (!empty($user)) {
                $user->login_attempts = 0;
                $user->locked = false;
                if (!empty(Yii::$app->session->sessionTable)) {
                    $user->generateAuthKey();
                }
                $user->save(false);

                if (!empty(Yii::$app->session->sessionTable)) {
                    Yii::$app->db->createCommand()
                        ->delete(Yii::$app->session->sessionTable, ['user_id' => $user->id])
                        ->execute();
                }
                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            }
        }
        return false;
    }
}
