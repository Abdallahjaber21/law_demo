<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use common\behaviors\UserAuditBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sector".
 *
 * @property int $id
 * @property int $country_id
 * @property int $state_id
 * @property int $city_id
 * @property string $code
 * @property string $name
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $default_technician_id
 * @property int $main_sector_id
 * @property string $description
 *
 * @property AdminSector[] $adminSectors
 * @property Admin[] $admins
 * @property Location[] $locations
 * @property City $city
 * @property Country $country
 * @property Technician $defaultTechnician
 * @property MainSector $mainSector
 * @property State $state
 * @property SegmentPath[] $segmentPaths
 * @property Technician[] $technicians
 * @property TechnicianSector[] $technicianSectors
 * @property Technician[] $technicians0
 * @property WorkerSector[] $workerSectors
 * @property Worker[] $workers
 */
class Sector extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    const STATUS_DELETED = 30;



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sector';
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
            [['name', 'main_sector_id', 'country_id', 'state_id', 'city_id', 'code'], 'required'],
            [['country_id', 'state_id', 'city_id', 'status', 'created_by', 'updated_by', 'default_technician_id', 'main_sector_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['description'], 'string'],
            [['code'], 'unique'],
            [['code', 'name'], 'string', 'max' => 255],
            [['default_technician_id'], 'exist', 'skipOnError' => true, 'targetClass' => Technician::className(), 'targetAttribute' => ['default_technician_id' => 'id']],
            [['main_sector_id'], 'exist', 'skipOnError' => true, 'targetClass' => MainSector::className(), 'targetAttribute' => ['main_sector_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['state_id'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['state_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
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
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' =>  gmdate("Y-m-d h:i:s"),
            ],
            'blameable' => [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status' => [
                'class' => \common\behaviors\OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_ENABLED => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                    self::STATUS_DELETED => Yii::t("app", "Deleted"),

                ]
            ],
            'main_sector_id'    => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'main_sector_id',
                'options'   => ArrayHelper::map(MainSector::find()->all(), 'id', 'name')
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
            'country_id' => Yii::t('app', 'Country'),
            'state_id' => Yii::t('app', 'State'),
            'city_id' => Yii::t('app', 'City'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'default_technician_id' => Yii::t('app', 'Default Technician ID'),
            'main_sector_id' => Yii::t('app', 'Main Sector '),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdminSectors()
    {
        return $this->hasMany(AdminSector::className(), ['sector_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmins()
    {
        return $this->hasMany(Admin::className(), ['id' => 'admin_id'])->viaTable('admin_sector', ['sector_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['sector_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultTechnician()
    {
        return $this->hasOne(Technician::className(), ['id' => 'default_technician_id']);
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
    public function getSegmentPaths()
    {
        return $this->hasMany(SegmentPath::className(), ['sector_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicians()
    {
        return $this->hasMany(Technician::className(), ['sector_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicianSectors()
    {
        return $this->hasMany(TechnicianSector::className(), ['sector_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicians0()
    {
        return $this->hasMany(Technician::className(), ['id' => 'technician_id'])->viaTable('technician_sector', ['sector_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkerSectors()
    {
        return $this->hasMany(WorkerSector::className(), ['sector_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkers()
    {
        return $this->hasMany(Worker::className(), ['id' => 'worker_id'])->viaTable('worker_sector', ['sector_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['id' => 'state_id']);
    }
}
