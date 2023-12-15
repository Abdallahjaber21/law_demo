<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;

/**
 * This is the model class for table "engine_oil_types".
 *
 * @property integer $id
 * @property string $oil_viscosity
 * @property integer $motor_fuel_type_id
 * @property double $can_weight
 * @property double $oil_durability
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property VehicleOilChangeHistory[] $vehicleOilChangeHistories
 * @property string $status_label
 * @property label $status_list
 */
class EngineOilTypes extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;

    const MOTOR_TYPE_DIESEL = 10;
    const MOTOR_TYPE_PETROL = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'engine_oil_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['motor_fuel_type_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['can_weight', 'oil_durability'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['can_weight', 'oil_durability', 'oil_viscosity', 'motor_fuel_type_id'], 'required'],
            [['oil_viscosity'], 'string', 'max' => 255],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new \yii\db\Expression("now()"),
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
                    //self::STATUS_DELETED => Yii::t("app", "Deleted"),
                ]
            ],
            'motor_fuel_type_id' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'motor_fuel_type_id',
                'options' => [
                    self::MOTOR_TYPE_DIESEL => Yii::t("app", "Diesel"),
                    self::MOTOR_TYPE_PETROL => Yii::t("app", "Petrol"),

                ]
            ],
            // 'multilingual' => [
            // 'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            // 'attributes' => []
            // ],
        ];
    }
    // use \yeesoft\multilingual\db\MultilingualLabelsTrait;
    // public static function find()
    // {
    // return new \yeesoft\multilingual\db\MultilingualQuery(get_called_class());
    // }

    public static function findEnabled()
    {
        return parent::find()->where(['status' => self::STATUS_ENABLED]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'oil_viscosity' => 'Oil Viscosity',
            'motor_fuel_type_id' => 'Motor Fuel Type',
            'can_weight' => 'Can Weight (kg)',
            'oil_durability' => 'Oil Durability (km)',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @inheritdoc
     */

    /*
    public function beforeDelete() {
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
    */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleOilChangeHistories()
    {
        return $this->hasMany(VehicleOilChangeHistory::className(), ['oil_id' => 'id']);
    }

    public function fields()
    {
        return [
            'id',
            'oil_viscosity',
            'oil_durability',
        ];
    }
}
