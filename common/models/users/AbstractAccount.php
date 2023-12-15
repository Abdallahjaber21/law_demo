<?php

namespace common\models\users;

use common\behaviors\ImageUploadBehavior;
use common\behaviors\OptionsBehavior;
use common\behaviors\RandomTokenBehavior;
use common\behaviors\UserAuditBehavior;
use common\models\Account;
use kartik\password\StrengthValidator;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Description of AbstractAccount
 *
 * @author Tarek K. Ajaj
 */

/**
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $type
 * @property string $name
 * @property string $email
 * @property string $password
 * @property integer $status
 * @property string $phone_number
 * @property string $address
 * @property string $image
 * @property string $auth_key
 * @property string $access_token
 * @property string $random_token
 * @property string $password_reset_token
 * @property string $mobile_registration_id
 * @property string $web_registration_id
 * @property integer $enable_notification
 * @property boolean $locked
 * @property integer $login_attempts
 * @property string $last_login
 * @property string $created_at
 * @property string $signature
 * @property string $updated_at
 * @property string $language
 * @property string $timezone
 *
 * @property string $password_input write-only
 *
 * @property Account $account
 *
 * @property string $image_url
 * @property string $image_path
 * @property string $image_thumb_url
 * @property string $image_thumb_path
 * @property string $signature_url
 * @property string $signature_path
 * @property string $signature_thumb_url
 * @property string $signature_thumb_path
 * @property string $type_label
 * @property array $type_list
 * @property string $status_label
 * @property label $status_list
 */
abstract class AbstractAccount extends ActiveRecord implements IdentityInterface
{

  const SCENARIO_CREATE = 'create';

  //Password Input Helper
  public $password_input;
  public $account_type;
  public $is_mobile = false;

  const STATUS_DISABLED = 10;
  const STATUS_ENABLED = 20;
  const STATUS_DELETED = 30;

  // Backend
  const DEVELOPER = 10;
  const SUPER_ADMIN = 20;
  const ADMIN = 30;
  const FLEET_MANAGER = 40;
  const PLANT_MANAGER = 50;
  const STORE_KEEPER = 60;

  /**
   * @return integer The type of the account
   */
  public abstract function getUserType();

  /**
   * @inheritdoc
   */
  public function scenarios()
  {
    $scenarios = parent::scenarios();
    $scenarios[self::SCENARIO_CREATE] = ['name', 'email', 'password_input'];
    return $scenarios;
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      // Email
      ['email', 'required'],
      ['email', 'string', 'max' => 255],
      ['email', 'email'],
      [['email', 'phone_number'], 'unique'],
      // Full name
      ['name', 'required'],
      ['name', 'string', 'max' => 255],
      // Password
      ['password', 'required'], //is required
      ['password', 'string'], //is a string
      // Password Input
      ['password_input', 'required', 'on' => self::SCENARIO_CREATE], //is required on create only
      //['password_input', 'string', 'min' => 6, 'max' => 20], //should be between 6 and 20 characters
      ['password_input', StrengthValidator::className(), 'preset' => 'normal', 'userAttribute' => 'name'],
      // Status
      ['status', 'required'], //is required
      ['status', 'integer'], //is integer
      ['status', 'default', 'value' => self::STATUS_ENABLED], //default to active
      // Auth Key
      ['auth_key', 'required'],
      ['auth_key', 'string', 'max' => 255],
      // Account ID
      ['account_id', 'integer'],
      ['account_id', 'unique'],
      ['account_id', 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
      // Password Reset Token
      ['password_reset_token', 'unique'],
      ['password_reset_token', 'string', 'max' => 255],
      // Time Stamps
      [['created_at', 'updated_at'], 'safe'],
      // Profile Picture
      ['image', 'file', 'extensions' => 'jpg,png,jpeg', 'maxSize' => (1024 * 1024) * 2],
      [['signature'], 'file', 'extensions' => ['jpg', 'jpeg', 'png']],
      // Random Token
      ['random_token', 'string'],
      // Locking
      ['locked', 'boolean'],
      ['locked', 'default', 'value' => false],
      ['login_attempts', 'integer'],
      ['login_attempts', 'default', 'value' => 0],
      ['last_login', 'safe'],
      ['signature', 'safe'],
      // Access Token
      ['access_token', 'unique'],
      ['access_token', 'string', 'max' => 255],
      //
      ['enable_notification', 'boolean'],
      ['enable_notification', 'default', 'value' => true],
      [['phone_number', 'address'], 'string', 'max' => 255],
      [['mobile_registration_id', 'web_registration_id'], 'string'],
      //language
      ['language', 'string'],
      ['language', 'default', 'value' => 'en-US'],

      ['timezone', 'string'],
      ['timezone', 'default', 'value' => 'Asia/Dubai'],

      // [['account_type'], 'required', 'on' => self::SCENARIO_CREATE],
      [['account_type'], 'safe']
    ];
  }

  /**
   * @inheritdoc
   */
  public function behaviors()
  {
    return [
      'userAudit' => UserAuditBehavior::class,
      'timestamp' => [
        'class' => TimestampBehavior::className(),
        'createdAtAttribute' => 'created_at',
        'updatedAtAttribute' => 'updated_at',
        'value' =>  gmdate("Y-m-d h:i:s"),
      ],
      'status' => [
        'class' => OptionsBehavior::className(),
        'attribute' => 'status',
        'options' => [
          self::STATUS_ENABLED => Yii::t("app", "Active"),
          self::STATUS_DISABLED => Yii::t("app", "Inactive"),
          self::STATUS_DELETED => Yii::t("app", "Deleted"),

        ]
      ],


      'image' => [
        'class' => ImageUploadBehavior::className(),
        'attribute' => 'image',
        'createThumbsOnRequest' => true,
        'thumbs' => [
          'thumb' => ['width' => 250, 'height' => 250],
          'thumb_50' => ['width' => 50, 'height' => 50],
          'thumb_70' => ['width' => 70, 'height' => 70],
        ],
        'defaultUrl' => \Yii::getAlias('@staticWeb') . '/images/user-default.jpg',
        'filePath' => '@static/upload/images/profile_pictures/profile_picture_[[pk]]_[[attribute_random_token]].[[extension]]',
        'fileUrl' => '@staticWeb/upload/images/profile_pictures/profile_picture_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
        'thumbPath' => '@static/upload/images/profile_pictures/[[profile]]/profile_picture_[[pk]]_[[attribute_random_token]].[[extension]]',
        'thumbUrl' => '@staticWeb/upload/images/profile_pictures/[[profile]]/profile_picture_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
      ],
      // 'signature' => [
      //   'class' => ImageUploadBehavior::className(),
      //   'attribute' => 'signature',
      //   'createThumbsOnRequest' => true,
      //   'thumbs' => [
      //     'thumb' => ['width' => 250, 'height' => 250],
      //   ],
      //   'defaultUrl' => Yii::getAlias('@staticWeb') . '/images/user-default.jpg',
      //   'filePath' => '@static/upload/images/admin_signature/admin_signature[[pk]]_[[attribute_random_token]].[[extension]]',
      //   'fileUrl' => '@staticWeb/upload/images/admin_signature/admin_signature[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
      //   'thumbPath' => '@static/upload/images/admin_signature/[[profile]]/admin_signature[[pk]]_[[attribute_random_token]].[[extension]]',
      //   'thumbUrl' => '@staticWeb/upload/images/admin_signature/[[profile]]/admin_signature[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
      // ],
      'random_token' => [
        'class' => RandomTokenBehavior::className(),
        'attributes' => ['random_token'],
      ],
      'account_type'    => [
        'class'     => OptionsBehavior::className(),
        'attribute' => 'account_type',
        'options'   => [
          self::DEVELOPER => Yii::t("app", "Developer"),
          self::ADMIN => Yii::t("app", "Admin"),
          self::PLANT_MANAGER => Yii::t("app", "Division Manager"),
          self::FLEET_MANAGER => Yii::t("app", "Fleet Manager"),
          self::STORE_KEEPER => Yii::t("app", "Store Keeper"),
          self::SUPER_ADMIN  => Yii::t("app", "Super Admin"),

        ]
      ],
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'account_id' => Yii::t('app', 'Account ID'),
      'type' => Yii::t('app', 'Type'),
      'name' => Yii::t('app', 'Name'),
      'email' => Yii::t('app', 'Email'),
      'password' => Yii::t('app', 'Password'),
      'status' => Yii::t('app', 'Status'),
      'phone_number' => Yii::t('app', 'Phone Number'),
      'address' => Yii::t('app', 'Address'),
      'image' => Yii::t('app', 'Image'),
      'signature' => Yii::t('app', 'Signature'),
      'auth_key' => Yii::t('app', 'Auth Key'),
      'random_token' => Yii::t('app', 'Random Token'),
      'access_token' => Yii::t('app', 'Access Token'),
      'password_reset_token' => Yii::t('app', 'Password Reset Token'),
      'mobile_registration_id' => Yii::t('app', 'Mobile Registration ID'),
      'web_registration_id' => Yii::t('app', 'Web Registration ID'),
      'enable_notification' => Yii::t('app', 'Enable Notification'),
      'locked' => Yii::t('app', 'Is Locked'),
      'login_attempts' => Yii::t('app', 'Login Attenpts'),
      'last_login' => Yii::t('app', 'Last Login'),
      'created_at' => Yii::t('app', 'Created At'),
      'updated_at' => Yii::t('app', 'Updated At'),
      'password_input' => Yii::t('app', 'Password'),
      'language' => Yii::t('app', 'Language'),
    ];
  }

  /**
   * @inheritdoc
   */
  public function beforeValidate()
  {
    if (parent::beforeValidate()) {
      if (!empty($this->password_input)) {
        $this->setPassword($this->password_input);
      }
      if ($this->isNewRecord && empty($this->auth_key)) {
        $this->generateAuthKey();
      }
      if (empty($this->auth_key)) {
        $this->generateAuthKey();
      }
    }
    return true;
  }

  /**
   * @inheritdoc
   */
  public function beforeSave($insert)
  {

    if (parent::beforeSave($insert)) {
      if ($insert) {

        if (empty($this->status)) {
          $this->status = self::STATUS_ENABLED;
        }
        $account = new Account();
        $account->type = $this->account_type;
        if ($account->save()) {
          $this->id = $account->id;
          $this->account_id = $account->id;
        } else {
          print_r($account->errors);
          exit;
        }

        return true;
      } else { // update

        if (!empty($this->account_type) && !$this->is_mobile) {
          $account = Account::findOne($this->id);
          $account->type = $this->account_type;
          $account->save();
        }
      }
      return true;
    }
    return false;
  }
  // public function afterSave($insert, $changedAttributes)
  // {
  //   print_r($this->image);
  //   exit;
  // }


  // /**
  //  * @inheritdoc
  //  */
  // public function beforeDelete()
  // {

  //   if (parent::beforeDelete()) {
  //     $this->status = self::STATUS_DISABLED;
  //     $this->save();
  //   }
  //   return false;
  // }

  /**
   * @inheritdoc
   */
  public function attributeHints()
  {
    return [
      'password_input' => $this->isNewRecord ? null : Yii::t('app', 'Leave empty to keep password unchanged'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getAccount()
  {
    return $this->hasOne(Account::className(), ['id' => 'account_id']);
  }

  /**
   * @inheritdoc
   */
  public function getAuthKey()
  {
    return $this->auth_key;
  }

  /**
   * @inheritdoc
   */
  public function getId()
  {
    return $this->getPrimaryKey();
  }

  /**
   * @inheritdoc
   */
  public function validateAuthKey($authKey)
  {
    return $this->getAuthKey() == $authKey;
  }

  /**
   * @inheritdoc
   */
  public static function findIdentity($id)
  {
    return static::find()
      ->where([
        'AND',
        ['id' => $id],
        ['status' => self::STATUS_ENABLED],
      ])
      ->one();
  }

  /**
   * @inheritdoc
   */
  public static function findIdentityByAccessToken($token, $type = null)
  {
    return static::find()
      ->where([
        'AND',
        ['access_token' => $token],
        ['status' => self::STATUS_ENABLED]
      ])
      ->one();
  }

  /**
   * Generate a random access token for account
   */
  public function generateAccessToken()
  {
    $this->access_token = Yii::$app->getSecurity()->generateRandomString(64);
  }

  /**
   * Validates password
   *
   * @param string $password password to validate
   * @return boolean if password provided is valid for current user
   */
  public function validatePassword($password)
  {
    return Yii::$app->security->validatePassword($password, $this->password);
  }

  /**
   * Generates password hash from password and sets it to the model
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = Yii::$app->security->generatePasswordHash($password);
  }

  /**
   * Generates "remember me" authenticationf key
   */
  public function generateAuthKey()
  {
    $this->auth_key = Yii::$app->security->generateRandomString();
  }

  /**
   * Generates new password reset token
   */
  public function generatePasswordResetToken($generateCode = false)
  {
    if ($generateCode) {
      $this->password_reset_token = strval(rand(100000, 999999));
    } else {
      $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
  }

  /**
   * Finds out if password reset token is valid
   *
   * @param string $token password reset token
   * @return boolean
   */
  public static function isPasswordResetTokenValid($token)
  {
    if (empty($token)) {
      return false;
    }

    if (
      strlen($token) == 6 &&
      intval($token) <= 999999 &&
      intval($token) >= 100000
    ) {
      return true;
    }

    $timestamp = (int) substr($token, strrpos($token, '_') + 1);
    $expire = Yii::$app->params['user.passwordResetTokenExpire'];
    return $timestamp + $expire >= time();
  }

  /**
   * Finds user by password reset token
   *
   * @param string $token password reset token
   * @return static|null
   */
  public static function findByPasswordResetToken($token)
  {
    if (!static::isPasswordResetTokenValid($token)) {
      return null;
    }

    return static::findOne([
      'password_reset_token' => $token,
      'status' => self::STATUS_ENABLED,
    ]);
  }

  /**
   * Removes password reset token
   */
  public function removePasswordResetToken()
  {
    $this->password_reset_token = null;
  }

  /**
   * Find user model by his email
   *
   * @param string $email
   * @return static
   */
  public static function findByEmail($email)
  {
    $length = strlen($email);

    return static::find()
      ->where([
        'AND',
        [
          'OR',
          ['RIGHT(phone_number , ' . $length . ')' => $email],
          ['email' => $email],

        ],
        ['status' => self::STATUS_ENABLED]
      ])
      ->one();
  }

  /**
   * Find user model by his phone
   *
   * @param string $phone
   * @return static
   */
  public static function findByPhone($phone)
  {
    return static::find()
      ->where([
        'AND',
        ['phone_number' => $phone],
        ['status' => self::STATUS_ENABLED]
      ])
      ->one();
  }

  //----------------------------------------------------------------
  const FIELDS_MINIMUM = 10;
  const FIELDS_LIST = 20;

  public static $return_fields = null;

  public function fields()
  {
    switch (self::$return_fields) {
      case self::FIELDS_MINIMUM:
        return [
          'id',
          'name',
          'email',
          'phone_number',
          'image_thumb_url',
          'address',
          'access_token',
          'user_type' => function ($model) {
            return !empty($model->account->type) ? $model->account->type0->name : null;
          },
          'user_type_label' => function ($model) {
            return !empty($model->account->type) ? $model->account->type0->label : null;
          },
        ];
      case self::FIELDS_LIST:
        return [
          'id',
          'name',
          'phone_number',
          'email',
          'image_thumb_url',
          //'active' => function($model) {
          //return $model->status == self::STATUS_ENABLED;
          //},
        ];
      default:
        return [
          'id',
          'name',
          'email',
          'phone_number',
          'image_thumb_url',
          'address',
          'user_type' => function ($model) {
            return !empty($model->account->type) ? $model->account->type0->name : null;
          },
        ];
    }
  }
}
