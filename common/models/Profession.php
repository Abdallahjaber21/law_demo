<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use common\behaviors\UserAuditBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "profession".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 *  @property Profession[] $profession
 * @property ProfessionCategory[] $professionCategories 
 * @property Technician[] $technicians
 * @property Category[] $categories
 * @property User[] $users
 * @property label $status_list
 * @property State $state
 */
class Profession extends \yii\db\ActiveRecord
{
    public $prof_id;
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    const STATUS_DELETED = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profession';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique']
        ];
    }

    public function behaviors()
    {
        return [
            'userAudit' => UserAuditBehavior::class,
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
                    self::STATUS_DELETED => Yii::t("app", "Deleted"),


                ]
            ],
            //    'multilingual' => [
            //        'class' => \yeesoft\multilingual\behaviors\MultilingualBehavior::className(),
            //        'attributes' => []
            //    ],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfessionCategories()
    {
        return $this->hasMany(ProfessionCategory::className(), ['profession_id' => 'id']);
    }
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('profession_category', ['profession_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicians()
    {
        return $this->hasMany(Technician::className(), ['profession_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['profession_id' => 'id']);
    }
    public static function getProfessions()
    {
        return static::find()->select(['name'])->orderBy('name')->indexBy('id')->column();
    }
}
