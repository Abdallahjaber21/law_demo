<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use common\behaviors\UserAuditBehavior;
use yii\db\Expression;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "equipment_type".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property integer $meter_type
 * @property integer $alt_meter_type
 * @property string $equivalance
 * @property int $category_id
 * @property int $reference_value
 * @property Equipment[] $equipments
 * @property Category $category
 * @property string $status_label
 * @property label $status_list
 * @property int $status

 */
class EquipmentType extends ActiveRecord
{
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    const STATUS_DELETED = 30;

    const METER_TYPE_KM = 10;
    const METER_TYPE_HOUR = 20;

    const FREQ_MONTHLY = 30;
    const FREQ_QUARTERLY = 40;
    const FREQ_HALF_YEARLY = 50;
    const FREQ_YEARLY = 60;

    const ALT_METER_TYPE_HOUR = 10;
    const ALT_METER_TYPE_DAY = 20;
    const ALT_METER_TYPE_WEEK = 30;
    const ALT_METER_TYPE_MONTH = 40;
    const ALT_METER_TYPE_YEAR = 50;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipment_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'meter_type', 'alt_meter_type', 'status', 'reference_value'], 'integer'],
            [['name', 'code', 'category_id'], 'required'],
            [['code', 'name', 'equivalance'], 'string', 'max' => 255],
            [['code', 'name'], 'unique'],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],

        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'userAudit' => UserAuditBehavior::class,
            'status' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'status',
                'options' => [
                    self::STATUS_ENABLED => Yii::t("app", "Active"),
                    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
                    self::STATUS_DELETED => Yii::t("app", "Deleted"),

                ]
            ],
            'meter_type' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'meter_type',
                'options' => [
                    self::METER_TYPE_HOUR => Yii::t("app", "Hour"),
                    self::METER_TYPE_KM => Yii::t("app", "Km"),
                    self::FREQ_MONTHLY => Yii::t("app", "Monthly"),
                    self::FREQ_QUARTERLY => Yii::t("app", "Quarterly"),
                    self::FREQ_HALF_YEARLY => Yii::t("app", "Half Yearly"),
                    self::FREQ_YEARLY => Yii::t("app", "Yearly"),

                ]
            ],
            'alt_meter_type' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'alt_meter_type',
                'options' => [
                    self::ALT_METER_TYPE_HOUR => Yii::t("app", "Hour"),
                    self::ALT_METER_TYPE_DAY => Yii::t("app", "Day"),
                    self::ALT_METER_TYPE_WEEK => Yii::t("app", "Week"),
                    self::ALT_METER_TYPE_MONTH => Yii::t("app", "Month"),
                    self::ALT_METER_TYPE_YEAR => Yii::t("app", "Year"),

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
            'code' => 'Code',
            'meter_type' => 'Meter Type',
            'alt_meter_type' => 'Alt Meter Type',
            'equivalance' => 'Equivalance',
            'reference_value' => 'Reference Value',
            'category_id' => 'Category',
            'name' => 'Name',
        ];
    }
    public function getEquipments()
    {
        return $this->hasMany(Equipment::className(), ['equipment_type_id' => 'id']);
    }
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    public function getMallPpmTasks()
    {
        return $this->hasMany(MallPpmTasks::className(), ['equipment_type_id' => 'id']);
    }
}
