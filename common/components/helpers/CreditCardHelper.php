<?php


namespace common\components\helpers;


class CreditCardHelper
{
    private function isVisa($card)
    {
        return preg_match("/^4[0-9]{0,15}$/i", $card);
    }

    private function isMasterCard($card)
    {
        return preg_match("/^(5[1-5][0-9]{14}|2(22[1-9][0-9]{12}|2[3-9][0-9]{13}|[3-6][0-9]{14}|7[0-1][0-9]{13}|720[0-9]{12}))$/i", $card);
    }

    private function isAmex($card)
    {
        return preg_match("/^3$|^3[47][0-9]{0,13}$/i", $card);
    }

    private function isDiscover($card)
    {
        return preg_match("/^6$|^6[05]$|^601[1]?$|^65[0-9][0-9]?$|^6(?:011|5[0-9]{2})[0-9]{0,12}$/i", $card);
    }

    private function isJCB($card)
    {
        return preg_match("/^(?:2131|1800|35[0-9]{3})[0-9]{3,}$/i", $card);
    }

    private function isDinersClub($card)
    {
        return preg_match("/^3(?:0[0-5]|[68][0-9])[0-9]{4,}$/i", $card);
    }

    public function detect($card)
    {
        $cardTypes = [
            'Visa',
            //'Amex',
            'MasterCard',
            //'Discover',
            //'JCB',
            //'DinersClub'
        ];
        foreach ($cardTypes as $cardType) {
            $method = 'is' . $cardType;
            if ($this->$method($card)) {
                return $cardType;
            }
        }
        return false;
    }

    public static function detectCardType($card){
        return (new CreditCardHelper())->detect($card);
    }
}