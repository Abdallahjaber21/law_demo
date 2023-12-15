<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "barcode_scan".
 *
 * @property integer $id
 * @property integer $maintenance_id
 * @property integer $barcode_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $visit_id
 * @property integer $code
 *
 * @property EquipmentMaintenanceBarcode $barcode
 * @property Maintenance $maintenance
 * @property MaintenanceVisit $maintenanceVisit
 *
 * @property string $status_label
 * @property label $status_list
 */
class BarcodeScan extends ActiveRecord
{

    // Status
    const STATUS_SCANNED = 10;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'barcode_scan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['maintenance_id', 'barcode_id', 'status', 'created_by', 'updated_by','visit_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['barcode_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentMaintenanceBarcode::className(), 'targetAttribute' => ['barcode_id' => 'id']],
            [['maintenance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Maintenance::className(), 'targetAttribute' => ['maintenance_id' => 'id']],
            [['status'], 'default', 'value' => self::STATUS_SCANNED],
            [['code'], 'integer']
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'timestamp' => [
//                'class'              => TimestampBehavior::className(),
//                'createdAtAttribute' => 'created_at',
//                'updatedAtAttribute' => 'updated_at',
//                'value'              => new Expression("now()"),
//            ],
            'blameable' => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'    => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_SCANNED => Yii::t("app", "Scanned"),
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
            'id'             => 'ID',
            'maintenance_id' => 'Maintenance ID',
            'barcode_id'     => 'Barcode ID',
            'status'         => 'Status',
            'created_at'     => 'Created At',
            'updated_at'     => 'Updated At',
            'created_by'     => 'Created By',
            'updated_by'     => 'Updated By',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBarcode()
    {
        return $this->hasOne(EquipmentMaintenanceBarcode::className(), ['id' => 'barcode_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMaintenance()
    {
        return $this->hasOne(Maintenance::className(), ['id' => 'maintenance_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMaintenanceVisit()
    {
        return $this->hasOne(MaintenanceVisit::className(), ['id' => 'visit_id']);
    }
}
