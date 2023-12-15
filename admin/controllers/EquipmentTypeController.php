<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use common\models\EquipmentType;
use common\models\search\EquipmentTypeSearch;
use kartik\mpdf\Pdf;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;;

use yii\filters\AccessControl;


/**
 * EquipmentTypeController implements the CRUD actions for EquipmentType model.
 */
class EquipmentTypeController extends Controller
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
                        'allow' => P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_VIEW),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_EXPORT),
                        'actions' => ['export'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_NEW),
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
     * Lists all EquipmentType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EquipmentTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (Yii::$app->request->get('export') === 'pdf') {

            if ($dataProvider instanceof yii\data\ActiveDataProvider) {
                $dataProvider->pagination = false;
            }
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A3,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_DOWNLOAD,
                'options' => [
                    'title' => 'Locations',
                    'margin' => [
                        'top' => 20,
                        'right' => 15,
                        'bottom' => 20,
                        'left' => 15,
                    ],
                ],
                'methods' => [
                    'SetFooter' => ['|Page {PAGENO}|'],
                ],
            ]);
            $pdf->filename = 'equipment-type-report.pdf';
            $allContent = $this->renderPartial('export', compact('searchModel', 'dataProvider'));
            $pdf->content = $allContent;
            return $pdf->render();
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipmentType model.
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
     * Creates a new EquipmentType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($category_id = null)
    {
        $model = new EquipmentType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $model->name .  ' Has been created!');

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'category_id' => $category_id,
            ]);
        }
    }


    /**
     * Updates an existing EquipmentType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->addFlash("warning", $model->name . " has been updated successfully");

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipmentType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $name = $this->findModel($id)->name;

        $this->findModel($id)->delete();
        Yii::$app->session->addFlash("danger", $name . " is deleted");

        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipmentType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipmentType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipmentType::findOne($id)) !== null) {
            //        if (($model = EquipmentType::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
