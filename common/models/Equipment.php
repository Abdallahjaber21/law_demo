<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use common\components\helpers\DateTimeHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use common\behaviors\UserAuditBehavior;


/**
 * This is the model class for table "equipment".
 *
 * @property int $id
 * @property int $equipment_type_id
 * @property string $code
 * @property string $name
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $division_id
 * @property int $category_id
 * @property string $description
 *
 * @property Category $category
 * @property Division $division
 * @property EquipmentType $equipmentType
 * @property EquipmentCaValue[] $equipmentCaValues
 * @property EquipmentMaintenanceBarcodes[] $equipmentMaintenanceBarcodes
 * @property EquipmentPath[] $equipmentPaths
 * @property LocationEquipments[] $locationEquipments
 * @property Maintenance[] $maintenances
 * @property RepairRequest[] $repairRequests

 */
class Equipment extends ActiveRecord
{

    // public $equipment_path_safe;
    public $equipment_ca_safe;
    public $eq_qty;

    //Material
    const MATERIAL_ACTIVE = "ACTIVE";
    const MATERIAL_INACTIVE = "INACTIVE";

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    // const STATUS_UNDER_REPAIR = 30;
    // const STATUS_SCRAP = 40;
    // const STATUS_SPARE = 50;

    const STATUS_DELETED = 60;
    const FIELDS_DEFAULT = 10;
    const FIELDS_NO_RELATIONS = 20;
    const FIELDS_EXPORT = 30;
    public static $return_fields = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipment';
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
            [['equipment_type_id', 'status', 'created_by', 'updated_by', 'division_id', 'category_id'], 'integer'],
            [['created_at', 'updated_at', 'equipment_path_safe', 'equipment_ca_safe'], 'safe'],
            [['description'], 'string'],
            [['code', 'name'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
            [['equipment_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentType::className(), 'targetAttribute' => ['equipment_type_id' => 'id']],
            [['equipment_type_id', 'code', 'status', 'division_id', 'category_id', 'name'], 'required'],
            [['code'], 'unique'],


        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return [
            'userAudit' => UserAuditBehavior::class,
            'timestamp'      => [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression("now()"),
            ],
            'blameable'      => [
                'class'              => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'status'         => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'status',
                'options'   => [
                    self::STATUS_ENABLED  => Yii::t("app", "active"),
                    self::STATUS_DISABLED => Yii::t("app", "inactive"),
                    // self::STATUS_UNDER_REPAIR => Yii::t("app", "under repair"),
                    // self::STATUS_SCRAP => Yii::t("app", "scrap"),
                    // self::STATUS_SPARE => Yii::t("app", "spare"),
                    // self::STATUS_DELETED => Yii::t("app", "deleted"),
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
            'id' => 'ID',
            'equipment_type_id' => 'Equipment Type',
            'code' => 'Code',
            'name' => 'Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'division_id' => 'Division',
            'category_id' => 'Category',
            'description' => 'Description',
            'equipment_path_id' => 'Equipment Path'
        ];
    }
    /**
     * @inheritdoc
     */
    //    public function beforeDelete()
    //    {
    //        if (parent::beforeDelete()) {
    //            if ($this->status == self::STATUS_ENABLED) {
    //                $this->status = self::STATUS_DISABLED;
    //                $this->save();
    //                return false;
    //            } else {
    //                return true;
    //            }
    //        }
    //        return false;
    //    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    public function getLocationEquipments()
    {
        return $this->hasMany(LocationEquipments::className(), ['equipment_id' => 'id']);
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
    public function getEquipmentType()
    {
        return $this->hasOne(EquipmentType::className(), ['id' => 'equipment_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentTypes()
    {
        return $this->hasMany(EquipmentType::className(), ['id' => 'equipment_type_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentMaintenanceBarcodes()
    {
        return $this->hasMany(EquipmentMaintenanceBarcodes::className(), ['equipment_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentPath()
    {
        return $this->hasOne(EquipmentPath::className(), ['equipment_id' => 'id']);
    }
    public function getEquipmentCaValue($equipmentCaId)
    {
        $equipmentCaValue = $this->getEquipmentCaValues()->andWhere(['equipment_ca_id' => $equipmentCaId])->one();

        return $equipmentCaValue ? $equipmentCaValue->value : null;
    }
    public function getEquipmentCaValues()
    {
        return $this->hasMany(EquipmentCaValue::className(), ['equipment_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenances()
    {
        return $this->hasMany(Maintenance::className(), ['equipment_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRepairRequests()
    {
        return $this->hasMany(RepairRequest::className(), ['equipment_id' => 'id']);
    }

    public function fields()
    {
        switch (self::$return_fields) {
            case self::FIELDS_DEFAULT:
                return [
                    "id",
                    "name",
                    "code",
                    //"location_id",
                    //"location"       => function ($model) {
                    //  return !empty($model->location) ? "{$model->location->name} - {$model->location->address}" : null;
                    //},
                    "problems" => function ($model) {
                        //                        $type = Problem::mapEquipmentTypeToProbemType($model->type);
                        $type = Problem::mapEquipmentTypeToProbemType($model->equipment_type);
                        return Problem::findEnabled()
                            ->where([
                                'AND',
                                ['status' => Problem::STATUS_ENABLED],
                                [
                                    'OR',
                                    ['type' => Problem::TYPE_ALL],
                                    ['type' => $type],
                                ]
                            ])
                            ->indexBy("id")
                            ->all();
                    },
                ];
            case self::FIELDS_NO_RELATIONS:
                return [
                    "id",
                    "name",
                    "code",

                ];
            case self::FIELDS_EXPORT:
                return [
                    "code",

                    "category"     => function (Equipment $model) {
                        return $model->unit_type;
                    },
                ];
        }
    }

    public static function GetJsonSegmentValue($segment_pathes_arr) // array
    {
        $json_values = [];
        $counter = 0;

        foreach ($segment_pathes_arr as $index => $m) {
            $json_values[] = [
                'id' => $counter,
                'layer' => $index,
                'value' => ucfirst($m)
            ];

            $counter++;
        }

        return Json::encode($json_values);
    }

    public static function getLayersValue($layers_array, $seperator = null)
    {
        $out = [];

        $layers_array = is_array($layers_array) ? $layers_array : Json::decode($layers_array);

        foreach ($layers_array as $arr) {
            $out[] = '<strong>' . $arr['layer'] . '</strong>: ' . $arr['value'] . '<br />';
        }

        return implode(!empty($seperator) ? $seperator : '', $out);
    }

    public static function getLayerValue($layers_array, $layer_name = null, $return_first = null)
    {
        $out = '';

        $layers_array = is_array($layers_array) ? $layers_array : Json::decode($layers_array);

        foreach ($layers_array as $arr) {
            if (!empty($layer_name)) {
                if (strtolower(trim($arr['layer']))  == strtolower(trim($layer_name))) {
                    $out =  $arr['value'];
                }
            } else {
                if (!empty($return_first) && $return_first) {
                    $out =  $arr['value'];
                }
            }
        }

        return $out;
    }

    public static function getLayersValueTextInput($layers_array, $seperator = null)
    {
        $out = [];

        $layers_array = is_array($layers_array) ? $layers_array : Json::decode($layers_array);

        foreach ($layers_array as $arr) {
            $out[] = $arr['layer'] . ': ' . $arr['value'];
        }

        return implode(!empty($seperator) ? $seperator : '', $out);
    }

    public static function getLayersDataValue($layers_array, $seperator = null)
    {
        $out = [];

        $layers_array = is_array($layers_array) ? $layers_array : Json::decode($layers_array);

        foreach ($layers_array as $arr) {
            $out[] =  $arr['value'];
        }

        return implode(!empty($seperator) ? $seperator : '', $out);
    }

    public static function getArrayMatch($segment_array, $equipment_path_array)
    {
        $merged_array = array();

        $segment_array = is_array($segment_array) ? $segment_array : Json::decode($segment_array);
        $equipment_path_array = is_array($equipment_path_array) ? $equipment_path_array : Json::decode($equipment_path_array);



        foreach ($segment_array as $segment) {
            $matching_equipment = array_filter($equipment_path_array, function ($equipment) use ($segment) {
                return $equipment['id'] === $segment['id'] && $equipment['layer'] === $segment['layer'];
            });

            if (count($matching_equipment) > 0) {
                $equipment = reset($matching_equipment);
                $merged_array[] = array(
                    'id' => $segment['id'],
                    'layer' => $segment['layer'],
                    'value' => $equipment['value']
                );
            } else {
                $merged_array[] = array(
                    'id' => $segment['id'],
                    'layer' => $segment['layer'],
                    'value' => ''
                );
            }
        }

        return $merged_array;
    }

    public static function getEquipmentCustomAttributes($equipment_id, $location_equipment_id, $separator = null)
    {
        $eq = Equipment::findOne($equipment_id);
        $out = [];

        $attribute_values = EquipmentCaValue::find()->where(['equipment_id' => $eq->id, 'location_equipment_id' => $location_equipment_id])->all();

        foreach ($attribute_values as $index => $a) {
            $out[] = "{$a->equipmentCa->name}: {$a->value}";
        }

        if (!empty($out)) {
            if (!empty($separator)) {
                return implode($separator, $out);
            } else {
                return implode(',', $out);
            }
        }

        return null;
    }

    public static function getJsonEquipmentCustomAttributes($equipment_id, $location_equipment_id)
    {
        $eq = Equipment::findOne($equipment_id);
        $out = [];

        $attribute_values = EquipmentCaValue::find()->where(['equipment_id' => @$eq->id, 'location_equipment_id' => $location_equipment_id])->all();

        foreach ($attribute_values as $index => $a) {
            $out[] = array(
                'id' => $a->id,
                'layer' => $a->equipmentCa->name,
                'value' => $a->value
            );
        }

        return Json::encode($out);
    }

    public static function getEquipmentCustomAttribute($equipment_id, $location_equipment_id, $equipment_ca_id)
    {
        $out = '';

        $attribute_values = EquipmentCaValue::find()->where(['equipment_id' => $equipment_id, 'location_equipment_id' => $location_equipment_id, 'equipment_ca_id' => $equipment_ca_id])->one();

        $out = $attribute_values->value;

        return $out;
    }
}