<?php

namespace admin\controllers;

use common\config\includes\P;
use common\data\Countries;
use common\models\Account;
use common\models\Admin;
use common\models\LoginAudit;
use common\models\users\forms\AbstractLoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\httpclient\Client;
use \Smalot\PdfParser\Parser;

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

        if ($this->request->isPost) {
            $post = Yii::$app->request->post();

            $userQuestion = $post['user_question'];
            $pdfText = $post['pdf_text'];

            // Divide PDF text into chunks
            $chunkSize = 1000; // Adjust the chunk size as needed
            $pdfChunks = str_split($pdfText, $chunkSize);

            // Make a request to the Flask API
            $client = new Client();

            // Loop through each chunk and send it to the Python script
            foreach ($pdfChunks as $chunk) {
                $response = $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl('http://127.0.0.1:5000/')
                    ->addHeaders(['Content-Type' => 'application/json'])  // Set the content type to JSON
                    ->setContent(json_encode([
                        'pdf_text' => $chunk,
                        'user_question' => $userQuestion,
                    ]))
                    ->send();

                if ($response->isOk) {
                    $data = $response->getData();
                    $answer = $data['answer'];

                    print_r($answer);
                    exit;
                    // Process the answer as needed
                    // ...
                }
            }

        }
        return $this->render('pdf-gpt', [
            'pdf_path' => $pdf_path
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
}
