<?php

/**
 * This is the template for generating the model class of a specified table.
 */
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

$hasTimeStamp = false;
$hasBlameable = false;
$hasRandomToken = false;
$hasImage = false;
$hasStatus = false;
$hasType = false;
echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;

/**
* This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
*
<?php foreach ($tableSchema->columns as $column) : ?>
    <?php
    switch ($column->name) {
        case "created_at":
            $hasTimeStamp = true;
            break;
        case "created_by":
            $hasBlameable = true;
            break;
        case "random_token":
            $hasRandomToken = true;
            break;
        case "image":
            $hasImage = true;
            break;
        case "status":
            $hasStatus = true;
            break;
        case "type":
            $hasType = true;
            break;
        default:
            break;
    }
    ?>
<?php endforeach; ?>
<?php foreach ($tableSchema->columns as $column) : ?>
    * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)) : ?>
    *
    <?php foreach ($relations as $name => $relation) : ?>
        * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
    <?php endforeach; ?>
<?php endif; ?>
*
<?php foreach ($tableSchema->columns as $column) : ?>
    <?php
    switch ($column->name) {
        case "image":
    ?>
            * @property <?= "{$column->phpType} \${$column->name}_url\n" ?>
            * @property <?= "{$column->phpType} \${$column->name}_path\n" ?>
            * @property <?= "{$column->phpType} \${$column->name}_thumb_url\n" ?>
            * @property <?= "{$column->phpType} \${$column->name}_thumb_path\n" ?>
        <?php
            break;
        case "status":
        ?>
            * @property <?= "string \${$column->name}_label\n" ?>
            * @property <?= "label \${$column->name}_list\n" ?>
        <?php
            break;
        case "type":
        ?>
            * @property <?= "string \${$column->name}_label\n" ?>
            * @property <?= "array \${$column->name}_list\n" ?>
    <?php
            break;
        default:
            break;
    }
    ?>
    <?php  ?>
<?php endforeach; ?>
*/
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{

<?php if ($hasStatus) { ?>
    // Status
    const STATUS_DISABLED = 10;
    const STATUS_ENABLED = 20;
    //const STATUS_DELETED = 30;
<?php } ?>

<?php if ($hasType) { ?>
    // Status
    const TYPE_1 = 10;
    const TYPE_2 = 20;
<?php } ?>

/**
* @inheritdoc
*/
public static function tableName()
{
return '<?= $generator->generateTableName($tableName) ?>';
}
<?php if ($generator->db !== 'db') : ?>

    /**
    * @return \yii\db\Connection the database connection used by this AR class.
    */
    public static function getDb()
    {
    return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

/**
* @inheritdoc
*/
public function rules()
{
return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
}


/**
* @inheritdoc
*/
public function behaviors() {
return [
<?php if ($hasRandomToken) { ?>
    'random_token' => [
    'class' => \common\behaviors\RandomTokenBehavior::className(),
    'attributes' => ['random_token'],
    ],
<?php } ?>
<?php if ($hasTimeStamp) { ?>
    'timestamp' => [
    'class' => \yii\behaviors\TimestampBehavior::className(),
    'createdAtAttribute' => 'created_at',
    'updatedAtAttribute' => 'updated_at',
    'value' => new \yii\db\Expression("now()"),
    ],
<?php } ?>
<?php if ($hasBlameable) { ?>
    'blameable' => [
    'class' => \yii\behaviors\BlameableBehavior::className(),
    'createdByAttribute' => 'created_by',
    'updatedByAttribute' => 'updated_by',
    ],
<?php } ?>
<?php if ($hasStatus) { ?>
    'status' => [
    'class' => \common\behaviors\OptionsBehavior::className(),
    'attribute' => 'status',
    'options' => [
    self::STATUS_ENABLED => Yii::t("app", "Active"),
    self::STATUS_DISABLED => Yii::t("app", "Inactive"),
    //self::STATUS_DELETED => Yii::t("app", "Deleted"),
    ]
    ],
<?php } ?>
<?php if ($hasType) { ?>
    'type' => [
    'class' => \common\behaviors\OptionsBehavior::className(),
    'attribute' => 'type',
    'options' => [
    self::TYPE_1 => Yii::t("app", "type1"),
    self::TYPE_2 => Yii::t("app", "type2"),
    ]
    ],
<?php } ?>
<?php if ($hasImage) { ?>
    'image' => [
    'class' => \common\behaviors\ImageUploadBehavior::className(),
    'attribute' => 'image',
    'thumbs' => [
    'thumb' => ['width' => 250, 'height' => 250],
    ],
    'filePath' => '@static/upload/images/<?= $tableName ?>/<?= $tableName ?>_[[pk]]_[[attribute_random_token]].[[extension]]',
    'fileUrl' => '@staticWeb/upload/images/<?= $tableName ?>/<?= $tableName ?>_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
    'thumbPath' => '@static/upload/images/<?= $tableName ?>/[[profile]]/<?= $tableName ?>_[[pk]]_[[attribute_random_token]].[[extension]]',
    'thumbUrl' => '@staticWeb/upload/images/<?= $tableName ?>/[[profile]]/<?= $tableName ?>_[[pk]]_[[attribute_random_token]].[[extension]]?_=[[md5_attribute_updated_at]]',
    ],
<?php } ?>
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
<?php foreach ($labels as $name => $label) : ?>
    <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
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
<?php foreach ($relations as $name => $relation) : ?>

    /**
    * @return \yii\db\ActiveQuery
    */
    public function get<?= $name ?>()
    {
    <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName) : ?>
    <?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
    ?>
    /**
    * @inheritdoc
    * @return <?= $queryClassFullName ?> the active query used by this AR class.
    */
    public static function find()
    {
    return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
}