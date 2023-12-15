<?php


namespace admin\controllers;


use common\components\notification\Notification;
use common\config\includes\P;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class FakeController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => P::c(P::DEVELOPER),
                        //'actions' => '*',
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionNotification()
    {
        return $this->render("notification");
    }

    public function actionServiceNotification()
    {
        Notification::notifyCustomerUsers(
            1,
            "Hello World!",
            "Service Report",
            [],
            ['/site/index'],
            ['action' => 'view-service', 'id' => 1]
        );
        return $this->redirect(["notification"]);
    }

    // public function actionTechNotification()
    // {
    //     Notification::notifyTechnician(
    //         3,
    //         "Hello World!",
    //         "Service Report",
    //         [],
    //         ['/site/index'],
    //         ['action' => 'view-service', 'id' => 1]
    //     );
    //     return $this->redirect(["notification"]);
    // }


    public function actionContractNotification()
    {
        Notification::notifyCustomerUsers(
            1,
            "Contract Reminder",
            "Contract will expire soon",
            [],
            ['/site/index'],
            ['action' => 'view-contract', 'id' => 1]
        );
        return $this->redirect(["notification"]);
    }

    public function actionNewsNotification()
    {
        Notification::notifyCustomerUsers(
            1,
            "Designer cabins",
            "Read now",
            [],
            ['/site/index'],
            ['action' => 'view-article', 'id' => 5]
        );
        return $this->redirect(["notification"]);
    }
}
