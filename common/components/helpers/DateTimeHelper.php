<?php


namespace common\components\helpers;


use Yii;

class DateTimeHelper
{

    /**
     * Get human readable time difference between 2 dates
     *
     * Return difference between 2 dates in year, month, hour, minute or second
     * The $precision caps the number of time units used: for instance if
     * $time1 - $time2 = 3 days, 4 hours, 12 minutes, 5 seconds
     * - with precision = 1 : 3 days
     * - with precision = 2 : 3 days, 4 hours
     * - with precision = 3 : 3 days, 4 hours, 12 minutes
     *
     * From: http://www.if-not-true-then-false.com/2010/php-calculate-real-differences-between-two-dates-or-timestamps/
     *
     * @param mixed $time1 a time (string or timestamp)
     * @param mixed $time2 a time (string or timestamp)
     * @param integer $precision Optional precision
     * @return string time difference
     */
    public static function get_date_diff($time1, $time2, $precision = 2, $separator = ", ", $short = false)
    {
        // If not numeric then convert timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        // If time1 > time2 then swap the 2 values
        if ($time1 > $time2) {
            return "Soon";
            list($time1, $time2) = array($time2, $time1);
        }
        // Set up intervals and diffs arrays
        $intervals = array('year', 'month', 'day', 'hour', 'minute');//, 'second');

        $diffs = array();
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }
            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }
        $count = 0;
        $times = array();
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval if value is bigger than 0
            if ($value > 0) {
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }
        // Return string with times
        return implode($separator, $times);
    }


    public static function ETA($time1, $time2)
    {
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        $seconds = $time2 - $time1;
        $days = (int)($seconds / 3600 / 24);
        $hours = (int)(($seconds - $days * 24 * 3600) / 3600);
        $minutes = (int)(($seconds - $days * 24 * 3600 - $hours * 3600) / 60);
        if ($seconds < 0) {
            return "Soon";
        }

        $message = '';
        if ($days > 0) {
            $message .= Yii::t("app", "{days,plural,=0{} one{1 Day} other{# Days}} ", ['days' => round($days)]);
            return $message;
        }

        $msgHrs = substr("0{$hours}", -2);
        $msgMns = substr("0{$minutes}", -2);
        return "{$msgHrs}:{$msgMns}:00";

        if ($hours > 0) {
            $message .= Yii::t("app", "{hours,plural,=0{} one{1 Hr} other{# Hrs}} ", ['hours' => round($hours)]);
        }
        if ($minutes > 0) {
            if ($days == 0) {
                if ($hours == 0) {
                    if ($minutes >= 5) {
                        $message .= Yii::t("app", "{minutes} Mns", ['minutes' => round($minutes)]);
                    } else {
                        $message = "Soon";
                    }
                } else {
                    $message .= Yii::t("app", "{minutes,plural,=0{} one{1 Min} other{# Mns}} ", ['minutes' => round($minutes)]);
                }
            }
        }

        return $message;

        $minutes = ($time2 - $time1) / 60;

        $hours = $minutes / 60;

        $days = $hours / 24;

        if ($days > 1) {
            return Yii::t("app", "{days,plural,=0{Soon} one{1 Day} other{# Days}}", ['days' => round($days)]);
        }

        if ($hours > 1) {
            return Yii::t("app", "{hours,plural,=0{Soon} one{1 Hour} other{# Hours}}", [
                'hours' => round($hours)
            ]);
            return round($hours) . " Hours";
        }

        if ($minutes > 10) {
            return round($minutes) . " Minutes";
        }

        return "Soon";
    }


    public static function daysDiff($time2, $positivePrefix = '', $negativePrefix = "", $zeroPrefix = '')
    {
        $time1 = time();
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        $days = floor(($time2 - $time1) / (24 * 60 * 60));

        if ($days == 0) {
            return $zeroPrefix . Yii::t("app", "today");
        }

        if ($days == 1) {
            return $positivePrefix . Yii::t("app", "tomorrow");
        }


        if ($days > 1) {
            return $positivePrefix . Yii::t("app", "in {days} days", ['days' => $days]);
        }


        if ($days == -1) {
            return $negativePrefix . Yii::t("app", "yesterday");
        }


        if ($days < -1) {
            return $negativePrefix . Yii::t("app", "{days} days ago", ['days' => abs($days)]);
        }

    }

    public static function minutesToHoursMinutes($minutes)
    {
        $hours = (int)($minutes / 60);
        $minutes = substr("00".($minutes % 60), -2);
        return "{$hours}:$minutes";
    }
}