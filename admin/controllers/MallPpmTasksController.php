<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\Division;
use Yii;
use common\models\MallPpmTasks;
use common\models\RepairRequest;
use common\models\search\MallPpmTasksSearch as MallPpmTasksSearch;
use common\models\search\RepairRequestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;;

use yii\filters\AccessControl;


/**
 * MallPpmTasksController implements the CRUD actions for MallPpmTasks model.
 */
class MallPpmTasksController extends Controller
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
                        'allow' => P::c(P::PPM_MALL_PPM_TASKS_VIEW),
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_MALL_PPM_TASKS_VIEW),
                        'actions' => ['view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_MALL_PPM_SERVICES_VIEW),
                        'actions' => ['services'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_MALL_PPM_TASKS_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_MALL_PPM_TASKS_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_MALL_PPM_TASKS_NEW),
                        'actions' => ['create'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all MallPpmTasks models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MallPpmTasksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionServices()
    {
        $searchModel = new RepairRequestSearch();
        $searchModel->division_id = Division::DIVISION_MALL;
        $searchModel->service_type = RepairRequest::TYPE_PPM;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('services', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MallPpmTasks model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MallPpmTasks model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MallPpmTasks();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->addFlash("success", "Mall ppm task: " . $model->name . " has been created successfully");
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MallPpmTasks model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->addFlash("warning", "Mall ppm task: " . $model->name . " has been updated successfully");
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MallPpmTasks model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->addFlash("danger", "Mall ppm task is deleted");

        return $this->redirect(['index']);
    }

    /**
     * Finds the MallPpmTasks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MallPpmTasks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MallPpmTasks::findOne($id)) !== null) {
            //        if (($model = MallPpmTasks::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
