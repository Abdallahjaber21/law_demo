<?php

namespace console\controllers;

use common\config\includes\P;
use common\models\users\Admin;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;

class AccessController extends Controller
{

    //     public function actionImport()
    //     {
    //         $spreadsheet = IOFactory::load(Yii::getAlias("@console/models/advanced-admin-permissions.xlsx"));
    //         $worksheet = $spreadsheet->getActiveSheet();
    //         $iterator = $worksheet->getRowIterator();
    //         //        $header = $iterator->current();
    //         //        $cellIterator = $header->getCellIterator();
    //         //        $cellIterator->setIterateOnlyExistingCells(FALSE);

    //         $currentCategory = null;
    //         $currentGroup = null;
    //         $permissionsVariables = [];
    //         $permissionsKeyValues = [];

    //         $resultingArray = [];
    //         for ($iterator->current(); $iterator->valid(); $iterator->next()) {
    //             try {
    //                 $row = $iterator->current();
    //                 $cellIterator = $row->getCellIterator();
    //                 $cellIterator->setIterateOnlyExistingCells(FALSE);
    //                 foreach ($cellIterator as $cell) {
    //                     if ($cell->getColumn() === "A") {
    //                         $currentCategory = !empty($cell->getValue()) ? Inflector::slug($cell->getValue()) : $currentCategory;
    //                         if (!empty($cell->getValue())) {
    //                             $resultingArray[$currentCategory] = [
    //                                 'key'    => $currentCategory,
    //                                 'label'  => $cell->getValue(),
    //                                 'groups' => []
    //                             ];
    //                         }
    //                     }
    //                     if ($cell->getColumn() === "B") {
    //                         $currentGroup = !empty($cell->getValue()) ? Inflector::slug($cell->getValue()) : $currentGroup;
    //                         if (!empty($cell->getValue())) {
    //                             $resultingArray[$currentCategory]['groups'][$currentGroup] = [
    //                                 'key'         => $currentGroup,
    //                                 'label'       => $cell->getValue(),
    //                                 'permissions' => []
    //                             ];
    //                         }
    //                     }
    //                     if ($cell->getColumn() === "C") {
    //                         $currentPermission = $cell->getValue();
    //                         if (!empty($currentPermission)) {
    //                             if (!empty($resultingArray[$currentCategory])) {
    //                                 if (!empty($resultingArray[$currentCategory]['groups'][$currentGroup])) {
    //                                     $slug = Inflector::slug($currentPermission);
    //                                     $key = "{$currentCategory}-{$currentGroup}-{$slug}";
    //                                     $resultingArray[$currentCategory]['groups'][$currentGroup]['permissions'][$key] = $currentPermission;
    //                                     $permissionsVariables[strtoupper(Inflector::underscore(Inflector::id2camel($key)))] = $key;
    //                                     $permissionsKeyValues[$key] = $currentPermission;
    //                                 }
    //                             }
    //                             //insert the permission to rbac system
    //                         }
    //                     }
    //                 }
    //             } catch (Exception $exception) {
    //                 continue;
    //             }
    //         }
    //         file_put_contents(Yii::getAlias("@common/config/includes/_advanced-admin-permissions.json"), Json::encode($resultingArray));
    //         print_r($permissionsVariables);
    //         $classVariables = "";
    //         foreach ($permissionsVariables as $variableName => $variableValue) {
    //             $classVariables .= "CONST {$variableName} = '{$variableValue}';" . PHP_EOL;
    //         }
    //         $classFile = "<?php
    // namespace common\config\includes;

    // class P
    // {
    // CONST DEVELOPER = 'developer';
    // {$classVariables} 
    // public static function c(\$p){return \Yii::\$app->user->can(\$p);}
    // }";
    //         file_put_contents(Yii::getAlias("@common/config/includes/P.php"), $classFile);
    //         //        print_r($resultingArray);
    //         $this->updateRbacPermissions($permissionsKeyValues);
    //     }

    public function actionImportNew()
    {

        // $decision = $this->prompt('y (uncomment the line) , f (comment it)', ['required' => true]);

        $spreadsheet = IOFactory::load(Yii::getAlias("@console/models/advanced-admin-permissions-new.xlsx"));
        $worksheet = $spreadsheet->getActiveSheet();
        $iterator = $worksheet->getRowIterator();
        //        $header = $iterator->current();
        //        $cellIterator = $header->getCellIterator();
        //        $cellIterator->setIterateOnlyExistingCells(FALSE);

        $currentCategory = null;
        $currentGroup = null;
        $slug = null;
        $enabled_key = [];
        $permissionsVariables = [];
        $permissionsKeyValues = [];

        $resultingArray = [];
        for ($iterator->current(); $iterator->valid(); $iterator->next()) {
            try {
                $row = $iterator->current();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                foreach ($cellIterator as $cell) {
                    if ($cell->getColumn() === "A") {
                        $currentCategory = !empty($cell->getValue()) ? Inflector::slug($cell->getValue()) : $currentCategory;
                        if (!empty($cell->getValue())) {
                            $resultingArray[$currentCategory] = [
                                'key'    => $currentCategory,
                                'label'  => $cell->getValue(),
                                'groups' => []
                            ];

                            $enabled_key[$currentCategory] = [
                                'key'    => $currentCategory,
                                'label'  => $cell->getValue(),
                                'groups' => []
                            ];
                        }
                    }
                    if ($cell->getColumn() === "B") {
                        $currentGroup = !empty($cell->getValue()) ? Inflector::slug($cell->getValue()) : $currentGroup;
                        if (!empty($cell->getValue())) {
                            $resultingArray[$currentCategory]['groups'][$currentGroup] = [
                                'key'         => $currentGroup,
                                'label'       => $cell->getValue(),
                                'permissions' => []
                            ];

                            if ($cell->getValue() == "Enabled") {
                                $enabled_key[$currentCategory]['groups'] = [
                                    'key' => $currentCategory,
                                    'label' => $cell->getValue(),
                                ];
                            }
                        }
                    }
                    if ($cell->getColumn() === "C") {
                        $currentPage = $cell->getValue();
                        if (!empty($currentPage)) {
                            if (!empty($resultingArray[$currentCategory])) {
                                if (!empty($resultingArray[$currentCategory]['groups'][$currentGroup])) {
                                    $slug = Inflector::slug($currentPage);
                                    $key = "{$currentCategory}_{$currentGroup}_{$slug}"; // Modify this line
                                    $resultingArray[$currentCategory]['groups'][$currentGroup]['page'][$key] = $currentPage;
                                    $permissionsVariables[strtoupper(Inflector::underscore(Inflector::id2camel($key)))] = $key;
                                    $permissionsKeyValues[$key] = $currentPage;
                                }
                            }
                            // insert the permission to rbac system
                        }
                    }
                    if ($cell->getColumn() === "D") {
                        $currentPermission = $cell->getValue();
                        if (!empty($currentPermission)) {
                            if (!empty($resultingArray[$currentCategory])) {
                                if (!empty($resultingArray[$currentCategory]['groups'][$currentGroup])) {
                                    $permission_slug = Inflector::slug($currentPermission);
                                    $key = "{$currentCategory}_{$currentGroup}_{$slug}_{$permission_slug}"; // Modify this line
                                    $resultingArray[$currentCategory]['groups'][$currentGroup]['page'][$key]['permissions'][$key] = $currentPermission;
                                    $permissionsVariables[strtoupper(Inflector::underscore(Inflector::id2camel($key)))] = $key;
                                    $permissionsKeyValues[$key] = $currentPermission;
                                }
                            }
                            // insert the permission to rbac system
                        }
                    }
                }
            } catch (Exception $exception) {
                continue;
            }
        }
        file_put_contents(Yii::getAlias("@common/config/includes/_advanced-admin-permissions-new.json"), Json::encode($resultingArray));
        print_r($permissionsVariables);
        $classVariables = "";
        foreach ($permissionsVariables as $variableName => $variableValue) {
            $classVariables .= "CONST {$variableName} = '{$variableValue}';" . PHP_EOL;
        }
        $classFile = "<?php
namespace common\config\includes;

class P
{
CONST DEVELOPER = 'developer';
{$classVariables} 
public static function c(\$p){return \Yii::\$app->user->can(\$p);}
}";
        file_put_contents(Yii::getAlias("@common/config/includes/P.php"), $classFile);

        $this->updateRbacPermissionsNew($permissionsKeyValues, false, false);
        $this->updateRbacPermissionsNew($permissionsKeyValues, true, false);
    }

    // private function updateRbacPermissions($permissions)
    // {
    //     $this->createDefaultRoles();

    //     $auth = Yii::$app->authManager;
    //     $developer = $auth->getRole("developer");

    //     $superAdmin = $auth->getRole("super-admin");
    //     $admin = $auth->getRole("admin");

    //     $previousPermissions = ArrayHelper::getColumn($auth->getPermissions(), "name", false);
    //     $permissionsToRemove = array_diff($previousPermissions, array_keys($permissions));

    //     // $auth->removeAllPermissions();
    //     foreach ($permissions as $name => $desc) {

    //         $rbacViewPermissions = $auth->createPermission($name);
    //         $rbacViewPermissions->description = $desc;
    //         try {
    //             // $auth->add($rbacViewPermissions); // comment only on inserting permission to a new role for the first time
    //             if (!in_array($name, [
    //                 P::SUPER_ADMIN
    //             ])) {
    //                 $auth->addChild($developer, $rbacViewPermissions);
    //             }
    //             $auth->addChild($superAdmin, $rbacViewPermissions);
    //             echo "VVV - CREATED {$name}\n";
    //         } catch (Exception $exc) {
    //             echo "--- - Skipping {$name}\n";
    //         }
    //         try {
    //             if ($rbacViewPermissions) {
    //                 if (!in_array($name, [
    //                     P::MISC_MANAGE_ADMINS,
    //                     P::ADMINS_ROLE_PAGE_VIEW,
    //                     P::MISC_MANAGE_SETTINGS,
    //                     P::MISC_MANAGE_CACHE,
    //                     P::MISC_MANAGE_GII,
    //                     P::MISC_MANAGE_DEBUG,
    //                     P::SUPER_ADMIN
    //                 ])) {
    //                     $auth->addChild($admin, $rbacViewPermissions);
    //                 }
    //             }
    //         } catch (Exception $exc) {
    //         }
    //     }
    //     //print_r($permissionsToRemove);
    //     foreach ($permissionsToRemove as $permissionToRemove) {
    //         $perm = $auth->getPermission($permissionToRemove);
    //         if (!empty($perm)) {
    //             $auth->remove($perm);
    //             echo "XXX - REMOVING {$permissionToRemove}\n";
    //         }
    //     }
    // }

    private function updateRbacPermissionsNew($permissions, $uncomment_line = false, $create_default_roles = false)
    {
        if ($create_default_roles)
            $this->createDefaultRolesNew();

        $auth = Yii::$app->authManager;

        $developer = $auth->getRole("developer");

        $previousPermissions = ArrayHelper::getColumn($auth->getPermissions(), "name", false);
        $permissionsToRemove = array_diff($previousPermissions, array_keys($permissions));

        // $auth->removeAllPermissions();
        foreach ($permissions as $name => $desc) {

            $rbacViewPermissions = $auth->createPermission($name);
            $rbacViewPermissions->description = $desc;
            try {
                if ($uncomment_line) {
                    $auth->add($rbacViewPermissions);
                    $auth->addChild($developer, $rbacViewPermissions);
                } else {
                    $auth->addChild($developer, $rbacViewPermissions);
                }
                // $auth->add($rbacViewPermissions); // comment only on inserting permission to a new role for the first time
                echo "VVV - CREATED {$name}\n";
            } catch (Exception $exc) {
                echo "--- - Skipping {$name}\n";
            }
        }
        foreach ($permissionsToRemove as $permissionToRemove) {
            $perm = $auth->getPermission($permissionToRemove);
            if (!empty($perm)) {
                $auth->remove($perm);
                echo "XXX - REMOVING {$permissionToRemove}\n";
            }
        }
    }

    // private function createDefaultRoles()
    // {
    //     $auth = Yii::$app->authManager;

    //     $roles = [
    //         'developer'      => 'developer',
    //         'super-admin'          => 'Super Admin',
    //         'admin'          => 'Admin',
    //         'fleet-manager' => 'Fleet Manager',
    //         'store-keeper' => 'Store Keeper',
    //         'plant-manager' => 'Plant Manager',
    //     ];

    //     foreach ($roles as $key => $roleDesc) {
    //         $roleObj = $auth->createRole($key);
    //         $roleObj->description = $roleDesc;
    //         try {
    //             $auth->add($roleObj);
    //             if ($key === 'admin') {
    //                 $adminRole = $auth->getRole("admin");
    //                 $admins = Admin::find()->all();
    //                 foreach ($admins as $index => $admin) {
    //                     try {
    //                         $auth->assign($adminRole, $admin->id);
    //                     } catch (Exception $exc) {
    //                     }
    //                 }
    //             }
    //             echo "VVV - ROLE CREATED {$key}\n";
    //         } catch (Exception $exc) {
    //             echo "--- - ROLE Skipping {$key}\n";
    //         }
    //     }



    //     $allRoles = $auth->getRoles();
    //     foreach ($allRoles as $index => $role) {
    //         if (!array_key_exists($role->name, $roles)) {
    //             $auth->remove($role);
    //             echo "XXX - ROLE REMOVING ROLE {$role->name}\n";
    //         }
    //     }
    // }

    private function createDefaultRolesNew()
    {
        $auth = Yii::$app->authManager;

        $roles = [
            'developer'      => 'developer',
            // 'super-admin'          => 'Super Admin',
            // 'admin'          => 'Admin',
            // 'fleet-manager' => 'Fleet Manager',
            // 'store-keeper' => 'Store Keeper',
            // 'plant-manager' => 'Plant Manager',
        ];

        foreach ($roles as $key => $roleDesc) {
            $roleObj = $auth->createRole($key);
            $roleObj->description = $roleDesc;
            try {
                $auth->add($roleObj);
                // if ($key === 'admin') {
                //     $adminRole = $auth->getRole("admin");
                //     $admins = Admin::find()->all();
                //     foreach ($admins as $index => $admin) {
                //         try {
                //             $auth->assign($adminRole, $admin->id);
                //         } catch (Exception $exc) {
                //         }
                //     }
                // }
                echo "VVV - ROLE CREATED {$key}\n";
            } catch (Exception $exc) {
                echo "--- - ROLE Skipping {$key}\n";
            }
        }

        $allRoles = $auth->getRoles();

        print_r($allRoles);

        foreach ($allRoles as $index => $role) {
            if (!array_key_exists($role->name, $roles)) {
                $auth->remove($role);
                echo "XXX - ROLE REMOVING ROLE {$role->name}\n";
            }
        }
    }
}
