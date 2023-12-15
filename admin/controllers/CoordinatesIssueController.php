<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use common\models\CoordinatesIssue;
use common\models\search\CoordinatesIssueSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;;

use yii\filters\AccessControl;


/**
 * CoordinatesIssueController implements the CRUD actions for CoordinatesIssue model.
 */
class CoordinatesIssueController extends Controller
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
                        'allow' => P::c(P::MANAGEMENT_COORDINATES_ISSUES_PAGE_VIEW),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_COORDINATES_ISSUES_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_COORDINATES_ISSUES_PAGE_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_COORDINATES_ISSUES_PAGE_NEW),
                        'actions' => ['create', 'accept', 'reject'],
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
     * Lists all CoordinatesIssue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CoordinatesIssueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CoordinatesIssue model.
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
     * Creates a new CoordinatesIssue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CoordinatesIssue();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CoordinatesIssue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $model->status = CoordinatesIssue::STATUS_REJECTED;
        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionAccept($id)
    {
        $model = $this->findModel($id);
        $location = $model->location;
        $location->latitude = $model->new_latitude;
        $location->longitude = $model->new_longitude;
        $location->save();
        $model->status = CoordinatesIssue::STATUS_APPROVED;
        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing CoordinatesIssue model.
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
     * Finds the CoordinatesIssue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CoordinatesIssue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CoordinatesIssue::findOne($id)) !== null) {
            //        if (($model = CoordinatesIssue::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
