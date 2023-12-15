<?php

namespace common\components\notification;

use common\models\AdminNotifications;
use common\models\CoordinatesIssue;
use common\models\RepairRequest;
use common\models\users\AbstractAccount;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Description of NotificationController
 *
 * @author Tarek K. Ajaj
 */
class NotificationController extends Controller
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
                        'actions' => ['index', 'index-admin', 'update-regid', 'mark-read', 'click', 'click-admin', 'read-all', 'read-all-admin', 'delete-all', 'delete-all-admin', 'delete-admin', 'click-coordinate'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public $viewPath = '@common/components/notification/views';

    public function actionIndex()
    {
        $this->setViewPath($this->viewPath);
        $userId = \Yii::$app->getUser()->getId();
        $nots = Notification::find()->where(['account_id' => $userId])->limit(300)->orderBy(['id' => SORT_DESC])->all();
        return $this->render('index', ['notifications' => $nots]);
    }

    public function actionIndexAdmin()
    {
        $this->setViewPath($this->viewPath);
        $nots = AdminNotifications::find()->limit(300)->orderBy(['id' => SORT_DESC])->all();
        return $this->render('index-admin', ['notifications' => $nots]);
    }

    public function actionMarkRead($id)
    {
        $userId = \Yii::$app->getUser()->getId();
        Notification::markRead($userId, $id);
        return true;
    }

    public function MarkReadAdmin($id)
    {

        $not = AdminNotifications::findOne([
            'id' => $id,
        ]);

        if (!empty($not)) {
            $not->seen = true;
            $not->save(false);
        }

        return true;
    }

    public function actionUpdateRegid()
    {
        /* @var $user AbstractAccount */
        $identityClass = Yii::$app->user->identityClass;
        $regid = Yii::$app->getRequest()->post("regid");
        $identityClass::updateAll([
            'web_registration_id' => null
        ], [
            'web_registration_id' => $regid
        ]);

        $user = Yii::$app->getUser()->getIdentity();
        Yii::error("SET {$regid}", "NOTIFICATION");
        $user->refresh();
        $user->web_registration_id = $regid;
        $user->save();
        Yii::error(Json::encode($user->errors), "NOTIFICATION");
        return true;
    }

    public function actionClick($id)
    {
        $userId = \Yii::$app->getUser()->getId();
        $not = Notification::findOne([
            'account_id' => $userId,
            'id' => $id,
        ]);
        if (!empty($not)) {
            $not->seen = true;
            $not->save(false);
            $url = !empty($not->url) ? Json::decode($not->url) : ['/site/index'];
            return $this->redirect($url);
        } else {
            throw new NotFoundHttpException("Requested page does not exists");
        }
    }

    public function actionClickAdmin($id)
    {
        $not = AdminNotifications::findOne([
            'id' => $id,
        ]);

        if (!empty($not)) {
            $not->seen = true;
            $not->save(false);
            $url = ['/repair-request/view', 'id' => $not->request_id];
            return $this->redirect($url);
        } else {
            throw new NotFoundHttpException("Requested page does not exists");
        }
    }

    public function actionReadAll()
    {
        $userId = \Yii::$app->getUser()->getId();
        Notification::updateAll(['seen' => true], ['account_id' => $userId]);
        return $this->redirect(['index']);
    }
    public function actionReadAllAdmin()
    {
        AdminNotifications::updateAll(['seen' => true]);

        Yii::$app->session->setFlash('success', "Success");

        return $this->redirect(['index-admin']);
    }

    public function actionDeleteAll()
    {
        $userId = \Yii::$app->getUser()->getId();
        Notification::deleteAll(['account_id' => $userId]);
        return $this->redirect(['index']);
    }

    public function actionDeleteAllAdmin()
    {
        AdminNotifications::deleteAll();

        Yii::$app->session->setFlash('danger', "Success");

        return $this->redirect(['index-admin']);
    }

    public function actionDeleteAdmin($id)
    {
        $model = AdminNotifications::findOne($id);
        $model->delete();

        Yii::$app->session->setFlash('error', "Success");

        return $this->redirect(['index-admin']);

    }

    public function actionClickCoordinate($id)
    {
        $notification = AdminNotifications::findOne($id);
        $model = RepairRequest::findOne($notification->request_id);

        $this->MarkReadAdmin($id);

        if (!empty($model->location_id)) {
            $coordinate = CoordinatesIssue::find()->where(['location_id' => $model->location_id, 'status' => CoordinatesIssue::STATUS_PENDING])->orderBy(['created_at' => SORT_DESC])->one();

            if (!empty($coordinate)) {
                return $this->redirect(['coordinates-issue/view', 'id' => $coordinate->id]);
            }
        }

        Yii::$app->session->setFlash('error', "No Coordinates Issue Found!");
        return $this->redirect(['index-admin']);

    }
}
