<?php

namespace common\components\settings;

use common\exceptions\SettingNotFoundException;
use common\exceptions\UnableToSaveObjectException;
use common\widgets\inputs\ICheck;
use dosamigos\ckeditor\CKEditor;
use kartik\datetime\DateTimePicker;
use common\components\extensions\Select2;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Description of Setting
 *
 * @author Tarek K. Ajaj
 * Apr 7, 2017 11:18:03 AM
 * 
 * Setting.php
 * UTF-8
 * 
 * @property integer $id
 * @property integer $setting_category_id
 * @property string $name
 * @property string $label
 * @property string $description
 * @property integer $type
 * @property string $value
 * @property string $config
 *
 * @property SettingCategory $settingCategory
 */
class Setting extends ActiveRecord {

    CONST TYPE_STRING = 10;
    CONST TYPE_INTEGER = 20;
    CONST TYPE_BOOLEAN = 30;
    CONST TYPE_OPTION = 40;
    CONST TYPE_MULTIPLE = 50;
    CONST TYPE_DATETIME = 60;
    CONST TYPE_FILE = 70;
    CONST TYPE_RICHTEXT = 80;
    CONST TYPE_NUMBER = 90;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            //--- Category ID
            ['setting_category_id', 'required'],
            ['setting_category_id', 'integer'],
            ['setting_category_id', 'exist', 'skipOnError' => true, 'targetClass' => SettingCategory::className(), 'targetAttribute' => ['setting_category_id' => 'id']],
            //--- Name
            ['name', 'required'],
            ['name', 'unique'],
            ['name', 'string', 'max' => 50],
            //--- Label
            ['label', 'required'],
            ['label', 'string', 'max' => 255],
            //--- Label
            ['description', 'string', 'max' => 255],
            //--- Type
            ['type', 'required'],
            ['type', 'integer'],
            ['type', 'in', 'range' => [
                    self::TYPE_STRING,
                    self::TYPE_INTEGER,
                    self::TYPE_BOOLEAN,
                    self::TYPE_OPTION,
                    self::TYPE_MULTIPLE,
                    self::TYPE_DATETIME,
                    self::TYPE_FILE,
                    self::TYPE_RICHTEXT,
                    self::TYPE_NUMBER,
                ]
            ],
            //--- Value
            ['value', 'string'],
            //--- Config
            ['config', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t("app", 'ID'),
            'setting_category_id' => Yii::t("app", 'Setting Category'),
            'name' => Yii::t("app", 'Name'),
            'label' => Yii::t("app", 'Label'),
            'type' => Yii::t("app", 'Type'),
            'value' => Yii::t("app", 'Value'),
            'config' => Yii::t("app", 'Config'),
            'description' => Yii::t("app", 'Description'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSettingCategory() {
        return $this->hasOne(SettingCategory::className(), ['id' => 'setting_category_id']);
    }

    /**
     * find a setting by it's name
     * @param string $name
     */
    public static function findByName($name) {
        return self::findOne(['name' => $name]);
    }

    /**
     * Create a new setting with the given name and configuration assigned for
     * a specific category
     * 
     * $config is an array having the following keys
     * [
     *  'label', The label of the setting
     *  'description', Simple description about this setting
     *  'type', type of this setting see Setting for available types
     *  'default' the default value of this setting
     * ]
     * 
     * @param string $name
     * @param array $config
     * @param integer $category_id
     * @return boolean whether the setting is saved or not
     */
    public static function addNew($name, $config, $category_id) {
        $setting = new Setting();
        $setting->name = $name;
        return $setting->updateSetting($config, $category_id);
    }

    /**
     * update the current setting with the new configuration and category
     * 
     * @param array $config
     * @param integer $category_id
     * @return boolean whether the setting is saved or not
     */
    public function updateSetting($config, $category_id) {
        $this->label = !empty($config['label']) ? $config['label'] : Inflector::camel2words($this->name, true);
        $this->description = !empty($config['description']) ? $config['description'] : '';
        $this->setting_category_id = $category_id;
        $this->type = !empty($config['type']) ? $config['type'] : self::TYPE_STRING;
        $value = empty($this->value) ? !empty($config['default']) ? $config['default'] : '' : $this->value;
        switch ($this->type) {
            case self::TYPE_INTEGER:
                $value = strval($value);
                break;
            case self::TYPE_NUMBER:
                $value = strval($value);
                break;
            case self::TYPE_BOOLEAN:
                $value = strval($value);
                break;
            case self::TYPE_MULTIPLE:
                $value = implode(",", $value);
                break;
        }
        $this->value = $value;
        $this->config = !empty($config['config']) ? Json::encode($config['config']) : '{}';
        return $this->save();
    }

    public static $SETTINGS_CACHE = [];

    /**
     * get formatted value of a setting by name
     * 
     * @param string $name
     * @return mixed
     * @throws SettingNotFoundException
     */
    public static function getValue($name) {
        $cache = self::$SETTINGS_CACHE;
        if (!empty($cache[$name])) {
            return $cache[$name];
        }
        $setting = Setting::findByName($name);
        if (!empty($setting)) {
            switch ($setting->type) {
                case self::TYPE_INTEGER:
                    $cache[$name] = intval($setting->value);
                    break;
                case self::TYPE_NUMBER:
                    $cache[$name] = floatval($setting->value);
                    break;
                case self::TYPE_BOOLEAN:
                    $cache[$name] = boolval($setting->value);
                    break;
                case self::TYPE_MULTIPLE:
                    $cache[$name] = explode(",", $setting->value);
                    break;
                default:
                    $cache[$name] = $setting->value;
                    break;
            }
            self::$SETTINGS_CACHE = $cache;
            return $cache[$name];
        }
        throw new NotFoundHttpException(Yii::t("app", "{name} setting not found", ['name' => $name]));
    }

    /**
     * Update value of a setting by name
     *
     * @param string $name
     * @param mixed $value
     * @return Setting|null
     * @throws NotFoundHttpException
     */
    public static function setValue($name, $value){
        $setting = Setting::findByName($name);
        if (!empty($setting)) {
            switch ($setting->type) {
                case self::TYPE_INTEGER:
                    if(is_integer($value)) {
                        $setting->value = $value;
                    }
                    break;
                case self::TYPE_NUMBER:
                    if(is_numeric($value)) {
                        $setting->value = $value;
                    }
                    break;
                case self::TYPE_BOOLEAN:
                    if(is_bool($value)) {
                        $setting->value = $value;
                    }
                    break;
                case self::TYPE_MULTIPLE:
                    if(is_array($value)) {
                        $setting->value = implode(",", $value);
                    }
                    break;
                default:
                    $setting->value = $value;
            }
            $setting->save();
            return $setting;
        }
        throw new NotFoundHttpException(Yii::t("app", "{name} setting not found", ['name' => $name]));

    }

    /**
     * update the settings value from post data containing setting name
     * as key and the new value as its value
     * 
     * @param array $post
     */
    public static function updateSettings($post) {
        if (!empty($post)) {
            $settings = Setting::find()->all();
            foreach ($settings as $key => $setting) {
                if (!empty($post[$setting->name]) || $setting->type == Setting::TYPE_FILE) {
                    if ($setting->type == Setting::TYPE_FILE) {
                        $uploadedFile = UploadedFile::getInstanceByName($setting->name);
                        if (!empty($uploadedFile) && $uploadedFile instanceof UploadedFile) {
                            $random = \Yii::$app->getSecurity()->generateRandomString();
                            $path = Yii::getAlias("@upload/files/{$setting->id}_{$random}.{$uploadedFile->extension}");
                            FileHelper::createDirectory(pathinfo($path, PATHINFO_DIRNAME), 0775, true);
                            if ($uploadedFile->saveAs($path)) {
                                $setting->value = Yii::getAlias("@uploadWeb/files/{$setting->id}_{$random}.{$uploadedFile->extension}");
                            } else {
                                Yii::$app->getSession()->addFlash("error", "{$setting->label}: File upload failed");
                            }
                        }
                    } else if (is_array($post[$setting->name])) {
                        $setting->value = implode(",", $post[$setting->name]);
                    } else {
                        $setting->value = $post[$setting->name];
                    }
                } else {
                    if ($setting->type == Setting::TYPE_BOOLEAN) {
                        $setting->value = "0";
                    }
                }
                if (!$setting->save()) {
                    $errors = $setting->getErrors();
                    foreach ($errors as $attr) {
                        foreach ($attr as $error) {
                            Yii::$app->getSession()->addFlash("error", "{$setting->label}: {$error}");
                        }
                    }
                }
            }
        }
    }

    /**
     * returns the correspodning input of the setting based on its type
     * 
     * @return string
     */
    public function renderInput() {
        switch ($this->type) {
            case self::TYPE_STRING:
                return Html::tag("div"
                                , Html::tag("label", $this->label, ['class' => 'control-label']) .
                                Html::input('text', $this->name, $this->value, ['class' => 'form-control'])
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
            case self::TYPE_INTEGER:
                return Html::tag("div"
                                , Html::tag("label", $this->label, ['class' => 'control-label']) .
                                Html::input('number', $this->name, $this->value, ['class' => 'form-control'])
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
             case self::TYPE_NUMBER:
                return Html::tag("div"
                                , Html::tag("label", $this->label, ['class' => 'control-label']) .
                                Html::input('number', $this->name, $this->value, ['class' => 'form-control', 'step'=>"0.01"])
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
            case self::TYPE_DATETIME:
                return Html::tag("div"
                                , Html::tag("label", $this->label, ['class' => 'control-label']) .
                                DateTimePicker::widget([
                                    'name' => $this->name,
                                    'type' => DateTimePicker::TYPE_INPUT,
                                    'value' => $this->value,
                                    'layout' => '{input}{picker}',
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd hh:ii:00',
                                        'minuteStep' => 5,
                                    ]
                                ])
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
            case self::TYPE_BOOLEAN:
                return Html::tag("div"
                                , Html::tag("div", "&nbsp;", ['class' => 'control-label']) .
                                Html::tag("label", ICheck::widget(['name' => $this->name, 'is_checked' => $this->value]) .
                                        " " . $this->label)
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
            case self::TYPE_OPTION:
                $config = Json::decode($this->config);
                $options = $config['options'];
                return Html::tag("div"
                                , Html::tag("label", $this->label, ['class' => 'control-label']) .
                                Html::dropDownList($this->name, $this->value, $options, ['class' => 'form-control'])
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
            case self::TYPE_MULTIPLE:
                $config = Json::decode($this->config);
                $options = $config['options'];
                return Html::tag("div"
                                , Html::tag("label", $this->label, ['class' => 'control-label']) .
                                Select2::widget([
                                    'name' => $this->name,
                                    'data' => $options,
                                    'theme' => Select2::THEME_DEFAULT,
                                    'value' => explode(",", $this->value),
                                    'options' => [
                                        'multiple' => true
                                    ]
                                ])
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
            case self::TYPE_FILE:
                return Html::tag("div"
                                , Html::tag("label", $this->label, ['class' => 'control-label']) .
                                Html::a(' <i class="fa fa-eye"></i>', $this->value, ['target' => '_blank']) .
                                Html::fileInput($this->name, null, ['class' => 'form-control'])
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
            case self::TYPE_RICHTEXT:
                return Html::tag("div"
                                , Html::tag("label", $this->label, ['class' => 'control-label']) .
                                CKEditor::widget([
                                    'name' => $this->name,
                                    'options' => ['rows' => 10],
                                    'value' => $this->value,
                                    'preset' => 'full',
                                    'clientOptions' => [
                                        'filebrowserUploadUrl' => '/media/image-upload',
                                        'templates_files' => ['/mytemplates.js'],
                                        'enterMode' => 2, //CKEDITOR.ENTER_BR
                                        'shiftEnterMode' => 3, //CKEDITOR.ENTER_DIV
                                        'startupOutlineBlocks' => true
                                    ]
                                ])
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
            default:
                return Html::tag("div"
                                , Html::tag("label", $this->label, ['class' => 'control-label']) .
                                Html::input('text', $this->name, $this->value, ['class' => 'form-control'])
                                , ['class' => 'form-group', 'data-toggle' => 'tooltip', 'title' => $this->description]);
        }
    }

    /**
     * increment an integer setting value by the given number. default to 1
     * 
     * @param string $name
     * @param integer $increment
     * @return boolean
     */
    public static function incrementValue($name, $increment = 1) {
        $cache = self::$SETTINGS_CACHE;
        $setting = Setting::findByName($name);
        if (!empty($setting)) {
            if ($setting->type == self::TYPE_INTEGER) {
                $setting->value = strval(intval($setting->value) + $increment);
                if ($setting->save()) {
                    $cache[$name] = $setting->value;
                    self::$SETTINGS_CACHE = $cache;
                    return $cache[$name];
                } else {
                    throw new UnableToSaveObjectException(Json::encode($setting->getErrors()));
                }
            }
        }
        return false;
    }

}
