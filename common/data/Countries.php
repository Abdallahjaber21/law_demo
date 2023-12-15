<?php

namespace common\data;

use common\exceptions\InvalidCountryCodeException;
use common\exceptions\InvalidCurrencyCodeException;
use yii\helpers\ArrayHelper;

/**
 * Description of Countries
 *
 * @author Tarek K. Ajaj
 * Apr 8, 2017 10:31:49 AM
 * 
 * Countries.php
 * UTF-8
 * 
 */
class Countries {

    /**
     * return the full list of countries
     * 
     * @return array
     */
    public static function getCountriesList() {
        return ArrayHelper::getColumn(self::FULL_LIST, 'name', true);
    }

    /**
     * return the full list of timezones
     * 
     * @return array
     */
    public static function getTimeZonesList() {
        $list = ArrayHelper::map(self::FULL_LIST, 'timezone', 'timezone')+["UTC"=>"UTC"];
        asort($list);
        return $list;
    }

    /**
     * return the full list of currencies
     * 
     * @return array
     */
    public static function getCurrenciesList() {
        $list = ArrayHelper::map(self::FULL_LIST, 'currency', function($row) {
                    return "{$row['currency_name']} ({$row['currency']})";
                });
        asort($list);
        return $list;
    }

    /**
     * get the full name of a country by its code
     * 
     * @param string $country_code
     * @return string
     * @throws InvalidCountryCodeException
     */
    public static function getCountryName($country_code) {
        $list = self::FULL_LIST;
        if (!empty($list[$country_code])) {
            return $list[$country_code]['name'];
        }
        return null;
        //throw new InvalidCountryCodeException("Country code: $country_code does not exist");
    }

    /**
     * return the time zone for a country by it's code
     * if not available return UTC
     * 
     * @param string $country_code
     * @return string
     */
    public static function getTimeZone($country_code) {
        $list = self::$country_code;
        if (!empty($list[$country_code])) {
            return $list[$country_code]['timezone'];
        }
        return 'UTC';
    }

    /**
     * get the full name of a currency using it's currency code
     * 
     * @param string $currency_code
     * @return string
     * @throws InvalidCurrencyCodeException
     */
    public static function getCurrenyName($currency_code) {
        $list = ArrayHelper::map(self::FULL_LIST, 'currency', 'currency_name');
        if (!empty($list[$currency_code])) {
            return $list[$currency_code];
        }
        return null;
        //throw new InvalidCurrencyCodeException("Currency code: $currency_code does not exist");
    }

    /**
     * A complete List of all countries with their ISO 3166-1 Alpha-2 code
     */
    CONST FULL_LIST = [
        'AF' => ['name' => 'Afghanistan', 'currency' => 'AFN', 'currency_name' => 'Afghani', 'language' => 'fa-AF', 'timezone' => 'Asia/Kabul', 'utc_offset' => '+04:30', 'utc_dst_offset' => '+04:30',],
        'AL' => ['name' => 'Albania', 'currency' => 'ALL', 'currency_name' => 'Lek', 'language' => 'sq', 'timezone' => 'Europe/Tirane', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'DZ' => ['name' => 'Algeria', 'currency' => 'DZD', 'currency_name' => 'Algerian Dinar', 'language' => 'ar-DZ', 'timezone' => 'Africa/Algiers', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'AS' => ['name' => 'American Samoa', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'en-AS', 'timezone' => 'Pacific/Pago_Pago', 'utc_offset' => '-11:00', 'utc_dst_offset' => '-11:00',],
        'AD' => ['name' => 'Andorra', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'ca', 'timezone' => 'Europe/Andorra', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'AO' => ['name' => 'Angola', 'currency' => 'AOA', 'currency_name' => 'Kwanza', 'language' => 'pt-AO', 'timezone' => 'Africa/Luanda', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'AI' => ['name' => 'Anguilla', 'currency' => 'XCD', 'currency_name' => 'East Caribbean Dollar', 'language' => 'en-AI', 'timezone' => 'America/Anguilla', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'AG' => ['name' => 'Antigua and Barbuda', 'currency' => 'XCD', 'currency_name' => 'East Caribbean Dollar', 'language' => 'en-AG', 'timezone' => 'America/Antigua', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'AR' => ['name' => 'Argentina', 'currency' => 'ARS', 'currency_name' => 'Argentine Peso', 'language' => 'es-AR', 'timezone' => 'America/Argentina/Ushuaia', 'utc_offset' => '-03:00', 'utc_dst_offset' => '-03:00',],
        'AM' => ['name' => 'Armenia', 'currency' => 'AMD', 'currency_name' => 'Armenian Dram', 'language' => 'hy', 'timezone' => 'Asia/Yerevan', 'utc_offset' => '+04:00', 'utc_dst_offset' => '+04:00',],
        'AW' => ['name' => 'Aruba', 'currency' => 'AWG', 'currency_name' => 'Aruban Florin', 'language' => 'nl-AW', 'timezone' => 'America/Aruba', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'AU' => ['name' => 'Australia', 'currency' => 'AUD', 'currency_name' => 'Australian Dollar', 'language' => 'en-AU', 'timezone' => 'Australia/Sydney', 'utc_offset' => '+10:00', 'utc_dst_offset' => '+11:00',],
        'AT' => ['name' => 'Austria', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'de-AT', 'timezone' => 'Europe/Vienna', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'AZ' => ['name' => 'Azerbaijan', 'currency' => 'AZN', 'currency_name' => 'Azerbaijanian Manat', 'language' => 'az', 'timezone' => 'Asia/Baku', 'utc_offset' => '+04:00', 'utc_dst_offset' => '+04:00',],
        'BS' => ['name' => 'Bahamas', 'currency' => 'BSD', 'currency_name' => 'Bahamian Dollar', 'language' => 'en-BS', 'timezone' => 'America/Nassau', 'utc_offset' => '-05:00', 'utc_dst_offset' => '-04:00',],
        'BH' => ['name' => 'Bahrain', 'currency' => 'BHD', 'currency_name' => 'Bahraini Dinar', 'language' => 'ar-BH', 'timezone' => 'Asia/Bahrain', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'BD' => ['name' => 'Bangladesh', 'currency' => 'BDT', 'currency_name' => 'Taka', 'language' => 'bn-BD', 'timezone' => 'Asia/Dhaka', 'utc_offset' => '+06:00', 'utc_dst_offset' => '+06:00',],
        'BB' => ['name' => 'Barbados', 'currency' => 'BBD', 'currency_name' => 'Barbados Dollar', 'language' => 'en-BB', 'timezone' => 'America/Barbados', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'BY' => ['name' => 'Belarus', 'currency' => 'BYR', 'currency_name' => 'Belarussian Ruble', 'language' => 'be', 'timezone' => 'Europe/Minsk', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'BE' => ['name' => 'Belgium', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'nl-BE', 'timezone' => 'Europe/Brussels', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'BZ' => ['name' => 'Belize', 'currency' => 'BZD', 'currency_name' => 'Belize Dollar', 'language' => 'en-BZ', 'timezone' => 'America/Belize', 'utc_offset' => '-06:00', 'utc_dst_offset' => '-06:00',],
        'BJ' => ['name' => 'Benin', 'currency' => 'XOF', 'currency_name' => 'CFA Franc BCEAO', 'language' => 'fr-BJ', 'timezone' => 'Africa/Porto-Novo', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'BM' => ['name' => 'Bermuda', 'currency' => 'BMD', 'currency_name' => 'Bermudian Dollar', 'language' => 'en-BM', 'timezone' => 'Atlantic/Bermuda', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-03:00',],
        'BT' => ['name' => 'Bhutan', 'currency' => 'INR', 'currency_name' => 'Indian Rupee', 'language' => 'dz', 'timezone' => 'Asia/Thimphu', 'utc_offset' => '+06:00', 'utc_dst_offset' => '+06:00',],
        'BO' => ['name' => 'Bolivia (Plurinational State of)', 'currency' => 'BOB', 'currency_name' => 'Boliviano', 'language' => 'es-BO', 'timezone' => 'America/La_Paz', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'BA' => ['name' => 'Bosnia and Herzegovina', 'currency' => 'BAM', 'currency_name' => 'Convertible Mark', 'language' => 'bs', 'timezone' => 'Europe/Sarajevo', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'BW' => ['name' => 'Botswana', 'currency' => 'BWP', 'currency_name' => 'Pula', 'language' => 'en-BW', 'timezone' => 'Africa/Gaborone', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'BR' => ['name' => 'Brazil', 'currency' => 'BRL', 'currency_name' => 'Brazilian Real', 'language' => 'pt-BR', 'timezone' => 'America/Sao_Paulo', 'utc_offset' => '-03:00', 'utc_dst_offset' => '-02:00',],
        'VG' => ['name' => 'British Virgin Islands', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'en-VG', 'timezone' => 'America/Tortola', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'BN' => ['name' => 'Brunei Darussalam', 'currency' => 'BND', 'currency_name' => 'Brunei Dollar', 'language' => 'ms-BN', 'timezone' => 'Asia/Brunei', 'utc_offset' => '+08:00', 'utc_dst_offset' => '+08:00',],
        'BG' => ['name' => 'Bulgaria', 'currency' => 'BGN', 'currency_name' => 'Bulgarian Lev', 'language' => 'bg', 'timezone' => 'Europe/Sofia', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'BF' => ['name' => 'Burkina Faso', 'currency' => 'XOF', 'currency_name' => 'CFA Franc BCEAO', 'language' => 'fr-BF', 'timezone' => 'Africa/Ouagadougou', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'BI' => ['name' => 'Burundi', 'currency' => 'BIF', 'currency_name' => 'Burundi Franc', 'language' => 'fr-BI', 'timezone' => 'Africa/Bujumbura', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'KH' => ['name' => 'Cambodia', 'currency' => 'KHR', 'currency_name' => 'Riel', 'language' => 'km', 'timezone' => 'Asia/Phnom_Penh', 'utc_offset' => '+07:00', 'utc_dst_offset' => '+07:00',],
        'CM' => ['name' => 'Cameroon', 'currency' => 'XAF', 'currency_name' => 'CFA Franc BEAC', 'language' => 'en-CM', 'timezone' => 'Africa/Douala', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'CA' => ['name' => 'Canada', 'currency' => 'CAD', 'currency_name' => 'Canadian Dollar', 'language' => 'en-CA', 'timezone' => 'America/Yellowknife', 'utc_offset' => '-07:00', 'utc_dst_offset' => '-06:00',],
        'CV' => ['name' => 'Cabo Verde', 'currency' => 'CVE', 'currency_name' => 'Cabo Verde Escudo', 'language' => 'pt-CV', 'timezone' => 'Atlantic/Cape_Verde', 'utc_offset' => '-01:00', 'utc_dst_offset' => '-01:00',],
        'BQ' => ['name' => 'Bonaire, Sint Eustatius and Saba', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'nl', 'timezone' => 'America/Kralendijk', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'KY' => ['name' => 'Cayman Islands', 'currency' => 'KYD', 'currency_name' => 'Cayman Islands Dollar', 'language' => 'en-KY', 'timezone' => 'America/Cayman', 'utc_offset' => '-05:00', 'utc_dst_offset' => '-05:00',],
        'CF' => ['name' => 'Central African Republic', 'currency' => 'XAF', 'currency_name' => 'CFA Franc BEAC', 'language' => 'fr-CF', 'timezone' => 'Africa/Bangui', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'TD' => ['name' => 'Chad', 'currency' => 'XAF', 'currency_name' => 'CFA Franc BEAC', 'language' => 'fr-TD', 'timezone' => 'Africa/Ndjamena', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'CL' => ['name' => 'Chile', 'currency' => 'CLP', 'currency_name' => 'Chilean Peso', 'language' => 'es-CL', 'timezone' => 'Pacific/Easter', 'utc_offset' => '-06:00', 'utc_dst_offset' => '-05:00',],
        'CN' => ['name' => 'China', 'currency' => 'CNY', 'currency_name' => 'Yuan Renminbi', 'language' => 'zh-CN', 'timezone' => 'Asia/Urumqi', 'utc_offset' => '+06:00', 'utc_dst_offset' => '+06:00',],
        'CO' => ['name' => 'Colombia', 'currency' => 'COP', 'currency_name' => 'Colombian Peso', 'language' => 'es-CO', 'timezone' => 'America/Bogota', 'utc_offset' => '-05:00', 'utc_dst_offset' => '-05:00',],
        'KM' => ['name' => 'Comoros', 'currency' => 'KMF', 'currency_name' => 'Comoro Franc', 'language' => 'ar', 'timezone' => 'Indian/Comoro', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'CG' => ['name' => 'Congo', 'currency' => 'XAF', 'currency_name' => 'CFA Franc BEAC', 'language' => 'fr-CG', 'timezone' => 'Africa/Brazzaville', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'CK' => ['name' => 'Cook Islands', 'currency' => 'NZD', 'currency_name' => 'New Zealand Dollar', 'language' => 'en-CK', 'timezone' => 'Pacific/Rarotonga', 'utc_offset' => '-10:00', 'utc_dst_offset' => '-10:00',],
        'CR' => ['name' => 'Costa Rica', 'currency' => 'CRC', 'currency_name' => 'Costa Rican Colon', 'language' => 'es-CR', 'timezone' => 'America/Costa_Rica', 'utc_offset' => '-06:00', 'utc_dst_offset' => '-06:00',],
        'HR' => ['name' => 'Croatia', 'currency' => 'HRK', 'currency_name' => 'Croatian Kuna', 'language' => 'hr-HR', 'timezone' => 'Europe/Zagreb', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'CU' => ['name' => 'Cuba', 'currency' => 'CUP', 'currency_name' => 'Cuban Peso', 'language' => 'es-CU', 'timezone' => 'America/Havana', 'utc_offset' => '-05:00', 'utc_dst_offset' => '-04:00',],
        'CW' => ['name' => 'Curaçao', 'currency' => 'ANG', 'currency_name' => 'Netherlands Antillean Guilder', 'language' => 'nl', 'timezone' => 'America/Curacao', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'CY' => ['name' => 'Cyprus', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'el-CY', 'timezone' => 'Asia/Nicosia', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'CI' => ['name' => "Côte d'Ivoire", 'currency' => 'XOF', 'currency_name' => 'CFA Franc BCEAO', 'language' => 'fr-CI', 'timezone' => 'Africa/Abidjan', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'DK' => ['name' => 'Denmark', 'currency' => 'DKK', 'currency_name' => 'Danish Krone', 'language' => 'da-DK', 'timezone' => 'Europe/Copenhagen', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'DJ' => ['name' => 'Djibouti', 'currency' => 'DJF', 'currency_name' => 'Djibouti Franc', 'language' => 'fr-DJ', 'timezone' => 'Africa/Djibouti', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'DM' => ['name' => 'Dominica', 'currency' => 'XCD', 'currency_name' => 'East Caribbean Dollar', 'language' => 'en-DM', 'timezone' => 'America/Dominica', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'DO' => ['name' => 'Dominican Republic', 'currency' => 'DOP', 'currency_name' => 'Dominican Peso', 'language' => 'es-DO', 'timezone' => 'America/Santo_Domingo', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'EC' => ['name' => 'Ecuador', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'es-EC', 'timezone' => 'Pacific/Galapagos', 'utc_offset' => '-06:00', 'utc_dst_offset' => '-06:00',],
        'EG' => ['name' => 'Egypt', 'currency' => 'EGP', 'currency_name' => 'Egyptian Pound', 'language' => 'ar-EG', 'timezone' => 'Africa/Cairo', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'SV' => ['name' => 'El Salvador', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'es-SV', 'timezone' => 'America/El_Salvador', 'utc_offset' => '-06:00', 'utc_dst_offset' => '-06:00',],
        'GQ' => ['name' => 'Equatorial Guinea', 'currency' => 'XAF', 'currency_name' => 'CFA Franc BEAC', 'language' => 'es-GQ', 'timezone' => 'Africa/Malabo', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'ER' => ['name' => 'Eritrea', 'currency' => 'ERN', 'currency_name' => 'Nakfa', 'language' => 'aa-ER', 'timezone' => 'Africa/Asmara', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'EE' => ['name' => 'Estonia', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'et', 'timezone' => 'Europe/Tallinn', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'ET' => ['name' => 'Ethiopia', 'currency' => 'ETB', 'currency_name' => 'Ethiopian Birr', 'language' => 'am', 'timezone' => 'Africa/Addis_Ababa', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'FK' => ['name' => 'Falkland Islands (Malvinas)', 'currency' => 'FKP', 'currency_name' => 'Falkland Islands Pound', 'language' => 'en-FK', 'timezone' => 'Atlantic/Stanley', 'utc_offset' => '-03:00', 'utc_dst_offset' => '-03:00',],
        'FJ' => ['name' => 'Fiji', 'currency' => 'FJD', 'currency_name' => 'Fiji Dollar', 'language' => 'en-FJ', 'timezone' => 'Pacific/Fiji', 'utc_offset' => '+12:00', 'utc_dst_offset' => '+13:00',],
        'FI' => ['name' => 'Finland', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fi-FI', 'timezone' => 'Europe/Helsinki', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'FR' => ['name' => 'France', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr-FR', 'timezone' => 'Europe/Paris', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'GF' => ['name' => 'French Guiana', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr-GF', 'timezone' => 'America/Cayenne', 'utc_offset' => '-03:00', 'utc_dst_offset' => '-03:00',],
        'PF' => ['name' => 'French Polynesia', 'currency' => 'XPF', 'currency_name' => 'CFP Franc', 'language' => 'fr-PF', 'timezone' => 'Pacific/Tahiti', 'utc_offset' => '-10:00', 'utc_dst_offset' => '-10:00',],
        'GA' => ['name' => 'Gabon', 'currency' => 'XAF', 'currency_name' => 'CFA Franc BEAC', 'language' => 'fr-GA', 'timezone' => 'Africa/Libreville', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'GM' => ['name' => 'Gambia', 'currency' => 'GMD', 'currency_name' => 'Dalasi', 'language' => 'en-GM', 'timezone' => 'Africa/Banjul', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'GE' => ['name' => 'Georgia', 'currency' => 'GEL', 'currency_name' => 'Lari', 'language' => 'ka', 'timezone' => 'Asia/Tbilisi', 'utc_offset' => '+04:00', 'utc_dst_offset' => '+04:00',],
        'DE' => ['name' => 'Germany', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'de', 'timezone' => 'Europe/Busingen', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'GH' => ['name' => 'Ghana', 'currency' => 'GHS', 'currency_name' => 'Ghana Cedi', 'language' => 'en-GH', 'timezone' => 'Africa/Accra', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'GI' => ['name' => 'Gibraltar', 'currency' => 'GIP', 'currency_name' => 'Gibraltar Pound', 'language' => 'en-GI', 'timezone' => 'Europe/Gibraltar', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'GR' => ['name' => 'Greece', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'el-GR', 'timezone' => 'Europe/Athens', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'GL' => ['name' => 'Greenland', 'currency' => 'DKK', 'currency_name' => 'Danish Krone', 'language' => 'kl', 'timezone' => 'America/Thule', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-03:00',],
        'GD' => ['name' => 'Grenada', 'currency' => 'XCD', 'currency_name' => 'East Caribbean Dollar', 'language' => 'en-GD', 'timezone' => 'America/Grenada', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'GP' => ['name' => 'Guadeloupe', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr-GP', 'timezone' => 'America/Guadeloupe', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'GU' => ['name' => 'Guam', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'en-GU', 'timezone' => 'Pacific/Guam', 'utc_offset' => '+10:00', 'utc_dst_offset' => '+10:00',],
        'GT' => ['name' => 'Guatemala', 'currency' => 'GTQ', 'currency_name' => 'Quetzal', 'language' => 'es-GT', 'timezone' => 'America/Guatemala', 'utc_offset' => '-06:00', 'utc_dst_offset' => '-06:00',],
        'GG' => ['name' => 'Guernsey', 'currency' => 'GBP', 'currency_name' => 'Pound Sterling', 'language' => 'en', 'timezone' => 'Europe/Guernsey', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+01:00',],
        'GN' => ['name' => 'Guinea', 'currency' => 'GNF', 'currency_name' => 'Guinea Franc', 'language' => 'fr-GN', 'timezone' => 'Africa/Conakry', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'GW' => ['name' => 'Guinea-Bissau', 'currency' => 'XOF', 'currency_name' => 'CFA Franc BCEAO', 'language' => 'pt-GW', 'timezone' => 'Africa/Bissau', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'GY' => ['name' => 'Guyana', 'currency' => 'GYD', 'currency_name' => 'Guyana Dollar', 'language' => 'en-GY', 'timezone' => 'America/Guyana', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'HT' => ['name' => 'Haiti', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'ht', 'timezone' => 'America/Port-au-Prince', 'utc_offset' => '-05:00', 'utc_dst_offset' => '-05:00',],
        'HN' => ['name' => 'Honduras', 'currency' => 'HNL', 'currency_name' => 'Lempira', 'language' => 'es-HN', 'timezone' => 'America/Tegucigalpa', 'utc_offset' => '-06:00', 'utc_dst_offset' => '-06:00',],
        'HU' => ['name' => 'Hungary', 'currency' => 'HUF', 'currency_name' => 'Forint', 'language' => 'hu-HU', 'timezone' => 'Europe/Budapest', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'IS' => ['name' => 'Iceland', 'currency' => 'ISK', 'currency_name' => 'Iceland Krona', 'language' => 'is', 'timezone' => 'Atlantic/Reykjavik', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'IN' => ['name' => 'India', 'currency' => 'INR', 'currency_name' => 'Indian Rupee', 'language' => 'en-IN', 'timezone' => 'Asia/Kolkata', 'utc_offset' => '+05:30', 'utc_dst_offset' => '+05:30',],
        'ID' => ['name' => 'Indonesia', 'currency' => 'IDR', 'currency_name' => 'Rupiah', 'language' => 'id', 'timezone' => 'Asia/Pontianak', 'utc_offset' => '+07:00', 'utc_dst_offset' => '+07:00',],
        'IR' => ['name' => 'Iran (Islamic Republic of)', 'currency' => 'IRR', 'currency_name' => 'Iranian Rial', 'language' => 'fa-IR', 'timezone' => 'Asia/Tehran', 'utc_offset' => '+03:30', 'utc_dst_offset' => '+04:30',],
        'IQ' => ['name' => 'Iraq', 'currency' => 'IQD', 'currency_name' => 'Iraqi Dinar', 'language' => 'ar-IQ', 'timezone' => 'Asia/Baghdad', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'IE' => ['name' => 'Ireland', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'en-IE', 'timezone' => 'Europe/Dublin', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+01:00',],
        'IM' => ['name' => 'Isle of Man', 'currency' => 'GBP', 'currency_name' => 'Pound Sterling', 'language' => 'en', 'timezone' => 'Europe/Isle_of_Man', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+01:00',],
        'IL' => ['name' => 'Israel', 'currency' => 'ILS', 'currency_name' => 'New Israeli Sheqel', 'language' => 'he', 'timezone' => 'Asia/Jerusalem', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'IT' => ['name' => 'Italy', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'it-IT', 'timezone' => 'Europe/Rome', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'JM' => ['name' => 'Jamaica', 'currency' => 'JMD', 'currency_name' => 'Jamaican Dollar', 'language' => 'en-JM', 'timezone' => 'America/Jamaica', 'utc_offset' => '-05:00', 'utc_dst_offset' => '-05:00',],
        'JP' => ['name' => 'Japan', 'currency' => 'JPY', 'currency_name' => 'Yen', 'language' => 'ja', 'timezone' => 'Asia/Tokyo', 'utc_offset' => '+09:00', 'utc_dst_offset' => '+09:00',],
        'JE' => ['name' => 'Jersey', 'currency' => 'GBP', 'currency_name' => 'Pound Sterling', 'language' => 'en', 'timezone' => 'Europe/Jersey', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+01:00',],
        'JO' => ['name' => 'Jordan', 'currency' => 'JOD', 'currency_name' => 'Jordanian Dinar', 'language' => 'ar-JO', 'timezone' => 'Asia/Amman', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'KZ' => ['name' => 'Kazakhstan', 'currency' => 'KZT', 'currency_name' => 'Tenge', 'language' => 'kk', 'timezone' => 'Asia/Qyzylorda', 'utc_offset' => '+06:00', 'utc_dst_offset' => '+06:00',],
        'KE' => ['name' => 'Kenya', 'currency' => 'KES', 'currency_name' => 'Kenyan Shilling', 'language' => 'en-KE', 'timezone' => 'Africa/Nairobi', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'KI' => ['name' => 'Kiribati', 'currency' => 'AUD', 'currency_name' => 'Australian Dollar', 'language' => 'en-KI', 'timezone' => 'Pacific/Tarawa', 'utc_offset' => '+12:00', 'utc_dst_offset' => '+12:00',],
        'KW' => ['name' => 'Kuwait', 'currency' => 'KWD', 'currency_name' => 'Kuwaiti Dinar', 'language' => 'ar-KW', 'timezone' => 'Asia/Kuwait', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'KG' => ['name' => 'Kyrgyzstan', 'currency' => 'KGS', 'currency_name' => 'Som', 'language' => 'ky', 'timezone' => 'Asia/Bishkek', 'utc_offset' => '+06:00', 'utc_dst_offset' => '+06:00',],
        'LA' => ['name' => "Lao People's Democratic Republic", 'currency' => 'LAK', 'currency_name' => 'Kip', 'language' => 'lo', 'timezone' => 'Asia/Vientiane', 'utc_offset' => '+07:00', 'utc_dst_offset' => '+07:00',],
        'LV' => ['name' => 'Latvia', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'lv', 'timezone' => 'Europe/Riga', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'LB' => ['name' => 'Lebanon', 'currency' => 'LBP', 'currency_name' => 'Lebanese Pound', 'language' => 'ar-LB', 'timezone' => 'Asia/Beirut', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'LS' => ['name' => 'Lesotho', 'currency' => 'ZAR', 'currency_name' => 'Rand', 'language' => 'en-LS', 'timezone' => 'Africa/Maseru', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'LR' => ['name' => 'Liberia', 'currency' => 'LRD', 'currency_name' => 'Liberian Dollar', 'language' => 'en-LR', 'timezone' => 'Africa/Monrovia', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'LY' => ['name' => 'Libya', 'currency' => 'LYD', 'currency_name' => 'Libyan Dinar', 'language' => 'ar-LY', 'timezone' => 'Africa/Tripoli', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'LI' => ['name' => 'Liechtenstein', 'currency' => 'CHF', 'currency_name' => 'Swiss Franc', 'language' => 'de-LI', 'timezone' => 'Europe/Vaduz', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'LT' => ['name' => 'Lithuania', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'lt', 'timezone' => 'Europe/Vilnius', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'LU' => ['name' => 'Luxembourg', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'lb', 'timezone' => 'Europe/Luxembourg', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'MO' => ['name' => 'China, Macao Special Administrative Region', 'currency' => 'MOP', 'currency_name' => 'Pataca', 'language' => 'zh', 'timezone' => 'Asia/Macau', 'utc_offset' => '+08:00', 'utc_dst_offset' => '+08:00',],
        'MK' => ['name' => 'The former Yugoslav Republic of Macedonia', 'currency' => 'MKD', 'currency_name' => 'Denar', 'language' => 'mk', 'timezone' => 'Europe/Skopje', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'MG' => ['name' => 'Madagascar', 'currency' => 'MGA', 'currency_name' => 'Malagasy Ariary', 'language' => 'fr-MG', 'timezone' => 'Indian/Antananarivo', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'MW' => ['name' => 'Malawi', 'currency' => 'MWK', 'currency_name' => 'Kwacha', 'language' => 'ny', 'timezone' => 'Africa/Blantyre', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'MY' => ['name' => 'Malaysia', 'currency' => 'MYR', 'currency_name' => 'Malaysian Ringgit', 'language' => 'ms-MY', 'timezone' => 'Asia/Kuching', 'utc_offset' => '+08:00', 'utc_dst_offset' => '+08:00',],
        'MV' => ['name' => 'Maldives', 'currency' => 'MVR', 'currency_name' => 'Rufiyaa', 'language' => 'dv', 'timezone' => 'Indian/Maldives', 'utc_offset' => '+05:00', 'utc_dst_offset' => '+05:00',],
        'ML' => ['name' => 'Mali', 'currency' => 'XOF', 'currency_name' => 'CFA Franc BCEAO', 'language' => 'fr-ML', 'timezone' => 'Africa/Bamako', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'MT' => ['name' => 'Malta', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'mt', 'timezone' => 'Europe/Malta', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'MH' => ['name' => 'Marshall Islands', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'mh', 'timezone' => 'Pacific/Majuro', 'utc_offset' => '+12:00', 'utc_dst_offset' => '+12:00',],
        'MQ' => ['name' => 'Martinique', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr-MQ', 'timezone' => 'America/Martinique', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'MR' => ['name' => 'Mauritania', 'currency' => 'MRO', 'currency_name' => 'Ouguiya', 'language' => 'ar-MR', 'timezone' => 'Africa/Nouakchott', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'MU' => ['name' => 'Mauritius', 'currency' => 'MUR', 'currency_name' => 'Mauritius Rupee', 'language' => 'en-MU', 'timezone' => 'Indian/Mauritius', 'utc_offset' => '+04:00', 'utc_dst_offset' => '+04:00',],
        'YT' => ['name' => 'Mayotte', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr-YT', 'timezone' => 'Indian/Mayotte', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'MX' => ['name' => 'Mexico', 'currency' => 'MXN', 'currency_name' => 'Mexican Peso', 'language' => 'es-MX', 'timezone' => 'America/Tijuana', 'utc_offset' => '-08:00', 'utc_dst_offset' => '-07:00',],
        'FM' => ['name' => 'Micronesia (Federated States of)', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'en-FM', 'timezone' => 'Pacific/Pohnpei', 'utc_offset' => '+11:00', 'utc_dst_offset' => '+11:00',],
        'MD' => ['name' => 'Republic of Moldova', 'currency' => 'MDL', 'currency_name' => 'Moldovan Leu', 'language' => 'ro', 'timezone' => 'Europe/Chisinau', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'MC' => ['name' => 'Monaco', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr-MC', 'timezone' => 'Europe/Monaco', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'MN' => ['name' => 'Mongolia', 'currency' => 'MNT', 'currency_name' => 'Tugrik', 'language' => 'mn', 'timezone' => 'Asia/Ulaanbaatar', 'utc_offset' => '+08:00', 'utc_dst_offset' => '+09:00',],
        'ME' => ['name' => 'Montenegro', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'sr', 'timezone' => 'Europe/Podgorica', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'MS' => ['name' => 'Montserrat', 'currency' => 'XCD', 'currency_name' => 'East Caribbean Dollar', 'language' => 'en-MS', 'timezone' => 'America/Montserrat', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'MA' => ['name' => 'Morocco', 'currency' => 'MAD', 'currency_name' => 'Moroccan Dirham', 'language' => 'ar-MA', 'timezone' => 'Africa/Casablanca', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+01:00',],
        'MZ' => ['name' => 'Mozambique', 'currency' => 'MZN', 'currency_name' => 'Mozambique Metical', 'language' => 'pt-MZ', 'timezone' => 'Africa/Maputo', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'MM' => ['name' => 'Myanmar', 'currency' => 'MMK', 'currency_name' => 'Kyat', 'language' => 'my', 'timezone' => 'Asia/Yangon', 'utc_offset' => '+06:30', 'utc_dst_offset' => '+06:30',],
        'NA' => ['name' => 'Namibia', 'currency' => 'ZAR', 'currency_name' => 'Rand', 'language' => 'en-NA', 'timezone' => 'Africa/Windhoek', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'NR' => ['name' => 'Nauru', 'currency' => 'AUD', 'currency_name' => 'Australian Dollar', 'language' => 'na', 'timezone' => 'Pacific/Nauru', 'utc_offset' => '+12:00', 'utc_dst_offset' => '+12:00',],
        'NP' => ['name' => 'Nepal', 'currency' => 'NPR', 'currency_name' => 'Nepalese Rupee', 'language' => 'ne', 'timezone' => 'Asia/Kathmandu', 'utc_offset' => '+05:45', 'utc_dst_offset' => '+05:45',],
        'NL' => ['name' => 'Netherlands', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'nl-NL', 'timezone' => 'Europe/Amsterdam', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'NC' => ['name' => 'New Caledonia', 'currency' => 'XPF', 'currency_name' => 'CFP Franc', 'language' => 'fr-NC', 'timezone' => 'Pacific/Noumea', 'utc_offset' => '+11:00', 'utc_dst_offset' => '+11:00',],
        'NZ' => ['name' => 'New Zealand', 'currency' => 'NZD', 'currency_name' => 'New Zealand Dollar', 'language' => 'en-NZ', 'timezone' => 'Pacific/Chatham', 'utc_offset' => '+12:45', 'utc_dst_offset' => '+13:45',],
        'NI' => ['name' => 'Nicaragua', 'currency' => 'NIO', 'currency_name' => 'Cordoba Oro', 'language' => 'es-NI', 'timezone' => 'America/Managua', 'utc_offset' => '-06:00', 'utc_dst_offset' => '-06:00',],
        'NE' => ['name' => 'Niger', 'currency' => 'XOF', 'currency_name' => 'CFA Franc BCEAO', 'language' => 'fr-NE', 'timezone' => 'Africa/Niamey', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'NG' => ['name' => 'Nigeria', 'currency' => 'NGN', 'currency_name' => 'Naira', 'language' => 'en-NG', 'timezone' => 'Africa/Lagos', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'NU' => ['name' => 'Niue', 'currency' => 'NZD', 'currency_name' => 'New Zealand Dollar', 'language' => 'niu', 'timezone' => 'Pacific/Niue', 'utc_offset' => '-11:00', 'utc_dst_offset' => '-11:00',],
        'NF' => ['name' => 'Norfolk Island', 'currency' => 'AUD', 'currency_name' => 'Australian Dollar', 'language' => 'en-NF', 'timezone' => 'Pacific/Norfolk', 'utc_offset' => '+11:00', 'utc_dst_offset' => '+11:00',],
        'KP' => ['name' => "Democratic People's Republic of Korea", 'currency' => 'KPW', 'currency_name' => 'North Korean Won', 'language' => 'ko-KP', 'timezone' => 'Asia/Pyongyang', 'utc_offset' => '+08:30', 'utc_dst_offset' => '+08:30',],
        'MP' => ['name' => 'Northern Mariana Islands', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'fil', 'timezone' => 'Pacific/Saipan', 'utc_offset' => '+10:00', 'utc_dst_offset' => '+10:00',],
        'NO' => ['name' => 'Norway', 'currency' => 'NOK', 'currency_name' => 'Norwegian Krone', 'language' => 'no', 'timezone' => 'Europe/Oslo', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'OM' => ['name' => 'Oman', 'currency' => 'OMR', 'currency_name' => 'Rial Omani', 'language' => 'ar-OM', 'timezone' => 'Asia/Muscat', 'utc_offset' => '+04:00', 'utc_dst_offset' => '+04:00',],
        'PK' => ['name' => 'Pakistan', 'currency' => 'PKR', 'currency_name' => 'Pakistan Rupee', 'language' => 'ur-PK', 'timezone' => 'Asia/Karachi', 'utc_offset' => '+05:00', 'utc_dst_offset' => '+05:00',],
        'PW' => ['name' => 'Palau', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'pau', 'timezone' => 'Pacific/Palau', 'utc_offset' => '+09:00', 'utc_dst_offset' => '+09:00',],
        'PA' => ['name' => 'Panama', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'es-PA', 'timezone' => 'America/Panama', 'utc_offset' => '-05:00', 'utc_dst_offset' => '-05:00',],
        'PG' => ['name' => 'Papua New Guinea', 'currency' => 'PGK', 'currency_name' => 'Kina', 'language' => 'en-PG', 'timezone' => 'Pacific/Port_Moresby', 'utc_offset' => '+10:00', 'utc_dst_offset' => '+10:00',],
        'PY' => ['name' => 'Paraguay', 'currency' => 'PYG', 'currency_name' => 'Guarani', 'language' => 'es-PY', 'timezone' => 'America/Asuncion', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-03:00',],
        'PE' => ['name' => 'Peru', 'currency' => 'PEN', 'currency_name' => 'Nuevo Sol', 'language' => 'es-PE', 'timezone' => 'America/Lima', 'utc_offset' => '-05:00', 'utc_dst_offset' => '-05:00',],
        'PH' => ['name' => 'Philippines', 'currency' => 'PHP', 'currency_name' => 'Philippine Peso', 'language' => 'tl', 'timezone' => 'Asia/Manila', 'utc_offset' => '+08:00', 'utc_dst_offset' => '+08:00',],
        'PN' => ['name' => 'Pitcairn', 'currency' => 'NZD', 'currency_name' => 'New Zealand Dollar', 'language' => 'en-PN', 'timezone' => 'Pacific/Pitcairn', 'utc_offset' => '-08:00', 'utc_dst_offset' => '-08:00',],
        'PL' => ['name' => 'Poland', 'currency' => 'PLN', 'currency_name' => 'Zloty', 'language' => 'pl', 'timezone' => 'Europe/Warsaw', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'PT' => ['name' => 'Portugal', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'pt-PT', 'timezone' => 'Europe/Lisbon', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+01:00',],
        'PR' => ['name' => 'Puerto Rico', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'en-PR', 'timezone' => 'America/Puerto_Rico', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'QA' => ['name' => 'Qatar', 'currency' => 'QAR', 'currency_name' => 'Qatari Rial', 'language' => 'ar-QA', 'timezone' => 'Asia/Qatar', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'RO' => ['name' => 'Romania', 'currency' => 'RON', 'currency_name' => 'New Romanian Leu', 'language' => 'ro', 'timezone' => 'Europe/Bucharest', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'RU' => ['name' => 'Russian Federation', 'currency' => 'RUB', 'currency_name' => 'Russian Ruble', 'language' => 'ru', 'timezone' => 'Europe/Volgograd', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'RW' => ['name' => 'Rwanda', 'currency' => 'RWF', 'currency_name' => 'Rwanda Franc', 'language' => 'rw', 'timezone' => 'Africa/Kigali', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'RE' => ['name' => 'Réunion', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr-RE', 'timezone' => 'Indian/Reunion', 'utc_offset' => '+04:00', 'utc_dst_offset' => '+04:00',],
        'WS' => ['name' => 'Samoa', 'currency' => 'WST', 'currency_name' => 'Tala', 'language' => 'sm', 'timezone' => 'Pacific/Apia', 'utc_offset' => '+13:00', 'utc_dst_offset' => '+14:00',],
        'SM' => ['name' => 'San Marino', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'it-SM', 'timezone' => 'Europe/San_Marino', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'SA' => ['name' => 'Saudi Arabia', 'currency' => 'SAR', 'currency_name' => 'Saudi Riyal', 'language' => 'ar-SA', 'timezone' => 'Asia/Riyadh', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'SN' => ['name' => 'Senegal', 'currency' => 'XOF', 'currency_name' => 'CFA Franc BCEAO', 'language' => 'fr-SN', 'timezone' => 'Africa/Dakar', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'RS' => ['name' => 'Serbia', 'currency' => 'RSD', 'currency_name' => 'Serbian Dinar', 'language' => 'sr', 'timezone' => 'Europe/Belgrade', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'SC' => ['name' => 'Seychelles', 'currency' => 'SCR', 'currency_name' => 'Seychelles Rupee', 'language' => 'en-SC', 'timezone' => 'Indian/Mahe', 'utc_offset' => '+04:00', 'utc_dst_offset' => '+04:00',],
        'SL' => ['name' => 'Sierra Leone', 'currency' => 'SLL', 'currency_name' => 'Leone', 'language' => 'en-SL', 'timezone' => 'Africa/Freetown', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'SG' => ['name' => 'Singapore', 'currency' => 'SGD', 'currency_name' => 'Singapore Dollar', 'language' => 'cmn', 'timezone' => 'Asia/Singapore', 'utc_offset' => '+08:00', 'utc_dst_offset' => '+08:00',],
        'SX' => ['name' => 'Sint Maarten (Dutch part)', 'currency' => 'ANG', 'currency_name' => 'Netherlands Antillean Guilder', 'language' => 'nl', 'timezone' => 'America/Lower_Princes', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'SK' => ['name' => 'Slovakia', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'sk', 'timezone' => 'Europe/Bratislava', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'SI' => ['name' => 'Slovenia', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'sl', 'timezone' => 'Europe/Ljubljana', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'SB' => ['name' => 'Solomon Islands', 'currency' => 'SBD', 'currency_name' => 'Solomon Islands Dollar', 'language' => 'en-SB', 'timezone' => 'Pacific/Guadalcanal', 'utc_offset' => '+11:00', 'utc_dst_offset' => '+11:00',],
        'SO' => ['name' => 'Somalia', 'currency' => 'SOS', 'currency_name' => 'Somali Shilling', 'language' => 'so-SO', 'timezone' => 'Africa/Mogadishu', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'ZA' => ['name' => 'South Africa', 'currency' => 'ZAR', 'currency_name' => 'Rand', 'language' => 'zu', 'timezone' => 'Africa/Johannesburg', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'KR' => ['name' => 'Republic of Korea', 'currency' => 'KRW', 'currency_name' => 'Won', 'language' => 'ko-KR', 'timezone' => 'Asia/Seoul', 'utc_offset' => '+09:00', 'utc_dst_offset' => '+09:00',],
        'SS' => ['name' => 'South Sudan', 'currency' => 'SSP', 'currency_name' => 'South Sudanese Pound', 'language' => 'en', 'timezone' => 'Africa/Juba', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'ES' => ['name' => 'Spain', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'es-ES', 'timezone' => 'Europe/Madrid', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'LK' => ['name' => 'Sri Lanka', 'currency' => 'LKR', 'currency_name' => 'Sri Lanka Rupee', 'language' => 'si', 'timezone' => 'Asia/Colombo', 'utc_offset' => '+05:30', 'utc_dst_offset' => '+05:30',],
        'BL' => ['name' => 'Saint Barthélemy', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr', 'timezone' => 'America/St_Barthelemy', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'SH' => ['name' => 'Saint Helena', 'currency' => 'SHP', 'currency_name' => 'Saint Helena Pound', 'language' => 'en-SH', 'timezone' => 'Atlantic/St_Helena', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'KN' => ['name' => 'Saint Kitts and Nevis', 'currency' => 'XCD', 'currency_name' => 'East Caribbean Dollar', 'language' => 'en-KN', 'timezone' => 'America/St_Kitts', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'LC' => ['name' => 'Saint Lucia', 'currency' => 'XCD', 'currency_name' => 'East Caribbean Dollar', 'language' => 'en-LC', 'timezone' => 'America/St_Lucia', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'MF' => ['name' => 'Saint Martin (French part)', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr', 'timezone' => 'America/Marigot', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'PM' => ['name' => 'Saint Pierre and Miquelon', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'fr-PM', 'timezone' => 'America/Miquelon', 'utc_offset' => '-03:00', 'utc_dst_offset' => '-02:00',],
        'VC' => ['name' => 'Saint Vincent and the Grenadines', 'currency' => 'XCD', 'currency_name' => 'East Caribbean Dollar', 'language' => 'en-VC', 'timezone' => 'America/St_Vincent', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'SD' => ['name' => 'Sudan', 'currency' => 'SDG', 'currency_name' => 'Sudanese Pound', 'language' => 'ar-SD', 'timezone' => 'Africa/Khartoum', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'SR' => ['name' => 'Suriname', 'currency' => 'SRD', 'currency_name' => 'Surinam Dollar', 'language' => 'nl-SR', 'timezone' => 'America/Paramaribo', 'utc_offset' => '-03:00', 'utc_dst_offset' => '-03:00',],
        'SJ' => ['name' => 'Svalbard and Jan Mayen Islands', 'currency' => 'NOK', 'currency_name' => 'Norwegian Krone', 'language' => 'no', 'timezone' => 'Arctic/Longyearbyen', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'SZ' => ['name' => 'Swaziland', 'currency' => 'SZL', 'currency_name' => 'Lilangeni', 'language' => 'en-SZ', 'timezone' => 'Africa/Mbabane', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'SE' => ['name' => 'Sweden', 'currency' => 'SEK', 'currency_name' => 'Swedish Krona', 'language' => 'sv-SE', 'timezone' => 'Europe/Stockholm', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'CH' => ['name' => 'Switzerland', 'currency' => 'CHF', 'currency_name' => 'Swiss Franc', 'language' => 'de-CH', 'timezone' => 'Europe/Zurich', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'SY' => ['name' => 'Syrian Arab Republic', 'currency' => 'SYP', 'currency_name' => 'Syrian Pound', 'language' => 'ar-SY', 'timezone' => 'Asia/Damascus', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'ST' => ['name' => 'Sao Tome and Principe', 'currency' => 'STD', 'currency_name' => 'Dobra', 'language' => 'pt-ST', 'timezone' => 'Africa/Sao_Tome', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'TJ' => ['name' => 'Tajikistan', 'currency' => 'TJS', 'currency_name' => 'Somoni', 'language' => 'tg', 'timezone' => 'Asia/Dushanbe', 'utc_offset' => '+05:00', 'utc_dst_offset' => '+05:00',],
        'TZ' => ['name' => 'United Republic of Tanzania', 'currency' => 'TZS', 'currency_name' => 'Tanzanian Shilling', 'language' => 'sw-TZ', 'timezone' => 'Africa/Dar_es_Salaam', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'TH' => ['name' => 'Thailand', 'currency' => 'THB', 'currency_name' => 'Baht', 'language' => 'th', 'timezone' => 'Asia/Bangkok', 'utc_offset' => '+07:00', 'utc_dst_offset' => '+07:00',],
        'TL' => ['name' => 'Timor-Leste', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'tet', 'timezone' => 'Asia/Dili', 'utc_offset' => '+09:00', 'utc_dst_offset' => '+09:00',],
        'TG' => ['name' => 'Togo', 'currency' => 'XOF', 'currency_name' => 'CFA Franc BCEAO', 'language' => 'fr-TG', 'timezone' => 'Africa/Lome', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+00:00',],
        'TK' => ['name' => 'Tokelau', 'currency' => 'NZD', 'currency_name' => 'New Zealand Dollar', 'language' => 'tkl', 'timezone' => 'Pacific/Fakaofo', 'utc_offset' => '+13:00', 'utc_dst_offset' => '+13:00',],
        'TO' => ['name' => 'Tonga', 'currency' => 'TOP', 'currency_name' => 'Pa’anga', 'language' => 'to', 'timezone' => 'Pacific/Tongatapu', 'utc_offset' => '+13:00', 'utc_dst_offset' => '+14:00',],
        'TT' => ['name' => 'Trinidad and Tobago', 'currency' => 'TTD', 'currency_name' => 'Trinidad and Tobago Dollar', 'language' => 'en-TT', 'timezone' => 'America/Port_of_Spain', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'TN' => ['name' => 'Tunisia', 'currency' => 'TND', 'currency_name' => 'Tunisian Dinar', 'language' => 'ar-TN', 'timezone' => 'Africa/Tunis', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+01:00',],
        'TR' => ['name' => 'Turkey', 'currency' => 'TRY', 'currency_name' => 'Turkish Lira', 'language' => 'tr-TR', 'timezone' => 'Europe/Istanbul', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'TM' => ['name' => 'Turkmenistan', 'currency' => 'TMT', 'currency_name' => 'Turkmenistan New Manat', 'language' => 'tk', 'timezone' => 'Asia/Ashgabat', 'utc_offset' => '+05:00', 'utc_dst_offset' => '+05:00',],
        'TC' => ['name' => 'Turks and Caicos Islands', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'en-TC', 'timezone' => 'America/Grand_Turk', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'TV' => ['name' => 'Tuvalu', 'currency' => 'AUD', 'currency_name' => 'Australian Dollar', 'language' => 'tvl', 'timezone' => 'Pacific/Funafuti', 'utc_offset' => '+12:00', 'utc_dst_offset' => '+12:00',],
        'VI' => ['name' => 'United States Virgin Islands', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'en-VI', 'timezone' => 'America/St_Thomas', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'GB' => ['name' => 'United Kingdom of Great Britain and Northern Ireland', 'currency' => 'GBP', 'currency_name' => 'Pound Sterling', 'language' => 'en-GB', 'timezone' => 'Europe/London', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+01:00',],
        'US' => ['name' => 'United States of America', 'currency' => 'USD', 'currency_name' => 'US Dollar', 'language' => 'en-US', 'timezone' => 'Pacific/Honolulu', 'utc_offset' => '-10:00', 'utc_dst_offset' => '-10:00',],
        'UG' => ['name' => 'Uganda', 'currency' => 'UGX', 'currency_name' => 'Uganda Shilling', 'language' => 'en-UG', 'timezone' => 'Africa/Kampala', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'UA' => ['name' => 'Ukraine', 'currency' => 'UAH', 'currency_name' => 'Hryvnia', 'language' => 'uk', 'timezone' => 'Europe/Zaporozhye', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',],
        'AE' => ['name' => 'United Arab Emirates', 'currency' => 'AED', 'currency_name' => 'UAE Dirham', 'language' => 'ar-AE', 'timezone' => 'Asia/Dubai', 'utc_offset' => '+04:00', 'utc_dst_offset' => '+04:00',],
        'UY' => ['name' => 'Uruguay', 'currency' => 'UYU', 'currency_name' => 'Peso Uruguayo', 'language' => 'es-UY', 'timezone' => 'America/Montevideo', 'utc_offset' => '-03:00', 'utc_dst_offset' => '-03:00',],
        'UZ' => ['name' => 'Uzbekistan', 'currency' => 'UZS', 'currency_name' => 'Uzbekistan Sum', 'language' => 'uz', 'timezone' => 'Asia/Tashkent', 'utc_offset' => '+05:00', 'utc_dst_offset' => '+05:00',],
        'VU' => ['name' => 'Vanuatu', 'currency' => 'VUV', 'currency_name' => 'Vatu', 'language' => 'bi', 'timezone' => 'Pacific/Efate', 'utc_offset' => '+11:00', 'utc_dst_offset' => '+11:00',],
        'VA' => ['name' => 'Holy See', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'la', 'timezone' => 'Europe/Vatican', 'utc_offset' => '+01:00', 'utc_dst_offset' => '+02:00',],
        'VE' => ['name' => 'Venezuela (Bolivarian Republic of)', 'currency' => 'VEF', 'currency_name' => 'Bolivar', 'language' => 'es-VE', 'timezone' => 'America/Caracas', 'utc_offset' => '-04:00', 'utc_dst_offset' => '-04:00',],
        'VN' => ['name' => 'Viet Nam', 'currency' => 'VND', 'currency_name' => 'Dong', 'language' => 'vi', 'timezone' => 'Asia/Ho_Chi_Minh', 'utc_offset' => '+07:00', 'utc_dst_offset' => '+07:00',],
        'WF' => ['name' => 'Wallis and Futuna Islands', 'currency' => 'XPF', 'currency_name' => 'CFP Franc', 'language' => 'wls', 'timezone' => 'Pacific/Wallis', 'utc_offset' => '+12:00', 'utc_dst_offset' => '+12:00',],
        'EH' => ['name' => 'Western Sahara', 'currency' => 'MAD', 'currency_name' => 'Moroccan Dirham', 'language' => 'ar', 'timezone' => 'Africa/El_Aaiun', 'utc_offset' => '+00:00', 'utc_dst_offset' => '+01:00',],
        'YE' => ['name' => 'Yemen', 'currency' => 'YER', 'currency_name' => 'Yemeni Rial', 'language' => 'ar-YE', 'timezone' => 'Asia/Aden', 'utc_offset' => '+03:00', 'utc_dst_offset' => '+03:00',],
        'ZM' => ['name' => 'Zambia', 'currency' => 'ZMW', 'currency_name' => 'Zambian Kwacha', 'language' => 'en-ZM', 'timezone' => 'Africa/Lusaka', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'ZW' => ['name' => 'Zimbabwe', 'currency' => 'ZWL', 'currency_name' => 'Zimbabwe Dollar', 'language' => 'en-ZW', 'timezone' => 'Africa/Harare', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+02:00',],
        'AX' => ['name' => 'Åland Islands', 'currency' => 'EUR', 'currency_name' => 'Euro', 'language' => 'sv-AX', 'timezone' => 'Europe/Mariehamn', 'utc_offset' => '+02:00', 'utc_dst_offset' => '+03:00',]
    ];

}
