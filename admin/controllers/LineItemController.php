<?php

namespace admin\controllers;

use common\config\includes\P;
use common\models\LineItem;
use common\models\search\LineItemSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


/**
 * LineItemController implements the CRUD actions for LineItem model.
 */
class LineItemController extends Controller
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
                        'allow'   => P::c(P::MISC_MANAGE_LINE_ITEMS),
                        'actions' => ['index', 'view'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => P::c(P::WORKS_DASHBOARD_DEPARTED_SERVICES_EDIT_LINE_ITEM) || P::c(P::REPAIR_DASHBOARD_DEPARTED_SERVICES_EDIT_LINE_ITEM),
                        'actions' => ['update'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => P::c(P::MISC_MANAGE_LINE_ITEMS),
                        'actions' => ['delete'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => P::c(P::MISC_MANAGE_LINE_ITEMS),
                        'actions' => ['create'],
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    //                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all LineItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LineItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LineItem model.
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
     * Creates a new LineItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LineItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['repair-request/view', 'id' => $model->repair_request_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing LineItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            if ($model->type == LineItem::TYPE_TECHNICIAN) {
                LineItem::deleteAll([
                    'AND',
                    ['repair_request_id' => $model->repair_request_id],
                    ['type' => LineItem::TYPE_ATL]
                ]);
                $repair_request_id =  $model->repair_request_id;
                $model = new LineItem();
                $model->repair_request_id = $repair_request_id;
                $model->type = LineItem::TYPE_ATL;
            }
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $model->repairRequest->log("Updated Line item");
                return $this->redirect(['repair-request/view', 'id' => $model->repair_request_id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LineItem model.
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
     * Finds the LineItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LineItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LineItem::findOne($id)) !== null) {
            //        if (($model = LineItem::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
