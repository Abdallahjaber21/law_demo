<?php

namespace common\components\payment;

use yii\base\Component;
use yii\helpers\Json;
use yii\web\ServerErrorHttpException;

/**
 * Description of Payment
 *
 * @author Tarek K. Ajaj
 */
class Payment extends Component {

  public $merchantId;
  public $userId;
  public $password;
  public $baseUrl;
  public $checkoutUrl;

  public function sendRequest($url, $type = "GET", $data = []) {
    $curl = curl_init();
    $curlData = [
        CURLOPT_URL => "{$this->baseUrl}/{$this->merchantId}/{$url}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $type,
        CURLOPT_USERPWD => $this->userId . ":" . $this->password,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
        ],
    ];
    if (!empty($data)) {
      $curlData[CURLOPT_POSTFIELDS] = Json::encode($data);
    }
    curl_setopt_array($curl, $curlData);

    $response = curl_exec($curl);
    $err = curl_error($curl);



    \Yii::info("{$this->baseUrl}/{$this->merchantId}/{$url}", "Payment");
    \Yii::info(curl_getinfo($curl), "Payment");
    \Yii::info($data, "Payment");
    \Yii::info(Json::decode($response), "Payment");
    
    curl_close($curl);

    if ($err) {
      \Yii::info($err);
      throw new ServerErrorHttpException($err);
    } else {
      return $response;
    }
  }

}
