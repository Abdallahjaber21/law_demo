<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use common\models\TechnicianShift;
use common\models\search\TechnicianShiftSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Technician;
use common\models\Shift;
use yii\filters\AccessControl;
use DateTime;
use yii\web\Response;
use yii\helpers\Html;
use common\models\Account;
use common\models\Division;
use yii\helpers\ArrayHelper;

/**
 * TechnicianShiftController implements the CRUD actions for TechnicianShift model.
 */
class TechnicianShiftController extends Controller
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
                        'allow' => Yii::$app->getUser()->can("developer") || Yii::$app->getUser()->can("super-admin") || Yii::$app->getUser()->can("admin") || P::c(P::MANAGEMENT_TECHNICIAN_SHIFTS_PAGE_VIEW),
                        'actions' => ['index', 'view', 'view-all', 'get-technician-shifts', 'save-shifts'],
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
     * Lists all TechnicianShift models.
     * @return mixed
     */

    public function actionIndex($fromday = null, $endday = null, $month = null, $year = null, $technician_id = null)
    {

        $user = \Yii::$app->getUser()->getIdentity();
        $adminMainSectorIds = Account::getAdminMainSectorId();
        $searchModel = new TechnicianShiftSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (empty($technician_id)) {
            if (empty(Account::getAdminAccountTypeDivisionModel())) {
                $technicians = Technician::find()->joinWith('division')->where(['division.has_shifts' => 1])->all();
            } else {
                $technicians = Technician::find()->joinWith('division')->where(['=', 'technician.main_sector_id', Account::getAdminMainSectorId()])->andwhere(['division.has_shifts' => 1])->all();
            }
        } else {
            $technicians = Technician::find()->innerJoin('division')->where(['technician.id' => $technician_id])->andwhere(['division.has_shifts' => 1])->all();
        }
        $allShiftTypes = Shift::find()->all();
        $model = new TechnicianShift();
        $months = [];
        $years = [];
        $daysInMonth = [];
        $shiftData = [];
        $daysArray = [];
        $selectedMonth = date('n');
        $selectedYear = date('Y');
        $currentYear = ($year != null) ? $year : date('Y');
        $currentMonth = ($month != null) ? $month : date('n');
        if ($currentMonth == date('n') && $currentYear == date('Y')) {
            $currentDay = date('d');
        } else if (!empty($fromday) && !empty($endday)) {
            $currentDay = $fromday;
        } else {
            $currentDay = date('1');
        }
        if ($currentMonth <= 12 && $currentMonth >= 1) {
            if (!empty($fromday) && !empty($endday)) {
                $daysArray = range($fromday, $endday);
            } else {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

                $daysArray = range($currentDay, $daysInMonth);
            }
            // exit;
            $months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
            $years = array_combine(range($currentYear, $currentYear + 1), range($currentYear, $currentYear + 1));
            $selectedYear = $currentYear;
            $selectedMonth = $currentMonth;
            if ($technician_id == null) {
                $existingShifts = TechnicianShift::find()
                    ->where(['YEAR(date)' => $currentYear, 'MONTH(date)' => $currentMonth])
                    ->all();
            } else {
                $existingShifts = TechnicianShift::find()
                    ->where(['YEAR(date)' => $currentYear, 'MONTH(date)' => $currentMonth])->andWhere(['technician_id' => $technician_id])
                    ->all();
            }
            foreach ($existingShifts as $existingShift) {
                $shiftData[$existingShift->technician_id][$existingShift->date][$existingShift->shift_id] = true;
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'months' => $months,
            'years' => $years,
            'days' => $daysArray,
            'allShiftTypes' => $allShiftTypes,
            'technicians' => $technicians,
            'technician_id' => $technician_id,
            'shiftData' => $shiftData,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'daysInMonth' => $daysInMonth,
        ]);
    }
    public function actionSaveShifts()
    {
        $technicianShiftsDataJSON = Yii::$app->request->post('technicianShiftsData');
        $shiftsData = json_decode($technicianShiftsDataJSON, true);
        $completeddate = Yii::$app->request->post('completedDate');
        $technician_id = Yii::$app->request->post('technician_id');
        $year = date('Y', strtotime($completeddate));
        $month = date('m', strtotime($completeddate));
        $fromday = Yii::$app->request->post('fromday');
        $endday = Yii::$app->request->post('endday');
        $currentYear = date('Y');
        $currentMonth = date('n');
        $query = [
            'YEAR(date)' => $year,
            'MONTH(date)' => $month,
        ];

        if (!empty($fromday) && !empty($endday)) {
            $days = range($fromday, $endday);
            $query['DAY(date)'] = $days;
        }

        if (!empty($technician_id)) {
            $query['technician_id'] = $technician_id;
        }
        if ($year >= $currentYear && $month >= $currentMonth) {
            if (!empty($shiftsData) && (!empty($completeddate))) {
                TechnicianShift::deleteAll($query);
                foreach ($shiftsData as $technicianId => $daysData) {
                    foreach ($daysData as $day => $shifts) {
                        $formattedDate = $year . '-' . $month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                        $technicianShift = new TechnicianShift();
                        $technicianShift->technician_id = $technicianId;
                        $technicianShift->date = $formattedDate;

                        foreach ($shifts as $shiftType) {
                            $shift = Shift::findOne(['id' => $shiftType]);
                            if ($shift) {
                                $technicianShift->shift_id = $shift->id;
                            }
                        }

                        if (!$technicianShift->save()) {

                            Yii::$app->getSession()->addFlash("error", "Failed to save changes.");
                            return $this->redirect(Yii::$app->request->referrer);
                        }
                    }
                }

                Yii::$app->getSession()->addFlash("success", "Your changes have been saved.");
                return $this->redirect(Yii::$app->request->referrer);
            } else {


                Yii::$app->getSession()->addFlash("error", "Failed to save changes.");
                return $this->redirect(Yii::$app->request->referrer);
            }
        } else {


            Yii::$app->getSession()->addFlash("error", "Failed to save with old dates.");
            return $this->redirect(Yii::$app->request->referrer);
        }
    }






    /**
     * Displays a single TechnicianShift model.
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
     * Creates a new TechnicianShift model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TechnicianShift();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Updates an existing TechnicianShift model.
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

    /**
     * Deletes an existing TechnicianShift model.
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
     * Finds the TechnicianShift model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TechnicianShift the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TechnicianShift::findOne($id)) !== null) {
            //        if (($model = TechnicianShift::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
