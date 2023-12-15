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
 * This is the model class for table "customer".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property User[] $users
 * @property CustomerUser[] $customerUsers
 * @property Location[] $locations
 *
 * @property string $status_label
 * @property label $status_list
 */
class Customer extends ActiveRecord
{

    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
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
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
            [['code', 'name', 'address', 'phone'], 'string', 'max' => 255],
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
            'id'         => Yii::t('app', 'ID'),
            'code'       => Yii::t('app', 'Code'),
            'name'       => Yii::t('app', 'Name'),
            'address'    => Yii::t('app', 'Address'),
            'phone'      => Yii::t('app', 'Phone'),
            'status'     => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
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
    public function getEquipments()
    {

        return $this->hasMany(Equipment::className(), ['location_id' => 'id'])
            ->via("locations");
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomerUsers()
    {
        return $this->hasMany(CustomerUser::className(), ['customer_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable("customer_user", ["customer_id" => "id"]);
    }

    /**
     * @return ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['customer_id' => 'id']);
    }
}
