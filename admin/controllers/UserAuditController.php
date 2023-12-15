<?php

namespace admin\controllers;

use Yii;
use common\models\UserAudit;
use common\models\search\UserAuditSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\config\includes\P;



/**
 * UserAuditController implements the CRUD actions for UserAudit model.
 */
class UserAuditController extends Controller
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
                        'allow' => P::c(P::MANAGEMENT_LOCATION_EQUIPMENTS_PAGE_AUDIT) || P::c(P::MANAGEMENT_LOCATION_PAGE_AUDIT) || P::c(P::MANAGEMENT_EQUIPMENT_PAGE_AUDIT) || P::c(P::MANAGEMENT_TECHNICIAN_PAGE_AUDIT) || P::c(P::MANAGEMENT_EQUIPMENT_TYPE_PAGE_AUDIT) || P::c(P::ADMINS_ADMIN_PAGE_AUDIT) || P::c(P::CONFIGURATIONS_CATEGORY_PAGE_AUDIT)
                            || P::c(P::CONFIGURATIONS_CATEGORY_PAGE_AUDIT)
                            || P::c(P::CONFIGURATIONS_MAIN_SECTOR_PAGE_AUDIT)
                            || P::c(P::CONFIGURATIONS_PROFESSION_PAGE_AUDIT)
                            || P::c(P::CONFIGURATIONS_SECTOR_PAGE_AUDIT)
                            || P::c(P::MANAGEMENT_SEGMENT_PATH_PAGE_AUDIT),
                        'actions' => ['index'],
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
     * Lists all UserAudit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserAuditSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
