<?php

namespace common\models;

use common\behaviors\ImageUploadBehavior;
use common\models\users\AbstractAccount;
use common\models\Account;
use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "technician".
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $main_sector_id
 * @property string $name
 * @property string $email
 * @property string $country
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
 * @property integer $enable_notification
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
 * @property string $title
 * @property string $code
 * @property integer $profession_id
 * @property integer $division_id
 * @property string $badge_number
 * @property string $description
 * @property string $longitude
 * @property string $latitude
 *
 * @property Assignee[] $assignees
 * @property Log[] $logs
 * @property Maintenance[] $maintenances
 * @property MaintenanceReport[] $maintenanceReports
 * @property MaintenanceVisit[] $maintenanceVisits
 * @property RepairRequest[] $repairRequests
 * @property RepairRequest[] $repairRequests0
 * @property RepairRequest[] $repairRequests1
 * @property Sector[] $sectors
 * @property Account $account
 * @property Division $division
 * @property MainSector $mainSector
 * @property Profession $profession
 * @property TechnicianLocation[] $technicianLocations
 * @property TechnicianSector[] $technicianSectors
 * @property Sector[] $sectors0
 * @property TechnicianShift[] $technicianShifts
 * @property UserBreak[] $userBreaks
 *
 * @property string $image_url
 * @property string $image_path
 * @property string $image_thumb_url
 * @property string $image_thumb_path
 * @property string $status_label
 * @property label $status_list
 */
class Technician extends AbstractAccount
{
    public $account_type;
    public $shift_id;
    public static $request_id;

    public $work_status;

    const SUPER_ADMIN = 20;
    const ADMIN = 30;
    const FLEET_MANAGER = 40;
    const DIVISION_MANAGER = 50;
    const STORE_KEEPER = 60;
    // APP
    const SUPERVISOR = 70;
    const TECHNICIAN = 80;
    const PURCHASER = 90;
    const COORDINATOR = 100;
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    const STATUS_DELETED = 30;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'technician';
    }

    /**
     * @return integer The type of the account
     */
    public function getUserType()
    {
        return "1";
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['password_input', 'account_type', 'profession_id', 'main_sector_id', 'phone_number', 'name', 'timezone', 'description', 'address', 'division_id', 'badge_number', 'country'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'main_sector_id', 'login_attempts', 'status', 'created_by', 'updated_by', 'profession_id', 'division_id'], 'integer'],
            [['mobile_registration_id', 'web_registration_id', 'description'], 'string'],
            [['last_login', 'created_at', 'updated_at'], 'safe'],
            [['name', 'email', 'country', 'phone_number', 'address', 'auth_key', 'access_token', 'random_token', 'password_reset_token', 'timezone', 'language', 'platform', 'title', 'longitude', 'latitude'], 'string', 'max' => 255],
            [['badge_number'], 'string', 'max' => 50],
            [['phone_number'], 'unique'],
            [['image'], 'safe'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
            [['main_sector_id'], 'exist', 'skipOnError' => true, 'targetClass' => MainSector::className(), 'targetAttribute' => ['main_sector_id' => 'id']],
            [['profession_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profession::className(), 'targetAttribute' => ['profession_id' => 'id']],
            [['main_sector_id', 'status', 'badge_number', 'profession_id', 'division_id', 'name', 'phone_number'], 'required'],
            [['password_input', 'account_type', 'profession_id', 'main_sector_id', 'phone_number', 'division_id', 'name'], 'required', 'on' => self::SCENARIO_CREATE],
            ['badge_number', 'unique']
        ];
    }
    // public function attributeLabels()
    // {
    //     return array_merge(parent::attributeLabels(), [
    //         'code' => 'Personnel no.'
    //     ]);
    // }
    // public function behaviors()
    // {
    //     return [
    //         'timestamp' => [
    //             'class'              => TimestampBehavior::className(),
    //             'createdAtAttribute' => 'created_at',
    //             'updatedAtAttribute' => 'updated_at',
    //             'value'              => gmdate("Y-m-d h:i:s"),
    //         ],

    //         'status'    => [
    //             'class'     => OptionsBehavior::className(),
    //             'attribute' => 'status',
    //             'options'   => [
    //                 self::STATUS_ENABLED  => Yii::t("app", "Active"),
    //                 self::STATUS_DISABLED => Yii::t("app", "Inactive"),

    //             ]
    //         ],

    //         'account_type'    => [
    //             'class'     => OptionsBehavior::className(),
    //             'attribute' => 'account_type',
    //             'options'   => [
    //                 self::COORDINATOR => Yii::t("app", "Coordinator"),
    //                 self::PURCHASER => Yii::t("app", "Purchaser"),
    //                 self::SUPERVISOR => Yii::t("app", "Supervisor"),
    //                 self::TECHNICIAN => Yii::t("app", "Technician"),

    //             ]
    //         ],
    //         'image'        => [
    //             'class'     => ImageUploadBehavior::className(),
    //             'attribute' => 'image',
    //             'createThumbsOnRequest' => true,
    //             'thumbs'    => [
    //                 'thumb' => ['width' => 250, 'height' => 250],
    //                 'thumb_100' => ['width' => 100, 'height' => 100],
    //             ],
    //             'defaultUrl' => \Yii::getAlias('@staticWeb') . '/images/user-default.jpg',
    //             'filePath'  => '@static/upload/images/technician/technician_[[pk]]_[[attribute_random_token]].[[extension]]',
    //             'fileUrl'   => '@staticWeb/upload/images/technician/technician_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
    //             'thumbPath' => '@static/upload/images/technician/[[profile]]/technician_[[pk]]_[[attribute_random_token]].[[extension]]',
    //             'thumbUrl'  => '@staticWeb/upload/images/technician/[[profile]]/technician_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
    //         ],


    //     ];
    // }


    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'account_id' => Yii::t('app', 'Technician Type'),
            'main_sector_id' => 'Main Sector',
            'name' => Yii::t('app', 'Name'),
            'account_type' => Yii::t('app', 'Account Type'),
            'email' => Yii::t('app', 'Email'),
            'shift_id' => Yii::t('app', 'Shift'),
            'password' => Yii::t('app', 'Password'),
            'password_input' => Yii::t('app', 'Password'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'country' => Yii::t('app', 'Nationality'),
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
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'platform' => Yii::t('app', 'Platform'),
            'title' => Yii::t('app', 'Title'),
            'code' => Yii::t('app', 'Code'),
            'profession_id' => Yii::t('app', 'Profession '),
            'division_id' => Yii::t('app', 'Division'),
            'badge_number' => Yii::t('app', 'Badge Number'),
            'description' => Yii::t('app', 'Description'),
            'longitude' => Yii::t('app', 'Longitude'),
            'latitude' => Yii::t('app', 'Latitude'),
        ];
    }


    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this->status == self::STATUS_ENABLED) {
                $this->status = self::STATUS_DISABLED;
                $this->save();
                return false;
            } else {
                return true;
            }
        }
        return false;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignees()
    {
        return $this->hasMany(Assignee::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Log::className(), ['technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenances()
    {
        return $this->hasMany(Maintenance::className(), ['technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenanceReports()
    {
        return $this->hasMany(MaintenanceReport::className(), ['technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenanceVisits()
    {
        return $this->hasMany(MaintenanceVisit::className(), ['technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequests()
    {
        return $this->hasMany(RepairRequest::className(), ['owner_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequests0()
    {
        return $this->hasMany(RepairRequest::className(), ['team_leader_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequests1()
    {
        return $this->hasMany(RepairRequest::className(), ['technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSectors()
    {
        return $this->hasMany(Sector::className(), ['default_technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainSector()
    {
        return $this->hasOne(MainSector::className(), ['id' => 'main_sector_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfession()
    {
        return $this->hasOne(Profession::className(), ['id' => 'profession_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicianLocations()
    {
        return $this->hasMany(TechnicianLocation::className(), ['technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicianSectors()
    {
        return $this->hasMany(TechnicianSector::className(), ['technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSectors0()
    {
        return $this->hasMany(Sector::className(), ['id' => 'sector_id'])->viaTable('technician_sector', ['technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicianShifts()
    {
        return $this->hasMany(TechnicianShift::className(), ['technician_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserBreaks()
    {
        return $this->hasMany(UserBreak::className(), ['technician_id' => 'id']);
    }

    public static function getTechnicianSectorsOptions()
    {
        $models = @MainSector::findOne(Account::getAdminMainSectorId())->sectors; // So We Only Get Sectors Related To The Admin Main Sector

        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $models = Sector::find()->orderBy(['name' => SORT_ASC])->all();
        } else if ((Account::getAdminDivisionID() == Division::DIVISION_VILLA)) {
            $models = Sector::find()->joinWith('mainSector')->where([MainSector::tableName() . '.division_id' => Account::getAdminDivisionID()])->orderBy(['name' => SORT_ASC])->all();
        }

        return $models;
    }
    public function getLatestAssignee()
    {
        return $this->hasOne(Assignee::class, ['user_id' => 'id'])
            ->where(['in', 'status', [Assignee::STATUS_BUSY, Assignee::STATUS_BREAK, Assignee::STATUS_HOURLY_LEAVE]])
            ->orderBy(['updated_at' => SORT_DESC]);
    }
    public static function getTechnicianAccountTypeID($user_id)
    {
        if (!empty($user_id)) {
            return Technician::findOne($user_id)->account->type0->name;
        }
    }

    public static function getTechnicianAccountTypeLabel($user_id)
    {
        if (!empty($user_id)) {
            return Technician::findOne($user_id)->account->type0->label;
        }
    }

    public function getRepairOrders()
    {
        $orders = RepairRequest::find()->innerJoin('assignee', 'assignee.repair_request_id = repair_request.id')
            ->where([Assignee::tableName() . '.user_id' => $this->id])
            // ->orWhere([RepairRequest::tableName() . '.owner_id' => $this->id])

            ->andWhere([
                'IN',
                RepairRequest::tableName() . '.status',
                [
                    RepairRequest::STATUS_DRAFT,
                    RepairRequest::STATUS_CREATED,
                    RepairRequest::STATUS_CHECKED_IN,
                    RepairRequest::STATUS_ON_HOLD,
                    RepairRequest::STATUS_REQUEST_COMPLETION,
                    RepairRequest::STATUS_UNABLE_TO_ACCESS,
                    RepairRequest::STATUS_REQUEST_DIFFERENT_TECHNICIAN,
                ]
            ])
            ->andWhere(["!=", Assignee::tableName() . '.status', Assignee::STATUS_REJEJCTED]) // all repairs
            // ->andWhere(['<=', 'DATE(repair_request.scheduled_at)', gmdate('Y-m-d')]) // all repairs
            // ->indexBy(['id'])
            ->andWhere([RepairRequest::tableName() . '.division_id' => $this->division_id])
            ->orderBy(['urgent_status' => SORT_DESC, 'scheduled_at' => SORT_ASC])

            ->all();

        if (!empty($orders)) {
            return $orders;
        } else {
            return null;
        }
    }

    public function getTodayRepairOrders()
    {
        $orders = RepairRequest::find()->innerJoin('assignee', 'assignee.repair_request_id = repair_request.id')
            ->where([Assignee::tableName() . '.user_id' => $this->id])
            // ->orWhere([RepairRequest::tableName() . '.owner_id' => $this->id])
            ->andWhere([
                'IN',
                RepairRequest::tableName() . '.status',
                [
                    RepairRequest::STATUS_DRAFT,
                    RepairRequest::STATUS_CREATED,
                    RepairRequest::STATUS_CHECKED_IN,
                    RepairRequest::STATUS_ON_HOLD,
                    RepairRequest::STATUS_REQUEST_COMPLETION,
                    RepairRequest::STATUS_UNABLE_TO_ACCESS,
                    RepairRequest::STATUS_REQUEST_DIFFERENT_TECHNICIAN,

                ]
            ])
            ->andWhere(["!=", Assignee::tableName() . '.status', Assignee::STATUS_REJEJCTED]) // all repairs

            ->andWhere(['<=', 'DATE(repair_request.scheduled_at)', gmdate('Y-m-d')]) // until today
            ->andWhere(['!=', RepairRequest::tableName() . '.service_type', RepairRequest::TYPE_PPM])
            ->andWhere([RepairRequest::tableName() . '.division_id' => $this->division_id])

            // ->indexBy(['id'])
            ->orderBy(['urgent_status' => SORT_DESC, 'scheduled_at' => SORT_ASC])
            ->all();


        if (!empty($orders)) {
            return $orders;
        } else {
            return null;
        }
    }
    public static function getTechnicianByDivision()
    {
        if (!empty(\Yii::$app->user->identity->division_id)) {
            return ArrayHelper::map(Technician::find()->where(['status' => Technician::STATUS_ENABLED])
                ->andWhere(['division_id' => \Yii::$app->user->identity->division_id])
                ->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
        } else {
            return ArrayHelper::map(Technician::find()
                ->where(['status' => Technician::STATUS_ENABLED])
                ->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
        }
    }

    public function getUpcomingRepairOrders()
    {
        $orders = RepairRequest::find()->innerJoin('assignee', 'assignee.repair_request_id = repair_request.id')
            ->where([Assignee::tableName() . '.user_id' => $this->id])
            // ->orWhere([RepairRequest::tableName() . '.owner_id' => $this->id])
            ->andWhere([
                'IN',
                RepairRequest::tableName() . '.status',
                [
                    RepairRequest::STATUS_DRAFT,
                    RepairRequest::STATUS_CREATED,
                    RepairRequest::STATUS_CHECKED_IN,
                    RepairRequest::STATUS_ON_HOLD,
                    RepairRequest::STATUS_REQUEST_COMPLETION,
                    RepairRequest::STATUS_UNABLE_TO_ACCESS,
                    RepairRequest::STATUS_REQUEST_DIFFERENT_TECHNICIAN,

                ]
            ])
            ->andWhere(["!=", Assignee::tableName() . '.status', Assignee::STATUS_REJEJCTED]) // all repairs

            ->andWhere(['>', 'DATE(repair_request.scheduled_at)', gmdate('Y-m-d')]) // after today
            ->andWhere(['!=', RepairRequest::tableName() . '.service_type', RepairRequest::TYPE_PPM])
            ->andWhere([RepairRequest::tableName() . '.division_id' => $this->division_id])

            // ->indexBy(['id'])
            ->orderBy(['urgent_status' => SORT_DESC, 'scheduled_at' => SORT_ASC])
            ->all();

        if (!empty($orders)) {
            return $orders;
        } else {
            return null;
        }
    }

    public function getPpmRepairOrders()
    {
        $orders = RepairRequest::find()->innerJoin('assignee', 'assignee.repair_request_id = repair_request.id')
            ->where([Assignee::tableName() . '.user_id' => $this->id])
            // ->orWhere([RepairRequest::tableName() . '.owner_id' => $this->id])
            ->andWhere([
                'IN',
                RepairRequest::tableName() . '.status',
                [
                    RepairRequest::STATUS_DRAFT,
                    RepairRequest::STATUS_CREATED,
                    RepairRequest::STATUS_CHECKED_IN,
                    RepairRequest::STATUS_ON_HOLD,
                    RepairRequest::STATUS_REQUEST_COMPLETION,
                    RepairRequest::STATUS_UNABLE_TO_ACCESS,
                    RepairRequest::STATUS_REQUEST_DIFFERENT_TECHNICIAN,

                ]
            ])
            ->andWhere(["!=", Assignee::tableName() . '.status', Assignee::STATUS_REJEJCTED]) // all repairs

            ->andWhere([RepairRequest::tableName() . '.service_type' => RepairRequest::TYPE_PPM])
            ->andWhere([RepairRequest::tableName() . '.division_id' => $this->division_id])
            // ->indexBy(['id'])
            ->orderBy(['urgent_status' => SORT_DESC, 'scheduled_at' => SORT_ASC])
            ->all();

        if (!empty($orders)) {
            return $orders;
        } else {
            return null;
        }
    }

    public function getCompletedRepairOrders()
    {
        $orders = RepairRequest::find()->innerJoin('assignee', 'assignee.repair_request_id = repair_request.id')
            ->where([Assignee::tableName() . '.user_id' => $this->id])
            // ->orWhere([RepairRequest::tableName() . '.owner_id' => $this->id])
            ->andWhere([
                'IN',
                RepairRequest::tableName() . '.status',
                [
                    RepairRequest::STATUS_COMPLETED,
                ]
            ])
            ->andWhere(["!=", Assignee::tableName() . '.status', Assignee::STATUS_REJEJCTED]) // all repairs

            ->andWhere([RepairRequest::tableName() . '.division_id' => $this->division_id])
            // ->indexBy(['id'])
            ->orderBy(['urgent_status' => SORT_DESC, 'scheduled_at' => SORT_ASC])
            ->all();

        if (!empty($orders)) {
            return $orders;
        } else {
            return null;
        }
    }

    public function getTechnicianWorkStatus($label = false)
    {
        $user_id = $this->id;

        $assignee = Assignee::find()->where(['user_id' => $user_id])->orderBy(['updated_at' => SORT_DESC])->one();

        if (!empty($label) && $label) {
            return empty(@$assignee->status_label) ? (new Assignee())->status_list[Assignee::STATUS_FREE] : @$assignee->status_label;
        } else {
            return empty(@$assignee->status) ? Assignee::STATUS_FREE : @$assignee->status;
        }
    }

    public function getTechnicianWorkOrderStatus($label = false)
    {
        $user_id = $this->id;
        $order_id = $this->request_id;

        $assignee = Assignee::find()->where(['user_id' => $user_id, 'repair_request_id' => $order_id])->one();
        return empty(@$assignee->status) ? Assignee::STATUS_FREE : @$assignee->status;
    }

    public function getRelatedTechnicians()
    {
        $technicians = Technician::find()->select(['id', 'name'])->where([
            'AND',
            ['division_id' => $this->division_id],
            ['main_sector_id' => $this->main_sector_id],
        ])->asArray()->all();

        if (!empty($technicians)) {
            return $technicians;
        } else {
            return null;
        }
    }

    public static function getTechnicianWorkStatuses()
    {
        $assignee = new Assignee();

        return @$assignee->status_list;
    }

    public function fields()
    {
        $parent = parent::fields();

        return array_merge($parent, [
            'profession' => function ($model) {
                return $model->profession->name;
            },
            'selected' => function ($model) {
                if (!empty(self::$request_id)) {
                    $request_assignees = ArrayHelper::getColumn(RepairRequest::findOne(self::$request_id)->assignees, 'user_id');

                    $technician_id = $this->id;

                    $existsInAssignees = in_array($technician_id, $request_assignees);

                    if ($existsInAssignees) {
                        return true;
                    } else {
                        return false;
                    }
                }
            },
            'is_owner' => function ($model) {
                $owner_id = @RepairRequest::findOne(self::$request_id)->owner_id;

                if ($owner_id) {
                    if ($owner_id === $this->id) {
                        return true;
                    }
                }

                return false;
            },
            'related_technicians' => function ($model) {
                return $model->getRelatedTechnicians();
            },
            'status' => function ($model) {
                return $model->getTechnicianWorkStatus();
            },
            'status_label' => function ($model) {
                return $model->getTechnicianWorkStatus(true);
            },
            'statuses' => function ($model) {
                return [$model->getTechnicianWorkStatuses()];
            },
            'division' => function ($model) {
                return (new Division())->name_list[$model->division_id];
            }
            // 'current_work_status' => function ($model) {
            //     $assignee = Assignee::find()->where(['repair_request_id' => $this->request_id, 'user_id' => $this->id])->one();

            //     return !empty($assignee->status) ? $assignee->status : Assignee::STATUS_FREE;
            // }
        ]);
    }
}
