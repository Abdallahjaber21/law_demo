<?php

namespace admin\controllers;

use common\models\WorkerSector;
use Yii;
use common\models\Worker;
use common\models\search\WorkerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;;

use yii\filters\AccessControl;


/**
 * WorkerController implements the CRUD actions for Worker model.
 */
class WorkerController extends Controller
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
                        'allow' => Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin"),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin"),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin"),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin"),
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
     * Lists all Worker models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WorkerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Worker model.
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
     * Creates a new Worker model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Worker();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $user = Yii::$app->request->post("Worker");
            WorkerSector::deleteAll(['worker_id' => $model->id]);
            if (!empty($user['workerSectors'])) {
                $workerSectors = $user['workerSectors'];
                if (!empty($workerSectors)) {
                    foreach ($workerSectors as $index => $workerSector) {
                        (new WorkerSector([
                            'sector_id' => $workerSector,
                            'worker_id'     => $model->id,
                        ]))->save();
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Worker model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $user = Yii::$app->request->post("Worker");
            WorkerSector::deleteAll(['worker_id' => $model->id]);
            if (!empty($user['workerSectors'])) {
                $workerSectors = $user['workerSectors'];
                if (!empty($workerSectors)) {
                    foreach ($workerSectors as $index => $workerSector) {
                        (new WorkerSector([
                            'sector_id' => $workerSector,
                            'worker_id'     => $model->id,
                        ]))->save();
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Worker model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Worker model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Worker the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Worker::findOne($id)) !== null) {
            //        if (($model = Worker::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
