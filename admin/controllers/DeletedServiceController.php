<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\DeletedService;
use common\models\RepairRequest;
use common\models\search\DeletedServiceSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


/**
 * DeletedServiceController implements the CRUD actions for DeletedService model.
 */
class DeletedServiceController extends Controller
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
                        'allow'   => P::c(P::REPAIR_COMPLETED_REPAIRS_PAGE_VIEW),
                        'actions' => ['index', 'view'],
                        'roles'   => ['@'],
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
     * Lists all DeletedService models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DeletedServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DeletedService model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $deletedService = $this->findModel($id);
        $model = Json::decode($deletedService->model);
        $serviceLogs = Json::decode($deletedService->logs);

        $m = new RepairRequest();
        $m->load($model, '');
        $m->id = $model['id'];

        return $this->render('view', [
            'model'       => $m,
            'serviceLogs' => $serviceLogs
        ]);
    }

    /**
     * Finds the DeletedService model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DeletedService the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DeletedService::findOne(['service_id' => $id])) !== null) {
            //        if (($model = DeletedService::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
