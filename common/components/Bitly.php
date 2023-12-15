<?php

namespace common\components;

use Exception;
use yii\base\Component;
use yii\helpers\Json;

class Bitly extends Component
{

    public $token;
    public $domain;
    public $group_guid;

    public function init()
    {
        parent::init();
    }

    public function shortenLink($link)
    {
        $post = [
            "long_url"   => $link,
            "domain"     => $this->domain,
            "group_guid" => $this->group_guid,
        ];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => 'https://api-ssl.bitly.com/v4/shorten',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => Json::encode($post),
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ),
        ));
        $response = curl_exec($curl);
        \Yii::info($response);
        if (curl_errno($curl)) {
            $err = curl_error($curl);
            throw new Exception($err);
        }
        curl_close($curl);
        $json = Json::decode($response);
        if($json['link']){
            return $json['link'];
        }
        return null;
    }
}