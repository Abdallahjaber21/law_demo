<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ppm_additional_tasks".
 *
 * @property integer $id
 * @property integer $equipment_type_id
 * @property string $name
 * @property string $service
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property EquipmentType $equipmentType
 * @property PpmAdditionalTasksValues[] $ppmAdditionalTasksValues
 *
 * @property string $status_label
 * @property label $status_list
 */
class PpmAdditionalTasks extends \yii\db\ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ppm_additional_tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment_type_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'service'], 'string', 'max' => 255],
            [['equipment_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EquipmentType::className(), 'targetAttribute' => ['equipment_type_id' => 'id']],
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
            'equipment_type_id' => 'Equipment Type ID',
            'name' => 'Name',
            'service' => 'Service',
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
    public function getEquipmentType()
    {
        return $this->hasOne(EquipmentType::className(), ['id' => 'equipment_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPpmAdditionalTasksValues()
    {
        return $this->hasMany(PpmAdditionalTasksValues::className(), ['additional_task_id' => 'id']);
    }
}
