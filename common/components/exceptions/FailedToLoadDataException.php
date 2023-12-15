<?php

namespace common\components\exceptions;

use Exception;
use Yii;
use yii\web\HttpException;

/**
 * Description of FailedToLoadDataException
 *
 * @author Tarek K. Ajaj
 */
class FailedToLoadDataException extends HttpException {

  public function __construct($code = 0, Exception $previous = null) {
    parent::__construct(400, Yii::t("app", "Failed to load data"), $code, $previous);
  }

}
