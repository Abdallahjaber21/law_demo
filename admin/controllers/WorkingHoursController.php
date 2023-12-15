<?php

namespace admin\controllers;

use common\components\helpers\DateTimeHelper;
use common\config\includes\P;
use common\models\search\WorkingHoursSearch;
use common\models\WorkingHours;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;;


/**
 * WorkingHoursController implements the CRUD actions for WorkingHours model.
 */
class WorkingHoursController extends Controller
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
                        'allow' => P::c(P::MISC_MANAGE_WORKING_HOURS),
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all WorkingHours models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WorkingHoursSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WorkingHours model.
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
     * Creates a new WorkingHours model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WorkingHours();

        if (Yii::$app->request->isPost) {
            if ($this->handlePost($model)) {
                return $this->redirect(['working-hours/index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing WorkingHours model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            if ($this->handlePost($model)) {
                return $this->redirect(['working-hours/index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    private function handlePost($model)
    {
        if (Yii::$app->request->isPost) {
            //loop through the days of the month, skip holidays and off days
            //calculate the sum of working hours in the month
            $year_month = Yii::$app->request->post("year_month");
            $holidaysIn = Yii::$app->request->post("holidays");
            $holidays = !empty($holidaysIn) ? explode(",", $holidaysIn) : [];

            $starts = Yii::$app->request->post("start");
            $ends = Yii::$app->request->post("end");
            $lunchs = Yii::$app->request->post("lunch");
            $offs = Yii::$app->request->post("off");

            list($year, $month) = explode("-", $year_month);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $dailyMinutes = [];
            $dailyHours = [];
            for ($i = 1; $i <= $daysInMonth; $i++) { //loop month
                $date = "{$year_month}-" . substr("0{$i}", -2);
                $dayOfWeek = (int)date("N", strtotime($date)) - 1;
                if (in_array($date, $holidays)) {
                    continue;
                }
                if (!empty($offs[$dayOfWeek])) {
                    continue;
                }
                if (empty($starts[$dayOfWeek]) || empty($ends[$dayOfWeek]) || empty($lunchs[$dayOfWeek])) {
                    $offs[$dayOfWeek] = 'on';
                    continue;
                }

                $startE = explode(":", $starts[$dayOfWeek]);
                $endE = explode(":", $ends[$dayOfWeek]);
                $lunchE = explode(":", $lunchs[$dayOfWeek]);

                if (count($startE) < 2 || count($endE) < 2 || count($lunchE) < 2) {
                    $offs[$dayOfWeek] = 'on';
                    continue;
                }

                $startM = (((int)$startE[0]) * 60) + ((int)$startE[1]);
                $endM = (((int)$endE[0]) * 60) + ((int)$endE[1]);
                $lunchM = (((int)$lunchE[0]) * 60) + ((int)$lunchE[1]);

                $dailyHours[$date . '-' . $dayOfWeek] = DateTimeHelper::minutesToHoursMinutes(($endM - $startM) - $lunchM);
                $dailyMinutes[$date . '-' . $dayOfWeek] = ($endM - $startM) - $lunchM;
            }
            $totalMinutes = array_sum($dailyMinutes);
            $totalHours = $totalMinutes / 60;

            $model->year_month = $year_month;
            $model->total_hours = $totalHours;
            $model->holidays = Json::encode($holidays);
            $model->daily_hours = Json::encode([
                'starts' => $starts,
                'ends'   => $ends,
                'lunchs' => $lunchs,
                'offs'   => $offs,
            ]);
            if ($model->save()) {
                return true;
            } else {
                if (!empty($model->errors)) {
                    foreach ($model->errors as $attribute => $errors) {
                        foreach ($errors as $index => $error) {
                            Yii::$app->session->addFlash("danger", $error);
                        }
                    }
                }
            }
            //            echo "<pre>";
            //            echo $totalHours . PHP_EOL;
            //            echo $totalMinutes . PHP_EOL;
            //            echo DateTimeHelper::minutesToHoursMinutes($totalMinutes) . PHP_EOL;
            //            print_r($dailyHours);
            //            print_r($dailyMinutes);
            //            print_r(Yii::$app->request->post());
            //            exit();
        }
        return false;
    }

    /**
     * Deletes an existing WorkingHours model.
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
     * Finds the WorkingHours model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WorkingHours the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WorkingHours::findOne($id)) !== null) {
            //        if (($model = WorkingHours::find()->multilingual()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
