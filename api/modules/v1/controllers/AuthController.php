<?php

namespace api\modules\v1\controllers;

use common\components\exceptions\FailedToLoadDataException;
use common\components\extensions\api\ApiController;
use common\models\Employee;
use common\models\LocationCode;
use common\models\Metric;
use common\models\Metrics;
use common\models\RouteAssignment;
use common\models\Truck;
use common\models\User;
use common\models\UserLocation;
use common\models\users\AbstractAccount;
use common\models\users\forms\AbstractForgetPasswordForm;
use common\models\users\forms\AbstractLoginForm;
use common\models\users\forms\AbstractPasswordCodeForm;
use common\models\users\forms\AbstractRegisterForm;
use common\models\users\forms\AbstractResetPasswordForm;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * Auth controller
 */
class AuthController extends ApiController
{

    public $serializer = [
        'class' => 'yii\rest\Serializer',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return $behaviors;
    }


    /**
     * Register
     */
    public function actionRegister()
    {
        $this->isPost();
        $model = new AbstractRegisterForm([
            'UserClass'       => User::className(),
            'UserClassFilter' => [],
        ]);

        $validLocationCode = false;
        $location_code = Yii::$app->request->post('location_code');
        if (!empty($location_code)) {
            $locationCode = LocationCode::find()
                ->where([
                    'AND',
                    ['code' => $location_code],
                    ['status' => LocationCode::STATUS_ENABLED]
                ])
                ->one();
            if (!empty($locationCode)) {
                if ($locationCode->usages_limit > $locationCode->usages_count) {
                    $validLocationCode = true;
                }
            }
        }
        if (!$validLocationCode) {
            throw new BadRequestHttpException(
                'Invalid location code'
            );
        }
        if ($model->load(Yii::$app->request->post(), '') && $model->register()) {
            $user = $model->getUser();

            $floor_number = Yii::$app->request->post('floor_number');
            $birthdate = Yii::$app->request->post('birthdate');
            $job_category = Yii::$app->request->post('job_category');
            $company_name = Yii::$app->request->post('company_name');
            $job_title = Yii::$app->request->post('job_title');

            $user->floor_number = $floor_number;
            $user->birthdate = $birthdate;
            $user->job_category = $job_category;
            $user->company_name = $company_name;
            $user->job_title = $job_title;

            if ($validLocationCode) {
                $userLocation = new UserLocation();

                if ($locationCode->type == LocationCode::TYPE_DECISION_MAKER) {
                    $userLocation->role = UserLocation::ROLE_DECISION_MAKER;
                }
                if ($locationCode->type == LocationCode::TYPE_RESIDENT) {
                    $userLocation->role = UserLocation::ROLE_RESIDENT;
                }

                $userLocation->location_id = $locationCode->location_id;
                $userLocation->user_id = $user->id;
                $userLocation->status = UserLocation::STATUS_ENABLED;
                $userLocation->save();

                $locationCode->usages_count += 1;
                $locationCode->save(false);
            }

            $user->generateAccessToken();
            $user->save(false);
            AbstractAccount::$return_fields = AbstractAccount::FIELDS_MINIMUM;
            Metric::addTo(Metrics::TYPE_ADMIN, null, Metrics::NUMBER_USERS_REGISTERED, 1);
            Metric::addTo(Metrics::TYPE_ADMIN, null, Metrics::NUMBER_USERS_REGISTERED_MOBILE, 1);
            return $user;
        } else {
            $user = $model->getUser();
            if (!empty($user)) {
                $errors = $user->getErrorSummary(true);
                throw new BadRequestHttpException(implode("<br/>", $errors));
            }
            $errors = $model->getErrorSummary(true);
            if (!empty($errors)) {
                throw new BadRequestHttpException(implode("<br/>", $errors));
            } else {
                throw new FailedToLoadDataException();
            }
        }
    }

    /**
     * Login
     */
    public function actionLogin()
    {
        $this->isPost();
        $model = new AbstractLoginForm([
            'UserClass'       => User::className(),
            'UserClassFilter' => [],
        ]);
        if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
            /* @var $user User */
            $user = $model->getUser();
            $user->generateAccessToken();
            $user->save(false);
            AbstractAccount::$return_fields = AbstractAccount::FIELDS_MINIMUM;
            return $user;
        } else {
            $firstErrors = $model->getFirstErrors();
            if (!empty($firstErrors)) {
                throw new BadRequestHttpException(array_values($firstErrors)[0]);
            } else {
                throw new FailedToLoadDataException();
            }
        }
    }


    /**
     * Forgot password.
     */
    public function actionForgotPassword()
    {
        $this->isPost();
        $model = new AbstractForgetPasswordForm([
            'UserClass'       => User::className(),
            'useCode'         => true,
            'UserClassFilter' => [],
        ]);
        if ($model->load(Yii::$app->request->post(), '') && $model->forgetPassword()) {
            Metric::addTo(Metrics::TYPE_ADMIN, null, Metrics::NUMBER_USERS_FORGOT_PASSWORD, 1);
            return [
                'alerts' => [
                    \Yii::t("app", "Please check your email for instructions to reset your password.")
                ]
            ];
        } else {
            $firstErrors = $model->getFirstErrors();
            if (!empty($firstErrors)) {
                throw new BadRequestHttpException(array_values($firstErrors)[0]);
            } else {
                throw new FailedToLoadDataException();
            }
        }
    }

    /**
     * Forgot password.
     */
    public function actionVerifyPasswordCode()
    {
        $this->isPost();
        $model = new AbstractPasswordCodeForm([
            'UserClass'       => User::className(),
            'UserClassFilter' => [],
        ]);
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            return [
                'success' => true
            ];
        } else {
            $firstErrors = $model->getFirstErrors();
            if (!empty($firstErrors)) {
                throw new BadRequestHttpException(array_values($firstErrors)[0]);
            } else {
                throw new FailedToLoadDataException();
            }
        }
    }

    /**
     * Resend password reset password.
     */
    public function actionResendPasswordCode()
    {
        $this->isPost();
        $email = \Yii::$app->getRequest()->post("email");
        $user = User::findByEmail($email);
        if (!empty($user)) {
            if (!empty($user->password_reset_token)) {
                Yii::$app->mailer->compose('password-reset-code', [
                    'link' => $user->password_reset_token
                ])
                    ->setFrom(\Yii::$app->params['passwordResetEmail'])
                    ->setTo($user->email)
                    ->setSubject(\Yii::$app->params['project-name'] . ' - ' . \Yii::t("app", 'Password Reset'))
                    ->send();
                return [
                    'success' => true
                ];
            }
        }
        throw new BadRequestHttpException(\Yii::t("app", "Invalid request"));
    }

    /**
     * Reset password.
     *
     * @return string
     */
    public function actionResetPassword()
    {
        $this->isPost();
        $model = new AbstractResetPasswordForm([
            'UserClass'       => User::className(),
            'UserClassFilter' => [],
        ]);
        if ($model->load(Yii::$app->request->post(), '') && $model->resetPassword()) {
            return [
                'alerts' => [
                    \Yii::t("app", "Password successfully reset")
                ]
            ];
        } else {
            $firstErrors = $model->getFirstErrors();
            if (!empty($firstErrors)) {
                throw new BadRequestHttpException(array_values($firstErrors)[0]);
            } else {
                throw new FailedToLoadDataException();
            }
        }
    }
}
