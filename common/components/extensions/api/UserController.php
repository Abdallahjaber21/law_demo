<?php

namespace common\components\extensions\api;

use common\components\exceptions\ApiModelException;
use common\components\helpers\ImageUploadHelper;
use common\models\users\AbstractAccount;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Description of UserController
 *
 * @author Tarek K. Ajaj
 */
class UserController extends ApiController
{

  public function actionEdit()
  {

    $this->isPost();
    $model = Yii::$app->getUser()->getIdentity();

    if ($model->load(Yii::$app->getRequest()->post(), '') && $model->save()) {
      AbstractAccount::$return_fields = AbstractAccount::FIELDS_MINIMUM;
      return $model;
    }
    throw new ApiModelException($model, \Yii::t("app", "Unknown error has occured"));
  }

  public function actionProfilePicture()
  {
    $this->isPost();

    $model = Yii::$app->getUser()->getIdentity();
    $base64image = Yii::$app->getRequest()->post("image");

    if (ImageUploadHelper::uploadBase64Image($base64image, $model)) {
      AbstractAccount::$return_fields = AbstractAccount::FIELDS_MINIMUM;
      $model->refresh();
      return $model;
    }

    throw new ApiModelException($model, \Yii::t("app", "Unknown error has occured"));
  }

  public function actionPassword()
  {
    $this->isPost();

    /* @var $model AbstractAccount */
    $model = Yii::$app->getUser()->getIdentity();
    if ($model->validatePassword(Yii::$app->getRequest()->post("current_password"))) {
      if ($model->load(Yii::$app->getRequest()->post(), '') && $model->save()) {
        AbstractAccount::$return_fields = AbstractAccount::FIELDS_MINIMUM;
        return $model;
      }
      throw new ApiModelException($model, \Yii::t("app", "Unknown error has occured"));
    }
    throw new ForbiddenHttpException(\Yii::t("app", "Current password is incorrect"));
  }

  public function actionProfile()
  {
    $this->isGet();
    /* @var $user AbstractAccount */
    $user = Yii::$app->getUser()->getIdentity();
    AbstractAccount::$return_fields = AbstractAccount::FIELDS_MINIMUM;
    return $user;
  }

  public function actionRegid()
  {
    $this->isPost();
    /* @var $user AbstractAccount */
    $user = Yii::$app->getUser()->getIdentity();
    $user->mobile_registration_id = Yii::$app->request->post("regid");
    $user->platform = Yii::$app->request->post("platform");
    $user->save(false);
    return ['success' => true];
  }

  public function actionLogout()
  {
    $this->isPost();
    /* @var $user AbstractAccount */
    $user = Yii::$app->getUser()->getIdentity();
    $user->access_token = null;
    $user->mobile_registration_id = null;
    $user->save(false);
    return ['success' => true];
  }

  public function actionNotification()
  {
    $this->isPost();
    /* @var $user AbstractAccount */
    $user = Yii::$app->getUser()->getIdentity();
    $user->enable_notification = Yii::$app->request->post("notification");
    $user->save(false);
    return ['success' => true];
  }

  public function actionLanguage()
  {
    $this->isPost();
    /* @var $user AbstractAccount */
    $user = Yii::$app->getUser()->getIdentity();
    if (
      array_key_exists(Yii::$app->request->post("language"), Yii::$app->params['languages']) || in_array(Yii::$app->request->post("language"), Yii::$app->params['languageRedirects'])
    ) {
      $user->language = Yii::$app->request->post("language");
      $user->save(false);
      return ['success' => true];
    }
    return ['success' => Yii::$app->params['languages']];
  }
}
