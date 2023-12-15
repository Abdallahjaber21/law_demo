<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use common\models\users\AbstractAccount;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $phone_number
 * @property string $address
 * @property string $image
 * @property string $auth_key
 * @property string $access_token
 * @property string $random_token
 * @property string $password_reset_token
 * @property string $mobile_registration_id
 * @property string $web_registration_id
 * @property string $firstname
 * @property string $lastname
 * @property boolean $enable_notification
 * @property boolean $contracts_reminders
 * @property boolean $maintenance_notifications
 * @property boolean $news_notifications
 * @property integer $locked
 * @property integer $login_attempts
 * @property string $last_login
 * @property string $timezone
 * @property string $language
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $platform
 * @property integer $access_type
 * @property string $floor_number
 * @property string $birthdate
 * @property string $job_category
 * @property string $company_name
 * @property string $job_title
 *
 * @property CustomerUser[] $customerUsers
 * @property RepairRequest[] $repairRequests
 * @property Account $account
 * @property Customer $customers
 * @property Location[] $locations
 * @property Equipment $equipments
 * @property DefaultLocation $defaultLocation
 * @property Location $myLocation
 * @property UserLocation[] $userLocations
 *
 *
 * @property string $image_url
 * @property string $image_path
 * @property string $image_thumb_url
 * @property string $image_thumb_path
 * @property string $status_label
 * @property label $status_list
 */
class User extends AbstractAccount
{
    // Backend
    const SUPER_ADMIN = 20;
    const ADMIN = 30;
    const FLEET_MANAGER = 40;
    const PLANT_MANAGER = 50;
    const STORE_KEEPER = 60;

    // APP
    const SUPERVISOR = 70;
    const TECHNICIAN = 80;
    const PURCHASER = 90;
    const COORDINATOR = 100;

    const ACCESS_TYPE_FULL = 10;
    const ACCESS_TYPE_LIMITED = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @return integer The type of the account
     */
    public function getUserType()
    {
        return "user";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['email', 'unique', 'targetClass' => User::className()],
            [['firstname', 'lastname', 'platform'], 'string'],
            ['access_type', 'integer'],
            ['access_type', 'default', 'value' => self::ACCESS_TYPE_FULL],
            [['contracts_reminders', 'maintenance_notifications', 'news_notifications'], 'boolean'],
            [['contracts_reminders', 'maintenance_notifications', 'news_notifications'], 'default', 'value' => true],
            [['floor_number', 'job_category', 'company_name', 'job_title'], 'string', 'max' => 255],
            [['birthdate'], 'safe'],
        ]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access_type'] = [
            'class'     => OptionsBehavior::className(),
            'attribute' => 'access_type',
            'options'   => [
                self::ACCESS_TYPE_FULL    => Yii::t("app", "Full access"),
                self::ACCESS_TYPE_LIMITED => Yii::t("app", "Limited access"),
            ]
        ];
        return [
            $behaviors,
            'type'    => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'type',
                'options'   => [
                    self::SUPER_ADMIN  => Yii::t("app", "Super Admin"),
                    self::ADMIN => Yii::t("app", "Admin"),
                    self::FLEET_MANAGER => Yii::t("app", "Fleet Manager"),
                    self::PLANT_MANAGER => Yii::t("app", "Plant Manager"),
                    self::STORE_KEEPER => Yii::t("app", "Store Keeper"),
                    self::SUPERVISOR => Yii::t("app", "Supervisor"),
                    self::TECHNICIAN => Yii::t("app", "Technician"),
                    self::PURCHASER => Yii::t("app", "Purchaser"),
                    self::COORDINATOR => Yii::t("app", "Coordinator"),




                ]
            ],

        ];
    }

    public function beforeValidate()
    {
        $this->name = "{$this->firstname} {$this->lastname}";
        return parent::beforeValidate();
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomerUsers()
    {
        return $this->hasMany(CustomerUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserLocations()
    {
        return $this->hasMany(UserLocation::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['id' => 'location_id'])->viaTable('user_location', ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRepairRequests()
    {
        return $this->hasMany(RepairRequest::className(), ['user_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customer::className(), ['id' => 'customer_id'])
            ->viaTable("customer_user", ["user_id" => "id"]);
    }

    /**
     * @return ActiveQuery
     */
    //    public function getLocations()
    //    {
    //        return $this->hasMany(Location::className(), ['customer_id' => 'id'])
    //            ->via("customers");
    //    }

    /**
     * @return ActiveQuery
     */
    public function getEquipments()
    {
        return $this->hasMany(Equipment::className(), ['location_id' => 'id'])
            ->via("locations");
    }

    public function getRepairRequests2()
    {
        return $this->hasMany(RepairRequest::className(), ['equipment_id' => 'id'])
            ->via("equipments");
    }


    /**
     * @return ActiveQuery
     */
    public function getDefaultLocation()
    {
        return $this->hasOne(DefaultLocation::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMyLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id'])
            ->via("defaultLocation");
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['enable_notification'] = function ($model) {
            return boolval($model->enable_notification);
        };
        $fields['contracts_reminders'] = function ($model) {
            return boolval($model->contracts_reminders);
        };
        $fields['maintenance_notifications'] = function ($model) {
            return boolval($model->maintenance_notifications);
        };
        $fields['news_notifications'] = function ($model) {
            return boolval($model->news_notifications);
        };
        $fields[] = "firstname";
        $fields[] = "lastname";
        $fields[] = "access_type";
        $fields[] = "job_category";
        $fields[] = "job_title";
        $fields[] = "birthdate";
        $fields[] = "floor_number";
        return $fields;
    }
}