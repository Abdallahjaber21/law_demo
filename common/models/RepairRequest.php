<?php

namespace common\models;

use common\behaviors\DateFormatBehavior;
use common\behaviors\ImageUploadBehavior;
use common\behaviors\OptionsBehavior;
use common\behaviors\PriceFormatBehavior;
use common\behaviors\RandomTokenBehavior;
use common\components\helpers\DateTimeHelper;
use common\components\notification\Notification;
use common\components\settings\Setting;
use common\config\includes\P;
use common\models\Technician;
use common\models\Assignee;
use common\models\users\Admin;
use DateTime;
use kartik\mpdf\Pdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "repair_request".
 *
 * @property int $id
 * @property int $equipment_id
 * @property int $service_type
 * @property int $labor_charge
 * @property string $requested_at
 * @property string $scheduled_at
 * @property string $informed_at
 * @property string $arrived_at
 * @property string $departed_at
 * @property integer $sector_id
 * @property string $supervisor_note
 * @property string supervisor_signature
 * @property int $status
 * @property int $need_review
 * @property string $created_at
 * @property string $updated_at
 * @property string $service_note
 * @property string $coordinator_note
 * @property int $created_by
 * @property int $updated_by
 * @property string $assigned_at
 * @property string $customer_signature
 * @property string $admin_signature
 * @property string $random_token
 * @property string $completed_at
 * @property string $note
 * @property string $technician_signature
 * @property string $coordinator_signature
 * @property string $reported_by_name
 * @property string $reported_by_phone
 * @property string $notification_id
 * @property int $completed_by
 * @property int $owner_id
 * @property integer $gallery_id
 * @property int $team_leader_id
 * @property string $description
 * @property int $urgent_status
 * @property int $division_id
 * @property int $project_id
 * @property int $location_id
 * @property int $template_id
 * @property int $category_id
 * @property int $technician_from_another_division
 * @property string $repair_request_path
 * @property string $problem
 *
 * @property Assignee[] $assignees
 * @property CompletedMaintenanceTask[] $completedMaintenanceTasks
 * @property Gallery $gallery
 * @property LineItem[] $lineItems
 * @property Log[] $logs
 * @property MallPpmTasksHistory[] $mallPpmTasksHistories
 * @property PlantPpmTasksHistory[] $plantPpmTasksHistories
 * @property PpmAdditionalTasksValues[] $ppmAdditionalTasksValues
 * @property VillaPpmTemplates $template
 * @property Location $category
 * @property Location $location
 * @property Project $project
 * @property Division $division
 * @property LocationEquipments $equipment
 * @property Technician $owner
 * @property Problem $problem
 * @property Technician $teamLeader
 * @property Sector $sector
 * @property RepairRequestChats[] $repairRequestChats
 * @property RepairRequestMaintenanceTask[] $repairRequestMaintenanceTasks
 * @property MaintenanceTaskGroup[] $maintenanceTaskGroups
 * @property RepairRequestRating[] $repairRequestRatings
 * @property ServiceLog[] $serviceLogs
 * @property TechnicianLocation[] $technicianLocations
 * @property UserBreak[] $userBreaks
 */
class RepairRequest extends ActiveRecord
{
    public $equipment_code;
    public $location_code;
    public $fileUpload;

    public $type;
    public $technician_id;

    // Status
    const STATUS_DRAFT = 10;
    const STATUS_CREATED = 20;
    const STATUS_CHECKED_IN = 30;
    const STATUS_ON_HOLD = 40;
    const STATUS_COMPLETED = 50;
    const STATUS_CANCELLED = 60;
    const STATUS_REQUEST_ANOTHER_TECHNICIAN = 70;
    const STATUS_NOT_COMPLETED = 80;
    const STATUS_REQUEST_COMPLETION = 90;
    const STATUS_UNABLE_TO_ACCESS = 100;
    const STATUS_REQUEST_DIFFERENT_TECHNICIAN = 110;
    // const STATUS_REQUEST_COMPLETION = 90;
    // const STATUS_UNABLE_TO_ACCESS = 100;

    // Schedule
    const SCHEDULE_RIGHT_NOW = 10;
    const SCHEDULE_NEXT_BUSINESS_DAY = 20;
    const SCHEDULE_SCHEDULED = 30;
    // TYPE
    const TYPE_REACTIVE = 10;
    const TYPE_CORRECTIVE = 20;
    const TYPE_PPM = 30;
    const TYPE_BREAKDOWN = 40;
    const TYPE_SCHEDULED_WORK = 50;
    const TYPE_WORK = 60;

    //Scenario
    const SCENARIO_CREATE_ADMIN = "create_from_admin";
    const SCENARIO_CREATE_CRONJOB = "create_from_cronjob";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'repair_request';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_type', 'status', 'created_by', 'updated_by', 'completed_by', 'owner_id', 'team_leader_id', 'urgent_status', 'division_id', 'project_id', 'need_review', 'technician_from_another_division', 'sector_id', 'template_id'], 'integer'],
            [['equipment_id', 'requested_at', 'scheduled_at', 'informed_at', 'arrived_at', 'departed_at', 'created_at', 'updated_at', 'assigned_at', 'completed_at', 'location_id', 'sector_id'], 'safe'],
            [['description', 'problem', 'service_note', 'admin_signature', 'coordinator_note'], 'string'],
            [['labor_charge'], 'number'],
            [['random_token', 'note', 'reported_by_name', 'reported_by_phone', 'notification_id', 'repair_request_path'], 'string', 'max' => 255],
            [['customer_signature', 'technician_signature', 'supervisor_note', 'supervisor_signature', 'coordinator_signature'], 'string'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['category_id' => 'id']],
            // [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['owner_id' => 'id']],
            [['team_leader_id'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['team_leader_id' => 'id']],
            [['repair_request_path', 'service_type', 'scheduled_at', 'category_id'], 'required']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE_ADMIN] = $scenarios['default'];
        $scenarios[self::SCENARIO_CREATE_CRONJOB] = ['service_type', 'category_id'];
        return $scenarios;
    }

    public function validateTimeFlow($attribute, $params)
    {
        if (!empty($this->requested_at) && !empty($this->scheduled_at)) {
            if ($this->requested_at > $this->scheduled_at) {
                $this->addError($attribute, $this->getAttributeLabel("requested_at") . " cannot be after " . $this->getAttributeLabel("scheduled_at"));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression("now()"),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_DRAFT => Yii::t("app", "Draft"),
                    self::STATUS_CREATED => Yii::t("app", "Created"),
                    self::STATUS_CHECKED_IN => Yii::t("app", "Checked In"),
                    self::STATUS_ON_HOLD => Yii::t("app", "On Hold"),
                    self::STATUS_CANCELLED => Yii::t("app", "Cancelled"),
                    self::STATUS_COMPLETED => Yii::t("app", "Completed"),
                    self::STATUS_NOT_COMPLETED => Yii::t("app", "Not Complete"),
                    self::STATUS_REQUEST_COMPLETION => Yii::t("app", "Request Completion"),
                    self::STATUS_UNABLE_TO_ACCESS => Yii::t("app", "Unable to access"),
                    self::STATUS_REQUEST_DIFFERENT_TECHNICIAN => Yii::t("app", "Different Technician"),
                    // self::STATUS_REQUEST_COMPLETION => Yii::t("app", "Request Completion"),
                    // self::STATUS_UNABLE_TO_ACCESS => Yii::t("app", "Request Unable to Access"),

                ]
            ],
            // 'schedule'  => [
            //     'class'     => OptionsBehavior::className(),
            //     'attribute' => 'schedule',
            //     'options'   => [
            //         self::SCHEDULE_RIGHT_NOW         => Yii::t("app", "Right now"),
            //         self::SCHEDULE_NEXT_BUSINESS_DAY => Yii::t("app", "Next business day"),
            //         self::SCHEDULE_SCHEDULED         => Yii::t("app", "Scheduled"),
            //     ]
            // ],
            'type' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'type',
                'options' => [
                    self::TYPE_REACTIVE => Yii::t("app", "Reactive"),
                    //self::TYPE_MAINTENANCE => Yii::t("app", "Maintenance"),
                ]
            ],
            'service_type' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'service_type',
                'options' => [
                    self::TYPE_REACTIVE => Yii::t("app", "Reactive"),
                    self::TYPE_CORRECTIVE => Yii::t("app", "Corrective"),
                    self::TYPE_PPM => Yii::t("app", "PPM"),
                    self::TYPE_BREAKDOWN => Yii::t("app", "Breakdown"),
                    self::TYPE_SCHEDULED_WORK => Yii::t("app", "Scheduled Work"),
                    self::TYPE_WORK => Yii::t("app", "Work"),
                    //self::TYPE_MAINTENANCE => Yii::t("app", "Maintenance"),
                ]
            ],

            'customer_signature' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'customer_signature',
                'createThumbsOnRequest' => true,
                'thumbs' => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'defaultUrl' => Yii::getAlias('@staticWeb') . '/images/signature-default.jpg',
                'filePath' => '@static/upload/images/customer_signature/customer_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl' => '@staticWeb/upload/images/customer_signature/customer_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath' => '@static/upload/images/customer_signature/[[profile]]/customer_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl' => '@staticWeb/upload/images/customer_signature/[[profile]]/customer_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            ],
            'technician_signature' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'technician_signature',
                'createThumbsOnRequest' => true,
                'thumbs' => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'defaultUrl' => Yii::getAlias('@staticWeb') . '/images/signature-default.jpg',
                'filePath' => '@static/upload/images/technician_signature/technician_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl' => '@staticWeb/upload/images/technician_signature/technician_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath' => '@static/upload/images/technician_signature/[[profile]]/technician_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl' => '@staticWeb/upload/images/technician_signature/[[profile]]/technician_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            ],
            'supervisor_signature' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'supervisor_signature',
                'createThumbsOnRequest' => true,
                'thumbs' => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'defaultUrl' => Yii::getAlias('@staticWeb') . '/images/signature-default.jpg',
                'filePath' => '@static/upload/images/supervisor_signature/supervisor_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl' => '@staticWeb/upload/images/supervisor_signature/supervisor_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath' => '@static/upload/images/supervisor_signature/[[profile]]/supervisor_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl' => '@staticWeb/upload/images/supervisor_signature/[[profile]]/supervisor_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            ],
            'coordinator_signature' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'coordinator_signature',
                'createThumbsOnRequest' => true,
                'thumbs' => [
                    'thumb' => ['width' => 250, 'height' => 250],
                ],
                'defaultUrl' => Yii::getAlias('@staticWeb') . '/images/signature-default.jpg',
                'filePath' => '@static/upload/images/coordinator_signature/coordinator_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'fileUrl' => '@staticWeb/upload/images/coordinator_signature/coordinator_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
                'thumbPath' => '@static/upload/images/coordinator_signature/[[profile]]/coordinator_signature_[[pk]]_[[attribute_random_token]].[[extension]]',
                'thumbUrl' => '@staticWeb/upload/images/coordinator_signature/[[profile]]/coordinator_signature_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            ],
            // 'hard_copy_report'     => [
            //     'class'                 => ImageUploadBehavior::className(),
            //     'attribute'             => 'hard_copy_report',
            //     'createThumbsOnRequest' => true,
            //     'thumbs'                => [
            //         'thumb' => ['width' => 250, 'height' => 250],
            //     ],
            //     'defaultUrl'            => Yii::getAlias('@staticWeb') . '/images/placeholder.jpg',
            //     'filePath'              => '@static/upload/images/hard_copy_report/hard_copy_report_[[pk]]_[[attribute_random_token]].[[extension]]',
            //     'fileUrl'               => '@staticWeb/upload/images/hard_copy_report/hard_copy_report_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            //     'thumbPath'             => '@static/upload/images/hard_copy_report/[[profile]]/hard_copy_report_[[pk]]_[[attribute_random_token]].[[extension]]',
            //     'thumbUrl'              => '@staticWeb/upload/images/hard_copy_report/[[profile]]/hard_copy_report_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
            // ],

            'random_token' => [
                'class' => RandomTokenBehavior::className(),
                'attributes' => ['random_token'],
            ],
            'extra_cost' => [
                'class' => PriceFormatBehavior::className(),
                'attribute' => 'extra_cost',
                'currency' => 'USD'
            ],
            "datetimeformatter" => [
                "class" => DateFormatBehavior::className(),
                "type" => DateFormatBehavior::TYPE_DATE_TIME,
                "attributes" => ['assigned_at', 'completed_at', 'requested_at', 'informed_at', 'arrived_at', 'departed_at', 'created_at', 'updated_at'],
            ],
            "datetimeformatter2" => [
                "class" => DateFormatBehavior::className(),
                "type" => DateFormatBehavior::TYPE_DATE_TIME,
                "attributes" => ['eta', 'scheduled_at'],
                "format" => "E, d LLL h:mma"
            ],
            //    'multilingual' => [
            //        'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            //        'attributes' => []
            //    ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment_id' => 'Equipment',
            'service_type' => 'Service Type',
            'requested_at' => 'Requested At',
            'scheduled_at' => 'Scheduled For',
            'informed_at' => 'Informed At',
            'arrived_at' => 'Arrived At',
            'departed_at' => 'Departed At',
            'labor_charge' => 'Labor Charge (H)',
            'sector_id' => 'Sector ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'assigned_at' => 'Assigned At',
            'customer_signature' => 'Requester Signature',
            'random_token' => 'Random Token',
            'completed_at' => 'Completed At',
            'note' => 'Technician Note',
            'technician_signature' => 'Technician Signature',
            'coordinator_signature' => 'Coordinator Signature',
            'reported_by_name' => 'Reported By Name',
            'reported_by_phone' => 'Reported By Phone',
            'notification_id' => 'Notification',
            'completed_by' => 'Completed By',
            'owner_id' => 'Supervisor',
            'team_leader_id' => 'Team Leader',
            'description' => 'Description',
            'urgent_status' => 'Is Urgent',
            'division_id' => 'Division',
            'project_id' => 'Project',
            'location_id' => 'Location',
            'category_id' => 'Category',
            'repair_request_path' => 'Repair Request Path',
            'need_review' => 'Confirmation needed from supervisor',
            'technician_from_another_division' => 'Technician From Another Division',
            'technician_id' => 'Assignees',
            'supervisor_note' => 'Supervisor Note',
            'supervisor_signature' => 'Supervisor Signature',
            'coordinator_note' => 'Complaints'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMallPpmTasksHistories()
    {
        return $this->hasMany(MallPpmTasksHistory::className(), ['ppm_service_id' => 'id']);
    }
    public function getRepairRequestFiles()
    {
        return $this->hasMany(RepairRequestFiles::className(), ['repair_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequestChats()
    {
        return $this->hasMany(RepairRequestChats::className(), ['request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPpmAdditionalTasksValues()
    {
        return $this->hasMany(PpmAdditionalTasksValues::className(), ['ppm_service_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlantPpmTasksHistories()
    {
        return $this->hasMany(PlantPpmTasksHistory::className(), ['ppm_service_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignees()
    {
        return $this->hasMany(Assignee::className(), ['repair_request_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssigneesArr()
    {
        return $this->hasMany(Assignee::className(), ['repair_request_id' => 'id'])->asArray();
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompletedMaintenanceTasks()
    {
        return $this->hasMany(CompletedMaintenanceTask::className(), ['repair_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasOne(Gallery::className(), ['id' => 'gallery_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLineItems()
    {
        return $this->hasMany(LineItem::className(), ['repair_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Log::className(), ['repair_request_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSector()
    {
        return $this->hasOne(Sector::className(), ['id' => 'sector_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
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
    public function getTemplate()
    {
        return $this->hasOne(VillaPpmTemplates::className(), ['id' => 'template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(LocationEquipments::className(), ['id' => 'equipment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Technician::className(), ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamLeader()
    {
        return $this->hasOne(Technician::className(), ['id' => 'team_leader_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequestMaintenanceTasks()
    {
        return $this->hasMany(RepairRequestMaintenanceTask::className(), ['repair_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenanceTaskGroups()
    {
        return $this->hasMany(MaintenanceTaskGroup::className(), ['id' => 'maintenance_task_group_id'])->viaTable('repair_request_maintenance_task', ['repair_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequestRatings()
    {
        return $this->hasMany(RepairRequestRating::className(), ['repair_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceLogs()
    {
        return $this->hasMany(ServiceLog::className(), ['service_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicianLocations()
    {
        return $this->hasMany(TechnicianLocation::className(), ['repair_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserBreaks()
    {
        return $this->hasMany(UserBreak::className(), ['repair_request_id' => 'id']);
    }

    public function getNextStatus($label = null)
    {
        $list = $this->status_list;
        $logged_in_for_backend = @Account::getAdminAccountTypeModel()->for_backend;

        $out = [];

        $out[$this->status] = [
            "id" => $this->status,
            "value" => $this->status_label,
            "color" => $this->getStatusColor($this->status),
            "selected" => true
        ];

        switch ($this->status) {
            case self::STATUS_DRAFT:
                // $out[] = [
                //     "id" => self::STATUS_CREATED,
                //     "value" => $list[self::STATUS_CREATED],
                //     "color" => $this->getStatusColor(self::STATUS_CREATED),
                //     "selected" => false
                // ];
                // $out[] = ["id" => self::STATUS_CANCELLED, "value" => $list[self::STATUS_CANCELLED], "color" => $this->getStatusColor(self::STATUS_CANCELLED), "selected" => false];
                break;
            case self::STATUS_CREATED:
                $out[] = ["id" => self::STATUS_CHECKED_IN, "value" => $list[self::STATUS_CHECKED_IN], "color" => $this->getStatusColor(self::STATUS_CHECKED_IN), "selected" => false];
                $out[] = ["id" => self::STATUS_UNABLE_TO_ACCESS, "value" => $list[self::STATUS_UNABLE_TO_ACCESS], "color" => $this->getStatusColor(self::STATUS_UNABLE_TO_ACCESS), "selected" => false];
                $out[] = ["id" => self::STATUS_REQUEST_DIFFERENT_TECHNICIAN, "value" => $list[self::STATUS_REQUEST_DIFFERENT_TECHNICIAN], "color" => $this->getStatusColor(self::STATUS_REQUEST_DIFFERENT_TECHNICIAN), "selected" => false];
                break;
            case self::STATUS_CHECKED_IN:
                $out[] = ["id" => self::STATUS_ON_HOLD, "value" => $list[self::STATUS_ON_HOLD], "color" => $this->getStatusColor(self::STATUS_ON_HOLD), "selected" => false];
                $out[] = ["id" => self::STATUS_REQUEST_DIFFERENT_TECHNICIAN, "value" => $list[self::STATUS_REQUEST_DIFFERENT_TECHNICIAN], "color" => $this->getStatusColor(self::STATUS_REQUEST_DIFFERENT_TECHNICIAN), "selected" => false];

                // $out[] =  ["id" => self::STATUS_COMPLETED, "value" => $list[self::STATUS_COMPLETED]];
                // $out[] =  ["id" => self::STATUS_NOT_COMPLETED, "value" => $list[self::STATUS_NOT_COMPLETED]];
                // $out[] =  ["id" => self::STATUS_REQUEST_ANOTHER_TECHNICIAN, "value" => $list[self::STATUS_REQUEST_ANOTHER_TECHNICIAN]];
                // $out[] =  ["id" => self::STATUS_REQUEST_COMPLETION, "value" => $list[self::STATUS_REQUEST_COMPLETION]];
                break;
            case self::STATUS_ON_HOLD:
                $out[] = [
                    "id" => self::STATUS_CHECKED_IN,
                    "value" => $list[self::STATUS_CHECKED_IN],
                    "color" => $this->getStatusColor(self::STATUS_CHECKED_IN),
                    "selected" => false
                ];
                $out[] = ["id" => self::STATUS_REQUEST_DIFFERENT_TECHNICIAN, "value" => $list[self::STATUS_REQUEST_DIFFERENT_TECHNICIAN], "color" => $this->getStatusColor(self::STATUS_REQUEST_DIFFERENT_TECHNICIAN), "selected" => false];

                break;
            // case self::STATUS_REQUEST_ANOTHER_TECHNICIAN:
            //     $out[] = ["id" => self::STATUS_COMPLETED, "value" => $list[self::STATUS_COMPLETED]];
            //     $out[] = ["id" => self::STATUS_NOT_COMPLETED, "value" => $list[self::STATUS_NOT_COMPLETED]];
            //     $out[] =  ["id" => self::STATUS_REQUEST_COMPLETION, "value" => $list[self::STATUS_REQUEST_COMPLETION]];
            //     break;
            case self::STATUS_UNABLE_TO_ACCESS:
                $out[] = [
                    "id" => self::STATUS_CHECKED_IN,
                    "value" => $list[self::STATUS_CHECKED_IN],
                    "color" => $this->getStatusColor(self::STATUS_CHECKED_IN),
                    "selected" => false
                ];
                break;
            case self::STATUS_REQUEST_COMPLETION:
                if ($logged_in_for_backend) {
                    $out[] = [
                        "id" => self::STATUS_NOT_COMPLETED,
                        "value" => $list[self::STATUS_NOT_COMPLETED],
                        "color" => $this->getStatusColor(self::STATUS_NOT_COMPLETED),
                        "selected" => false
                    ];
                    $out[] = [
                        "id" => self::STATUS_COMPLETED,
                        "value" => $list[self::STATUS_COMPLETED],
                        "color" => $this->getStatusColor(self::STATUS_COMPLETED),
                        "selected" => false
                    ];
                }
                break;
            case self::STATUS_NOT_COMPLETED:
                if ($logged_in_for_backend) {
                    $out[] = [
                        "id" => self::STATUS_COMPLETED,
                        "value" => $list[self::STATUS_COMPLETED],
                        "color" => $this->getStatusColor(self::STATUS_COMPLETED),
                        "selected" => false
                    ];
                }
                break;
        }



        if (!empty($label)) {
            return ArrayHelper::map($out, 'id', 'value');
        }

        return $out;
    }

    public function getStatusColor($id)
    {
        switch ($id) {
            case 10:
                return "success";

            case 20:
                return "success";

            case 30:
                return "green";

            case 40:
                return "hold";

            case 50:
                return "success";

            case 60:
                return "success";

            case 70:
                return "success";

            case 80:
                return "not_complete";

            case 90:
                return "success";

            case 100:
                return "danger";

            case 110:
                return "request_different_technician";
        }
    }

    public function getNextServiceType($label = null)
    {
        $list = $this->service_type_list;
        $loggedInDivision = Account::getAdminDivisionID();
        $out = [];

        if (empty($loggedInDivision)) {
            return $list;
        }

        switch ($loggedInDivision) {
            case Division::DIVISION_VILLA:
                $out[] = ["id" => self::TYPE_BREAKDOWN, "value" => $list[self::TYPE_BREAKDOWN]];
                $out[] = ["id" => self::TYPE_SCHEDULED_WORK, "value" => $list[self::TYPE_SCHEDULED_WORK]];
                $out[] = ["id" => self::TYPE_WORK, "value" => $list[self::TYPE_WORK]];
                $out[] = ["id" => self::TYPE_PPM, "value" => $list[self::TYPE_PPM]];
                break;
            case Division::DIVISION_MALL:
                $out[] = ["id" => self::TYPE_CORRECTIVE, "value" => $list[self::TYPE_CORRECTIVE]];
                $out[] = ["id" => self::TYPE_REACTIVE, "value" => $list[self::TYPE_REACTIVE]];
                $out[] = ["id" => self::TYPE_SCHEDULED_WORK, "value" => $list[self::TYPE_SCHEDULED_WORK]];
                $out[] = ["id" => self::TYPE_PPM, "value" => $list[self::TYPE_PPM]];
                break;
            case Division::DIVISION_PLANT:
                $out[] = ["id" => self::TYPE_BREAKDOWN, "value" => $list[self::TYPE_BREAKDOWN]];
                $out[] = ["id" => self::TYPE_PPM, "value" => $list[self::TYPE_PPM]];
                break;
            default:
                $out = $list;
                break;
        }

        if (!empty($label)) {
            return ArrayHelper::map($out, 'id', 'value');
        }

        return $out;
    }

    public function getRepairTechnicians()
    { // from the same main sector of the repair order location

        $main_sector_id = $this->location->sector->main_sector_id; // of the repair location
        $out = [];

        Technician::$request_id = $this->id;
        Technician::$return_fields = Technician::FIELDS_MINIMUM;

        $technicians = Technician::find()->where([
            'main_sector_id' => $main_sector_id,
            'status' => Technician::STATUS_ENABLED
        ])
            ->all();


        if ($this->status == self::STATUS_COMPLETED) {
            $assignees = Assignee::find()->where(['repair_request_id' => $this->id])->asArray()->all(); // of the repair location
            $technicians = Technician::find()->where(['id' => ArrayHelper::getColumn($assignees, 'user_id')])->all();
        }

        if (!empty($technicians)) {
            foreach ($technicians as $tech) {

                if ($tech->id == $this->owner_id) {
                    array_unshift($out, $tech);
                } else {
                    $out[] = $tech;
                }

                //         // $professionName = $tech->profession->name;

                //         // // Check if the profession ID already exists in $out
                //         // if (isset($out[$professionName])) {
                //         //     // Key exists, add the technician to the existing profession array
                //         //     $out[$professionName][] = $tech;
                //         // } else {
                //         //     // Key doesn't exist, create a new row with the technician in an array
                //         $out[] = $tech;
                //         // }
            }
        }

        return $out;
    }

    public function getAcceptanceRepairTechnicians()
    { // from the same main sector of the repair order location

        $assignees = Assignee::find()->where(['repair_request_id' => $this->id, 'status' => Assignee::STATUS_ASSIGNED])->asArray()->all(); // of the repair location

        Technician::$request_id = $this->id;
        Technician::$return_fields = Technician::FIELDS_MINIMUM;
        $technicians = Technician::find()->where(['id' => ArrayHelper::getColumn($assignees, 'user_id')])->all();

        if (!empty($technicians)) {
            return $technicians;
        }

        return null;
    }

    public function getExternalRepairTechnicians()
    { // from the same main sector of the repair order location

        $out = [];

        Technician::$return_fields = Technician::FIELDS_MINIMUM;

        $technicians = Technician::find()->joinWith("assignees")->where([
            'division_id' => Division::DIVISION_VILLA,
            Assignee::tableName() . '.repair_request_id' => $this->id
        ])
            ->all();

        // if (!empty($technicians)) {
        //     foreach ($technicians as $tech) {
        //         $professionName = $tech->profession->name;

        //         // Check if the profession ID already exists in $out
        //         if (isset($out[$professionName])) {
        //             // Key exists, add the technician to the existing profession array
        //             $out[$professionName][] = $tech;
        //         } else {
        //             // Key doesn't exist, create a new row with the technician in an array
        //             $out[$professionName][] = $tech;
        //         }
        //     }
        // }

        return $technicians;
    }


    public static $return_fields = 10;
    const FIELDS_DEFAULT = 10;
    const FIELDS_REPORT = 20;
    const FIELDS_ORIGINAL = 30;
    const FIELDS_EXPORT = 40;
    public function fields()
    {


        switch (self::$return_fields) {


            case self::FIELDS_EXPORT:
                return [
                    'id',
                    'labor_charge',
                    'service_type_label',
                    'status_label',
                    "division" => function (RepairRequest $model) {
                        return $model->division->name;
                    },
                    "location" => function (RepairRequest $model) {
                        return $model->location->name;
                    },
                    'repair request path' => function (RepairRequest $model) {
                        return $model->repair_request_path;
                    },
                    'owner ' => function (RepairRequest $model) {
                        return @$model->owner->name;
                    },
                    'assignees' => function (RepairRequest $model) {
                        // return $model->getAssigneesDetails();
                        return str_replace('<br />', " - ", $model->getAssigneesDetails());
                    },

                    'team_leader_id' => function (RepairRequest $model) {
                        return @$model->teamLeader->name;
                    },
                    'created_at',
                    'created_by' => function ($model) {
                        return $this->getBlamable($model->created_by);
                    },
                    'completed_at',

                ];

            case self::FIELDS_ORIGINAL:
                return parent::fields();
            case self::FIELDS_DEFAULT:
            default:

                return [
                    "id",
                    "customer" => function (RepairRequest $model) {
                        return (!empty($model->equipment)
                            && !empty($model->equipment->location)
                            && !empty($model->equipment->location->customer))
                            ? [
                                'name' => $model->equipment->location->customer->name,
                                'phone' => $model->equipment->location->customer->phone,
                            ]
                            : [
                                'name' => null,
                                'phone' => null,
                            ];
                    },
                    "user" => function (RepairRequest $model) {
                        return !empty($model->user)
                            ? [
                                'name' => $model->user->name,
                                'phone' => $model->user->phone_number,
                            ]
                            : [
                                'name' => $model->reported_by_name,
                                'phone' => $model->reported_by_phone,
                            ];
                    },
                    "is_for_today" => function (RepairRequest $model) {
                        return date('Y-m-d') >= date('Y-m-d', strtotime($model->scheduled_at . ' UTC'));
                    },
                    "report_url" => function ($model) {
                        $path = Yii::getAlias("@static/upload/reports/client/{$model->random_token}.pdf");
                        $url = Yii::getAlias("@staticWeb/upload/reports/client/{$model->random_token}.pdf");
                        return file_exists($path) ? $url : null;
                    },
                    "equipment_id" => function ($model) {
                        return $model->equipment_id;
                    },
                    "equipment" => function ($model) {
                        return !empty($model->equipment_id) ? $model->equipment->equipment->name : (empty($model->category_id) ? null : $model->category->name);
                    },
                    "equipment_code" => function ($model) {
                        if (!empty($model->equipment)) {
                            if ($model->division_id == Division::DIVISION_PLANT) {
                                $json_value = json_decode(Equipment::getJsonEquipmentCustomAttributes($model->equipment->equipment_id, $model->equipment_id), true);
                                return !empty(array_column($json_value, 'value', 'layer')['Plate no']) ? "<span style='color:red; font-size:18px;'>" . array_column($json_value, 'value', 'layer')['Plate no'] . "</span>" : $model->equipment->code;
                            }
                        }
                        return !empty($model->equipment) ? $model->equipment->code : null;
                    },
                    "equipment_meter_value" => function ($model) {
                        if (!empty($model->equipment)) {
                            if ($model->division_id == Division::DIVISION_PLANT) {
                                return $model->equipment->meter_value;
                            }
                        }
                        return null;
                    },
                    "meter_types" => function ($model) {
                        if (!empty($model->equipment_id)) {
                            if ($model->division_id == Division::DIVISION_PLANT) {
                                $motor_fuel_type = $model->equipment->motor_fuel_type;

                                if (!empty($motor_fuel_type)) {
                                    return EngineOilTypes::find()->where(['motor_fuel_type_id' => $motor_fuel_type])->all();
                                }
                            }
                        }
                        return null;
                    },
                    "technician" => function ($model) {
                        return !empty($model->technician) ?
                            (!empty($model->technician->title) ? $model->technician->title . ' ' : '') .
                            $model->technician->name : null;
                    },
                    "technician_image_url" => function ($model) {
                        return !empty($model->technician) ? "data:image/png;base64," . base64_encode(
                            file_get_contents(
                                $model->technician->image_thumb_70_url,
                                false,
                                stream_context_create(
                                    array(
                                        "ssl" => array(
                                            "verify_peer" => false,
                                            "verify_peer_name" => false,
                                        ),
                                    )
                                )
                            )
                        ) : null;
                    },
                    "location" => function ($model) {

                        if (!empty($model->location)) {
                            if ($model->division_id == Division::DIVISION_VILLA) {
                                return $model->location->code;
                            }

                            return $model->location->name;
                        }

                        return null;
                    },
                    "division_id" => function ($model) {
                        return $model->division_id;
                    },
                    "location_code" => function ($model) {
                        return (!empty($model->location) && !empty($model->location))
                            ? $model->location->code : null;
                    },
                    "location_address" => function ($model) {
                        return (!empty($model->location) && !empty($model->location))
                            ? $model->location->address : null;
                    },
                    "location_location" => function ($model) {
                        return (!empty($model->location) && !empty($model->location))
                            ? [
                                'latitude' => $model->location->latitude,
                                'longitude' => $model->location->longitude,
                            ] : null;
                    },
                    'service_type_label',
                    'requested_at_formatted',
                    'assigned_at_formatted',
                    'completed_at_formatted',
                    'scheduled_at',
                    'scheduled_at_formatted',
                    'informed_at_formatted',
                    'arrived_at_formatted',
                    'scheduled_at_date' => function ($model) {
                        return !empty($model->scheduled_at) ? Yii::$app->getFormatter()->asDate($model->scheduled_at) :
                            Yii::$app->getFormatter()->asDate(gmdate("Y-m-d"));
                    },
                    'arrived_at_time' => function ($model) {
                        return Yii::$app->getFormatter()->asTime($model->arrived_at);
                    },
                    'departed_at_formatted',
                    'lineItems',
                    'response_time' => function (RepairRequest $model) {
                        return floor($model->calculateResponseTime());
                    },
                    'note',
                    'is_urgent' => function ($model) {
                        return ($model->urgent_status == 1 ? 'Urgent' : 'Normal');
                    },
                    'status',
                    'status_label',
                    'problem',
                    'created_by' => function ($model) {
                        return $this->getBlamable($model->created_by);
                    },
                    'supervisor' => function ($model) {
                        return @$model->owner->name;
                    },
                    'team_leader_id' => function ($model) {
                        return @$model->teamLeader->id;
                    },
                    'team_leader' => function ($model) {
                        return @$model->teamLeader->name;
                    },
                    'team' => function ($model) {
                        return explode('<br />', @$model->getAssigneesDetails());
                    },
                    'team_leaders' => function ($model) {
                        return @$model->getAssigneesInfos();
                    },
                    'owner_id' => function ($model) {
                        return $model->owner_id;
                    },
                    'available_statuses' => function ($model) {
                        return $model->getNextStatus();
                    },
                    'available_technicians' => function ($model) {
                        return $model->getRepairTechnicians();
                    },
                    'acceptance_technicians' => function ($model) {
                        return $model->getAcceptanceRepairTechnicians();
                    },
                    'external_technicians' => function ($model) {
                        if ($model->division_id == Division::DIVISION_MALL && $model->technician_from_another_division)
                            return $model->getExternalRepairTechnicians();
                    },
                    'selected_technicians_ids' => function ($model) {
                        return ArrayHelper::getColumn($model->assignees, 'user_id');
                    },
                    'division' => function ($model) {
                        return $model->division->name;
                    },
                    'main_Sector' => function ($model) {
                        return $model->location->sector->mainSector->name;
                    },
                    'repair_request_path',
                    'note',
                    "customer_signature_url" => function ($model) {
                        return !empty($model->customer_signature) ? "data:image/png;base64," . base64_encode(
                            file_get_contents(
                                $model->customer_signature_url,
                                false,
                                stream_context_create(
                                    array(
                                        "ssl" => array(
                                            "verify_peer" => false,
                                            "verify_peer_name" => false,
                                        ),
                                    )
                                )
                            )
                        ) : null;
                    },
                    "technician_signature_url" => function ($model) {
                        return !empty($model->technician_signature) ? "data:image/png;base64," . base64_encode(
                            file_get_contents(
                                $model->technician_signature_url,
                                false,
                                stream_context_create(
                                    array(
                                        "ssl" => array(
                                            "verify_peer" => false,
                                            "verify_peer_name" => false,
                                        ),
                                    )
                                )
                            )
                        ) : null;
                    },
                    "coordinator_signature_url" => function ($model) {
                        return !empty($model->coordinator_signature) ? "data:image/png;base64," . base64_encode(
                            file_get_contents(
                                $model->coordinator_signature_url,
                                false,
                                stream_context_create(
                                    array(
                                        "ssl" => array(
                                            "verify_peer" => false,
                                            "verify_peer_name" => false,
                                        ),
                                    )
                                )
                            )
                        ) : null;
                    },
                    "supervisor_signature_url" => function ($model) {
                        return !empty($model->supervisor_signature) ? "data:image/png;base64," . base64_encode(
                            file_get_contents(
                                $model->supervisor_signature_url,
                                false,
                                stream_context_create(
                                    array(
                                        "ssl" => array(
                                            "verify_peer" => false,
                                            "verify_peer_name" => false,
                                        ),
                                    )
                                )
                            )
                        ) : null;
                    },
                    'report_images' => function ($model) {

                        $out = null;

                        if (!empty($model->gallery)) {
                            $images = $model->gallery->images;

                            if (!empty($images)) {
                                foreach ($images as $key => $image) {
                                    $out[] = [
                                        "image" => $image->image_url,
                                        "note" => $image->note,
                                    ];
                                }
                            }
                        }

                        return $out;
                    },
                    'messages' => function ($model) {
                        return $model->repairRequestChats;
                    },
                    'supervisor_note',
                    'service_tasks' => function ($model) {
                        return $model->getPpmTasks();
                    },
                    'technician_from_another_division',
                    'service_note',
                    'color' => function ($model) {
                        return $model->getStatusColor($model->status);
                    },
                    'allowed_distance' => function ($model) {
                        return Setting::getValue('nearby_distance');
                    },
                    'meter_type_label' => function ($model) {
                        if (!empty($model->equipment_id)) {
                            return "(" . $model->equipment->equipment->equipmentType->meter_type_label . ")";
                        }
                    }
                ];
        }
    }

    // public function beforeValidate()
    // {
    //     if (empty($this->requested_at)) {
    //         $this->requested_at = gmdate("Y-m-d H:i:s");
    //     }
    //     if (!empty($this->user_id)) {
    //         $this->reported_by_name = $this->user->name;
    //         $this->reported_by_phone = $this->user->phone_number;
    //     }
    //     return parent::beforeValidate(); // TODO: Change the autogenerated stub
    // }

    public function beforeDelete()
    {
        $this->log("Deleted the service");
        $deletedService = new DeletedService();
        $deletedService->service_id = $this->id;
        RepairRequest::$return_fields = RepairRequest::FIELDS_ORIGINAL;

        $json = Json::encode($this);
        $logs = Json::encode($this->getServiceLogs()->asArray()->all());

        $deletedService->model = $json;
        $deletedService->logs = $logs;
        $deletedService->save();

        return parent::beforeDelete();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        //Delete all tech and user notifications for this order
        Notification::deleteAll(['relation_key' => $this->id]);
        Notification::deleteAll(['mobile_action' => '{"action":"view-service","id":"' . $this->id . '"}']);
        Notification::deleteAll(['mobile_action' => '{"action":"view-service","id":' . $this->id . '}']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        // if ($insert) {
        //     $this->log("Created the service for {$this->equipment->name}[{$this->equipment->code}] in {$this->equipment->location->name}[{$this->equipment->location->code}]");
        // }
    }

    public function log($message, $username = null)
    {
        ServiceLog::log($this->id, $message, $username);
    }

    public function generatePdfReport($preview = false, $forceOverride = false, $inBrowser = false)
    {
        $model = $this;
        //generate Invoice
        if (empty($model->random_token)) {
            $model->random_token = Yii::$app->getSecurity()->generateRandomString();
            $model->save(false);
        }
        $path = Yii::getAlias("@static/upload/reports");
        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => [148, 210],
            //A5 => 148mm x 210mm
            'content' => Yii::$app->controller->renderPartial("@admin/views/_repair_report", [
                'model' => $model
            ]),
            'options' => [
                'title' => \Yii::t("app", "Repair Report #{id}", ['id' => $model->id]),
                'fontDir' => array_merge($fontDirs, [
                    Yii::getAlias("@static/fonts"),
                ]),
                'fontdata' => [
                    'tajawal' => [
                        'R' => 'almarai-v5-arabic-regular.ttf',
                        'B' => 'almarai-v5-arabic-regular.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ],
                ],
                'default_font' => 'helvetica'
            ],
            'methods' => [
                //                'SetFooter' => ['|{PAGENO}/{nb}|'],
            ],
            'cssInline' => "
                    .en{font-family: helvetica;}
                    .ar{font-family: tajawal;}
                    .blue{color: rgb(55,150,255);}
                    .red{color: #ff3737;}
                    .brightred{color: #ff3737;}
                    .small{font-size: 2.9mm;}
                    .medium{font-size: 3.2mm;}
                    .large{font-size: 3.8mm;}
                    .fw500{font-weight: 500;}
                    .bold{font-weight: bold;}
                    .bolder{font-weight: 900;}
                    .right{text-align: right;}
                    .center{text-align: center;}
                    table.bordered{padding: 7px;border: 0.1mm solid #888;}
                    table.from-to-table td{font-size: 9px;}
                    .border-right{border-right: 0.1mm solid #888;}
                    .border-left{border-left: 0.1mm solid #888;}
                    .border-bottom{border-bottom: 0.1mm solid #888;}
                    .border-bottom-dotted{border-bottom: 0.4mm dotted #888;}
                    .border-all{border: 0.1mm solid #888;border-left: none;}
                    .border-all-l{border: 0.1mm solid #888;border-right: none;}
                    .border-v{border: 0.1mm solid #888;border-left: none;border-right: none;}
                    .padding-left{padding-left: 5px}
                    .all-border-bottom tr td{border-bottom: 0.1mm solid #888; padding-top:2mm}
                    .no-border-bottom tr td{border-bottom: none; padding-top:0mm}
                    .order-details{font-size: 8px;}
                    .order-details tr.title{font-size: 8px;}
                    .order-details tr.title{background-color: #fafafa;}
                    .order-details tr.title th{padding-top: 2px;border-bottom: 0.1mm solid #888;border-top: 0.1mm solid #888;}
                    .order-details tbody tr td{border-bottom: 0.1mm solid #888;}
                    .summary-1{margin-bottom:10px;margin-top:10px;font-size:9pt;padding-top:10px}
                    .summary-table .border-top{border-top: 0.1mm solid #888;}
                    .summary-table .border-bottom{border-bottom: 0.1mm solid #555;}
                ",
            'filename' => $path . DIRECTORY_SEPARATOR . $model->random_token . ".pdf",
            'destination' => 'F',
            //            'destination' => 'I',
        ]);
        $pdf->marginHeader = 0;
        $pdf->marginLeft = 7;
        $pdf->marginRight = 7;
        $pdf->marginTop = 10;
        $pdf->marginBottom = 7;

        $pdf->defaultFont = 'helvetica';
        $pdf->defaultFontSize = 5;

        $pdf->getApi()->AddFontDirectory(Yii::getAlias("@static/fonts"));

        if ($inBrowser) {
            $pdf->destination = Pdf::DEST_BROWSER;
            return $pdf->render();
        }

        $pdf->render();

        if (!$preview) {
            $path2 = Yii::getAlias("@static/upload/reports/client");
            if (!file_exists($path2)) {
                if (!mkdir($path2, 0755, true) && !is_dir($path2)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $path2));
                }
            }
            $copyFile = $path2 . DIRECTORY_SEPARATOR . $model->random_token . ".pdf";
            $originalFile = $path . DIRECTORY_SEPARATOR . $model->random_token . ".pdf";
            if ($forceOverride || !file_exists($copyFile)) {
                copy($originalFile, $copyFile);
            }
        }

        //start save a tif version
        $tifPath = $path . DIRECTORY_SEPARATOR . $model->random_token . ".tif";
        $im2 = new \Imagick();
        $im2->setResolution(100, 100);
        $im2->setCompressionQuality(50);
        $im2->setCompression(\Imagick::COMPRESSION_JPEG);
        $im2->readImage($path . DIRECTORY_SEPARATOR . $model->random_token . ".pdf");
        $im2->setImageFormat("tiff");
        $im2->setImageColorSpace(\Imagick::COLORSPACE_RGB);
        $im2->writeImages($tifPath, true);
        //end save a tif version

        return $originalFile;
    }

    // public function hasWarning()
    // {
    //     if ($this->type == self::TYPE_REQUEST) {
    //         if ($this->status == self::STATUS_PENDING) {
    //             return true;
    //         }
    //         if ($this->status == self::STATUS_ASSIGNED) {
    //             if ($this->person_trapped) {
    //                 if (gmdate("Y-m-d H:i:s") > gmdate("Y-m-d H:i:s", strtotime($this->assigned_at . " UTC +2 minutes"))) {
    //                     return true;
    //                 }
    //             } else {
    //                 if (gmdate("Y-m-d H:i:s") > gmdate("Y-m-d H:i:s", strtotime($this->assigned_at . " UTC +10 minutes"))) {
    //                     return true;
    //                 }
    //             }
    //         }
    //         if ($this->status == self::STATUS_INFORMED) {
    //             if ($this->person_trapped) {
    //                 if (gmdate("Y-m-d H:i:s") > gmdate("Y-m-d H:i:s", strtotime($this->informed_at . " UTC +5 minutes"))) {
    //                     return true;
    //                 }
    //             } else {
    //                 if (gmdate("Y-m-d H:i:s") > gmdate("Y-m-d H:i:s", strtotime($this->informed_at . " UTC +20 minutes"))) {
    //                     return true;
    //                 }
    //             }
    //         }
    //         if ($this->status == self::STATUS_EN_ROUTE) {
    //             if (gmdate("Y-m-d H:i:s") > gmdate("Y-m-d H:i:s", strtotime($this->eta . " UTC +5 minutes"))) {
    //                 return true;
    //             }
    //         }
    //         if ($this->status == self::STATUS_ARRIVED) {
    //             if (gmdate("Y-m-d H:i:s") > gmdate("Y-m-d H:i:s", strtotime($this->arrived_at . " UTC +35 minutes"))) {
    //                 return true;
    //             }
    //         }
    //         if ($this->status == self::STATUS_DEPARTED) {
    //             if (gmdate("Y-m-d H:i:s") > gmdate("Y-m-d H:i:s", strtotime($this->departed_at . " UTC +1 day"))) {
    //                 return true;
    //             }
    //         }
    //     }
    //     if ($this->type == self::TYPE_SCHEDULED) {
    //         if ($this->status == self::STATUS_PENDING) {
    //             return true;
    //         }
    //         if (gmdate("Y-m-d") > gmdate("Y-m-d", strtotime($this->scheduled_at . '  UTC + 30 minutes'))) {
    //             return true;
    //         }

    //         if ($this->status == self::STATUS_EN_ROUTE) {
    //             if (gmdate("Y-m-d H:i:s") > gmdate("Y-m-d H:i:s", strtotime($this->eta . " UTC +10 minutes"))) {
    //                 return true;
    //             }
    //         }
    //         if ($this->status == self::STATUS_ARRIVED) {
    //             if (gmdate("Y-m-d") > gmdate("Y-m-d", strtotime($this->scheduled_at . " UTC"))) {
    //                 return true;
    //             }
    //         }
    //         if ($this->status == self::STATUS_DEPARTED) {
    //             if (gmdate("Y-m-d") > gmdate("Y-m-d", strtotime($this->departed_at . " UTC"))) {
    //                 return true;
    //             }
    //         }
    //     }
    //     return false;
    // }

    public function calculateResponseTime()
    {
        if (!empty($this->arrived_at)) {
            $time = (strtotime($this->arrived_at) - strtotime($this->scheduled_at)) / 60;
            if ($time < 0) {
                $time = 0;
            }
            return $time;
        }
        return 0;
    }

    /**
     * Get details of assignees for the repair request.
     *
     * @return string Returns a string containing assignee details.
     */
    public function getAssigneesDetails()
    {
        $out = [];
        $assignees = $this->getAssignees()->all();

        if (!empty($assignees)) {

            foreach ($assignees as $assignee) {
                $statuses = ArrayHelper::merge($assignee->status_list, $assignee->acceptance_status_list);
                $out[] = $assignee->user->name . " | " . $assignee->user->account->type0->label . " | " . $assignee->user->profession->name . " | " . $statuses[$assignee->status];
            }
        }

        if (!empty($out)) {
            return implode("<br />", $out);
        } else {
            return null;
        }
    }

    public function getAssigneesInfos()
    {
        $out = [];
        $assignees = $this->getAssignees()->all();

        if (!empty($assignees)) {
            foreach ($assignees as $assignee) {
                $out[] = [
                    "id" => $assignee->user->id,
                    "name" => $assignee->user->name
                ];
            }
        }

        if (!empty($out)) {
            return $out;
        } else {
            return null;
        }
    }

    public function getBlamable($id, $type = null)
    {

        if (!empty($id)) {
            $out = "";

            $technician = @Technician::findOne($id)->name;
            $admin = @Admin::findOne($id)->name;

            if (!empty($technician)) {
                if (!empty($type)) {
                    $out = $technician . " | Technician";
                } else {
                    $out = $technician;
                }
            } else {
                if (!empty($type)) {
                    $out = $admin . " | Admin";
                } else {
                    $out = $admin;
                }
            }

            return $out;
        } else {
            return null;
        }
    }

    public function getStatusTag()
    {
        switch ($this->status) {
            case RepairRequest::STATUS_DRAFT:
                return "<span class='label tag-label label-warning' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_CREATED:
                return "<span class='label tag-label label-primary' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_CHECKED_IN:
                return "<span class='label tag-label label-checked-in' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_ON_HOLD:
                return "<span class='label tag-label label-on-hold' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_COMPLETED:
                return "<span class='label tag-label label-success' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_CANCELLED:
                return "<span class='label tag-label label-danger' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_REQUEST_ANOTHER_TECHNICIAN:
                return "<span class='label tag-label label-another-technician' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_NOT_COMPLETED:
                return "<span class='label tag-label label-not-completed' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_REQUEST_COMPLETION:
                return "<span class='label tag-label label-request-completion' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_UNABLE_TO_ACCESS:
                return "<span class='label tag-label label-unable-to-access' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            case RepairRequest::STATUS_REQUEST_DIFFERENT_TECHNICIAN:
                return "<span class='label tag-label label-unable-to-access' style='position:relative;top:0.5rem;'>" . $this->status_label . "</span>";
                break;
            default:
                return "<span class='label tag-label'>" . $this->status_label . "</span>";
                break;
        }
    }
    public function getStatusDropDownColor()
    {
        return "DropDown_" . $this->getStatusColor($this->status);
    }

    public function checkNotificationEmergency($status, $technician_id, $title, $description)
    {
        if ($status == true) {
            Notification::notifyTechnician(
                $technician_id,
                "<div style='color: #ce222a;font-weight: 700'>{$description}</div>",
                $title,
                [],
                ['/site/index'],
                ['action' => 'view-service', 'id' => $this->id],
                null,
                Notification::TYPE_NOTIFICATION,
                $this->id
            );
        } else {
            Notification::notifyTechnician(
                $technician_id,
                "<div style='color: #00567d;font-weight: 700'>{$description}</div>",
                $title,
                [],
                ['/site/index'],
                ['action' => 'view-service', 'id' => $this->id],
                null,
                Notification::TYPE_NOTIFICATION,
                $this->id
            );
        }

        return true;
    }

    public function createMallPpmTask()
    {
        $asset = $this->equipment_id;
        $hm = date('z') + 1;
        $ET = @$this->equipment->equipment->equipment_type_id;
        $year = (new DateTime())->format("Y");
        $service_id = $this->id;

        if (!empty($service_id)) {
            $command = Yii::$app->db->createCommand('
            INSERT INTO mall_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `year`, `status`, `created_at`, `updated_at`)
            SELECT t.id, (FLOOR(:hm / t.occurence_value) + :year) as occurence, :asset as asset, :service_id as service_id, :year as year, :status as status, NOW(), NOW()
            FROM mall_ppm_tasks_history h
            RIGHT JOIN mall_ppm_tasks t ON h.task_id = t.id AND h.meter_ratio = (FLOOR(:hm / t.occurence_value) + :year) AND h.asset_id = :asset AND h.year = :year
            WHERE FLOOR(:hm / t.occurence_value) > 0 AND h.id IS NULL AND t.equipment_type_id = :ET
        ', [
                ':asset' => $asset,
                ':hm' => $hm,
                ':year' => $year,
                ':service_id' => $service_id,
                ':status' => MallPpmTasksHistory::TASK_STATUS_NOT_DONE,
                ':ET' => $ET,
            ]);

            $command->execute();
        }

        return true;
    }

    public function createPlantPpmTask($service_id, $hm, $meter_type, $asset_id)
    {
        if (!empty($service_id)) {

            $year = (new DateTime())->format("Y");
            $checklist_hm = date('z') + 1;

            $command = Yii::$app->db->createCommand('
            insert into plant_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `status`, `task_type`, `created_at`, `updated_at`)
            select t.id, floor(:hm/t.occurence_value), :asset, :ppm_service_id, :status,t.task_type, NOW(), NOW() from plant_ppm_tasks_history h
            right join plant_ppm_tasks t on h.task_id = t.id and h.meter_ratio = floor(:hm/t.occurence_value) and h.asset_id = :asset
            where floor(:hm/t.occurence_value) > 0 and t.meter_type = :meter_type and h.id is null
        ', [
                ':asset' => $asset_id,
                ':hm' => $hm,
                ':status' => PlantPpmTasksHistory::TASK_STATUS_PENDING,
                ':ppm_service_id' => $service_id,
                ':meter_type' => $meter_type
            ]);

            $command->execute();

            $command = Yii::$app->db->createCommand('
            insert into plant_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `status`,`task_type`,  `created_at`, `updated_at`)
            select t.id, (FLOOR(:hm / t.occurence_value) + :year), :asset, :ppm_service_id, :status,t.task_type, NOW(), NOW() from plant_ppm_tasks_history h
            right join plant_ppm_tasks t on h.task_id = t.id and h.meter_ratio = (FLOOR(:hm / t.occurence_value) + :year) and h.asset_id = :asset
            where t.meter_type = 30 and h.id is null
        ', [
                ':asset' => $asset_id,
                ':hm' => $checklist_hm,
                ':year' => $year,
                ':status' => PlantPpmTasksHistory::TASK_STATUS_NOT_DONE,
                ':ppm_service_id' => $service_id,
            ]);

            $command->execute();
        }

        return true;
    }

    public function createPlantPpmChecklistTask($service_id, $asset_id)
    {
        $year = (new DateTime())->format("Y");
        $checklist_hm = date('z') + 1;

        if (!empty($service_id)) {
            $command = Yii::$app->db->createCommand('
            insert into plant_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `status`,`task_type`,  `created_at`, `updated_at`)
            select t.id, (FLOOR(:hm / t.occurence_value) + :year), :asset, :ppm_service_id, :status,t.task_type, NOW(), NOW() from plant_ppm_tasks_history h
            right join plant_ppm_tasks t on h.task_id = t.id and h.meter_ratio = (FLOOR(:hm / t.occurence_value) + :year) and h.asset_id = :asset
            where  (FLOOR(:hm / t.occurence_value) + :year)>0 and t.meter_type = 30 and h.id is null
        ', [
                ':asset' => $asset_id,
                ':hm' => $checklist_hm,
                ':year' => $year,
                ':status' => PlantPpmTasksHistory::TASK_STATUS_NOT_DONE,
                ':ppm_service_id' => $service_id,
            ]);

            $command->execute();
        }

        return true;
    }

    public function createPlantPpmTaskKm($asset_id, $service_id, $next_oil_change, $oil_result, $filter_result)
    {
        if (!empty($service_id)) {

            //     [0] => Array
            // (
            //     [id] => 1
            //     [meter_ratio] => 1
            //     [asset] => 29249
            //     [status] => 10
            //     [task_type] => 20
            // )

            if (count($oil_result) > 0) {
                $command = Yii::$app->db->createCommand('
                insert into plant_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `status`, `task_type`, `created_at`, `updated_at`)
                Values (:task_id , :meter_ratio , :asset_id , :ppm_service_id , :status , :task_type  , NOW(), NOW())
            ', [
                    ':task_id' => $oil_result[0]['id'],
                    ':meter_ratio' => $oil_result[0]['meter_ratio'],
                    ':asset_id' => $asset_id,
                    ':ppm_service_id' => $service_id,
                    ':task_type' => $oil_result[0]['task_type'],
                    ':status' => PlantPpmTasksHistory::TASK_STATUS_PENDING
                ]);

                $command->execute();

                // OIL DUE TABLE
                $command = Yii::$app->db->createCommand('
                insert into oil_change_due (`repair_request_id`, `asset_id`, `next_oil_change`, `status`, `created_at`, `updated_at`)
                Values (:ppm_service_id ,  :asset_id , :next_oil_change , :status , NOW(), NOW())
            ', [
                    ':ppm_service_id' => $service_id,
                    ':asset_id' => $asset_id,
                    ':next_oil_change' => $next_oil_change,
                    ':status' => OilChangeDue::STATUS_ENABLED
                ]);

                $command->execute();
            }

            if (count($filter_result) > 0) {
                $command = Yii::$app->db->createCommand('
                insert into plant_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `status`, `task_type`, `created_at`, `updated_at`)
                Values (:task_id , :meter_ratio , :asset_id , :ppm_service_id , :status , :task_type  , NOW(), NOW())
            ', [
                    ':task_id' => $filter_result[0]['id'],
                    ':meter_ratio' => $filter_result[0]['meter_ratio'],
                    ':asset_id' => $asset_id,
                    ':ppm_service_id' => $service_id,
                    ':task_type' => $filter_result[0]['task_type'],
                    ':status' => PlantPpmTasksHistory::TASK_STATUS_PENDING
                ]);

                $command->execute();
            }
        }

        return true;
    }

    public function createVillaPpmTask($asset_id, $request_id, $tasks)
    {
        foreach ($tasks as $task) {
            $command = Yii::$app->db->createCommand('
            INSERT INTO villa_ppm_tasks_history (`task_id`, `asset_id`, `ppm_service_id`, `frequency`, `status`, `created_at`, `updated_at`)
            VALUES (:task_id , :asset_id , :request_id , 0 , :status ,  NOW(), NOW())
        ', [
                ':task_id' => $task,
                ':asset_id' => $asset_id,
                ':request_id' => $request_id,
                ':status' => VillaPpmTasksHistory::TASK_STATUS_NOT_DONE,
            ]);

            $command->execute();
        }


        return true;
    }

    public function getPpmTasks()
    {
        if ($this->service_type == RepairRequest::TYPE_PPM || $this->division_id == Division::DIVISION_PLANT) {
            if ($this->division_id == Division::DIVISION_MALL) {
                return $this->getMallPpmTasks();
            } else if ($this->division_id == Division::DIVISION_PLANT) {
                return $this->getPlantPpmTasks();
            } else if ($this->division_id == Division::DIVISION_VILLA) {
                return $this->getVillaPpmTasks();
            }
        }

        return null;
    }

    public function getMallPpmTasks()
    {
        $out = [];


        $ET = @$this->equipment->equipment->equipment_type_id;
        $service_id = $this->id;

        if (!empty($service_id)) {

            $tasks = MallPpmTasksHistory::find()->joinWith('task')->where(['ppm_service_id' => $service_id])->select(['mall_ppm_tasks_history.id', 'mall_ppm_tasks.name', 'mall_ppm_tasks_history.status as current_status'])->asArray()->all();

            $out['tasks'] = $tasks;

            $additional_tasks = PpmAdditionalTasks::find()
                ->join('LEFT JOIN', 'ppm_additional_tasks_values', "ppm_additional_tasks_values.additional_task_id = ppm_additional_tasks.id AND ppm_additional_tasks_values.ppm_service_id = {$service_id}")
                ->where([PpmAdditionalTasks::tableName() . '.equipment_type_id' => $ET])
                ->andWhere(['!=', PpmAdditionalTasks::tableName() . '.name', "FAULTS OBSERVED/REMARKS"])
                ->select([PpmAdditionalTasks::tableName() . '.id', PpmAdditionalTasks::tableName() . '.name', PpmAdditionalTasks::tableName() . '.service', PpmAdditionalTasksValues::tableName() . '.value'])
                // ->createCommand()->rawSql;
                // return $additional_tasks;
                ->asArray()
                ->all();

            $additional_task_remark = PpmAdditionalTasks::find()
                ->join('LEFT JOIN', 'ppm_additional_tasks_values', "ppm_additional_tasks_values.additional_task_id = ppm_additional_tasks.id AND ppm_additional_tasks_values.ppm_service_id = {$service_id}")
                ->where([PpmAdditionalTasks::tableName() . '.equipment_type_id' => $ET])
                ->andWhere(['=', PpmAdditionalTasks::tableName() . '.name', "FAULTS OBSERVED/REMARKS"])
                ->select([PpmAdditionalTasks::tableName() . '.id', PpmAdditionalTasks::tableName() . '.name', PpmAdditionalTasks::tableName() . '.service', PpmAdditionalTasksValues::tableName() . '.value'])
                ->asArray()
                ->one();

            if (!empty($additional_task_remark))
                $out["additional_tasks_remark"] = $additional_task_remark;

            $out["additional_tasks"] = [];
            foreach ($additional_tasks as $task) {
                $name = $task['name'];
                if (!isset($out["additional_tasks"][$name])) {
                    $out["additional_tasks"][$name] = [];
                }
                $out["additional_tasks"][$name][] = $task;
            }
        }

        return $out;
    }

    public function getPlantPpmTasks()
    {
        $out = [];


        $ET = @$this->equipment->equipment->equipment_type_id;
        $service_id = $this->id;

        if (!empty($service_id)) {

            $tasks = PlantPpmTasksHistory::find()->joinWith('task')->where(['ppm_service_id' => $service_id])
                ->andWhere(['plant_ppm_tasks_history.task_type' => PlantPpmTasksHistory::TASK_TYPE_SERVICE])
                ->select(['plant_ppm_tasks_history.id', 'plant_ppm_tasks.id as task_id', 'plant_ppm_tasks.name', 'plant_ppm_tasks_history.status as current_status', 'plant_ppm_tasks_history.remarks as remark'])
                ->asArray()->all();

            $out['plant_tasks'] = $tasks;

            $additional_tasks = PlantPpmTasksHistory::find()->joinWith(
                'task'
            )
                ->where(['ppm_service_id' => $service_id])
                ->andWhere(['plant_ppm_tasks_history.task_type' => PlantPpmTasksHistory::TASK_TYPE_CHECKLIST])
                ->select(['plant_ppm_tasks_history.id', 'plant_ppm_tasks.id as task_id', 'plant_ppm_tasks.name', 'plant_ppm_tasks_history.status as current_status', 'plant_ppm_tasks_history.remarks as remark'])
                ->asArray()->all();

            // $additional_task_remark = PpmAdditionalTasks::find()
            //     ->join('LEFT JOIN', 'ppm_additional_tasks_values', "ppm_additional_tasks_values.additional_task_id = ppm_additional_tasks.id AND ppm_additional_tasks_values.ppm_service_id = {$service_id}")
            //     ->where([PpmAdditionalTasks::tableName() . '.equipment_type_id' => $ET])
            //     ->andWhere(['=', PpmAdditionalTasks::tableName() . '.name', "FAULTS OBSERVED/REMARKS"])
            //     ->select([PpmAdditionalTasks::tableName() . '.id', PpmAdditionalTasks::tableName() . '.name', PpmAdditionalTasks::tableName() . '.service', PpmAdditionalTasksValues::tableName() . '.value'])
            //     ->asArray()
            //     ->one();

            // if (!empty($additional_task_remark))
            //     $out["additional_tasks_remark"] = $additional_task_remark;

            $out["checklist_tasks"] = $additional_tasks;
        }

        return $out;
    }

    public function getVillaPpmTasks()
    {
        $out = [];

        $service_id = $this->id;

        if (!empty($service_id)) {

            $tasks = VillaPpmTasksHistory::find()->joinWith('task')->where(['ppm_service_id' => $service_id])->select(['villa_ppm_tasks_history.id', 'villa_ppm_tasks.name', 'villa_ppm_tasks_history.remarks as remark', 'villa_ppm_tasks_history.status as current_status'])->asArray()->all();

            $out['villa_tasks'] = $tasks;
        }

        return $out;
    }

    public function checkMissingAssignees($selected_technicians_array, $notify = false)
    {
        $request = $this;

        $assignees_ids = ArrayHelper::getColumn($this->assignees, 'user_id'); // 78 => Supervisor ( Always here and doesn't appear )
        $to_be_removed = @array_diff($assignees_ids, ArrayHelper::getColumn($selected_technicians_array, 'id'));

        $statuses = ArrayHelper::merge((new Assignee())->status_list, (new Assignee())->acceptance_status_list);

        if (!empty($selected_technicians_array)) {
            foreach ($selected_technicians_array as $technician_id) {
                if (!in_array($technician_id['id'], $assignees_ids)) {
                    $model = new Assignee();
                    $model->user_id = $technician_id['id'];
                    $model->repair_request_id = $request->id;
                    $model->datetime = $request->scheduled_at;
                    $model->status = $technician_id['status'];
                    $model->save();

                    Log::AddLog($model->user_id, $request->id, Log::TYPE_TECHNICIAN, "Assigning Technician", "Technician: " . $model->user->name . " Was Assigned", Assignee::STATUS_ASSIGNED);

                    if ($notify) {
                        $request->checkNotificationEmergency($request->urgent_status, $technician_id['id'], "Service #{$request->id} assigned to you", "{$request->location->name} | {$request->equipment->equipment->name} - {$request->equipment->code}");
                    }
                } else {
                    $model = Assignee::find()->where(['repair_request_id' => $this->id, 'user_id' => $technician_id['id']])->one();

                    if ($model->status != $technician_id['status']) {
                        $model->status = $technician_id['status'];
                        if ($model->save()) {
                            Log::AddLog($model->user_id, $request->id, Log::TYPE_TECHNICIAN, "Technician Status", "Technician: " . $model->user->name . " Status Changed to: " . $statuses[$technician_id['status']], $technician_id['status']);
                        }
                    }
                }
            }
        }

        // Delete if not supervisor id
        if (!empty($to_be_removed)) {
            foreach ($to_be_removed as $id) {
                if ($id != $request->owner_id) {
                    $user = Assignee::find()->where(['repair_request_id' => $request->id, 'user_id' => $id])->one();

                    if ($user->user->division_id == $request->division_id) {
                        Log::AddLog($id, $request->id, Log::TYPE_TECHNICIAN, "Technician Removal", "Technician: " . $user->user->name . " Was Removed", Assignee::STATUS_FREE);
                        $user->delete();

                        if ($notify) {
                            $request->checkNotificationEmergency($request->urgent_status, $id, 'Removed', "You Have Been Removed From Request #{$request->id}");
                        }
                    }
                }
            }
        }

        return true;
    }

    public function checkMissingAssigneesWithoutStatus($selected_technicians_array, $notify = false)
    {



        $request = $this;

        $assignees_ids = ArrayHelper::getColumn($this->assignees, 'user_id'); // 78 => Supervisor ( Always here and doesn't appear )


        // print_r($assignees_ids);
        // echo "<br />";
        // print_r($selected_technicians_array);

        $to_be_removed = @array_diff($assignees_ids, $selected_technicians_array);

        // echo "<br />";
        // print_r($to_be_removed);
        // exit;

        if (!empty($selected_technicians_array)) {
            foreach ($selected_technicians_array as $technician_id) {
                if (!in_array($technician_id, $assignees_ids)) {
                    $model = new Assignee();
                    $model->user_id = $technician_id;
                    $model->repair_request_id = $request->id;
                    $model->datetime = $request->scheduled_at;
                    $model->status = Assignee::STATUS_ASSIGNED;
                    if ($model->save()) {
                        Log::AddLog($model->user_id, $request->id, Log::TYPE_TECHNICIAN, "Assigning Technician", "Technician: " . $model->user->name . " Was Assigned", Assignee::STATUS_ASSIGNED);
                    }

                    if ($notify) {
                        $request->checkNotificationEmergency($request->urgent_status, $technician_id, "Service #{$request->id} assigned to you", "{$request->location->name} | {$request->equipment->equipment->name} - {$request->equipment->code}");
                    }
                }
            }
        }

        // Delete if not supervisor id
        if (!empty($to_be_removed)) {
            foreach ($to_be_removed as $id) {
                if ($id != $request->owner_id) {
                    $user = Assignee::find()->where(['repair_request_id' => $request->id, 'user_id' => $id])->one();

                    if ($user->user->division_id == $request->division_id) {
                        Log::AddLog($id, $request->id, Log::TYPE_TECHNICIAN, "Technician Removal", "Technician: " . $user->user->name . " Was Removed", Assignee::STATUS_FREE);
                        $user->delete();

                        if ($notify) {
                            $request->checkNotificationEmergency($request->urgent_status, $id, 'Removed', "You Have Been Removed From Request #{$request->id}");
                        }
                    }
                }
            }
        }

        return true;
    }

    public function CheckPlantPpmTasks($asset_id)
    {


        // Loop On All Assets 
        $asset = LocationEquipments::find()->innerJoinWith('equipment')->innerJoinWith('location')->innerJoinWith('equipment.equipmentType')->select([
            LocationEquipments::tableName() . '.id',
            LocationEquipments::tableName() . '.location_id',
            LocationEquipments::tableName() . '.value',
            LocationEquipments::tableName() . '.meter_value',
            LocationEquipments::tableName() . '.meter_damaged',
            // Equipment::tableName() . '.equipment_type_id',
            Equipment::tableName() . '.category_id',
            EquipmentType::tableName() . '.meter_type',
            EquipmentType::tableName() . '.reference_value',
            EquipmentType::tableName() . '.equivalance',
            Location::tableName() . '.sector_id',

        ])->where([LocationEquipments::tableName() . '.division_id' => Division::DIVISION_PLANT, LocationEquipments::tableName() . '.id' => $asset_id])->asArray()->one();

        // print_r($asset);
        // exit;

        if (!empty($asset)) {
            $hm = $asset['meter_value'];
            $meter_type = $asset['meter_type'];
            $asset_id = $asset['id'];

            // DAMAGED
            $is_damged = $asset['meter_damaged'];

            $equivalance = $asset['equivalance'];
            $reference_value = $asset['reference_value'];

            if ($is_damged == 0) {
                $last_repair_request = RepairRequest::find()->where([
                    'equipment_id' => $asset_id,
                    'service_type' => RepairRequest::TYPE_PPM,
                    // 'status' => RepairRequest::STATUS_COMPLETED,
                ])
                    ->orderBy(['created_at' => SORT_DESC])
                    ->one();

                if (!empty($last_repair_request)) {
                    $created_at = strtotime($last_repair_request->created_at);
                    $today = strtotime(date('Y-m-d'));

                    $date_diff = floor(($today - $created_at) / (60 * 60 * 24)); // Difference in days

                    if ($date_diff >= $equivalance) {
                        // $asset_model = LocationEquipments::findOne($asset_id);

                        // $asset_model->meter_value = ($hm + $reference_value);

                        $new_hour_meter = $hm + $reference_value;

                        LocationEquipments::updateAll(['meter_value' => $new_hour_meter], ['id' => $asset_id]);
                        $hm = $new_hour_meter;
                    }
                }
            }

            // $ET = @$asset['equipment_type_id'];
            // $service_id = $this->id;

            $command = null;
            $filter_command = null;

            if ($meter_type == EquipmentType::METER_TYPE_HOUR) {
                $command = Yii::$app->db->createCommand('
                    #insert into plant_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `status`)
                    select t.id,floor(:hm/t.occurence_value),:asset,10 from plant_ppm_tasks_history h right join plant_ppm_tasks t on h.task_id = t.id 
                    and h.meter_ratio = floor(:hm/t.occurence_value) and h.asset_id=:asset 
                    where floor(:hm/t.occurence_value)>0 and t.meter_type=:meter_type and h.id is null
                    ', [
                    ':asset' => $asset_id,
                    ':hm' => $hm,
                    ':status' => PlantPpmTasksHistory::TASK_STATUS_PENDING,
                    // ':ET' => $ET,
                    ':meter_type' => $meter_type
                ]);
            } else if ($meter_type == EquipmentType::METER_TYPE_KM) {
                $next_oil_change = VehicleOilChangeHistory::find()->where(['asset_id' => $asset_id])->orderBy(['datetime' => SORT_DESC])->one();

                if (empty($next_oil_change)) {
                    $next_oil_change = 0;
                } else {
                    $next_oil_change = $next_oil_change->next_oil_change;
                }

                if ($hm >= $next_oil_change) {
                    $command = Yii::$app->db->createCommand('
                        SELECT p.id,floor(:hm/:next_oil_change) as meter_ratio,:asset_id as asset,10 as status, p.task_type 
                        FROM plant_ppm_tasks p LEFT JOIN plant_ppm_tasks_history h ON h.task_id = p.id AND h.asset_id = :asset_id 
                        WHERE p.id = 1 AND h.asset_id NOT IN (
                        SELECT asset_id from oil_change_due WHERE next_oil_change = :next_oil_change  AND asset_id = :asset_id
                        )
                     ', [
                        ':asset_id' => $asset_id,
                        ':hm' => $hm,
                        ":next_oil_change" => $next_oil_change
                    ]);
                }

                $filter_command = Yii::$app->db->createCommand('
                    select t.id,floor(:hm/10000) as meter_ratio,:asset as asset_id,10 as status , t.task_type from plant_ppm_tasks_history h
                    right join plant_ppm_tasks t on h.task_id = t.id 
                    and h.meter_ratio = floor(:hm/10000) and h.asset_id=:asset 
                    where t.id = 2 AND floor(:hm/10000)> 0  and h.id is null
                    ', [
                    ':asset' => $asset_id,
                    ':hm' => $hm,
                    ':status' => PlantPpmTasksHistory::TASK_STATUS_PENDING,
                    // ':ET' => $ET,
                ]);

                $filter_result = !empty($filter_command) ? $filter_command->queryAll() : null;
            }

            $result = !empty($command) ? $command->queryAll() : null;

            if (!empty($result) || !empty($filter_result)) {

                // Create a new repair req then assign it's id to the ppm history table
                if ($meter_type == EquipmentType::METER_TYPE_HOUR) {
                    $this->createPlantPpmTask($this->id, $hm, $meter_type, $asset_id);
                } else if ($meter_type == EquipmentType::METER_TYPE_KM) {

                    $this->createPlantPpmTaskKm($asset_id, $this->id, $next_oil_change, $result, $filter_result);
                }
            }

            return true;
        }
    }


    public function CheckPlantChecklistTasks($asset_id)
    {
        $asset = LocationEquipments::find()->innerJoinWith('equipment')->innerJoinWith('location')->innerJoinWith('equipment.equipmentType')->select([
            LocationEquipments::tableName() . '.id',
            LocationEquipments::tableName() . '.location_id',
            LocationEquipments::tableName() . '.value',
            LocationEquipments::tableName() . '.meter_value',
            LocationEquipments::tableName() . '.meter_damaged',
            // Equipment::tableName() . '.equipment_type_id',
            Equipment::tableName() . '.category_id',
            EquipmentType::tableName() . '.meter_type',
            Location::tableName() . '.sector_id',

        ])->where([LocationEquipments::tableName() . '.division_id' => Division::DIVISION_PLANT, LocationEquipments::tableName() . '.id' => $asset_id])->asArray()->one();
        $count = 0;

        if (!empty($asset)) {
            $hm = date('z') + 1;
            $year = (new DateTime())->format("Y");
            $asset_id = $asset['id'];

            $command = Yii::$app->db->createCommand('
                    #insert into plant_ppm_tasks_history (`task_id`, `meter_ratio`, `asset_id`, `ppm_service_id`, `status`,`task_type`)
                    select t.id,(FLOOR(:hm / t.occurence_value) + :year),:asset_id,10 from plant_ppm_tasks_history h right join plant_ppm_tasks t on h.task_id = t.id 
                    and h.meter_ratio = (FLOOR(:hm / t.occurence_value) + :year) and h.asset_id= :asset_id 
                    where (FLOOR(:hm / t.occurence_value) + :year) > 0 and t.meter_type=30 and h.id is null
             ', [
                ':asset_id' => $asset_id,
                ':hm' => $hm,
                ':year' => $year,
            ]);

            $result = !empty($command) ? $command->queryAll() : null;

            if (!empty($result)) {
                $count++;

                $this->createPlantPpmChecklistTask($this->id, $asset_id);
            }
            return true;
        }
    }
}
