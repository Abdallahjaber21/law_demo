<?php

namespace common\components\extensions;

/**
 * Description of MultilingualBootstrapInterface
 *
 * @author Tarek K. Ajaj
 */
class MultilingualBootstrapInterface implements \yii\base\BootstrapInterface {

  public function bootstrap($app) {
     if ($language = $app->session->get('language')) {
      $app->language = $language;
    } elseif (isset($app->request->cookies['language'])) {
      $language = $app->request->cookies['language']->value;
      $app->language = $language;
    }
  }

}
