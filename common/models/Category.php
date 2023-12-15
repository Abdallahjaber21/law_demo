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
use yii\helpers\ArrayHelper;
use yii\db\Query;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $parent_id
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Category $parent
 * @property Category[] $categories
 *  * @property Profession[] $professions
 * @property Equipment[] $equipments
 * @property EquipmentType[] $equipmentTypes

 * @property ProfessionCategory[] $professionCategories
 *  * @property label $state_id_list
 * @property State $state
 */
class Category extends \yii\db\ActiveRecord
{
    public $cat_id;

    const STATUS_ENABLED = 20;
    const STATUS_DISABLED = 10;
    const STATUS_DELETED = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['description', 'code'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['parent_id' => 'id']],
            ['code', 'required']
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

        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'parent_id' => Yii::t('app', 'Parent Category '),
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

    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getCategories()
    {
        return static::find()->select(['name'])->where(['<>', 'status', Profession::STATUS_DELETED])->orderBy('name')->indexBy('id')->column();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipments()
    {
        return $this->hasMany(Equipment::className(), ['category_id' => 'id']);
    }
    public function getEquipmentTypes()
    {
        return $this->hasMany(EquipmentType::className(), ['category_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfessionCategories()
    {
        return $this->hasMany(ProfessionCategory::className(), ['category_id' => 'id']);
    }
    public function getProfessions()
    {
        return $this->hasMany(Profession::class, ['id' => 'profession_id'])
            ->viaTable('profession_category', ['category_id' => 'id']);
    }
}
