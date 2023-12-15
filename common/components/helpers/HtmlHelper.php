<?php

namespace common\components\helpers;

/**
 * Description of HtmlHelper
 *
 * @author Tarek K. Ajaj
 */
class HtmlHelper {

  public static function HtmlTableFromJson($data) {
    $html = "<table class='table table-striped table-bordered table-hover table-condensed'>";
    $html .= "<tbody>";
    foreach ($data as $key => $value) {
      $html .= "<tr>";
      $html .= "<th>";
      $html .= \yii\helpers\Inflector::camel2words($key);
      $html .= "</th>";
      $html .= "<td>";
      if (is_array($value)) {
        $html .= HtmlHelper::HtmlTableFromJson($value);
      } else {
        $html .= $value;
      }
      $html .= "</td>";
      $html .= "</tr>";
    }
    $html .= "</table>";
    $html .= "</tbody>";
    return $html;
  }

}
