<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use common\behaviors\UserAuditBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property int $sector_id
 * @property int $segment_path_id
 * @property string $code
 * @property string $name
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $expiry_date
 * @property int $created_by
 * @property int $updated_by
 * @property string $address
 * @property string $latitude
 * @property string $longitude
 * @property int $is_restricted
 * @property int $division_id
 * @property string $owner
 * @property string $owner_phone
 *
 * @property DefaultLocation[] $defaultLocations
 * @property EquipmentPath[] $equipmentPaths
 * @property Division $division
 * @property Sector $sector
 * @property SegmentPath $segmentPath
 * @property LocationCode[] $locationCodes
 * @property LocationContract[] $locationContracts
 * @property LocationEquipments[] $locationEquipments
 * @property Maintenance[] $maintenances
 * @property MaintenanceReport[] $maintenanceReports
 * @property MaintenanceVisit[] $maintenanceVisits
 * @property Project[] $projects
 * @property RepairRequest[] $repairRequests
 * @property RepairRequest[] $repairRequests0
 * @property UserLocation[] $userLocations
 */
class Location extends ActiveRecord
{

    public $equipment_type;
    public $clone_qty;

    public $country_id;
    public $state_id;
    public $city_id;
    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    const STATUS_DELETED = 30;

    const DIVISION_PLANT = 10;
    const DIVISION_MALL = 20;
    const DIVISION_VILLA = 30;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location';
    }

    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sector_id', 'segment_path_id', 'status', 'created_by', 'updated_by', 'is_restricted', 'division_id'], 'integer'],
            [['created_at', 'updated_at', 'eq_qty', 'country_id', 'state_id', 'city_id', 'clone_qty', 'expiry_date'], 'safe'],
            [['code', 'name', 'address', 'latitude', 'longitude'], 'string', 'max' => 255],
            [['owner', 'owner_phone'], 'string', 'max' => 100],
            [['sector_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sector::className(), 'targetAttribute' => ['sector_id' => 'id']],
            [['segment_path_id'], 'exist', 'skipOnError' => true, 'targetClass' => SegmentPath::className(), 'targetAttribute' => ['segment_path_id' => 'id']],
            [['sector_id', 'segment_path_id', 'name', 'status', 'code'], 'required'],
            [['code'], 'unique'],
        ];
    }

    //    use \yeesoft\multilingual\db\MultilingualLabelsTrait;
    //    public static function find()
    //    {
    //        return new \yeesoft\multilingual\db\MultilingualQuery(get_called_class());
    //    }

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
                    self::STATUS_ENABLED => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                    self::STATUS_DELETED => Yii::t("app", "Deleted"),
                ]
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
            'id' => Yii::t('app', 'ID'),
            'sector_id' => Yii::t('app', 'Sector '),
            'country_id' => Yii::t('app', 'Country'),
            'state_id' => Yii::t('app', 'State'),
            'city_id' => Yii::t('app', 'City'),
            'expiry_date' => Yii::t('app', 'Contract Expiry Date'),
            'segment_path_id' => 'Segment Path',
            'code' => Yii::t('app', 'Code'),
            'clone_qty' => Yii::t('app', 'Quantity'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'address' => Yii::t('app', 'Address'),
            'latitude' => Yii::t('app', 'Latitude'),
            'longitude' => Yii::t('app', 'Longitude'),
            'is_restricted' => Yii::t('app', 'Is Restricted'),
            'division_id' => Yii::t('app', 'Division '),
            'owner' => Yii::t('app', 'Owner'),
            'owner_phone' => Yii::t('app', 'Owner Phone'),
            'equipment_path_id' => Yii::t('app', 'Equipment Path'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultLocations()
    {
        return $this->hasMany(DefaultLocation::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentPaths()
    {
        return $this->hasMany(EquipmentPath::className(), ['location_id' => 'id']);
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
    public function getSector()
    {
        return $this->hasOne(Sector::className(), ['id' => 'sector_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegmentPath()
    {
        return $this->hasOne(SegmentPath::className(), ['id' => 'segment_path_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationCodes()
    {
        return $this->hasMany(LocationCode::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationContracts()
    {
        return $this->hasMany(LocationContract::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationEquipments()
    {
        return $this->hasMany(LocationEquipments::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenances()
    {
        return $this->hasMany(Maintenance::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenanceReports()
    {
        return $this->hasMany(MaintenanceReport::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenanceVisits()
    {
        return $this->hasMany(MaintenanceVisit::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequests()
    {
        return $this->hasMany(RepairRequest::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequests0()
    {
        return $this->hasMany(RepairRequest::className(), ['location_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLocations()
    {
        return $this->hasMany(UserLocation::className(), ['location_id' => 'id']);
    }

    const FIELDS_DEFAULT = 10;
    const FIELDS_EXPORT = 30;
    public static $return_fields = 10;

    public function fields()
    {
        switch (self::$return_fields) {
            case self::FIELDS_EXPORT:
                return [
                    "code" => function (Location $model) {
                        return $model->code;
                    },

                    "name" => function (Location $model) {
                        return $model->name;
                    },
                    "sector" => function (Location $model) {
                        return $model->sector->code;
                    },
                    "address" => function (Location $model) {
                        return $model->address;
                    },
                    "coordinates" => function (Location $model) {
                        return "{$model->latitude},{$model->longitude}";
                    }
                ];
            case self::FIELDS_DEFAULT:
            default:
                return [
                    'id',
                    'code',
                    'name',
                    'latitude',
                    'longitude',
                    'address',
                    'sector' => function ($model) {
                        return !empty($model->sector) ? $model->sector->name : null;
                    },
                    'is_restricted' => function (Location $model) {
                        return (bool) $model->is_restricted;
                    },
                ];
        }
    }

    public static function getLocationsCrud()
    {
        $admin_division_id = Account::getAdminDivisionModel()->id;

        $locations = Location::find()->where(['status' => Location::STATUS_ENABLED]);

        if (!empty($admin_division_id)) {
            $locations = $locations->andWhere(['division_id' => $admin_division_id]);
        }

        $locations = $locations->all();

        return ArrayHelper::map($locations, 'id', function ($model) {
            return "{$model->code} - {$model->name}";
        });
    }
}
