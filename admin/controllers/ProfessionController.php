<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use common\models\Profession;
use common\models\search\ProfessionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ProfessionCategory;
use yii\filters\AccessControl;


/**
 * ProfessionController implements the CRUD actions for Profession model.
 */
class ProfessionController extends Controller
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
                        'allow' => P::c(P::CONFIGURATIONS_PROFESSION_PAGE_VIEW),
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_PROFESSION_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_PROFESSION_PAGE_UPDATE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_PROFESSION_PAGE_NEW),
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
     * Lists all Profession models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProfessionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Profession model.
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
     * Creates a new Profession model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Profession();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $postData = Yii::$app->request->post();
            $profession = $postData['Profession']['professionCategory'];
            ProfessionCategory::deleteAll(['profession_id' => $model->id]);
            if (!empty($profession)) {
                $professionCategory = $profession;
                if (!empty($professionCategory)) {
                    foreach ($professionCategory as $index => $professionCat) {
                        (new ProfessionCategory([
                            'category_id' => $professionCat,
                            'profession_id'     => $model->id,
                        ]))->save();
                    }
                }
            }
            Yii::$app->getSession()->addFlash("success", "Profession: " . $model->name . " is created successfully");
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Profession model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $postData = Yii::$app->request->post();
            $profession = $postData['Profession']['professionCategory'];
            ProfessionCategory::deleteAll(['profession_id' => $model->id]);
            if (!empty($profession)) {
                $professionCategory = $profession;
                if (!empty($professionCategory)) {
                    foreach ($professionCategory as $index => $professionCat) {
                        (new ProfessionCategory([
                            'category_id' => $professionCat,
                            'profession_id'     => $model->id,
                        ]))->save();
                    }
                }
            }
            Yii::$app->getSession()->addFlash("warning", "Profession: " . $model->name . " is updated successfully");
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Profession model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $name = $this->findModel($id)->name;
        $model = $this->findModel($id);
        if ($model->status == Profession::STATUS_ENABLED) {
            $model->status = Profession::STATUS_DELETED;
        } else {
            $model->status = Profession::STATUS_ENABLED;
        }
        $model->save();
        if ($model->status == Profession::STATUS_ENABLED) {

            Yii::$app->session->addFlash("warning", "Profession  " . $name . " has been undeleted");
        } else {
            Yii::$app->session->addFlash("danger", "Profession  " . $name . " has been deleted");
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Profession model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Profession the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Profession::findOne($id)) !== null) {
            //        if (($model = Profession::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}