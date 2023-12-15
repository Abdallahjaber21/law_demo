<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "division".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property boolean $has_shifts
 *
 * @property Admin[] $admins
 * @property Equipment[] $equipments
 * @property Location[] $locations
 * @property MainSector[] $mainSectors
 * @property RepairRequest[] $repairRequests
 * @property Technician[] $technicians
 * @property User[] $users
 * @property EquipmentCa[] $equipmentCas

 */
class Division extends \yii\db\ActiveRecord
{
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;

    const DIVISION_PLANT = 10;
    const DIVISION_MALL = 20;
    const DIVISION_VILLA = 30;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'division';
    }

    /**
     * {@inheritdoc}
     */
    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['has_shifts'], 'boolean'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
            [['name'], 'string', 'max' => 255],
            [['has_shifts'], 'required']
        ];
    }
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' =>  gmdate("Y-m-d h:i:s"),
            ],
            'blameable' => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'    => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_ENABLED  => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),

                ]
            ],
            'name'    => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'name',
                'options'   => [
                    self::DIVISION_MALL  => Yii::t("app", "Mall"),
                    self::DIVISION_PLANT => Yii::t("app", "Plant"),
                    self::DIVISION_VILLA => Yii::t("app", "Villa"),
                ]
            ],
            //    'multilingual' => [
            //        'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            //        'attributes' => []
            //    ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'has_shifts' => Yii::t('app', 'Has Shifts'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmins()
    {
        return $this->hasMany(Admin::className(), ['division_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipments()
    {
        return $this->hasMany(Equipment::className(), ['division_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['division_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainSectors()
    {
        $query = $this->hasMany(MainSector::className(), ['division_id' => 'id']);

        return $query;
    }
    public function getAvailableMainSectors()
    {
        $query = $this->hasMany(MainSector::className(), ['division_id' => 'id']);
        $query->where(['status' => MainSector::STATUS_ENABLED]);

        return $query;
    }
    public function getEquipmentCas()
    {
        return $this->hasMany(EquipmentCa::className(), ['division_id' => 'id']);
    }
    public static function getSectors($div_id)
    {
        if (!empty($div_id)) {
            $model = self::findOne($div_id);

            return $model->hasMany(Sector::className(), ['main_sector_id' => 'id'])->via('mainSectors')->orderBy(['name' => SORT_ASC])->all();
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequests()
    {
        return $this->hasMany(RepairRequest::className(), ['division_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicians()
    {
        return $this->hasMany(Technician::className(), ['division_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['division_id' => 'id']);
    }

    public static function checkHasCustomAttributes($division_id)
    {
        $model = Division::findOne($division_id);

        return count($model->getEquipmentCas()->all()) > 0;
    }
}
