<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use common\models\Technician;
use common\models\search\TechnicianSearch;
use yii\web\Controller;
use common\data\Countries;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Account;
use common\models\AccountType;
use kartik\mpdf\Pdf;
use common\models\Division;
use common\models\Profession;
use yii\filters\AccessControl;
use common\models\UserGrid;
use yii\web\Response;
use common\models\users\AbstractAccount;



/**
 * TechnicianController implements the CRUD actions for Technician model.
 */
class TechnicianController extends Controller
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
                        'allow' => P::c(P::MANAGEMENT_TECHNICIAN_PAGE_VIEW),
                        'actions' => ['index', 'save', 'view', 'delete-picture', 'save-hidden-attributes'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_TECHNICIAN_PAGE_UPDATE),
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_TECHNICIAN_PAGE_DELETE),
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_TECHNICIAN_PAGE_EXPORT),
                        'actions' => ['export'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => P::c(P::MANAGEMENT_TECHNICIAN_PAGE_NEW),
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
     * Lists all Technician models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TechnicianSearch();
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

            $pdf->filename = 'technician-report.pdf';
            $pdf->content = $this->renderPartial('export', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

            return $pdf->render();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSave()
    {
        $fadedColumns = Yii::$app->request->post('faded_columns');
        $user_id = Yii::$app->user->id;
        $page_id = Yii::$app->request->post('controller_id');
        $user_grid = UserGrid::find()->where(['user_id' => $user_id])->andWhere(['page_id' => $page_id])->one();

        $existingValue = $user_grid ? $user_grid->value : '';
        $updatedValue = $existingValue ? $existingValue . ',' . $fadedColumns : $fadedColumns;

        if (empty($user_grid)) {
            $model = new UserGrid();
            $model->user_id = $user_id;
            $model->page_id = $page_id;
            $model->value = $updatedValue;
        } else {
            $user_grid->value = $updatedValue;
            $model = $user_grid;
        }
        if (!empty($updatedValue)) {
            if ($model->save()) {
                Yii::$app->getSession()->addFlash("warning", "The selected columns have been hidden");
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
    }

    public function actionSaveHiddenAttributes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $hiddenAttributes = Yii::$app->request->post('hidden-attributes', []);
            $page_id = Yii::$app->request->post('controller_id', '');
            $user_grid = UserGrid::find()->where(['user_id' => Yii::$app->user->id])->andWhere(['page_id' => $page_id])->one();

            if ($user_grid) {
                $valuesArray = explode(',', $user_grid->value);
                $valuesArray = array_diff($valuesArray, $hiddenAttributes);
                $user_grid->value = implode(',', $valuesArray);
                $user_grid->save();
                Yii::$app->session->set('hiddenAttributes', $hiddenAttributes);
                Yii::$app->getSession()->addFlash("success", "The selected columns have been shown");
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Displays a single Technician model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    // public function actionGenerate()
    // {

    //     $model = new Technician();
    //     $model->account_type  = Technician::TECHNICIAN;
    //     $model->division_id   = Division::DIVISION_MALL;
    //     $model->profession_id = Profession::find()->one()->id;
    //     $model->name = Yii::$app->security->generateRandomString(20);
    //     $model->email = Yii::$app->security->generateRandomString(10) . '@gmail.com';
    //     $model->phone_number = '0501234152';
    //     $model->password_input = 'Pass*123';
    //     $model->badge_number = Yii::$app->security->generateRandomString(5);

    //     return $this->render('_form', [
    //         'model' => $model
    //     ]);
    // }

    /**
     * Creates a new Technician model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Technician();

        $model->scenario = Technician::SCENARIO_CREATE;
        if ($model->load(Yii::$app->request->post())) {
            if (Account::ValidateNumber($model->phone_number, $model->country)) {
                if ($model->save()) {

                    Yii::$app->getSession()->addFlash("success", $model->name . " is created successfully");
                } else {

                    $out = [];
                    $count = 0;

                    foreach ($model->errors as $key => $datum) {
                        $out[] = $datum[$count];

                        $count++;
                    }

                    Yii::$app->session->setFlash('error', implode(' | ', $out));
                    return $this->render('_form', [
                        'model' => $model
                    ]);
                }
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->addFlash("error", $model->phone_number . ' is not a valid number for: ' . Countries::getCountryName(Account::GetCountryName($model->phone_number)));
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionDeletePicture($id)
    {
        $model = $this->findModel($id);

        Technician::updateAll(['image' => null], ['id' => $model->id]);
        $model->cleanFiles();

        Yii::$app->session->setFlash('error', 'Profile Image Deleted');

        return $this->redirect(['index']);
    }


    /**
     * Updates an existing Technician model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $old_type = $model->account->type;

        $model->account_type = $model->account->type;

        if ($model->load(Yii::$app->request->post())) {

            $model->account_type = Yii::$app->request->post('Technician')['account_type'];

            if (Account::ValidateNumber($model->phone_number, $model->country)) {

                if ($model->save()) {
                    if ($old_type != $model->account_type) {
                        $account = Account::findOne($model->id);
                        $account->type = $model->account_type;
                        if ($account->save()) {

                            Yii::$app->getSession()->addFlash("success", '<strong>' . $model->name . '</strong> is now a <strong>' . Technician::getTechnicianAccountTypeID($model->id) . '</strong>');
                        }
                    }
                    Yii::$app->getSession()->addFlash("warning", $model->name . "  updated successfully");
                    return $this->redirect(['index']);
                }
            } else {
                Yii::$app->getSession()->addFlash("error", $model->phone_number . ' is not a valid number for: ' . Countries::getCountryName(Account::GetCountryName($model->phone_number)));
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing Technician model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $name = $this->findModel($id)->name;
        $model = $this->findModel($id);
        $model->account_type = $model->account->type;
        $accountType = @AccountType::find()->where(['id' => $model->account->type])->one();
        $accountTypestatus = @$accountType->status;

        if ($accountTypestatus != AccountType::STATUS_DELETED) {
            if ($model->status == Technician::STATUS_ENABLED) {
                $model->status = Technician::STATUS_DELETED;
            } else {
                $model->status = Technician::STATUS_ENABLED;
            }
            $model->save();
            if ($model->status == Technician::STATUS_ENABLED) {

                Yii::$app->session->addFlash("success", "Technician " . $name . " has been undeleted");
            } else {
                Yii::$app->session->addFlash("danger", "Technician " . $name . " has been deleted");
            }
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->addFlash("warning", "You need to undelete the account type: " . $accountType->name . ' before deleting ' . $name);
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Technician model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Technician the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $query = Technician::find()->where(['id' => $id]);

        if (empty(Account::getAdminAccountTypeDivisionModel())) {
        } else if (Account::getAdminDivisionID() == Division::DIVISION_VILLA) {
            $query->andFilterWhere(['division_id' => Account::getAdminDivisionID()]);
        } else {
            $query->andFilterWhere(['main_sector_id' => Account::getAdminMainSectorId()]);
        }

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
