<?php

/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 31/08/15
 * Time: 14:20
 */
class CurrencyFormatter
{
    var $currencies = array();

    function __construct()
    {
        $this->currencies['ARS'] = array(2, ',', '.');          //  Argentine Peso
        $this->currencies['AMD'] = array(2, '.', ',');          //  Armenian Dram
        $this->currencies['AWG'] = array(2, '.', ',');          //  Aruban Guilder
        $this->currencies['AUD'] = array(2, '.', ' ');          //  Australian Dollar
        $this->currencies['BSD'] = array(2, '.', ',');          //  Bahamian Dollar
        $this->currencies['BHD'] = array(3, '.', ',');          //  Bahraini Dinar
        $this->currencies['BDT'] = array(2, '.', ',');          //  Bangladesh, Taka
        $this->currencies['BZD'] = array(2, '.', ',');          //  Belize Dollar
        $this->currencies['BMD'] = array(2, '.', ',');          //  Bermudian Dollar
        $this->currencies['BOB'] = array(2, '.', ',');          //  Bolivia, Boliviano
        $this->currencies['BAM'] = array(2, '.', ',');          //  Bosnia and Herzegovina, Convertible Marks
        $this->currencies['BWP'] = array(2, '.', ',');          //  Botswana, Pula
        $this->currencies['BRL'] = array(2, ',', '.');          //  Brazilian Real
        $this->currencies['BND'] = array(2, '.', ',');          //  Brunei Dollar
        $this->currencies['CAD'] = array(2, '.', ',');          //  Canadian Dollar
        $this->currencies['KYD'] = array(2, '.', ',');          //  Cayman Islands Dollar
        $this->currencies['CLP'] = array(0, '', '.');           //  Chilean Peso
        $this->currencies['CNY'] = array(2, '.', ',');          //  China Yuan Renminbi
        $this->currencies['COP'] = array(2, ',', '.');          //  Colombian Peso
        $this->currencies['CRC'] = array(2, ',', '.');          //  Costa Rican Colon
        $this->currencies['HRK'] = array(2, ',', '.');          //  Croatian Kuna
        $this->currencies['CUC'] = array(2, '.', ',');          //  Cuban Convertible Peso
        $this->currencies['CUP'] = array(2, '.', ',');          //  Cuban Peso
        $this->currencies['CYP'] = array(2, '.', ',');          //  Cyprus Pound
        $this->currencies['CZK'] = array(2, '.', ',');          //  Czech Koruna
        $this->currencies['DKK'] = array(2, ',', '.');          //  Danish Krone
        $this->currencies['DOP'] = array(2, '.', ',');          //  Dominican Peso
        $this->currencies['XCD'] = array(2, '.', ',');          //  East Caribbean Dollar
        $this->currencies['EGP'] = array(2, '.', ',');          //  Egyptian Pound
        $this->currencies['SVC'] = array(2, '.', ',');          //  El Salvador Colon
        $this->currencies['ATS'] = array(2, ',', '.');          //  Euro
        $this->currencies['BEF'] = array(2, ',', '.');          //  Euro
        $this->currencies['DEM'] = array(2, ',', '.');          //  Euro
        $this->currencies['EEK'] = array(2, ',', '.');          //  Euro
        $this->currencies['ESP'] = array(2, ',', '.');          //  Euro
        $this->currencies['EUR'] = array(2, ',', '.');          //  Euro
        $this->currencies['FIM'] = array(2, ',', '.');          //  Euro
        $this->currencies['FRF'] = array(2, ',', '.');          //  Euro
        $this->currencies['GRD'] = array(2, ',', '.');          //  Euro
        $this->currencies['IEP'] = array(2, ',', '.');          //  Euro
        $this->currencies['ITL'] = array(2, ',', '.');          //  Euro
        $this->currencies['LUF'] = array(2, ',', '.');          //  Euro
        $this->currencies['NLG'] = array(2, ',', '.');          //  Euro
        $this->currencies['PTE'] = array(2, ',', '.');          //  Euro
        $this->currencies['GHC'] = array(2, '.', ',');          //  Ghana, Cedi
        $this->currencies['GIP'] = array(2, '.', ',');          //  Gibraltar Pound
        $this->currencies['GTQ'] = array(2, '.', ',');          //  Guatemala, Quetzal
        $this->currencies['HNL'] = array(2, '.', ',');          //  Honduras, Lempira
        $this->currencies['HKD'] = array(2, '.', ',');          //  Hong Kong Dollar
        $this->currencies['HUF'] = array(0, '', '.');           //  Hungary, Forint
        $this->currencies['ISK'] = array(0, '', '.');           //  Iceland Krona
        $this->currencies['INR'] = array(2, '.', ',');          //  Indian Rupee
        $this->currencies['IDR'] = array(4, ',', '.');          //  Indonesia, Rupiah
        $this->currencies['IRR'] = array(2, '.', ',');          //  Iranian Rial
        $this->currencies['JMD'] = array(2, '.', ',');          //  Jamaican Dollar
        $this->currencies['JPY'] = array(0, '', ',');           //  Japan, Yen
        $this->currencies['JOD'] = array(3, '.', ',');          //  Jordanian Dinar
        $this->currencies['KES'] = array(2, '.', ',');          //  Kenyan Shilling
        $this->currencies['KWD'] = array(3, '.', ',');          //  Kuwaiti Dinar
        $this->currencies['LVL'] = array(2, '.', ',');          //  Latvian Lats
        $this->currencies['LBP'] = array(0, '', ' ');           //  Lebanese Pound
        $this->currencies['LTL'] = array(2, ',', ' ');          //  Lithuanian Litas
        $this->currencies['MKD'] = array(2, '.', ',');          //  Macedonia, Denar
        $this->currencies['MYR'] = array(2, '.', ',');          //  Malaysian Ringgit
        $this->currencies['MTL'] = array(2, '.', ',');          //  Maltese Lira
        $this->currencies['MUR'] = array(0, '', ',');           //  Mauritius Rupee
        $this->currencies['MXN'] = array(2, '.', ',');          //  Mexican Peso
        $this->currencies['MZM'] = array(2, ',', '.');          //  Mozambique Metical
        $this->currencies['NPR'] = array(2, '.', ',');          //  Nepalese Rupee
        $this->currencies['ANG'] = array(2, '.', ',');          //  Netherlands Antillian Guilder
        $this->currencies['ILS'] = array(2, '.', ',');          //  New Israeli Shekel
        $this->currencies['TRY'] = array(2, '.', ',');          //  New Turkish Lira
        $this->currencies['NZD'] = array(2, '.', ',');          //  New Zealand Dollar
        $this->currencies['NOK'] = array(2, ',', '.');          //  Norwegian Krone
        $this->currencies['PKR'] = array(2, '.', ',');          //  Pakistan Rupee
        $this->currencies['PEN'] = array(2, '.', ',');          //  Peru, Nuevo Sol
        $this->currencies['UYU'] = array(2, ',', '.');          //  Peso Uruguayo
        $this->currencies['PHP'] = array(2, '.', ',');          //  Philippine Peso
        $this->currencies['PLN'] = array(2, '.', ' ');          //  Poland, Zloty
        $this->currencies['GBP'] = array(2, '.', ',');          //  Pound Sterling
        $this->currencies['OMR'] = array(3, '.', ',');          //  Rial Omani
        $this->currencies['RON'] = array(2, ',', '.');          //  Romania, New Leu
        $this->currencies['ROL'] = array(2, ',', '.');          //  Romania, Old Leu
        $this->currencies['RUB'] = array(2, ',', '.');          //  Russian Ruble
        $this->currencies['SAR'] = array(2, '.', ',');          //  Saudi Riyal
        $this->currencies['SGD'] = array(2, '.', ',');          //  Singapore Dollar
        $this->currencies['SKK'] = array(2, ',', ' ');          //  Slovak Koruna
        $this->currencies['SIT'] = array(2, ',', '.');          //  Slovenia, Tolar
        $this->currencies['ZAR'] = array(2, '.', ' ');          //  South Africa, Rand
        $this->currencies['KRW'] = array(0, '', ',');           //  South Korea, Won
        $this->currencies['SZL'] = array(2, '.', ', ');         //  Swaziland, Lilangeni
        $this->currencies['SEK'] = array(2, ',', '.');          //  Swedish Krona
        $this->currencies['CHF'] = array(2, '.', '\'');         //  Swiss Franc
        $this->currencies['TZS'] = array(2, '.', ',');          //  Tanzanian Shilling
        $this->currencies['THB'] = array(2, '.', ',');          //  Thailand, Baht
        $this->currencies['TOP'] = array(2, '.', ',');          //  Tonga, Paanga
        $this->currencies['AED'] = array(2, '.', ',');          //  UAE Dirham
        $this->currencies['UAH'] = array(2, ',', ' ');          //  Ukraine, Hryvnia
        $this->currencies['USD'] = array(2, '.', ',');          //  US Dollar
        $this->currencies['VUV'] = array(0, '', ',');           //  Vanuatu, Vatu
        $this->currencies['VEF'] = array(2, ',', '.');          //  Venezuela Bolivares Fuertes
        $this->currencies['VEB'] = array(2, ',', '.');          //  Venezuela, Bolivar
        $this->currencies['VND'] = array(0, '', '.');           //  Viet Nam, Dong
        $this->currencies['ZWD'] = array(2, '.', ' ');  //  Zimbabwe Dollar
    }

    function format($floatcurr, $curr = "IDR")
    {
        if ($curr == "INR") {
            return $this->formatinr((double)$floatcurr);
        } else {
            $number = number_format((double)$floatcurr, $this->currencies[$curr][0], $this->currencies[$curr][1], $this->currencies[$curr][2]);
            $number = rtrim((strpos($number,$this->currencies[$curr][1]) !== false ? rtrim($number, "0") : $number),$this->currencies[$curr][1]);
            return $number;
        }
    }
    function formatinr($input)
    {
        //CUSTOM FUNCTION TO GENERATE ##,##,###.##
        $dec = "";
        $pos = strpos($input, ".");
        if ($pos === false) {
            //no decimals
        } else {
            //decimals
            $dec = substr(round(substr($input, $pos), 2), 1);
            $input = substr($input, 0, $pos);
        }
        $num = substr($input, -3); //get the last 3 digits
        $input = substr($input, 0, -3); //omit the last 3 digits already stored in $num
        while (strlen($input) > 0) //loop the process - further get digits 2 by 2
        {
            $num = substr($input, -2) . "," . $num;
            $input = substr($input, 0, -2);
        }
        return $num . $dec;
    }

}