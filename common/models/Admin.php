<?php

namespace common\models;

use common\behaviors\ImageUploadBehavior;
use common\behaviors\OptionsBehavior;
use Faker\Provider\bg_BG\PhoneNumber;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveQuery;
use common\models\users\AbstractAccount;
use kartik\password\StrengthValidator;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "admin".
 *
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property string $email
 * @property string $country
 * @property string $password
 * @property int $status
 * @property string $phone_number
 * @property string $signature
 * @property string $address
 * @property string $image
 * @property string $auth_key
 * @property string $access_token
 * @property string $random_token
 * @property string $password_reset_token
 * @property string $mobile_registration_id
 * @property string $web_registration_id
 * @property int $enable_notification
 * @property int $locked
 * @property int $login_attempts
 * @property string $last_login
 * @property string $timezone
 * @property string $language
 * @property string $created_at
 * @property string $updated_at
 * @property int $division_id
 * @property string $badge_number
 * @property string $description
 *
 * @property Division $division
 * @property Account $account
 * @property Profession $profession
 * @property AdminSector[] $adminSectors
 * @property MainSector $mainSector

 * @property Sector[] $sectors
 * @property label $state_id_list


 */
class Admin extends AbstractAccount
{
    // Backend
    const DEVELOPER = 10;
    const SUPER_ADMIN = 20;
    const ADMIN = 30;
    const FLEET_MANAGER = 40;
    const DIVISION_MANAGER = 50;
    const STORE_KEEPER = 60;
    // APP
    const SUPERVISOR = 70;
    const TECHNICIAN = 80;
    const TEAM_LEADER = 90;
    const PURCHASER = 100;
    const COORDINATOR = 110;

    const STATUS_ENABLED = 20;
    const STATUS_DISABLED = 10;
    const STATUS_DELETED = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin';
    }
    public function getUserType()
    {
        return "0";
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['password_input', 'name', 'badge_number', 'status', 'email', 'timezone', 'description', 'address', 'phone_number', 'country', 'division_id', 'main_sector_id', 'account_type'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['account_id', 'status', 'enable_notification', 'locked', 'login_attempts', 'division_id', 'main_sector_id'], 'integer'],
            [['mobile_registration_id', 'web_registration_id', 'description'], 'string'],
            [['last_login', 'created_at', 'updated_at', 'account_type', 'signature'], 'safe'],
            ['email', 'email'],
            [['email', 'phone_number'], 'unique'],
            [['name', 'email', 'country', 'password_input', 'phone_number', 'address', 'auth_key', 'access_token', 'random_token', 'password_reset_token', 'timezone', 'language'], 'string', 'max' => 255],
            [['badge_number'], 'string', 'max' => 50],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
            [['name', 'email'], 'required'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['main_sector_id'], 'exist', 'skipOnError' => true, 'targetClass' => MainSector::className(), 'targetAttribute' => ['main_sector_id' => 'id']],
            [['name', 'badge_number', 'status', 'email', 'phone_number'], 'required'],
            [['password_input', 'name', 'badge_number', 'status', 'email', 'phone_number'], 'required', 'on' => self::SCENARIO_CREATE],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'account_id' => Yii::t('app', 'Account'),
            'account_type' => Yii::t('app', 'Account Type'),
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'country' => Yii::t('app', 'Nationality'),
            'password' => Yii::t('app', 'Password'),
            'password_input' => Yii::t('app', 'Password'),
            'status' => Yii::t('app', 'Status'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'address' => Yii::t('app', 'Address'),
            'image' => Yii::t('app', 'Image'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'access_token' => Yii::t('app', 'Access Token'),
            'random_token' => Yii::t('app', 'Random Token'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'mobile_registration_id' => Yii::t('app', 'Mobile Registration ID'),
            'web_registration_id' => Yii::t('app', 'Web Registration ID'),
            'enable_notification' => Yii::t('app', 'Enable Notification'),
            'locked' => Yii::t('app', 'Locked'),
            'login_attempts' => Yii::t('app', 'Login Attempts'),
            'last_login' => Yii::t('app', 'Last Login'),
            'timezone' => Yii::t('app', 'Timezone'),
            'language' => Yii::t('app', 'Language'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'division_id' => Yii::t('app', 'Division '),
            'badge_number' => Yii::t('app', 'Badge Number'),
            'description' => Yii::t('app', 'Description'),
            'main_sector_id' => 'Main Sector',

        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'signature' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'signature',
                'createThumbsOnRequest' => true,
                'thumbs' => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'defaultUrl' => Yii::getAlias('@staticWeb') . '/images/signature-default.jpg',
                'filePath' => '@static/upload/images/signature/signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl' => '@staticWeb/upload/images/signature/signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath' => '@static/upload/images/signature/[[profile]]/signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl' => '@staticWeb/upload/images/signature/[[profile]]/signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            ],
        ]);
    }

    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(Division::className(), ['id' => 'division_id']);
    }
    public function getMainSector()
    {
        return $this->hasOne(MainSector::className(), ['id' => 'main_sector_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdminSectors()
    {
        return $this->hasMany(AdminSector::className(), ['admin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSectors()
    {
        return $this->hasMany(Sector::className(), ['id' => 'sector_id'])->viaTable('admin_sector', ['admin_id' => 'id']);
    }

    public function sectorsIds()
    {
        return ArrayHelper::getColumn($this->adminSectors, 'sector_id', false);
        ;
    }

    public static function activeSectorsIds()
    {
        return \Yii::$app->user->identity->sectorsIds();
    }

    public static function getAdminId()
    {
        return Yii::$app->user->id;
    }
}
