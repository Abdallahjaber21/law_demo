<?php

namespace console\controllers;

use common\models\Account;
use common\models\AccountType;
use common\models\Division;
use common\models\Admin;
use Yii;
use yii\base\Exception;
use yii\console\Controller;

/**
 * Description of SetupController
 *
 * @author Tarek K. Ajaj
 */
class SetupController extends Controller
{

    public function actionAdmin()
    {
        $fullname = $this->prompt('Choose a name:', ['required' => true]);
        $email = $this->prompt('Choose an email:', ['required' => true]);
        $number = $this->prompt('Choose a phone number:', ['required' => true]);
        $password = $this->prompt('Choose a password:', ['required' => true]);
        $division = $this->prompt('Choose a Division: (M,P,V)[Mall,Plant,Villa]', ['required' => true]);
        $admin = new Admin();
        $admin->name = $fullname;
        $admin->email = $email;
        $admin->password_input = $password;
        $admin->account_type = Account::ADMIN;
        $admin->phone_number = $number;

        if (strtolower(trim($division)) == 'm') {
            $admin->division_id = Division::DIVISION_MALL;
        } else if (strtolower(trim($division)) == 'v') {
            $admin->division_id = Division::DIVISION_VILLA;
        } else if (strtolower(trim($division)) == 'p') {
            $admin->division_id = Division::DIVISION_PLANT;
        } else {
            $admin->division_id = Division::DIVISION_MALL;
        }
        $admin->status = Admin::STATUS_ENABLED;
        if ($admin->save()) {

            $auth = Yii::$app->authManager;
            try {
                $developer = $auth->createRole('developer');
                $auth->add($developer);
            } catch (Exception $exc) {
                echo "Developer role exists\n";
                //echo $exc->getTraceAsString();
            }
            $developer = $auth->getRole("developer");
            $auth->assign($developer, $admin->id);
            echo "Admin account created successfully and assigned `developer` role \n";
        } else {
            echo "Account creation failed ";
            $errors = $admin->getErrors();
            foreach ($errors as $key => $error) {
                echo implode("\n ", $error) . "\n";
            }
        }
    }

    public function actionSuperAdmin()
    {
        $fullname = $this->prompt('Choose a name:', ['required' => true]);
        $email = $this->prompt('Choose an email:', ['required' => true]);
        // $number = $this->prompt('Choose a phone number:', ['required' => true]);
        $password = $this->prompt('Choose a password:', ['required' => true]);
        // $division = $this->prompt('Choose a Division: (M,P,V)[Mall,Plant,Villa]', ['required' => true]);
        $admin = new Admin();
        $admin->name = $fullname;
        $admin->email = $email;
        $admin->password_input = $password;
        $admin->account_type = Account::SUPER_ADMIN;
        $admin->phone_number = null;
        $admin->division_id = null;
        $admin->main_sector_id = null;

        // if (strtolower(trim($division)) == 'm') {
        //     $admin->division_id = Division::DIVISION_MALL;
        // } else if (strtolower(trim($division)) == 'v') {
        //     $admin->division_id = Division::DIVISION_VILLA;
        // } else if (strtolower(trim($division)) == 'p') {
        //     $admin->division_id = Division::DIVISION_PLANT;
        // } else {
        //     $admin->division_id = Division::DIVISION_MALL;
        // }
        $admin->status = Admin::STATUS_ENABLED;
        if ($admin->save()) {

            $auth = Yii::$app->authManager;
            try {
                $developer = $auth->createRole('super-admin');
                $auth->add($developer);
            } catch (Exception $exc) {
                echo "Developer role exists\n";
                //echo $exc->getTraceAsString();
            }
            $developer = $auth->getRole("super-admin");
            $auth->assign($developer, $admin->id);
            echo "Admin account created successfully and assigned `Super Admin` role \n";
        } else {
            echo "Account creation failed ";
            $errors = $admin->getErrors();
            foreach ($errors as $key => $error) {
                echo implode("\n ", $error) . "\n";
            }
        }
    }

    public function actionDeveloper()
    {
        $fullname = $this->prompt('Choose a name:', ['required' => true]);
        $email = $this->prompt('Choose an email:', ['required' => true]);
        // $number = $this->prompt('Choose a phone number:', ['required' => true]);
        $password = $this->prompt('Choose a password:', ['required' => true]);
        // $division = $this->prompt('Choose a Division: (M,P,V)[Mall,Plant,Villa]', ['required' => true]);
        $admin = new Admin();


        $admin->name = $fullname;
        $admin->email = $email;
        $admin->password_input = $password;
        $admin->account_type = AccountType::find()->where(['name' => 'developer'])->one()->id;
        $admin->phone_number = '123';
        $admin->badge_number = '123';
        $admin->division_id = null;
        $admin->main_sector_id = null;

        // if (strtolower(trim($division)) == 'm') {
        //     $admin->division_id = Division::DIVISION_MALL;
        // } else if (strtolower(trim($division)) == 'v') {
        //     $admin->division_id = Division::DIVISION_VILLA;
        // } else if (strtolower(trim($division)) == 'p') {
        //     $admin->division_id = Division::DIVISION_PLANT;
        // }

        $admin->status = Admin::STATUS_ENABLED;
        if ($admin->save()) {

            $auth = Yii::$app->authManager;
            try {
                $developer = $auth->createRole('developer');
                $auth->add($developer);
            } catch (Exception $exc) {
                echo "Developer role exists\n";
                //echo $exc->getTraceAsString();
            }
            $developer = $auth->getRole("developer");
            $auth->assign($developer, $admin->id);
            echo "Admin account created successfully and assigned `Developer` role \n";
        } else {
            echo "Account creation failed ";
            $errors = $admin->getErrors();
            foreach ($errors as $key => $error) {
                echo implode("\n ", $error) . "\n";
            }
        }
    }

    public function actionDivisions()
    {
        // Delete All Models 
        Division::deleteAll(true);
        print_r("Deleted ALL Models \n\n");

        $list = (new Division())->name_list;
        foreach ($list as $index => $division) {

            $model = new Division();
            $model->id = $index;
            $model->name = $division;
            $model->description = $division . "'s Division";
            $model->status = Division::STATUS_ENABLED;
            $model->has_shifts = ($index == Division::DIVISION_MALL) ? true : false;

            if ($model->save()) {
                print_r($division . " Added \n");
            } else {
                print_r($model->errors);
                exit;
            }
        }
    }

    public function actionRbac()
    {
        $auth = Yii::$app->authManager;
        try {
            $developer = $auth->createRole('developer');
            $auth->add($developer);
            echo "Developer role Created\n";
        } catch (Exception $exc) {
            echo "Developer role exists\n";
            //echo $exc->getTraceAsString();
        }
        $developer = $auth->getRole("developer");
        $permissions = require(\Yii::getAlias("@common/config/includes/_admin-permissions.php"));
        foreach ($permissions as $name => $desc) {
            $this->addPermission($developer, $name, $desc);
        }
    }

    public function addPermission($role, $name, $desc)
    {
        $auth = Yii::$app->authManager;

        $rbacViewPermissions = $auth->createPermission($name);
        $rbacViewPermissions->description = $desc;
        try {
            // $auth->add($rbacViewPermissions);
            $auth->addChild($role, $rbacViewPermissions);
            echo "$name added to {$role->name}\n";
        } catch (Exception $exc) {
            echo "Skipping $name\n";
            //echo $exc->getTraceAsString();
        }
    }


    public function actionCreateAccountType()
    {
        $auth = Yii::$app->authManager;

        $developer = $auth->getRole("developer");

        $model = new AccountType();

        $model->name = $developer->name;
        $model->label = ucfirst($developer->name);
        $model->role_id = $developer->name;
        $model->for_backend = 1;
        $model->status = AccountType::STATUS_ENABLED;

        if ($model->save()) {
            print_r("Created Account Type Developer!! \n");

            $this->actionDeveloper();
        } else {
            print_r($model->errors);
        }
    }

    public function actionTest()
    {
        $auth = Yii::$app->authManager;

        $super_admin = $auth->getRole("super-admin");

        if (empty($super_admin)) {
            // do smthng
            $super_admin = $auth->createRole("super-admin");
            print_r('Created a new role!!!!' . "\n\n\n\n");
            $auth->add($super_admin);

            print_r($auth);
        } else {
            print_r('It already exist!!!!' . "\n\n\n\n");

            print_r($super_admin);
        }
    }
}
