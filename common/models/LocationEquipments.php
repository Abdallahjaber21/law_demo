<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use common\behaviors\UserAuditBehavior;


/**
 * This is the model class for table "location_equipments".
 *
 * @property integer $division_id
 * @property integer $location_id
 * @property integer $equipment_id
 * @property integer $motor_fuel_type
 * @property integer $driver_id
 * @property string $code
 * @property string $chassie_number
 * @property string $value
 * @property int $status
 * @property int $safety_status
 * @property integer $meter_value
 * @property integer $meter_damaged
 * @property string $remarks
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Division $division
 * @property Technician $driver
 * @property Equipment $equipment
 * @property Location $location
 * @property EquipmentCaValue[] $equipmentCaValues
 * @property VehicleOilChangeHistory[] $vehicleOilChangeHistories
 */
class LocationEquipments extends \yii\db\ActiveRecord
{

    public static $return_fields;
    public $apply_all;
    // Export
    const FIELDS_EXPORT_MALL = 10;
    const FIELDS_EXPORT_PLANT = 20;
    const FIELDS_EXPORT_VILLA = 30;
    const CASE_MOBILE = 40;

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    const STATUS_UNDER_REPAIR = 30;
    const STATUS_SCRAP = 40;
    const STATUS_DELETED = 50;
    const STATUS_OFF_HIRE = 60;

    const MOTOR_TYPE_DIESEL = 10;
    const MOTOR_TYPE_PETROL = 20;

    const SAFETY_STATUS_ACCEPTED = 10;
    const SAFETY_STATUS_NOT_ACCEPTED = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location_equipments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['division_id', 'location_id', 'equipment_id', 'driver_id', 'meter_value', 'status', 'safety_status', 'created_by', 'updated_by', 'motor_fuel_type'], 'integer'],
            [['value', 'chassie_number'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'required'],
            [['meter_damaged'], 'integer', 'max' => 1],
            [['code', 'remarks'], 'string', 'max' => 255],
            [['equipment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Equipment::className(), 'targetAttribute' => ['equipment_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['location_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'division_id' => 'Division',
            'location_id' => 'Location',
            'meter_value' => 'Meter Value',
            'chassie_number' => 'Chassis Number',
            'motor_fuel_type' => 'Motor Fuel Type',
            'meter_damaged' => 'Meter Status',
            'equipment_id' => 'Equipment',
            'driver_id' => 'Driver',
            'code' => 'Code',
            'value' => 'Path',
            'remarks' => 'Remarks',
            'status' => 'Status',
            'safety_status' => 'Safety Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
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
                    self::STATUS_UNDER_REPAIR => Yii::t("app", "Under Repair"),
                    self::STATUS_SCRAP => Yii::t("app", "Scrap"),
                    self::STATUS_DELETED => Yii::t("app", "Deleted"),
                    self::STATUS_OFF_HIRE => Yii::t("app", "Off-Hire"),


                ]
            ],
            'motor_fuel_type' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'motor_fuel_type',
                'options' => [
                    self::MOTOR_TYPE_DIESEL => Yii::t("app", "Diesel"),
                    self::MOTOR_TYPE_PETROL => Yii::t("app", "Petrol"),

                ]
            ],
            'safety_status' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'safety_status',
                'options' => [
                    self::SAFETY_STATUS_ACCEPTED => Yii::t("app", "Accepted"),
                    self::SAFETY_STATUS_NOT_ACCEPTED => Yii::t("app", "Not Accepted"),

                ]
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentCaValues()
    {
        return $this->hasMany(EquipmentCaValue::className(), ['location_equipment_id' => 'id']);
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
    public function getDriver()
    {
        return $this->hasOne(Technician::className(), ['id' => 'driver_id']);
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
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleOilChangeHistories()
    {
        return $this->hasMany(VehicleOilChangeHistory::className(), ['asset_id' => 'id']);
    }

    public static function SaveEquipments($location_id, $location_equipments_arr, $selection, $isNewRecord)
    {
        if (!$isNewRecord) {
            LocationEquipments::deleteAll(['location_id' => $location_id]);
            EquipmentCaValue::deleteAll(['location_equipment_id' => array_keys($location_equipments_arr)]);
        }

        if (!empty($location_equipments_arr)) {
            foreach ($location_equipments_arr as $key => $value) {
                if (!in_array($key, $selection)) {
                    unset($location_equipments_arr[$key]);
                }
            }

            foreach ($location_equipments_arr as $index => $m) {
                foreach ($m as $i => $datum) {
                    $location_equipment_model = new LocationEquipments();
                    $location_equipment_model->equipment_id = $index;
                    $location_equipment_model->code = $datum['code'];
                    $location_equipment_model->value = Equipment::GetJsonSegmentValue($datum['value'], ',');
                    $location_equipment_model->location_id = $location_id;
                    $location_equipment_model->status = Equipment::STATUS_ENABLED;
                    if ($location_equipment_model->save()) {
                        foreach ($datum['Ca_value'] as $index2 => $datatum) {
                            $custom_attrs_model = new EquipmentCaValue();
                            $custom_attrs_model->equipment_ca_id = $index2;
                            $custom_attrs_model->equipment_id = $index;
                            $custom_attrs_model->location_equipment_id = $location_equipment_model->id;
                            $custom_attrs_model->value = $datatum;
                            $custom_attrs_model->save();
                        }
                    }
                }
            }
        }
    }

    public static function SaveEquipment($location_id, $location_equipments_arr)
    {

        $location = Location::findOne($location_id);

        if (!empty($location_equipments_arr)) {
            foreach ($location_equipments_arr as $key => $arr) {
                $equipment_id = $key;
                // Delete On Update Action
                // LocationEquipments::deleteAll(['location_id' => $location_id, 'equipment_id' => $equipment_id]);
                // EquipmentCaValue::deleteAll(['location_equipment_id' => array_keys($location_equipments_arr) , 'equipment_id' => $equipment_id]);

                // Insert Values
                foreach ($arr as $key => $datum) {
                    $location_equipment_model = new LocationEquipments();
                    $location_equipment_model->division_id = $location->division_id;
                    $location_equipment_model->equipment_id = $equipment_id;
                    $location_equipment_model->code = $datum['code'];
                    $location_equipment_model->value = Equipment::GetJsonSegmentValue($datum['value'], ',');
                    $location_equipment_model->location_id = $location_id;
                    $location_equipment_model->status = Equipment::STATUS_ENABLED;
                    if ($location_equipment_model->save()) {
                        if (!empty($datum['Ca_value'])) {
                            foreach ($datum['Ca_value'] as $index2 => $datatum) {
                                $custom_attrs_model = new EquipmentCaValue();
                                $custom_attrs_model->equipment_ca_id = $index2;
                                $custom_attrs_model->equipment_id = $equipment_id;
                                $custom_attrs_model->location_equipment_id = $location_equipment_model->id;
                                $custom_attrs_model->value = $datatum;
                                $custom_attrs_model->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public static function getCrudInputsFromLayers($json_layers)
    {
        $json_layers = Json::decode($json_layers);
        return $json_layers;
    }
    public static function getDriverTechnicianId()
    {
        $driverIds = [];
        $accounttype = AccountType::find()->where(['like', 'name', 'driver'])->one();
        if (!empty($accounttype)) {
            $type = $accounttype->id;
            $technicians = Technician::find()->joinWith('account')->where(['account.type' => $type])->all();
            foreach ($technicians as $technician) {
                array_push($driverIds, $technician->id);
            }
        }
        return $driverIds;
    }
    public function fields()
    {
        switch (self::$return_fields) {
            case self::FIELDS_EXPORT_MALL:
                return [
                    'id',
                    'location' => function ($model) {
                        return @$model->location->code;
                    },
                    'equipment' => function ($model) {
                        return @$model->equipment->name;
                    },
                    'code',
                    'Floor' => function ($model) {
                        return Equipment::getLayerValue(@$model->value, 'floor');
                    },
                    'Zone' => function ($model) {
                        return Equipment::getLayerValue(@$model->value, 'zone');
                    },
                    'Unit No' => function ($model) {
                        return Equipment::getLayerValue(@$model->value, 'unit no');
                    },
                    'Location' => function ($model) {
                        return Equipment::getLayerValue(@$model->value, 'location');
                    },
                ];
                break;
            case self::FIELDS_EXPORT_PLANT:
                return [
                    'id',
                    'location' => function ($model) {
                        return @$model->location->code;
                    },
                    'equipment' => function ($model) {
                        return @$model->equipment->name;
                    },
                    'code',
                    'Location' => function ($model) {
                        return Equipment::getLayerValue(@$model->value, 'location');
                    },
                    'Division' => function ($model) {
                        return Equipment::getLayerValue(@$model->value, 'division');
                    },
                    'Plate no' => function ($model) {
                        return Equipment::getEquipmentCustomAttribute(@$model->equipment_id, $model->id, EquipmentCa::find()->where(['name' => 'Plate no'])->one()->id);
                    },
                    'Item Brand' => function ($model) {
                        return Equipment::getEquipmentCustomAttribute(@$model->equipment_id, $model->id, EquipmentCa::find()->where(['name' => 'Item Brand'])->one()->id);
                    },
                    'Model' => function ($model) {
                        return Equipment::getEquipmentCustomAttribute(@$model->equipment_id, $model->id, EquipmentCa::find()->where(['name' => 'Model'])->one()->id);
                    },
                    'Item Owner' => function ($model) {
                        return Equipment::getEquipmentCustomAttribute(@$model->equipment_id, $model->id, EquipmentCa::find()->where(['name' => 'Item Owner'])->one()->id);
                    },
                    'remarks',
                    'status' => function ($model) {
                        return @(new LocationEquipments())->status_list[$model->status];
                    },
                    'Driver' => function ($model) {
                        return @$model->driver->name;
                    },
                    'Chassie Number' => function ($model) {
                        return $model->chassie_number;
                    },
                    'Motor Fuel Type' => function ($model) {
                        return $model->motor_fuel_type_label;
                    },
                    'Meter Value' => function ($model) {
                        return $model->meter_value;
                    },
                    'Meter Status' => function ($model) {
                        return $model->meter_damaged;
                    },
                ];
                break;
            case self::FIELDS_EXPORT_VILLA:
                return [
                    'id',
                    'location' => function ($model) {
                        return @$model->location->code;
                    },
                    'equipment' => function ($model) {
                        return @$model->equipment->name;
                    },
                    'code',
                    'equipment path' => function ($model) {
                        return Equipment::getLayerValue(@$model->value, null, true);
                    },
                ];
                break;
            case self::CASE_MOBILE:
                return [
                    'id',
                    'location_id',
                    'status',
                    'value',
                    'code' => function ($model) {
                        if (!empty(@Equipment::getEquipmentCustomAttribute(@$model->equipment_id, $model->id, EquipmentCa::find()->where(['name' => 'Plate no'])->one()->id))) {
                            return "<strong style='color:red;font-size:18px;'>" . @Equipment::getEquipmentCustomAttribute(@$model->equipment_id, $model->id, EquipmentCa::find()->where(['name' => 'Plate no'])->one()->id) . "</strong>";
                        }

                        return $model->code;
                    },
                    'equipment_id',
                    'custom_attributes' => function ($model) {
                        return EquipmentCaValue::find()->where(['equipment_id' => $model->equipment_id, 'location_equipment_id' => $model->id])->all();
                    },
                    'parent' => function ($model) {
                        return $model->equipment->name;
                    },
                    "location" => function ($model) {
                        $location = $model->location;

                        return $location->code . ' - ' . $location->name;
                    },
                    'equipment_path' => function ($model) {
                        return Equipment::getLayersValue($model->value);
                    },
                    "chassie_number",
                    "motor_fuel_type",
                    "custom_attributes_value" => function ($model) {
                        return Equipment::getEquipmentCustomAttributes($model->equipment_id, $model->id);
                    },
                    "available_fuel_types" => function ($model) {
                        return $model->motor_fuel_type_list;
                    },
                    'meter_value',
                    'meter_damaged',
                    'work_type' => function ($model) {
                        $out = [];
                        $out[] = ["id" => 30, "value" => "Service"];
                        $out[] = ["id" => 40, "value" => "BreakDown"];

                        return $out;
                    },
                    'meter_type_label' => function ($model) {
                        if (!empty($model->equipment_id)) {
                            return "(" . $model->equipment->equipmentType->meter_type_label . ")";
                        }
                    },
                ];
                break;
            default:
                return [
                    'id',
                    'location_id',
                    'status',
                    'value',
                    'code',
                    'equipment_id',
                    'custom_attributes' => function ($model) {
                        return EquipmentCaValue::find()->where(['equipment_id' => $model->equipment_id, 'location_equipment_id' => $model->id])->all();
                    },
                    'parent' => function ($model) {
                        return $model->equipment->name;
                    },
                    "location" => function ($model) {
                        $location = $model->location;

                        return $location->code . ' - ' . $location->name;
                    },
                    'equipment_path' => function ($model) {
                        return Equipment::getLayersValue($model->value);
                    },
                    "chassie_number",
                    "motor_fuel_type",
                    "custom_attributes_value" => function ($model) {
                        return Equipment::getEquipmentCustomAttributes($model->equipment_id, $model->id);
                    },
                    "available_fuel_types" => function ($model) {
                        return $model->motor_fuel_type_list;
                    },
                    'meter_value',
                    'meter_damaged',
                    'work_type' => function ($model) {
                        $out = [];
                        $out[] = ["id" => 30, "value" => "Service"];
                        $out[] = ["id" => 40, "value" => "BreakDown"];

                        return $out;
                    },
                ];
                break;
        }
    }


    public static function GetPpmList($location_equipment_id, $division_id)
    {
        $equipmemt = LocationEquipments::findOne($location_equipment_id);
        $tasks = null;

        switch ($division_id) {
            case Division::DIVISION_PLANT:
                $tasks = PlantPpmTasks::find()->select(['id', 'name', 'occurence_value', 'meter_type'])->where(['occurence_value' => $equipmemt->meter_value])->asArray()->all();

                return [
                    $equipmemt->id => $tasks
                ];

                break;
                return;
        }
    }
}