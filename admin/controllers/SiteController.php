<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

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
                        'actions' => ['index', 'profile', 'save-signature', 'delete-signature', 'upload-pdf', 'pdf-gpt'],
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
        return $this->render('index');
    }

    public function actionPdfGpt($pdf_path)
    {
        return $this->render('pdf-gpt', [
            'path' => $pdf_path
        ]);
    }

    public function actionUploadPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $uploadedFile = UploadedFile::getInstanceByName('file');

            if ($uploadedFile !== null) {
                $uploadPath = Yii::getAlias('@static/upload/pdf/');
                $filename = uniqid('pdf_') . '.' . $uploadedFile->getExtension();
                $filePath = $uploadPath . $filename;

                if ($uploadedFile->saveAs($filePath)) {
                    // Return a response, for example, a JSON response
                    return json_encode(['success' => true, 'filepath' => $filePath]);
                } else {
                    // File upload failed
                    return json_encode(['success' => false, 'error' => 'Failed to upload file.']);
                }
            } else {
                // No file uploaded
                return json_encode(['success' => false, 'error' => 'No file uploaded.']);
            }
        }

        // If not a POST request, return an error response
        return json_encode(['success' => false, 'error' => 'Invalid request.']);
    }

}
