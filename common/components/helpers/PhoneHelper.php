<?php

namespace common\components\helpers;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneHelper
{

    public static function formatToInternational($phone = null, $onlyDigits = false){
        if(!empty($phone)){
            $phone = preg_replace("/[^0-9]/", "", $phone);
            if (strlen($phone) == 7) {
                $phone = "0{$phone}";
            }
            $phoneUtil = PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($phone, 'LB');
            if (!empty($number)) {
                if ($phoneUtil->isValidNumber($number)) {
                    $phone = $phoneUtil->format($number, PhoneNumberFormat::INTERNATIONAL);
                }
            }
        }
        if($onlyDigits){
            $phone = preg_replace("/[^0-9]/", "", $phone);
        }
        return $phone;
    }
}
