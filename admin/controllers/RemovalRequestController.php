<?php

namespace admin\controllers;

use common\components\notification\Notification;
use common\config\includes\P;
use common\models\RemovalRequest;
use common\models\search\RemovalRequestSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;;


/**
 * RemovalRequestController implements the CRUD actions for RemovalRequest model.
 */
class RemovalRequestController extends Controller
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
                        'allow' => P::c(P::MISC_MANAGE_REMOVAL_REQUESTS),
                        //'actions' => ['index', 'approve', 'reject'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all RemovalRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RemovalRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        if (!empty($model)) {

            Notification::notifyTechnician(
                $model->userLocation->user_id,
                "You were removed from the location '{$model->userLocation->location->name}' by '{$model->requester->name}'.<br/>Reason: {$model->reason}.",
                "You were removed from a location",
                [],
                ['/site/index'],
                ['action' => 'open-popup'],
                null,
                Notification::TYPE_NOTIFICATION,
                null
            );

            $model->userLocation->delete();
        }
        return $this->redirect(['index']);
    }

    public function actionReject($id)
    {
        $model = $this->findModel($id);
        if (!empty($model)) {
            Notification::notifyTechnician(
                $model->requester_id,
                "Your request to remove '{$model->userLocation->user->name}' from '{$model->userLocation->location->name}' was rejected",
                "User removal request rejected",
                [],
                ['/site/index'],
                ['action' => 'open'],
                null,
                Notification::TYPE_NOTIFICATION,
                null
            );

            $model->delete();
        }
        return $this->redirect(['index']);
    }

    public function actionRejectForm($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isPost) {
            $reason = Yii::$app->request->post("reject-reason");
            if (!empty($reason)) {
                Notification::notifyTechnician(
                    $model->requester_id,
                    "Your request to remove '{$model->userLocation->user->name}' from '{$model->userLocation->location->name}' was rejected.<br/>Reason: {$reason}.",
                    "User removal request rejected",
                    [],
                    ['/site/index'],
                    ['action' => 'open-popup'],
                    null,
                    Notification::TYPE_NOTIFICATION,
                    null
                );

                $model->delete();
            } else {
                Yii::$app->session->addFlash("danger", "Please provide a reason");
            }
            return $this->redirect(['removal-request/index']);
        }
        return $this->renderPartial('reject-form', [
            'model' => $model
        ]);
    }


    /**
     * Finds the RemovalRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RemovalRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RemovalRequest::findOne($id)) !== null) {
            //        if (($model = RemovalRequest::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
