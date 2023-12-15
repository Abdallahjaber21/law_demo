<?php

namespace admin\controllers;

use common\config\includes\P;
use Yii;
use yii\caching\CacheInterface;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class DeveloperController extends Controller
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
            'actions' => ['clear-cache'],
            'allow' => P::c(P::DEVELOPMENT_CLEAR_CACHE_PAGE_VIEW),
            //'roles' => ['developer'],
          ],
        ],
      ],
    ];
  }

  public function actionClearCache()
  {
    /* @var $cache CacheInterface */
    if (P::c(P::DEVELOPMENT_CLEAR_CACHE_PAGE_VIEW)) {
      $cache = Yii::$app->cache;
      if ($cache->flush()) {
        Yii::$app->getSession()->addFlash("info", Yii::t("app", "Cache cleared successfully"));
      } else {
        Yii::$app->getSession()->addFlash("danger", Yii::t("app", "A problem occured while clearing the cache"));
      }

      return $this->goHome();
    } else {
      throw new NotFoundHttpException('The requested page does not exist.');
    }
  }
}
