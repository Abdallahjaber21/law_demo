<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\Division;
use Yii;
use common\models\PlantPpmTasks;
use common\models\RepairRequest;
use common\models\search\PlantPpmTasksSearch;
use common\models\search\RepairRequestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

;

use yii\filters\AccessControl;


/**
 * PlantPpmTasksController implements the CRUD actions for PlantPpmTasks model.
 */
class PlantPpmTasksController extends Controller
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
                        'allow' => P::c(P::PPM_PLANT_PPM_TASKS_VIEW),
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_PLANT_PPM_TASKS_VIEW),
                        'actions' => ['view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_PLANT_PPM_SERVICES_VIEW),
                        'actions' => ['services'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_PLANT_PPM_TASKS_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_PLANT_PPM_TASKS_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::PPM_PLANT_PPM_TASKS_NEW),
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
     * Lists all PlantPpmTasks models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlantPpmTasksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionServices()
    {

        $searchModel = new RepairRequestSearch();
        $searchModel->division_id = Division::DIVISION_PLANT;
        $searchModel->service_type = RepairRequest::TYPE_PPM;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('services', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single PlantPpmTasks model.
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
     * Creates a new PlantPpmTasks model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PlantPpmTasks();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->addFlash("success", "Plant ppm task: " . $model->name . " has been created successfully");
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PlantPpmTasks model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->addFlash("warning", "Plant ppm task: " . $model->name . " has been created successfully");
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PlantPpmTasks model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->addFlash("danger", "Plant ppm task is deleted successfully");

        return $this->redirect(['index']);
    }

    /**
     * Finds the PlantPpmTasks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PlantPpmTasks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlantPpmTasks::findOne($id)) !== null) {
            //        if (($model = PlantPpmTasks::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
