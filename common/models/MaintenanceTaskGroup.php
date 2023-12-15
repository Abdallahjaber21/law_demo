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
 * This is the model class for table "maintenance_task_group".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property integer $equipment_type
 * @property integer $group_order
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property MaintenanceTask[] $maintenanceTasks
 *
 * @property string $status_label
 * @property label $status_list
 */
class MaintenanceTaskGroup extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //

    const TYPE_ELECTRIC_ELEVATOR = 10;
    const TYPE_HYDRAULIC_ELEVATOR = 20;
    const TYPE_ESCALATOR = 30;
    const TYPE_3RDPARTY_ELECTRIC_ELEVATOR = 40;
    const TYPE_3RDPARTY_HYDRAULIC_ELEVATOR = 50;
    const TYPE_3RDPARTY_ESCALATOR = 60;
    const TYPE_GARAGE_DOOR = 70;
    const TYPE_REVOLVING_DOOR = 80;
    const TYPE_WINDOW_CLEANING_SYSTEM = 90;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'maintenance_task_group';
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
            [['group_order', 'status', 'created_by', 'updated_by', 'equipment_type'], 'integer'],
            [['code', 'name', 'equipment_type'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'name'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['group_order'], 'default', 'value' => 99],
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
                ]
            ],

            'equipment_type' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'equipment_type',
                'options' => [
                    self::TYPE_ELECTRIC_ELEVATOR           => Yii::t("app", "Electric Elevator"),
                    self::TYPE_HYDRAULIC_ELEVATOR          => Yii::t("app", "Hydraulic Elevator"),
                    self::TYPE_ESCALATOR                   => Yii::t("app", "Escalator"),
                    self::TYPE_3RDPARTY_ELECTRIC_ELEVATOR  => Yii::t("app", "Electric Elevator"),
                    self::TYPE_3RDPARTY_HYDRAULIC_ELEVATOR => Yii::t("app", "Hydraulic Elevator"),
                    self::TYPE_3RDPARTY_ESCALATOR          => Yii::t("app", "Escalator"),
                    self::TYPE_GARAGE_DOOR                 => Yii::t("app", "Garage Door"),
                    self::TYPE_REVOLVING_DOOR              => Yii::t("app", "Revolving Door"),
                    self::TYPE_WINDOW_CLEANING_SYSTEM      => Yii::t("app", "Window Cleaning System"),
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
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'group_order' => Yii::t('app', 'Group Order'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'equipment_type' => Yii::t('app', 'Equipment Type'),
        ];
    }

    public function fields()
    {
        return [
            "id",
            "code",
            "name",
            "maintenanceTasks" => function (MaintenanceTaskGroup $model) {
                $month = date('n');
                $date = date('j');
                $AorB = $date > 15 ? 'b' : 'a';
                return $model->getMaintenanceTasks()
                    ->where(["m_{$month}_{$AorB}" => 1])
                    ->orderBy(['code' => SORT_ASC])
                    ->all();
            },
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMaintenanceTasks()
    {
        return $this->hasMany(MaintenanceTask::className(), ['maintenance_task_group_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMaintenanceTasksByDate($date)
    {
        if (empty($date)) {
            return [];
        }
        $month = date('n', strtotime($date));
        $date = date('j', strtotime($date));
        $AorB = $date > 15 ? 'b' : 'a';
        return $this->getMaintenanceTasks()
            ->where(["m_{$month}_{$AorB}" => 1])
            ->orderBy(['code' => SORT_ASC])
            ->all();
    }
}
