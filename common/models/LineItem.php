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
 * This is the model class for table "line_item".
 *
 * @property integer $id
 * @property integer $repair_request_id
 * @property integer $object_code_id
 * @property integer $cause_code_id
 * @property integer $damage_code_id
 * @property integer $manufacturer_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $type
 *
 * @property CauseCode $causeCode
 * @property DamageCode $damageCode
 * @property Manufacturer $manufacturer
 * @property ObjectCode $objectCode
 * @property RepairRequest $repairRequest
 *
 * @property string $status_label
 * @property label $status_list
 */
class LineItem extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    // TYPE
    const TYPE_TECHNICIAN = 10;
    const TYPE_ATL = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'line_item';
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
            [['repair_request_id', 'object_code_id', 'cause_code_id', 'damage_code_id', 'manufacturer_id'], 'required'],
            [['repair_request_id', 'object_code_id', 'cause_code_id', 'damage_code_id', 'manufacturer_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['cause_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => CauseCode::className(), 'targetAttribute' => ['cause_code_id' => 'id']],
            [['damage_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => DamageCode::className(), 'targetAttribute' => ['damage_code_id' => 'id']],
            [['manufacturer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Manufacturer::className(), 'targetAttribute' => ['manufacturer_id' => 'id']],
            [['object_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectCode::className(), 'targetAttribute' => ['object_code_id' => 'id']],
            [['repair_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => RepairRequest::className(), 'targetAttribute' => ['repair_request_id' => 'id']],
            ['type', 'integer'],
            ['type', 'default', 'value' => self::TYPE_TECHNICIAN],
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
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression("now()"),
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
            'type'    => [
                'class'     => OptionsBehavior::className(),
                'attribute' => 'type',
                'options'   => [
                    self::TYPE_ATL  => Yii::t("app", "ATL"),
                    self::TYPE_TECHNICIAN => Yii::t("app", "Technician"),
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
            'id'              => Yii::t('app', 'ID'),
            'object_code_id'  => Yii::t('app', 'Object Code'),
            'cause_code_id'   => Yii::t('app', 'Cause Code'),
            'damage_code_id'  => Yii::t('app', 'Damage Code'),
            'manufacturer_id' => Yii::t('app', 'Manufacturer'),
            'status'          => Yii::t('app', 'Status'),
            'created_at'      => Yii::t('app', 'Created At'),
            'updated_at'      => Yii::t('app', 'Updated At'),
            'created_by'      => Yii::t('app', 'Created By'),
            'updated_by'      => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
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

    /**
     * @return ActiveQuery
     */
    public function getCauseCode()
    {
        return $this->hasOne(CauseCode::className(), ['id' => 'cause_code_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDamageCode()
    {
        return $this->hasOne(DamageCode::className(), ['id' => 'damage_code_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getManufacturer()
    {
        return $this->hasOne(Manufacturer::className(), ['id' => 'manufacturer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getObjectCode()
    {
        return $this->hasOne(ObjectCode::className(), ['id' => 'object_code_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRepairRequest()
    {
        return $this->hasOne(RepairRequest::className(), ['id' => 'repair_request_id']);
    }

    public function fields()
    {
        return [
            "id",
            "objectCode",
            "causeCode",
            "damageCode",
            "manufacturer",
        ];
    }
}
