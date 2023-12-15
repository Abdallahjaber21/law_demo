<?php

namespace common\models;

use Yii;

/**
* This is the model class for table "auth_item".
*
                                * @property string $name
    * @property integer $type
    * @property string $description
    * @property string $rule_name
    * @property resource $data
    * @property integer $created_at
    * @property integer $updated_at
    *
            * @property AccountType[] $accountTypes
            * @property AuthAssignment[] $authAssignments
            * @property AuthRule $ruleName
            * @property AuthItemChild[] $authItemChildren
            * @property AuthItemChild[] $authItemChildren0
            * @property AuthItem[] $children
            * @property AuthItem[] $parents
    *
                        * @property string $type_label
            * @property array $type_list
                                                */
class AuthItem extends \yii\db\ActiveRecord
{


    // Status
    const TYPE_1 = 10;
    const TYPE_2 = 20;

/**
* @inheritdoc
*/
public static function tableName()
{
return 'auth_item';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
}


/**
* @inheritdoc
*/
public function behaviors() {
return [
    'timestamp' => [
    'class' => \yii\behaviors\TimestampBehavior::className(),
    'createdAtAttribute' => 'created_at',
    'updatedAtAttribute' => 'updated_at',
    'value' => new \yii\db\Expression("now()"),
    ],
    'type' => [
    'class' => \common\behaviors\OptionsBehavior::className(),
    'attribute' => 'type',
    'options' => [
    self::TYPE_1 => Yii::t("app", "type1"),
    self::TYPE_2 => Yii::t("app", "type2"),
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

public static function findEnabled() {
return parent::find()->where(['status'=> self::STATUS_ENABLED]);
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'name' => 'Name',
    'type' => 'Type',
    'description' => 'Description',
    'rule_name' => 'Rule Name',
    'data' => 'Data',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
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
    public function getAccountTypes()
    {
    return $this->hasMany(AccountType::className(), ['role_id' => 'name']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getAuthAssignments()
    {
    return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getRuleName()
    {
    return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getAuthItemChildren()
    {
    return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getAuthItemChildren0()
    {
    return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getChildren()
    {
    return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getParents()
    {
    return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }
}