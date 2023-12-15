<?php

namespace common\components\exceptions;

use yii\web\BadRequestHttpException;

/**
 * Description of ApiModelException
 *
 * @author Tarek K. Ajaj
 */
class ApiModelException extends BadRequestHttpException {

  public function __construct($model, $message = null, $code = 0, \Exception $previous = null) {
    $classname = get_class($model);
    $pos = strrpos($classname, '\\');
    $firstErrors = $model->getFirstErrors();
    if (!empty($firstErrors)) {
      $msg = array_values($firstErrors)[0];
      return parent::__construct($msg, $code, $previous);
    }
    return parent::__construct($message, $code, $previous);
  }

}
