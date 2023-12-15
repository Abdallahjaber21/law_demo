<?php

namespace common\models;

use DateTime;

/**
 * Description of Metrics
 *
 * @author Tarek K. Ajaj
 */
class Metrics
{

    //
    CONST TYPE_ADMIN = "Admin";
    //
    CONST AMOUNT_FUNDS_ADDED = "amount-fund-added";
    CONST AMOUNT_FUNDS_ADDED_FEES = "amount-fund-added-fees";
    CONST AMOUNT_FUNDS_ADDED_FAILED = "amount-fund-added-failed";
    CONST AMOUNT_FUNDS_ADDED_SUCCESS = "amount-fund-added-success";
    CONST AMOUNT_TOTAL_FEES = "amount-total-fees";
    CONST AMOUNT_FUND_REQUEST = "amount-fund-request";
    CONST AMOUNT_LOAN_GIVEN = "amount-loan-given";
    CONST AMOUNT_LOAN_INSTALLMENTS_PAID = "amount-loan-installments-paid";
    CONST AMOUNT_TRASNACTIONS = "amount-transactions";
    CONST AMOUNT_TRASNACTIONS_ACCEPTED = "amount-transactions-accepted";
    CONST AMOUNT_TRASNACTIONS_REJECTED = "amount-transactions-rejected";
    CONST AMOUNT_TRASNACTIONS_TIMEDOUT = "amount-transactions-timedout";
    CONST AMOUNT_TRASNACTIONS_FEE = "amount-transactions-fee";
    CONST AMOUNT_FUNDS_WITHDRAWN = "amount-fund-withdrawn";
    CONST AMOUNT_FUNDS_WITHDRAWN_FEES = "amount-fund-withdrawn-fees";
    CONST AMOUNT_FUNDS_WITHDRAWN_FAILED = "amount-fund-withdrawn-failed";
    CONST AMOUNT_FUNDS_WITHDRAWN_SUCCESS = "amount-fund-withdrawn-success";
    //
    CONST NUMBER_FUNDS_ADDED = "number-fund-added";
    CONST NUMBER_FUNDS_ADDED_FAILED = "number-fund-added-failed";
    CONST NUMBER_FUNDS_ADDED_SUCCESS = "number-fund-added-success";
    CONST NUMBER_FUND_REQUEST = "number-fund-request";
    CONST NUMBER_FUND_REQUEST_ACCEPTED = "number-fund-request-accepted";
    CONST NUMBER_FUND_REQUEST_REJECTED = "number-fund-request-rejected";
    CONST NUMBER_LOAN_REQUEST_RECEIVED = "number-loan-request-received";
    CONST NUMBER_LOAN_REQUEST_REJECTED = "number-loan-request-rejected";
    CONST NUMBER_LOAN_REQUEST_APPROVED = "number-loan-request-approved";
    CONST NUMBER_LOAN_COMPLETED = "number-loan-completed";
    CONST NUMBER_LOAN_INSTALLMENTS = "number-loan-installments";
    CONST NUMBER_LOAN_INSTALLMENTS_PAID = "number-loan-installments-paid";
    CONST NUMBER_TRASNACTIONS = "number-transactions";
    CONST NUMBER_TRASNACTIONS_ACCEPTED = "number-transactions-accepted";
    CONST NUMBER_TRASNACTIONS_REJECTED = "number-transactions-rejected";
    CONST NUMBER_TRASNACTIONS_TIMEDOUT = "number-transactions-timedout";
    CONST NUMBER_FUNDS_WITHDRAWN = "number-fund-withdrawn";
    CONST NUMBER_FUNDS_WITHDRAWN_FAILED = "number-fund-withdrawn-failed";
    CONST NUMBER_FUNDS_WITHDRAWN_SUCCESS = "number-fund-withdrawn-success";
    //
    CONST NUMBER_CONTACTS_INVITED = "number-contacts-invited";
    CONST NUMBER_BANKACCOUNTS_ADDED = "number-bankaccounts-added";
    CONST NUMBER_CREDITCARDS_ADDED = "number-creditcards-added";
    CONST NUMBER_ACTIVATE_TFA = "number-activate-tfa";
    CONST NUMBER_RESEND_TFA = "number-resend-tfa";
    CONST NUMBER_CANCEL_TFA = "number-cancel-tfa";
    CONST NUMBER_SUCCESS_TFA = "number-success-tfa";
    CONST NUMBER_DEACTIVATE_TFA = "number-deactivate-tfa";
    CONST NUMBER_UPDATE_PINCODE = "number-update-pincode";
    CONST NUMBER_SET_PINCODE = "number-set-pincode";
    CONST NUMBER_REQUEST_PINCODE = "number-request-pincode";
    //
    CONST NUMBER_USERS_REGISTERED = "number-users-registered";
    CONST NUMBER_USERS_REGISTERED_WEB = "number-users-registered-web";
    CONST NUMBER_USERS_REGISTERED_MOBILE = "number-users-registered-mobile";
    CONST NUMBER_USERS_FORGOT_PASSWORD = "number-users-forgot-password";
    CONST NUMBER_USERS_SUCCESS_TFA = "number-users-success-tfa";
    //

    public static function getPreviousPeriod($from, $to)
    {
        $start = new DateTime($from);
        $end = new DateTime($to);
        $diff = $end->diff($start)->format("%a");
        $diffP = $diff + 1;
        $prevFrom = date("Y-m-d", strtotime("{$from} - {$diffP} days"));
        $prevTo = date("Y-m-d", strtotime("{$from} - 1 day"));
        return [$prevFrom, $prevTo, $diff];
    }

    public static function vsPreviousPeriod($now, $prev)
    {
        $prercentageDiff = abs(round($now * 100.0 / ($prev > 0 ? $prev : 1) - ($prev > 0 ? 100.0 : 0), 2));

        if ($prev == 0) {
            if ($now == 0) {
                $prercentageDiff = 0;
            } else {
                $prercentageDiff = 100;
            }
        }

        $sign = $now >= $prev ? "+" : "-";
        return \Yii::t("app", "{$sign}{$prercentageDiff}% vs previous period");
    }

    public static function vsPreviousPeriodPercentage($now, $prev)
    {
        $prercentageDiff = min([round($now * 100.0 / ($prev > 0 ? $prev : 1), 2), 100]);
        return $prercentageDiff;
    }

}
