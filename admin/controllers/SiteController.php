<?php

namespace admin\controllers;

use common\components\helpers\ImageUploadHelper;
use common\config\includes\P;
use common\data\Countries;
use common\models\Account;
use common\models\Location;
use common\models\Assignee;
use common\models\Technician;
use common\models\Maintenance;
use common\models\Division;
use common\models\Metric;
use yii\helpers\Url;
use kartik\mpdf\Pdf;
use Dompdf\Dompdf;
use yii\helpers\FileHelper;
use Dompdf\Options;
use common\models\Metrics;
use common\models\RepairRequest;
use common\models\RouteAssignment;
use common\models\Sector;
use common\models\search\RepairRequestSearch;
use common\models\search\AssigneeSearch;
use yii\data\ActiveDataProvider;
use common\behaviors\ImageUploadBehavior;
use common\models\LoginAudit;
use common\models\users\Admin;
use common\models\users\forms\AbstractForgetPasswordForm;
use common\models\users\forms\AbstractLoginForm;
use common\models\users\forms\AbstractPasswordCodeForm;
use common\models\users\forms\AbstractResetPasswordForm;
use common\models\users\forms\AdminLoginForm;
use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use ZipArchive;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'login',
                            'error',
                            'forgot-password',
                            'forgot-password-code',
                            'reset-password',
                            'resend-code'
                        ],
                        'allow' => true,
                    ],
                    //                    [
                    //                        'actions' => ['index'],
                    //                        'allow'   => true,
                    //                        'roles'   => ['developer'],
                    //                    ],
                    [
                        'actions' => ['index', 'profile', 'save-signature', 'delete-signature'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['works-dashboard'],
                        'allow' => P::c(P::REPAIR_REPAIR_DASHBOARD_PAGE_VIEW),
                        'roles' => ['@'],
                    ],

                    [
                        'actions' => ['monthly-dashboard', 'monthly-pdf'],
                        'allow' => P::c(P::REPAIR_MONTHLY_DASHBOARD_PAGE_VIEW),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['labor-charge', 'export-labor-charge'],
                        'allow' => P::c(P::REPORT_LABOR_CHARGE_PAGE_VIEW),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['summary-dashboard'],
                        'allow' => P::c(P::REPAIR_SUMMARY_DASHBOARD_PAGE_VIEW),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['export-reports'],
                        'allow' => P::c(P::REPORT_SECTION_SECTION_ENABLED),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => \Yii::$app->getUser()->isGuest ? "main-login" : 'main'
            ],
        ];
    }

    /**
     * Displays homepage.
     */
    public function actionIndex()
    {
        if (P::c(P::REPAIR_SUMMARY_DASHBOARD_PAGE_VIEW)) {
            return $this->redirect(['site/summary-dashboard']);
        } else {
            return $this->redirect(['site/profile']);
        }
        $from = Yii::$app->getRequest()->get("_s");
        $to = Yii::$app->getRequest()->get("_e");
        if (empty($from)) {
            $from = date("Y-m-d", strtotime("-6 days"));
        }
        if (empty($to)) {
            $to = date("Y-m-d");
        }

        $filterId = Yii::$app->request->get("id");
        $filterSector = Yii::$app->request->get("sector_id");
        $filterTechnician = Yii::$app->request->get("technician_id");

        /* @var $admin Admin */
        $admin = Yii::$app->user->identity;
        $sectors = ArrayHelper::getColumn($admin->sectors, "id", false);
        $sectors = array_intersect($sectors, Admin::activeSectorsIds());
        $pendingRepairRequests = RepairRequest::find()
            ->joinWith(['equipment', 'location'])
            ->where([
                'AND',
                [
                    RepairRequest::tableName() . '.status' => [
                        RepairRequest::STATUS_DRAFT,
                        RepairRequest::STATUS_CREATED,
                    ]
                ],
                (!empty($sectors) ? [
                    Location::tableName() . '.sector_id' => $sectors
                ] : "1=1"
                ),
                (!empty($filterSector) ? [
                    Location::tableName() . '.sector_id' => $filterSector
                ] : "1=1"
                )
            ])
            ->andFilterWhere([RepairRequest::tableName() . '.id' => $filterId])
            ->andFilterWhere([RepairRequest::tableName() . '.technician_id' => $filterTechnician])
            ->orderBy([RepairRequest::tableName() . '.created_at' => SORT_DESC])
            ->all();

        $completedRepairRequests = RepairRequest::find()
            ->joinWith(['equipment', 'location'])
            ->where([
                'AND',

                [
                    RepairRequest::tableName() . '.status' => [
                        RepairRequest::STATUS_CHECKED_IN,


                    ]
                ],
                (!empty($sectors) ? [
                    Location::tableName() . '.sector_id' => $sectors
                ] : "1=1"
                ),
                (!empty($filterSector) ? [
                    Location::tableName() . '.sector_id' => $filterSector
                ] : "1=1"
                )
            ])
            ->andFilterWhere([RepairRequest::tableName() . '.id' => $filterId])
            ->andFilterWhere([RepairRequest::tableName() . '.technician_id' => $filterTechnician])
            ->orderBy([RepairRequest::tableName() . '.informed_at' => SORT_DESC])
            ->all();
        $departedRepairRequests = RepairRequest::find()
            ->joinWith(['equipment', 'location'])
            ->where([
                'AND',

                [
                    RepairRequest::tableName() . '.status' => [
                        RepairRequest::STATUS_COMPLETED,
                    ]
                ],
                (!empty($sectors) ? [
                    Location::tableName() . '.sector_id' => $sectors
                ] : "1=1"
                ),
                (!empty($filterSector) ? [
                    Location::tableName() . '.sector_id' => $filterSector
                ] : "1=1"
                )
            ])
            ->andFilterWhere([RepairRequest::tableName() . '.id' => $filterId])
            ->andFilterWhere([RepairRequest::tableName() . '.technician_id' => $filterTechnician])
            ->orderBy([RepairRequest::tableName() . '.departed_at' => SORT_DESC])
            ->all();

        return $this->render(
            'index',
            compact(
                "from",
                "to",
                "pendingRepairRequests",
                "departedRepairRequests",
                "completedRepairRequests"
            )
        );
    }
    public function actionSummaryDashboard()
    {

        $from = Yii::$app->getRequest()->get("_s");
        $to = Yii::$app->getRequest()->get("_e");
        if (empty($from)) {
            $from = date("Y-m-d", strtotime("-6 days"));
        }
        if (empty($to)) {
            $to = date("Y-m-d");
        }
        $admin = Yii::$app->user->identity;
        $repairSearchModel = new RepairRequestSearch(["formNameParam" => "RepairModelSearch"]);
        $params = Yii::$app->request->queryParams;
        $repairRequests = $repairSearchModel->search($params);
        $repairRequests->query
            ->andWhere([
                'NOT IN',
                'repair_request.status',
                [
                    RepairRequest::STATUS_DRAFT,
                    RepairRequest::STATUS_COMPLETED,
                    RepairRequest::STATUS_CANCELLED,
                ]
            ])
            ->andWhere(['<=', 'DATE(scheduled_at)', gmdate("Y-m-d")])
            ->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);
        $overdueSearchModel = new RepairRequestSearch(["formNameParam" => "OverdueModelSearch"]);
        $overdueparams = Yii::$app->request->queryParams;
        $overdue = $overdueSearchModel->search($overdueparams);
        $overdue->query
            ->select([
                'repair_request.*', 'DATEDIFF(NOW(), repair_request.created_at) AS date_diff',
            ])
            ->andWhere([
                'IN', 'repair_request.status', [RepairRequest::STATUS_DRAFT, RepairRequest::STATUS_CREATED]
            ])
            ->andWhere(['<', 'repair_request.created_at', gmdate("Y-m-d")])
            ->andWhere(['service_type' => RepairRequest::TYPE_PPM])
            ->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()])
            ->orderBy(['date_diff' => SORT_DESC, 'id' => SORT_ASC]);

        $subQuery = Assignee::find()
            ->select(['user_id', 'MAX(assignee.updated_at) AS max_updated_at'])
            ->innerJoin('technician', 'technician.id = assignee.user_id')
            ->andWhere(['in', 'assignee.status', [Assignee::STATUS_BUSY, Assignee::STATUS_BREAK, Assignee::STATUS_HOURLY_LEAVE]])
            ->andFilterWhere(['=', Technician::tableName() . '.division_id', Account::getAdminDivisionID()])
            ->groupBy('user_id');
        $searchModel = new AssigneeSearch();
        $params = Yii::$app->request->queryParams;
        $technicians = $searchModel->search($params);
        $technicians->query->select([
            'assignee.user_id',
            'assignee.updated_at',
            'technician.name',
            'technician.badge_number',
            'technician.main_sector_id',
            'technician.profession_id',
            'assignee.status',
            'assignee.repair_request_id',
            'repair_request.service_type',
        ])
            ->innerJoin(['latest_assignee' => $subQuery], 'assignee.user_id = latest_assignee.user_id AND assignee.updated_at = latest_assignee.max_updated_at')
            ->innerJoin('technician', 'technician.id = assignee.user_id')
            ->innerJoin('repair_request', 'repair_request.id = assignee.repair_request_id')
            ->orderBy(['assignee.updated_at' => SORT_DESC]);

        return $this->render(
            'summary',
            compact(
                "from",
                "to",
                "overdue",
                "repairRequests",
                "repairSearchModel",
                "overdueSearchModel",
                'technicians',
                "searchModel"

            )
        );
    }
    public function actionWorksDashboard()
    {

        $admin_division_id = @Account::getAdminDivisionModel()->id;
        $admin_main_sector_id = @Account::getAdminMainSectorId();

        $from = Yii::$app->getRequest()->get("_s");
        $to = Yii::$app->getRequest()->get("_e");
        if (empty($from)) {
            $from = date("Y-m-d", strtotime("-6 days"));
        }
        if (empty($to)) {
            $to = date("Y-m-d");
        }

        $filterid = Yii::$app->request->get("id");
        $filterSector = Yii::$app->request->get("sector_id");
        $filterTechnician = Yii::$app->request->get("technician_id");
        $filterServiceType = Yii::$app->request->get("service_type");

        /* @var $admin Admin */
        $admin = Yii::$app->user->identity;
        $sectors = ArrayHelper::getColumn($admin->sectors, "id", false);
        $pendingSearchModel = new RepairRequestSearch();
        $params = Yii::$app->request->queryParams;
        $pendingRepairRequests = $pendingSearchModel->search($params);

        $pendingRepairRequests->query->joinWith(['equipment', 'location'])
            ->leftJoin('assignee a', 'repair_request.id = a.repair_request_id')
            ->andWhere(['<=', 'DATE(scheduled_at)', gmdate("Y-m-d")])
            ->andWhere(
                [
                    'IN',
                    'repair_request.status',
                    [
                        RepairRequest::STATUS_DRAFT,
                        RepairRequest::STATUS_CREATED,
                    ]
                ]
            );
        if (!empty($filterTechnician)) {
            $pendingRepairRequests->query->andWhere(['IN', 'a.user_id', $filterTechnician]);
        }
        if (!empty($filterServiceType)) {
            $pendingRepairRequests->query->andWhere(['repair_request.service_type' => $filterServiceType]);
        }
        $pendingRepairRequests->query->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);
        $pendingRepairRequests->query->orderBy([RepairRequest::tableName() . '.created_at' => SORT_DESC]);
        // $pendingRepairRequests = RepairRequest::find()
        //     ->joinWith(['equipment', 'location'])
        //     ->leftJoin('assignee a', 'repair_request.id = a.repair_request_id')
        //     ->where([
        //         'AND',
        //         [
        //             RepairRequest::tableName() . '.status' => [
        //                 RepairRequest::STATUS_DRAFT,
        //                 RepairRequest::STATUS_CREATED,
        //             ]
        //         ],
        //         ['<=', 'DATE(scheduled_at)', gmdate("Y-m-d")],
        //     ]);
        // if (!empty($filterid)) {
        //     $pendingRepairRequests->andWhere(['repair_request.id' => $filterid]);
        // }
        // if (!empty($filterTechnician)) {
        //     $pendingRepairRequests->andWhere(['IN', 'a.user_id', $filterTechnician]);
        // }
        // if (!empty($filterServiceType)) {
        //     $pendingRepairRequests->andWhere(['repair_request.service_type' => $filterServiceType]);
        // }

        // $pendingRepairRequests->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);
        // $pendingRepairRequests = $pendingRepairRequests->orderBy([RepairRequest::tableName() . '.created_at' => SORT_DESC])
        //     ->all();
        // ->createCommand()
        // ->rawSql;

        // print_r($pendingRepairRequests);
        // exit;
        // if (!empty($admin_division_id)) {
        //     $pendingRepairRequests = $pendingRepairRequests->andFilterWhere([
        //         RepairRequest::tableName() . '.division_id' => $admin_division_id,
        //     ]);
        // }
        // if (!empty($admin_main_sector_id)) {
        //     $pendingRepairRequests = $pendingRepairRequests->andFilterWhere([
        //         RepairRequest::tableName() . '.main_sector_id' => $admin_main_sector_id,
        //     ]);
        // }



        // ONGOING SERVICES

        $ongoingSearchModel = new RepairRequestSearch(["formNameParam" => "ongoingModelSearch"]);
        $params = Yii::$app->request->queryParams;
        $ongoingServices = $ongoingSearchModel->search($params);
        $ongoingServices->query->joinWith(['equipment', 'location'])
            ->leftJoin('assignee a', 'repair_request.id = a.repair_request_id')
            ->andWhere([
                'AND',
                [
                    RepairRequest::tableName() . '.status' => [
                        RepairRequest::STATUS_CHECKED_IN,
                        RepairRequest::STATUS_ON_HOLD,
                        RepairRequest::STATUS_UNABLE_TO_ACCESS,
                        RepairRequest::STATUS_NOT_COMPLETED,
                        RepairRequest::STATUS_REQUEST_DIFFERENT_TECHNICIAN,
                    ],
                ],
            ]);

        if (!empty($filterTechnician)) {
            $ongoingServices->query->andWhere(['IN', 'a.user_id', $filterTechnician]);
        }
        if (!empty($filterServiceType)) {
            $ongoingServices->query->andWhere(['repair_request.service_type' => $filterServiceType]);
        }
        $ongoingServices->query->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);
        $ongoingServices->query->orderBy([RepairRequest::tableName() . '.created_at' => SORT_DESC]);
        // departed Services
        $departedSearchModel = new RepairRequestSearch(["formNameParam" => "departedModelSearch"]);
        $params = Yii::$app->request->queryParams;
        $departedRepairRequests = $departedSearchModel->search($params);
        $departedRepairRequests->query->joinWith(['equipment', 'location'])
            ->leftJoin('assignee a', 'repair_request.id = a.repair_request_id')

            ->andWhere([
                'AND',

                [
                    RepairRequest::tableName() . '.status' => [
                        RepairRequest::STATUS_COMPLETED,
                        RepairRequest::STATUS_REQUEST_COMPLETION,
                    ],

                ],
                // (!empty($sectors) ? [
                //     Location::tableName() . '.sector_id' => $sectors
                // ] : "1=1"
                // ),
                // (!empty($filterSector) ? [
                //     Location::tableName() . '.sector_id' => $filterSector
                // ] : "1=1"
                // )
            ])->andWhere([
                'OR',
                ['DATE(departed_at)' => gmdate("Y-m-d")],
                ['DATE(completed_at)' => gmdate("Y-m-d")],

            ]);
        if (!empty($filterTechnician)) {
            $departedRepairRequests->query->andWhere(['IN', 'a.user_id', $filterTechnician]);
        }
        if (!empty($filterServiceType)) {
            $departedRepairRequests->query->andWhere(['repair_request.service_type' => $filterServiceType]);
        }
        $departedRepairRequests->query->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);
        $departedRepairRequests->query->orderBy([RepairRequest::tableName() . '.created_at' => SORT_DESC]);

        // if (!empty($filterid)) {
        //     $departedRepairRequests->andWhere(['repair_request.id' => $filterid]);
        // }
        // if (!empty($filterTechnician)) {
        //     $departedRepairRequests->andWhere(['IN', 'a.user_id', $filterTechnician]);
        // }
        // if (!empty($filterServiceType)) {
        //     $departedRepairRequests->andWhere(['repair_request.service_type' => $filterServiceType]);
        // }
        // $departedRepairRequests->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);

        // if (!empty($admin_division_id)) {
        //     $departedRepairRequests = $departedRepairRequests->andFilterWhere([
        //         RepairRequest::tableName() . '.division_id' => $admin_division_id,
        //     ]);
        // }
        // if (!empty($admin_main_sector_id)) {
        //     $departedRepairRequests = $departedRepairRequests->andFilterWhere([
        //         RepairRequest::tableName() . '.main_sector_id' => $admin_main_sector_id,
        //     ]);
        // }

        // $departedRepairRequests = $departedRepairRequests->orderBy([RepairRequest::tableName() . '.departed_at' => SORT_DESC])
        //     ->all();


        // UPCOMING DAYS SERVICES
        $upcomingSearchModel = new RepairRequestSearch(["formNameParam" => "upcomingModelSearch"]);
        $params = Yii::$app->request->queryParams;
        $upcoming_days_services = $upcomingSearchModel->search($params);
        $upcoming_days_services->query->joinWith(['equipment', 'location'])
            ->leftJoin('assignee a', 'repair_request.id = a.repair_request_id')

            ->andWhere([
                'AND',

                [
                    RepairRequest::tableName() . '.status' => [
                        RepairRequest::STATUS_DRAFT,
                        RepairRequest::STATUS_CREATED,
                    ],

                ],
            ])
            ->andWhere([
                '>',
                'DATE(scheduled_at)',
                gmdate("Y-m-d"),
            ]);
        if (!empty($filterTechnician)) {
            $upcoming_days_services->query->andWhere(['IN', 'a.user_id', $filterTechnician]);
        }
        if (!empty($filterServiceType)) {
            $upcoming_days_services->query->andWhere(['repair_request.service_type' => $filterServiceType]);
        }
        $upcoming_days_services->query->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);
        $upcoming_days_services->query->orderBy([RepairRequest::tableName() . '.created_at' => SORT_DESC]);

        // END

        return $this->render(
            'works',
            compact(
                "from",
                "to",
                "pendingRepairRequests",
                "pendingSearchModel",
                "ongoingServices",
                "ongoingSearchModel",
                "departedRepairRequests",
                "departedSearchModel",
                "upcoming_days_services",
                "upcomingSearchModel",
            )
        );
    }

    public function actionMonthlyDashboard()
    {

        $admin_division_id = @Account::getAdminDivisionModel()->id;
        $from = Yii::$app->getRequest()->get("_s");
        $to = Yii::$app->getRequest()->get("_e");
        if (empty($from)) {
            $from = date("Y-m-d");
        }
        if (empty($to)) {
            $to = date("Y-m-t");
        }
        $admin = Yii::$app->user->identity;
        $loggedUserDivisionId = Yii::$app->user->identity->division_id;
        if (!empty($loggedUserDivisionId)) {

            $repairRequestCountData = new ActiveDataProvider([
                'query' => RepairRequest::find()
                    ->select(['service_type', 'MAX(id) as id', 'COUNT(id) as count'])
                    ->where(['between', 'scheduled_at', $from, $to])->andWhere(['division_id' => $loggedUserDivisionId])

                    ->orderBy(['count' => SORT_DESC])->groupBy(['service_type'])->asArray(),
            ]);

            $completedRepairRequestCountData = new ActiveDataProvider([
                'query' => RepairRequest::find()
                    ->select(['service_type', 'MAX(id) as id', 'COUNT(id) as count'])
                    ->where([
                        'status' => RepairRequest::STATUS_COMPLETED,
                        'division_id' => $loggedUserDivisionId,
                    ])
                    ->andWhere(['between', 'scheduled_at', $from, $to])
                    ->orderBy(['count' => SORT_DESC])
                    ->groupBy(['service_type'])
                    ->asArray(),
            ]);
        } else {

            $repairRequestCountData = new ActiveDataProvider([
                'query' => RepairRequest::find()
                    ->select(['service_type', 'division_id', 'MAX(id) as id', 'COUNT(id) as count'])
                    ->where(['between', 'scheduled_at', $from, $to])
                    ->orderBy(['count' => SORT_DESC])
                    ->groupBy(['service_type', 'division_id'])
                    ->asArray(),
            ]);

            $completedRepairRequestCountData = new ActiveDataProvider([
                'query' => RepairRequest::find()
                    ->select(['service_type', 'division_id', 'MAX(id) as id', 'COUNT(id) as count'])
                    ->where([
                        'status' => RepairRequest::STATUS_COMPLETED,
                    ])
                    ->andWhere(['between', 'scheduled_at', $from, $to])
                    ->groupBy(['service_type', 'division_id'])
                    ->orderBy(['count' => SORT_DESC])
                    ->asArray(),
            ]);
        }
        $searchModel = new RepairRequestSearch();
        $params = Yii::$app->request->queryParams;
        $approvedRepairRequests = $searchModel->search($params);
        $approvedRepairRequests->query
            ->andWhere([
                'NOT IN',
                'repair_request.status',
                [
                    RepairRequest::STATUS_DRAFT,
                    RepairRequest::STATUS_COMPLETED,
                    RepairRequest::STATUS_CANCELLED,
                ]
            ])
            ->andWhere(['between', 'scheduled_at', $from, $to])
            ->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);
        if (Yii::$app->request->get('export') === 'pdf') {
            $content = $this->renderPartial(
                'monthly-dashboard',

                compact(
                    "from",
                    "to",
                    "approvedRepairRequests",
                    "repairRequestCountData",
                    "completedRepairRequestCountData",
                    "searchModel",
                )
            );

            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A3,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'content' => $content,
                'options' => ['title' => 'Monthly Dashboard'],
                'methods' => [
                    'setHeader' => [$from . '->' . $to],
                    'SetFooter' => ['|Page {PAGENO}|'],
                ],
            ]);

            return $pdf->render();
        }
        return $this->render(
            'monthly-dashboard',
            compact(
                "from",
                "to",
                "approvedRepairRequests",
                "repairRequestCountData",
                "completedRepairRequestCountData",
                "searchModel",

            )
        );
    }

    public function actionLaborCharge()
    {
        $searchModel = new RepairRequestSearch();

        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        // $searchModel = new RepairRequestSearch();
        // $searchModel->load(Yii::$app->request->queryParams);
        // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>', 'labor_charge', 0])

            ->andFilterWhere(['=', RepairRequest::tableName() . '.division_id', Account::getAdminDivisionID()]);
        return $this->render('labor-charge', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionMaintenanceDashboard()
    {

        $from = Yii::$app->getRequest()->get("_s");
        $to = Yii::$app->getRequest()->get("_e");
        if (empty($from)) {
            $from = date("Y-m-d", strtotime("-6 days"));
        }
        if (empty($to)) {
            $to = date("Y-m-d");
        }

        $pendingRepairRequests = RepairRequest::find()
            ->where([
                'AND',
                [
                    'status' => [
                        RepairRequest::STATUS_DRAFT,
                        RepairRequest::STATUS_CREATED,
                    ]
                ],
                ['service_type' => RepairRequest::TYPE_MAINTENANCE],
            ])
            ->all();

        $completedRepairRequests = RepairRequest::find()
            ->where([
                'AND',
                ['service_type' => RepairRequest::TYPE_MAINTENANCE],
                [
                    'status' => [
                        RepairRequest::STATUS_CHECKED_IN,


                    ]
                ]
            ])
            ->all();

        $departedRepairRequests = RepairRequest::find()
            ->where([
                'AND',
                ['service_type' => RepairRequest::TYPE_MAINTENANCE],
                [
                    'status' => [
                        RepairRequest::STATUS_COMPLETED,
                    ]
                ]
            ])
            ->all();

        return $this->render(
            'index2',
            compact(
                "from",
                "to",
                "pendingRepairRequests",
                "departedRepairRequests",
                "completedRepairRequests"
            )
        );
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = "main-login";
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AbstractLoginForm();
        if ($model->load(Yii::$app->request->post())) {

            $model->UserClass = Admin::class;

            if ($model->login()) {

                // Login Success
                LoginAudit::logIp(LoginAudit::LOGIN_SUCCESS, Yii::$app->user->id, true, $model->email, null);
                // Log The IP END

                return $this->goBack();
            } else {
                LoginAudit::logIp(LoginAudit::LOGIN_DENIED, null, true, $model->email, null);
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        LoginAudit::logIp(null, Yii::$app->user->id, false, null, null);
        Yii::$app->user->logout();


        return $this->goHome();
    }

    public function actionProfile()
    {
        $model = Yii::$app->user->identity;
        if (!empty($model)) {
            if ($model->load(Yii::$app->request->post())) {
                $post = Yii::$app->request->post('Admin');

                if (Account::validateNumber($model->phone_number, $model->country)) {
                    // $model->image = 'user-default.jpg';  
                    // $model->random_token = '';
                    if ($model->save()) {

                        Yii::$app->getSession()->addFlash("success", Yii::t("app", "Profile updated successfully"));
                        return $this->redirect(['profile']);
                    }
                } else {
                    Yii::$app->getSession()->addFlash("error", $model->phone_number . ' is not a valid number for: ' . Countries::getCountryName(Account::GetCountryName($model->phone_number)));
                }
            }
        }
        return $this->render('edit-profile', [
            'model' => $model,
        ]);
    }
    public function actionSaveSignature()
    {
        $model = Yii::$app->user->identity;
        if (!empty($model)) {
            if ($model->load(Yii::$app->request->post())) {
                $post = Yii::$app->request->post('Admin');
                $imageData = $post['signature'];
                if (!empty($imageData)) {
                    // $model->signature = UploadedFile::getInstance($model, 'signature');
                    ImageUploadHelper::uploadBase64Image($imageData, $model, "signature");

                    // list($type, $data) = explode(';', $imageData);
                    // list(, $data)      = explode(',', $data);
                    // $imageBinary = base64_decode($data);
                    // $filename = "admin_signature_" . $model->id . ".jpg";
                    // $folderPath = Yii::getAlias('@static') . '/upload/images/admin_signatures/';
                    // if (!file_exists($folderPath)) {
                    //     mkdir($folderPath, 0777, true);
                    // }
                    // $filePath = $folderPath . $filename;
                    // file_put_contents($filePath, $imageBinary);
                    // $model->signature = $filename;
                    // if ($model->save()) {
                    //     Yii::$app->getSession()->addFlash("success", Yii::t("app", "Signature saved successfully"));
                    //     return $this->redirect(['profile']);
                    // }
                } else {
                    Yii::$app->getSession()->addFlash("danger", Yii::t("app", "Signature is invalid."));
                    return $this->redirect(['profile']);
                }
            } else {
                Yii::$app->getSession()->addFlash("danger", Yii::t("app", "Something went wrong"));
                return $this->redirect(['profile']);
            }
        }
        return $this->redirect(['profile']);
    }
    public function actionDeleteSignature($id)
    {
        $model = Admin::findOne($id);
        $signatureFilePath = Yii::getAlias('@static') . '/upload/images/signature/' . $model->signature;
        if (file_exists($signatureFilePath)) {
            unlink($signatureFilePath);
        } else {
            echo "File not found: $signatureFilePath";
        }
        Admin::updateAll(['signature' => null], ['id' => $model->id]);
        if ($model->save(false)) {
            Yii::$app->session->setFlash('error', 'Signature has been deleted');
        }
        return $this->redirect(['site/profile', 'id' => $id]);
    }



    /**
     * forgot password action.
     *
     * @return string
     */
    public function actionForgotPassword()
    {
        $this->layout = "main-login";
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AbstractForgetPasswordForm([
            'UserClass' => Admin::className(),
            'useCode' => true,
            'UserClassFilter' => [],
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->forgetPassword()) {
            //Redirect to enter code page
            Yii::$app->getSession()->set("reset-password-email", $model->email);
            Metric::addTo(Metrics::TYPE_ADMIN, null, Metrics::NUMBER_USERS_FORGOT_PASSWORD, 1);
            return $this->redirect(['/site/forgot-password-code']);
        } else {
            return $this->render('forgot-password', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Forget password code action.
     *
     * @return string
     */
    public function actionForgotPasswordCode()
    {
        $this->layout = "main-login";
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AbstractPasswordCodeForm([
            'UserClass' => Admin::className(),
            'UserClassFilter' => [],
        ]);
        $model->email = Yii::$app->getSession()->get("reset-password-email");
        if (empty($model->email)) {
            return $this->redirect(['/site/login']);
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->getSession()->set("reset-password-token", $model->token);
            return $this->redirect(['/site/reset-password']);
        } else {
            return $this->render('forgot-password-code', [
                'model' => $model,
                'can_resend' => Yii::$app->getSession()->get("reset-password-tries", 0) < 5
            ]);
        }
    }

    /**
     * Forget password code action.
     *
     * @return string
     */
    public function actionResetPassword()
    {
        $this->layout = "main-login";
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }


        $model = new AbstractResetPasswordForm([
            'UserClass' => Admin::className(),
            'UserClassFilter' => [],
        ]);
        $model->email = Yii::$app->getSession()->get("reset-password-email");
        $model->token = Yii::$app->getSession()->get("reset-password-token");
        if (empty($model->email)) {
            return $this->redirect(['/site/login']);
        }
        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            Yii::$app->getSession()->remove("reset-password-email");
            // return $this->goHome();

            $model = new AbstractLoginForm();

            return $this->render(
                'login',
                [
                    'model' => $model,
                    'reset_password' => true
                ]
            );
        } else {
            return $this->render('reset-password', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Resend password reset password.
     */
    public function actionResendCode()
    {
        $email = Yii::$app->getSession()->get("reset-password-email");

        $tries = Yii::$app->getSession()->get("reset-password-tries", 0);
        $tries++;
        Yii::$app->getSession()->set("reset-password-tries", $tries);
        if ($tries >= 5) {
            return $this->redirect(['/site/forgot-password-code']);
        }
        $user = Admin::findByEmail($email);
        if (!empty($user)) {
            if (!empty($user->password_reset_token)) {

                Yii::$app->mailer->compose('password-reset-code', [
                    'link' => $user->password_reset_token
                ])

                    ->setFrom(\Yii::$app->params['passwordResetEmail'])
                    ->setTo($user->email)
                    ->setSubject(\Yii::$app->params['project-name'] . ' - ' . \Yii::t("app", 'Password Reset'))
                    ->send();
                return $this->redirect(['/site/forgot-password-code']);
            }
        }
        return $this->goHome();
    }

    public function actionExportReports()
    {
        $from = Yii::$app->getRequest()->post("_s");
        $to = Yii::$app->getRequest()->post("_e");
        $fromHour = Yii::$app->getRequest()->post("from_hour");
        if ($fromHour < 10) {
            $fromHour = "0{$fromHour}";
        }
        if (empty($from)) {
            $from = date("Y-m-d 00:00:00");
        } else {
            $from .= " {$fromHour}:00:00";
        }
        if (empty($to)) {
            $to = date("Y-m-d 23:59:59");
        } else {
            $to .= ' 23:59:59';
        }

        if (Yii::$app->request->isPost) {

            /* @var $repairRequests RepairRequest[] */
            $repairRequests = RepairRequest::find()
                ->with(['equipment', 'location'])
                ->where([
                    'AND',
                    ['status' => RepairRequest::STATUS_COMPLETED],
                    ['>=', 'scheduled_at', $from],
                    ['<=', 'scheduled_at', $to],
                ])
                ->all();

            /* @var $maintenances Maintenance[] */
            $maintenances = Maintenance::find()
                ->with(['equipment', 'location', 'report'])
                ->where([
                    'AND',
                    ['status' => Maintenance::STATUS_COMPLETE],
                    ['atl_status' => Maintenance::STATUS_COMPLETE],
                    ['>=', 'completed_at', $from],
                    ['<=', 'completed_at', $to],
                ])
                ->all();

            if (!empty($repairRequests) || !empty($maintenances)) {
                $zip = new ZipArchive();
                $zipfilename = 'export-' . time() . '.zip';
                $zipPath = Yii::getAlias("@static/upload/zips");
                if (!file_exists($zipPath)) {
                    if (!mkdir($zipPath, 0755, true) && !is_dir($zipPath)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $zipPath));
                    }
                }
                $zipFilePath = "{$zipPath}/{$zipfilename}";
                if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
                    throw new \RuntimeException("cannot open <$zipFilePath>\n");
                }
                foreach ($repairRequests as $index => $repairRequest) {
                    $path = Yii::getAlias("@static/upload/reports/{$repairRequest->random_token}.tif");
                    if (file_exists($path)) {
                        //Yii::$app->session->addFlash("success", "Adding {$repairRequest->id}");

                        $atlComment = null;
                        if (!empty($repairRequest->atl_note)) {
                            setlocale(LC_ALL, 'en_US.utf8');
                            $atlComment = iconv('utf-8', 'us-ascii//TRANSLIT', substr($repairRequest->atl_note, 0, 100));
                        }
                        $prev = null;
                        if (!empty($repairRequest->related_request_id)) {
                            $prev = "[PREV.{$repairRequest->related_request_id}]";
                        }

                        $subfolder = strtoupper("{$repairRequest->equipment->location->code} - {$repairRequest->equipment->location->name}");
                        if ($repairRequest->service_type == RepairRequest::TYPE_REQUEST) {
                            $filename = "BI-" . date("Ymd", strtotime($repairRequest->scheduled_at)) . "-{$repairRequest->id}";
                            if (!empty($prev)) {
                                $filename .= $prev;
                            }
                            if (!empty($atlComment)) {
                                $filename .= "-" . $atlComment;
                            }
                            $zip->addFile($path, "{$zipfilename}/Projects/{$subfolder}/BREAKDOWNS/{$filename}.tif");
                        } else {
                            $filename = "RS-" . date("Ymd", strtotime($repairRequest->scheduled_at)) . "-{$repairRequest->id}";
                            if (!empty($prev)) {
                                $filename .= $prev;
                            }
                            if (!empty($atlComment)) {
                                $filename .= "-" . $atlComment;
                            }
                            $zip->addFile($path, "{$zipfilename}/Projects/{$subfolder}/WORKS/{$filename}.tif");
                        }
                    }
                }

                foreach ($maintenances as $index => $maintenance) {

                    $path = Yii::getAlias("@static/upload/maintenance_reports/{$maintenance->report->year}/{$maintenance->report->month}/{$maintenance->report->id}_{$maintenance->report->random_token}.tif");
                    if (file_exists($path)) {
                        $atlComment = null;
                        if (!empty($maintenance->atl_note)) {
                            setlocale(LC_ALL, 'en_US.utf8');
                            $atlComment = iconv('utf-8', 'us-ascii//TRANSLIT', substr($maintenance->atl_note, 0, 100));
                        }

                        $subfolder = strtoupper("{$maintenance->equipment->location->code} - {$maintenance->equipment->location->name}");
                        $filename = "MF-" . date("Ymd", strtotime($maintenance->completed_at)) . "-{$maintenance->id}";
                        if (!empty($atlComment)) {
                            $filename .= "-" . $atlComment;
                        }
                        $zip->addFile($path, "{$zipfilename}/Projects/{$subfolder}/MAINTENANCE VISITS/{$filename}.tif");
                    }
                }

                $zip->close();
                if (file_exists($zipFilePath)) {
                    return Yii::$app->response->sendFile($zipFilePath);
                }
            } else {
                Yii::$app->session->addFlash("danger", "No completed jobs found in the selected date range");
            }
        }

        return $this->render("export-reports", [
            'from' => $from,
            'to' => $to
        ]);
    }

    public function actionKpis()
    {

        $dynamicModel = new DynamicModel([
            '_s',
            '_e',
            's_s',
            's_e',
            'sector',
            'technician',
            'p_sector',
            'p_technician',
            'period',
            's_sector',
            's_technician',
        ]);
        $dynamicModel->addRule(['_s', '_e'], 'safe');
        $dynamicModel->addRule(['s_s', 's_e'], 'safe');
        $dynamicModel->addRule(['sector', 'technician'], 'safe');
        $dynamicModel->addRule(['p_sector', 'p_technician'], 'safe');
        $dynamicModel->addRule(['s_sector', 's_technician'], 'safe');

        $dynamicModel->addRule(['_s', 's_s'], 'default', ['value' => date("Y-m-d", strtotime("-30 days"))]);
        $dynamicModel->addRule(['_e', 's_e'], 'default', ['value' => date("Y-m-d")]);


        $dynamicModel->load(Yii::$app->request->get());
        if (empty($dynamicModel->_s)) {
            $dynamicModel->_s = date("Y-m-d", strtotime("-30 days"));
        }
        if (empty($dynamicModel->_e)) {
            $dynamicModel->_e = date("Y-m-d");
        }
        if (empty($dynamicModel->s_s)) {
            $dynamicModel->s_s = date("Y-m-d", strtotime("-30 days"));
        }
        if (empty($dynamicModel->s_e)) {
            $dynamicModel->s_e = date("Y-m-d");
        }

        if (empty($dynamicModel->sector)) {
            $dynamicModel->sector = Admin::activeSectorsIds();
        }
        if (empty($dynamicModel->p_sector)) {
            $dynamicModel->p_sector = Admin::activeSectorsIds();
        }
        if (empty($dynamicModel->s_sector)) {
            $dynamicModel->s_sector = Admin::activeSectorsIds();
        }
        $repairRequestsQuery = RepairRequest::find()
            ->joinWith(['equipment', 'location', 'location.sector'])
            ->with(['relatedRequests'])
            ->where([
                'AND',
                [RepairRequest::tableName() . '.service_type' => RepairRequest::TYPE_REQUEST],
                [RepairRequest::tableName() . '.status' => RepairRequest::STATUS_COMPLETED],
                ['>=', RepairRequest::tableName() . '.departed_at', $dynamicModel->_s . ' 00:00:00'],
                ['<=', RepairRequest::tableName() . '.departed_at', $dynamicModel->_e . ' 23:59:59'],
                ['related_request_id' => null]
            ])
            ->andFilterWhere([RepairRequest::tableName() . '.technician_id' => $dynamicModel->technician])
            ->andFilterWhere([Sector::tableName() . '.id' => $dynamicModel->sector]);
        Yii::error($repairRequestsQuery->createCommand()->rawSql);
        $repairRequests = $repairRequestsQuery->all();

        return $this->render("kpis", [
            'dynamicModel' => $dynamicModel,
            'models' => $repairRequests,
            'repairRequestsQuery' => $repairRequestsQuery,
        ]);
    }

    public function actionQrKpis()
    {
        return $this->render("qr-kpis");
    }
    public function actionGeneralProductivityKpis()
    {
        return $this->render("general-productivity-kpis");
    }




    public function actionDynamicPage($parent_id, $parent_class, $child_class_name)
    {
    }
}
