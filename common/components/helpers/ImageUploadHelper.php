<?php

namespace common\components\helpers;

use common\behaviors\ImageUploadBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * Description of ImageUploadHelper
 *
 * @author Tarek K. Ajaj
 */
class ImageUploadHelper
{

  /**
   * this function sets an image to a model from a base64 string
   * 
   * @param string $base64image
   * @param ActiveRecord $model
   * @param string $behaviorName
   * @return type
   */
  public static function uploadBase64Image($base64image, $model, $behaviorName = "image")
  {

    /* @var $behavior ImageUploadBehavior */
    $behavior = $model->getBehavior($behaviorName);
    if (!($behavior instanceof ImageUploadBehavior)) {
      return false;
    }

    $behavior->cleanFiles();
    $resolvePath = $behavior->resolvePath($behavior->filePath);
    $model->{$behavior->attribute} = rand(100000, 999999) . '_' . basename($resolvePath) . '.jpg';
    $resolvePath = $behavior->resolvePath($behavior->filePath);

    if (!file_exists(dirname($resolvePath))) {
      mkdir(dirname($resolvePath), 0777, true);
    }


    $data = explode(",", $base64image);
    $decoded = base64_decode($data[1]);
    if (file_put_contents($resolvePath, $decoded) !== FALSE) {
      $behavior->createThumbs();
      return $model->save();
    }
    return false;
  }
}
