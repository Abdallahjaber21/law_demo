<?php

namespace common\models;

use common\behaviors\OptionsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string $type
 * @property int $for_backend
 * @property string $description
 *
 * @property AccountType $type0
 * @property Admin[] $admins
 * @property Notification[] $notifications
 * @property Technician[] $technicians
 * @property User[] $users
 */
class Account extends \yii\db\ActiveRecord
{

    public $admin_account;
    public $technician_account;

    // Backend
    const DEVELOPER = 10;
    const SUPER_ADMIN = 20;
    const PLANT_ADMIN = 30;
    const MALL_ADMIN = 40;
    const VILLA_ADMIN = 50;
    const FLEET_MANAGER = 60;
    const PLANT_MANAGER = 70;
    const STORE_KEEPER = 80;

    // APP
    const SUPERVISOR = 70;
    const TECHNICIAN = 80;
    const PURCHASER = 90;
    const COORDINATOR = 100;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['for_backend', 'type'], 'integer'],
            [['description'], 'string', 'max' => 255],
            [['admin_account', 'technician_account'], 'safe']
        ];
    }

    public function behaviors()
    {
        return [
            'admin_account' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'admin_account',
                'options' => [
                    self::DEVELOPER => Yii::t("app", "Developer"),
                    self::PLANT_ADMIN => Yii::t("app", "Plant Admin"),
                    self::MALL_ADMIN => Yii::t("app", "Mall Admin"),
                    self::VILLA_ADMIN => Yii::t("app", "Villa Admin"),
                    self::FLEET_MANAGER => Yii::t("app", "Fleet Manager"),
                    self::PLANT_MANAGER => Yii::t("app", "Plant Manager"),
                    self::STORE_KEEPER => Yii::t("app", "Store Keeper"),
                    self::SUPER_ADMIN => Yii::t("app", "Super Admin"),
                ]
            ],
            'technician_account' => [
                'class' => OptionsBehavior::className(),
                'attribute' => 'technician_account',
                'options' => [
                    self::COORDINATOR => Yii::t("app", "Coordinator"),
                    self::PURCHASER => Yii::t("app", "Purchaser"),
                    self::SUPERVISOR => Yii::t("app", "Supervisor"),
                    self::TECHNICIAN => Yii::t("app", "Technician"),
                ]
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'for_backend' => Yii::t('app', 'For Backend'),
            'description' => Yii::t('app', 'Description'),
            'admin_account' => Yii::t('app', 'Account Type'),
            'technician_account' => Yii::t('app', 'Account Type'),

        ];
    }
    // public static function getAccountTypesForBackend0()
    // {
    //     return self::find()->where(['for_backend' => 0])->all();
    // }

    // /**
    //  * Get account types with backend_for = 1.
    //  *
    //  * @return array Account types with backend_for = 1.
    //  */
    // public static function getAccountTypesForBackend1()
    // {
    //     return self::find()->where(['for_backend' => 1])->all();
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(AccountType::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmins()
    {
        return $this->hasMany(Admin::className(), ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        // return $this->hasMany(Notification::className(), ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTechnicians()
    {
        return $this->hasMany(Technician::className(), ['account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['account_id' => 'id']);
    }

    public static function getAdminOptions()
    {
        $model = new Account();

        switch (static::getAdminTypeID()) { // 30
            case static::SUPER_ADMIN:
                return $model->admin_account_list;
                break;
            // case static::ADMIN:
            //     $array = $model->admin_account_list;
            //     unset($array[static::SUPER_ADMIN]);
            //     return $array;
            //     break;
        }

        return $model->admin_account_list;
    }

    public static function getAdminOptionsCrud()
    {
        $model = new Account();

        switch (static::getAdminTypeID()) { // 30
            case static::DEVELOPER:
                $array = $model->admin_account_list;
                return $array;
                break;
            case static::SUPER_ADMIN:
                $array = $model->admin_account_list;
                unset($array[static::DEVELOPER]);
                unset($array[static::SUPER_ADMIN]);
                return $array;
                break;
            case static::PLANT_ADMIN:
                $array = $model->admin_account_list;
                unset($array[static::DEVELOPER]);
                unset($array[static::SUPER_ADMIN]);
                unset($array[static::VILLA_ADMIN]);
                unset($array[static::MALL_ADMIN]);
                return $array;
                break;
            case static::VILLA_ADMIN:
                $array = $model->admin_account_list;
                unset($array[static::DEVELOPER]);
                unset($array[static::SUPER_ADMIN]);
                unset($array[static::PLANT_ADMIN]);
                unset($array[static::MALL_ADMIN]);
                return $array;
                break;
            case static::MALL_ADMIN:
                $array = $model->admin_account_list;
                unset($array[static::DEVELOPER]);
                unset($array[static::SUPER_ADMIN]);
                unset($array[static::VILLA_ADMIN]);
                unset($array[static::PLANT_ADMIN]);
                return $array;
                break;
            case static::FLEET_MANAGER:
                $array = $model->admin_account_list;
                unset($array[static::DEVELOPER]);
                unset($array[static::SUPER_ADMIN]);
                unset($array[static::VILLA_ADMIN]);
                unset($array[static::PLANT_ADMIN]);
                unset($array[static::MALL_ADMIN]);
                unset($array[static::PLANT_MANAGER]);
                unset($array[static::STORE_KEEPER]);
                return $array;
                break;
            case static::PLANT_MANAGER:
                $array = $model->admin_account_list;
                unset($array[static::DEVELOPER]);
                unset($array[static::SUPER_ADMIN]);
                unset($array[static::VILLA_ADMIN]);
                unset($array[static::PLANT_ADMIN]);
                unset($array[static::MALL_ADMIN]);
                unset($array[static::FLEET_MANAGER]);
                unset($array[static::STORE_KEEPER]);
                return $array;
                break;
            case static::STORE_KEEPER:
                $array = $model->admin_account_list;
                unset($array[static::DEVELOPER]);
                unset($array[static::SUPER_ADMIN]);
                unset($array[static::VILLA_ADMIN]);
                unset($array[static::PLANT_ADMIN]);
                unset($array[static::MALL_ADMIN]);
                unset($array[static::FLEET_MANAGER]);
                unset($array[static::PLANT_MANAGER]);
                return $array;
                break;
            default:
                $array = $model->admin_account_list;
                unset($array[static::DEVELOPER]);
                unset($array[static::SUPER_ADMIN]);
                unset($array[static::VILLA_ADMIN]);
                unset($array[static::PLANT_ADMIN]);
                unset($array[static::MALL_ADMIN]);
                return $array;
                break;
        }

        return $model->admin_account_list;
    }

    // public static function getAdminRoleOptions()
    // {

    //     $roles = Yii::$app->authManager->getRoles();

    //     $out = [];
    //     foreach ($roles as $index => $role) {
    //         $out[] = $index;
    //     }

    //     // return $out;

    //     $user_type_arr = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    //     $user_type = array_values($user_type_arr)[0]->name;

    //     switch ($user_type) {
    //         case 'developer':
    //             return $out;
    //             break;
    //         case 'super-admin':
    //             unset($out[array_search('developer', $out)]);
    //             unset($out[array_search('super-admin', $out)]);
    //             return $out;
    //             break;
    //         case 'admin':
    //             unset($out[array_search('developer', $out)]);
    //             unset($out[array_search('super-admin', $out)]);
    //             unset($out[array_search('admin', $out)]);
    //             return $out;
    //             break;
    //         case 'fleet-manager':
    //             unset($out[array_search('developer', $out)]);
    //             unset($out[array_search('super-admin', $out)]);
    //             unset($out[array_search('admin', $out)]);
    //             unset($out[array_search('store-keeper', $out)]);
    //             unset($out[array_search('plant-manager', $out)]);
    //             return $out;
    //             break;
    //         case 'plant-manager':
    //             unset($out[array_search('developer', $out)]);
    //             unset($out[array_search('super-admin', $out)]);
    //             unset($out[array_search('admin', $out)]);
    //             unset($out[array_search('store-keeper', $out)]);
    //             unset($out[array_search('fleet-manager', $out)]);
    //             return $out;
    //             break;
    //         case 'store-keeper':
    //             unset($out[array_search('developer', $out)]);
    //             unset($out[array_search('super-admin', $out)]);
    //             unset($out[array_search('admin', $out)]);
    //             unset($out[array_search('plant-manager', $out)]);
    //             unset($out[array_search('fleet-manager', $out)]);
    //             return $out;
    //             break;
    //         default:
    //             unset($out[array_search('developer', $out)]);
    //             unset($out[array_search('super-admin', $out)]);
    //             unset($out[array_search('admin', $out)]);
    //             return $out;
    //             break;
    //     }
    // }

    public static function getAdminTypeID($id = null)
    {

        if (!empty($id)) {
            return @Account::findOne($id)->type0->name; // Num
        }

        return Account::findOne(Yii::$app->user->id)->type; // Num
    }
    public static function getAdminDivisionID($id = null)
    {

        if (!empty($id)) {
            return Division::findOne($id)->id;
        }

        return Yii::$app->user->identity->division_id;
    }

    public static function getAdminDivisionModel($id = null)
    {

        if (!empty($id)) {
            return Admin::findOne($id)->division;
        }

        return Admin::findOne(Yii::$app->user->id)->division;
    }

    public static function getAdminTypeLabel($id = null)
    {
        $model = new Account();

        if (!empty($id)) {
            return @$model->admin_account_list[Account::findOne($id)->type];
        }

        return $model->admin_account_list[Account::findOne(Yii::$app->user->id)->type];
    }

    public static function getAdminMainSectorId($id = null)
    {
        $model = Admin::findOne(Yii::$app->user->id);

        if (!empty($id)) {
            $model = Admin::findOne($id);
        }

        return $model->main_sector_id;
    }

    public static function getTechnicianOptions()
    {
        $opts = AccountType::find()->where(['for_backend' => false])->andWhere(['status' => AccountType::STATUS_ENABLED])->orderBy(['name' => SORT_ASC])->all();

        return ArrayHelper::map($opts, 'id', 'label');
    }

    public static function gettechnicianTypeID($id = null)
    {

        if (!empty($id)) {
            return Account::findOne($id)->type; // Num
        }

        return Account::findOne(Yii::$app->user->id)->type; // Num
    }

    public static function getTechnicianTypeLabel($id = null)
    {
        $model = new Account();

        if (!empty($id)) {
            return @$model->technician_account_list[Account::findOne($id)->type];
        }

        return $model->technician_account_list[Account::findOne(Yii::$app->user->id)->type];
    }


    public static function ValidateNumber($number, $country_sym = null)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        // Parse a phone number
        $phoneNumber = @$phoneUtil->parse($number, null);

        // Get the country code
        $countryCode = $phoneNumber->getCountryCode();

        // Get the country symbol from the country code
        $countrySymbol = \libphonenumber\CountryCodeToRegionCodeMap::$countryCodeToRegionCodeMap[$countryCode][0];

        $phoneNumber = $phoneUtil->parse($number, $countrySymbol);

        return $phoneUtil->isValidNumber($phoneNumber);
    }

    public static function GetCountryName($number)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        // Parse a phone number
        $phoneNumber = @$phoneUtil->parse($number, null);

        // Get the country code
        $countryCode = $phoneNumber->getCountryCode();

        // Get the country symbol from the country code
        $countrySymbol = \libphonenumber\CountryCodeToRegionCodeMap::$countryCodeToRegionCodeMap[$countryCode][0];

        return $countrySymbol;
    }

    public static function getAdminLocations()
    {
        $out = Location::find()->joinWith('sector')->where(['division_id' => Yii::$app->user->identity->division_id, 'sector.main_sector_id' => Account::getAdminMainSectorId()])->all();

        if (empty(Account::getAdminAccountTypeDivisionModel())) {
            $out = MainSector::findOne(Account::getAdminMainSectorId())->locations;
        }

        return $out;
    }

    public static function DivisionhasShifts()
    {
        if (static::getAdminTypeID() == Admin::SUPER_ADMIN || static::getAdminTypeID() == Admin::DEVELOPER) {
            return true;
        } else {
            return @Division::findOne(static::getAdminDivisionID())->has_shifts;
        }
    }

    // public static function getHiddenAttributes($pageId)
    // {
    //     $encodedValueObject = UserGrid::find()
    //         ->select(['value'])
    //         ->where(['page_id' => $pageId, 'user_id' => Yii::$app->user->id])
    //         ->one();
    //     $encodedValue = isset($encodedValueObject->value) ? $encodedValueObject->value : '';
    //     $valuesArray = explode(',', $encodedValue);
    //     $valuesArray = array_filter($valuesArray);
    //     sort($valuesArray);
    //     $encodedValue = implode(',', $valuesArray);
    //     return $encodedValue;
    // }
    public static function getHiddenAttributes($pageId, $modelClass)
    {

        if (class_exists($modelClass)) {
            $encodedValueObject = UserGrid::find()
                ->select(['value'])
                ->where(['page_id' => $pageId, 'user_id' => Yii::$app->user->id])
                ->one();
            $encodedValue = isset($encodedValueObject->value) ? $encodedValueObject->value : '';
            $hiddenAttributeNames = explode(',', $encodedValue);
            $hiddenAttributeNames = array_filter($hiddenAttributeNames);

            $model = new $modelClass;
            $attributeLabels = $model->attributeLabels();
            $hiddenAttributeLabels = [];

            foreach ($hiddenAttributeNames as $attribute) {
                if (isset($attributeLabels[$attribute])) {
                    $hiddenAttributeLabels[] = $attributeLabels[$attribute];
                }
            }
            array_multisort($hiddenAttributeNames);


            return [
                'attributeNames' => $hiddenAttributeNames,
                'attributeLabels' => $hiddenAttributeLabels,
            ];
        } else {
            return [
                'attributeNames' => [],
                'attributeLabels' => [],
            ];
        }
    }












    public static function canManageThisUser($user_account)
    {
        if ($user_account->type == Account::getAdminTypeID() && $user_account->id != Yii::$app->user->id) {
            return false;
        }

        return true;
    }

    public static function getAdminAccountTypeModel($user_id = null)
    {
        if (!empty($user_id)) {
            return @Admin::findOne($user_id)->account->type0;
        }
        return @Admin::findOne(Yii::$app->user->id)->account->type0;
    }

    public static function getAdminAccountTypeID($user_id = null)
    {
        if (!empty($user_id)) {
            return Admin::findOne($user_id)->account->type;
        }
        return Admin::findOne(Yii::$app->user->id)->account->type;
    }

    public static function getAdminAccountTypeLabel($user_id = null)
    {
        if (!empty($user_id)) {
            return Admin::findOne($user_id)->account->type0->name;
        }
        return Admin::findOne(Yii::$app->user->id)->account->type0->name;
    }

    public static function getTechnicianAccountTypeLabel($user_id = null)
    {
        if (!empty($user_id)) {
            return Technician::findOne($user_id)->account->type0->name;
        }
        return Technician::findOne(Yii::$app->user->id)->account->type0->name;
    }

    public static function getHierarchy($parentId, $include_parent = false, $show_technician_types = null)
    {
        $currentParent = AccountType::findOne($parentId);
        $result = [];

        $nodes = AccountType::find()
            ->where(['parent_id' => $parentId])
            ->andWhere(['for_backend' => true])
            ->andWhere(['status' => AccountType::STATUS_ENABLED])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        if ($include_parent && !empty($currentParent)) {
            $result[] = [
                'id' => $currentParent->id,
                'name' => $currentParent->name,
                'label' => $currentParent->label,
            ];
        }

        foreach ($nodes as $index => $node) {

            $children = self::getHierarchy($node->id, false);

            $result[] = [
                'id' => $node->id,
                'name' => $node->name,
                'label' => $node->label,
                'children' => $children,
            ];
        }

        return $result;
    }

    public static function getHierarchyValues($childs)
    {
        $childData = [];

        foreach ($childs as $child) {

            $childData[] = [
                'id' => $child['id'],
                'name' => $child['name'],
                'label' => $child['label'],
            ];

            if (!empty($child['children'])) {
                $childData = array_merge($childData, self::getHierarchyValues($child['children']));
            }
        }

        return $childData;
    }

    public static function getAdminHierarchy($include_parent, $show_technician_types = null)
    {
        return Account::getHierarchyValues(Account::getHierarchy(Account::getAdminAccountTypeID(), $include_parent, $show_technician_types));
    }

    public static function getAdminAccountTypeDivisionModel()
    {
        return @AccountType::findOne(Account::getAdminAccountTypeID())->division;
    }

    public static function getParentPermissions($role_name)
    {
        $parent = AccountType::find()->where(['parent_id' => AccountType::findOne($role_name)->parent_id]);

        return $parent;
    }
}
