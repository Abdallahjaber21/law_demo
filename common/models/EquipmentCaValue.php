<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "equipment_ca_value".
 *
 * @property int $id
 * @property int $equipment_ca_id
 * @property int $equipment_id
 * @property int $location_equipment_id
 * @property string $value
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property EquipmentCa $equipmentCa
 * @property Equipment $equipment
 * @property LocationEquipments $locationEquipment
 */
class EquipmentCaValue extends \yii\db\ActiveRecord
{
    const STATUS_ENABLED = 20;
    const STATUS_DISABLED = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipment_ca_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['equipment_ca_id', 'equipment_id', 'status', 'created_by', 'location_equipment_id', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['value'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
            [['equipment_ca_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentCa::className(), 'targetAttribute' => ['equipment_ca_id' => 'id']],
            [['equipment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Equipment::className(), 'targetAttribute' => ['equipment_id' => 'id']],
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

        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment_ca_id' => 'Equipment Custom',
            'location_equipment_id' => 'Location Equipment',
            'equipment_id' => 'Equipment',
            'value' => 'Value',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentCa()
    {
        return $this->hasOne(EquipmentCa::className(), ['id' => 'equipment_ca_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment()
    {
        return $this->hasOne(Equipment::className(), ['id' => 'equipment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationEquipment()
    {
        return $this->hasOne(LocationEquipments::className(), ['id' => 'location_equipment_id']);
    }

    public function fields()
    {
        return [
            'id',
            'name' => function ($model) {
                return $model->equipmentCa->name;
            },
            'value',
            'location_equipment_id',
            'equipment_id',
        ];
    }
}
