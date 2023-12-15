<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use common\models\Category;
use common\models\Profession;
use common\models\ProfessionCategory;
use common\models\search\CategorySearch;
use kartik\mpdf\Pdf;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\validators\UniqueValidator;
use yii\filters\AccessControl;


/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
                        'allow' => P::c(P::CONFIGURATIONS_CATEGORY_PAGE_VIEW),
                        'actions' => ['index', 'view', 'viewall'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_CATEGORY_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_CATEGORY_PAGE_EXPORT),
                        'actions' => ['export'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_CATEGORY_PAGE_UPDATE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::CONFIGURATIONS_CATEGORY_PAGE_NEW),
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
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
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
            $pdf->filename = 'category-report.pdf';
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
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    public function actionViewall()
    {
        return $this->render('viewall');
    }


    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->parent_id == null) {
                $parentCategory = Category::find()
                    ->where(['name' => $model->name, 'parent_id' => null])
                    ->one();
                if (!empty($parentCategory)) {
                    Yii::$app->getSession()->addFlash("error", "Category name must be unique among parent categories.");
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }
            if ($model->save()) {
                $postData = Yii::$app->request->post();
                if (!empty($postData['Category']['professionCategory'])) {
                    $profession = $postData['Category']['professionCategory'];
                    ProfessionCategory::deleteAll(['category_id' => $model->id]);
                    if (!empty($profession)) {
                        $professionCategory = $profession;
                        if (!empty($professionCategory)) {
                            foreach ($professionCategory as $index => $professionCat) {
                                (new ProfessionCategory([
                                    'profession_id' => $professionCat,
                                    'category_id'     => $model->id,
                                ]))->save();
                            }
                        }
                    }
                }
                Yii::$app->getSession()->addFlash("success", "Category: " . $model->name . " is created successfully");
                return $this->redirect(['index']);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    public function actionUpdate($id)
    {
        $model = Category::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->parent_id == null) {
                $parentCategory = Category::find()
                    ->where(['name' => $model->name])
                    ->andWhere(['parent_id' => null])
                    ->andWhere(['!=', 'id', $model->id])
                    ->one();

                if (!empty($parentCategory)) {
                    Yii::$app->getSession()->addFlash("error", "Category name must be unique among parent categories.");
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            }
            if ($model->save(false)) {
                $postData = Yii::$app->request->post();
                if (!empty($postData['Category']['professionCategory'])) {
                    $profession = $postData['Category']['professionCategory'];
                    ProfessionCategory::deleteAll(['category_id' => $model->id]);
                    if (!empty($profession)) {
                        $professionCategory = $profession;
                        if (!empty($professionCategory)) {
                            foreach ($professionCategory as $index => $professionCat) {
                                (new ProfessionCategory([
                                    'profession_id' => $professionCat,
                                    'category_id'     => $model->id,
                                ]))->save();
                            }
                        }
                    }
                }

                Yii::$app->getSession()->addFlash("warning", "Category: " . $model->name . " is updated successfully");
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    // public function actionDelete($id)
    // {
    //     $name = $this->findModel($id)->name;
    //     $this->findModel($id)->delete();
    //     Yii::$app->session->addFlash("danger", "Category: " . $name . " is deleted");

    //     return $this->redirect(['index']);
    // }
    public function actionDelete($id)
    {
        $name = $this->findModel($id)->name;
        $model = $this->findModel($id);
        if ($model->status == Category::STATUS_ENABLED) {
            $model->status = Category::STATUS_DELETED;
        } else {
            $model->status = Category::STATUS_ENABLED;
        }
        $model->save();
        if ($model->status == Category::STATUS_ENABLED) {

            Yii::$app->session->addFlash("warning", "Category  " . $name . " has been undeleted");
        } else {
            Yii::$app->session->addFlash("danger", "Category  " . $name . " has been deleted");
        }
        return $this->redirect(['index']);
    }


    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            //        if (($model = Category::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
