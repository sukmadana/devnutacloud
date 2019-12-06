<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 02/12/2015
 * Time: 11:39
 */
if (!function_exists('ifNotAuthenticatedRedirectToLogin')) {
    function ifNotAuthenticatedRedirectToLogin()
    {
        $CI =& get_instance();
        $id = $CI->input->get('id');
        if (isNotEmpty($id)) {
            return $id;
        }
        $CI->load->library('session');
        $sessionDevid = $CI->session->userdata('id');

        if (!isNotEmpty($sessionDevid)) {
            redirect(base_url() . 'authentication/loginv2');
        }
    }
}
if (!function_exists('isNotEmpty')) {
    function isNotEmpty($value)
    {
        return isset($value) && trim($value) != "";
    }
}
if (!function_exists("getLoggedInUsername")) {
    function getLoggedInUsername()
    {
        $CI =& get_instance();
        $username = $CI->input->get('username');
        if (isNotEmpty($username)) {
            return $username;
        }
        $CI->load->library('session');
        return $CI->session->userdata('username');
    }
}
if (!function_exists("getLoggedInUserID")) {
    function getLoggedInUserID()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        return $CI->session->userdata('id');
    }
}
if (!function_exists("getLoggedInNamaPerusahaan")) {
    function getLoggedInNamaPerusahaan()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        return $CI->session->userdata('namaperusahaan');
    }
}
if (!function_exists("getLoggedInRegisterWithDeviceID")) {
    function getLoggedInRegisterWithDeviceID()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        return $CI->session->userdata('registerwithdeviceid');
    }
}
if (!function_exists("getLoggedInMenuPerusahaanVisibility")) {
    function getLoggedInMenuPerusahaanVisibility()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        return $CI->session->userdata('ismenuperusahaanvisible');
    }
}
if (!function_exists("getFoto")) {
    function getFoto()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        return $CI->session->userdata('foto');
    }
}
if (!function_exists("preventXSS")) {
    function preventXSS($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists("getTableSale")) {
    function getTableSale()
    {
        $CI =& get_instance();
        $id = $CI->input->get('id');
        if (isNotEmpty($id)) {
            return $id;
        }
        $CI->load->library('session');
        return $CI->session->userdata('nama_tabel_sale');
    }
}
if (!function_exists("getTableSaleDetail")) {
    function getTableSaleDetail()
    {
        $CI =& get_instance();
        $id = $CI->input->get('id');
        if (isNotEmpty($id)) {
            return $id;
        }
        $CI->load->library('session');
        return $CI->session->userdata('nama_tabel_sale_detail');
    }
}
if (!function_exists("getTableSaleDetailIngredients")) {
    function getTableSaleDetailIngredients()
    {
        $CI =& get_instance();
        $id = $CI->input->get('id');
        if (isNotEmpty($id)) {
            return $id;
        }
        $CI->load->library('session');
        return $CI->session->userdata('nama_tabel_sale_detail_bahan');
    }
}
if (!function_exists("getPerusahaanNo")) {
    function getPerusahaanNo()
    {
        $CI =& get_instance();
        $id = $CI->input->get('id');
        if (isNotEmpty($id)) {
            return $id;
        }
        $CI->load->library('session');
        return $CI->session->userdata('nomorperusahaan');
    }
}

if (!function_exists("CamelToWords")) {
    function CamelToWords($str)
    {
        $matches = array();
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', $str, $matches);

        $retval = '';
        if (count($matches) > 0) {
            foreach ($matches[0] as $match) {
                $retval .= ' ' . $match;
            }
        } else {
            $retval = $str;
        }
        return trim($retval);
    }
}
if (!function_exists("formatcurrency")) {
    function formatcurrency($floatcurr, $curr = "IDR")
    {
        $currencies['ARS'] = array(2, ',', '.');          //  Argentine Peso
        $currencies['AMD'] = array(2, '.', ',');          //  Armenian Dram
        $currencies['AWG'] = array(2, '.', ',');          //  Aruban Guilder
        $currencies['AUD'] = array(2, '.', ' ');          //  Australian Dollar
        $currencies['BSD'] = array(2, '.', ',');          //  Bahamian Dollar
        $currencies['BHD'] = array(3, '.', ',');          //  Bahraini Dinar
        $currencies['BDT'] = array(2, '.', ',');          //  Bangladesh, Taka
        $currencies['BZD'] = array(2, '.', ',');          //  Belize Dollar
        $currencies['BMD'] = array(2, '.', ',');          //  Bermudian Dollar
        $currencies['BOB'] = array(2, '.', ',');          //  Bolivia, Boliviano
        $currencies['BAM'] = array(2, '.', ',');          //  Bosnia and Herzegovina, Convertible Marks
        $currencies['BWP'] = array(2, '.', ',');          //  Botswana, Pula
        $currencies['BRL'] = array(2, ',', '.');          //  Brazilian Real
        $currencies['BND'] = array(2, '.', ',');          //  Brunei Dollar
        $currencies['CAD'] = array(2, '.', ',');          //  Canadian Dollar
        $currencies['KYD'] = array(2, '.', ',');          //  Cayman Islands Dollar
        $currencies['CLP'] = array(0, '', '.');           //  Chilean Peso
        $currencies['CNY'] = array(2, '.', ',');          //  China Yuan Renminbi
        $currencies['COP'] = array(2, ',', '.');          //  Colombian Peso
        $currencies['CRC'] = array(2, ',', '.');          //  Costa Rican Colon
        $currencies['HRK'] = array(2, ',', '.');          //  Croatian Kuna
        $currencies['CUC'] = array(2, '.', ',');          //  Cuban Convertible Peso
        $currencies['CUP'] = array(2, '.', ',');          //  Cuban Peso
        $currencies['CYP'] = array(2, '.', ',');          //  Cyprus Pound
        $currencies['CZK'] = array(2, '.', ',');          //  Czech Koruna
        $currencies['DKK'] = array(2, ',', '.');          //  Danish Krone
        $currencies['DOP'] = array(2, '.', ',');          //  Dominican Peso
        $currencies['XCD'] = array(2, '.', ',');          //  East Caribbean Dollar
        $currencies['EGP'] = array(2, '.', ',');          //  Egyptian Pound
        $currencies['SVC'] = array(2, '.', ',');          //  El Salvador Colon
        $currencies['ATS'] = array(2, ',', '.');          //  Euro
        $currencies['BEF'] = array(2, ',', '.');          //  Euro
        $currencies['DEM'] = array(2, ',', '.');          //  Euro
        $currencies['EEK'] = array(2, ',', '.');          //  Euro
        $currencies['ESP'] = array(2, ',', '.');          //  Euro
        $currencies['EUR'] = array(2, ',', '.');          //  Euro
        $currencies['FIM'] = array(2, ',', '.');          //  Euro
        $currencies['FRF'] = array(2, ',', '.');          //  Euro
        $currencies['GRD'] = array(2, ',', '.');          //  Euro
        $currencies['IEP'] = array(2, ',', '.');          //  Euro
        $currencies['ITL'] = array(2, ',', '.');          //  Euro
        $currencies['LUF'] = array(2, ',', '.');          //  Euro
        $currencies['NLG'] = array(2, ',', '.');          //  Euro
        $currencies['PTE'] = array(2, ',', '.');          //  Euro
        $currencies['GHC'] = array(2, '.', ',');          //  Ghana, Cedi
        $currencies['GIP'] = array(2, '.', ',');          //  Gibraltar Pound
        $currencies['GTQ'] = array(2, '.', ',');          //  Guatemala, Quetzal
        $currencies['HNL'] = array(2, '.', ',');          //  Honduras, Lempira
        $currencies['HKD'] = array(2, '.', ',');          //  Hong Kong Dollar
        $currencies['HUF'] = array(0, '', '.');           //  Hungary, Forint
        $currencies['ISK'] = array(0, '', '.');           //  Iceland Krona
        $currencies['INR'] = array(2, '.', ',');          //  Indian Rupee
        $currencies['IDR'] = array(0, ',', '.');          //  Indonesia, Rupiah
        $currencies['IRR'] = array(2, '.', ',');          //  Iranian Rial
        $currencies['JMD'] = array(2, '.', ',');          //  Jamaican Dollar
        $currencies['JPY'] = array(0, '', ',');           //  Japan, Yen
        $currencies['JOD'] = array(3, '.', ',');          //  Jordanian Dinar
        $currencies['KES'] = array(2, '.', ',');          //  Kenyan Shilling
        $currencies['KWD'] = array(3, '.', ',');          //  Kuwaiti Dinar
        $currencies['LVL'] = array(2, '.', ',');          //  Latvian Lats
        $currencies['LBP'] = array(0, '', ' ');           //  Lebanese Pound
        $currencies['LTL'] = array(2, ',', ' ');          //  Lithuanian Litas
        $currencies['MKD'] = array(2, '.', ',');          //  Macedonia, Denar
        $currencies['MYR'] = array(2, '.', ',');          //  Malaysian Ringgit
        $currencies['MTL'] = array(2, '.', ',');          //  Maltese Lira
        $currencies['MUR'] = array(0, '', ',');           //  Mauritius Rupee
        $currencies['MXN'] = array(2, '.', ',');          //  Mexican Peso
        $currencies['MZM'] = array(2, ',', '.');          //  Mozambique Metical
        $currencies['NPR'] = array(2, '.', ',');          //  Nepalese Rupee
        $currencies['ANG'] = array(2, '.', ',');          //  Netherlands Antillian Guilder
        $currencies['ILS'] = array(2, '.', ',');          //  New Israeli Shekel
        $currencies['TRY'] = array(2, '.', ',');          //  New Turkish Lira
        $currencies['NZD'] = array(2, '.', ',');          //  New Zealand Dollar
        $currencies['NOK'] = array(2, ',', '.');          //  Norwegian Krone
        $currencies['PKR'] = array(2, '.', ',');          //  Pakistan Rupee
        $currencies['PEN'] = array(2, '.', ',');          //  Peru, Nuevo Sol
        $currencies['UYU'] = array(2, ',', '.');          //  Peso Uruguayo
        $currencies['PHP'] = array(2, '.', ',');          //  Philippine Peso
        $currencies['PLN'] = array(2, '.', ' ');          //  Poland, Zloty
        $currencies['GBP'] = array(2, '.', ',');          //  Pound Sterling
        $currencies['OMR'] = array(3, '.', ',');          //  Rial Omani
        $currencies['RON'] = array(2, ',', '.');          //  Romania, New Leu
        $currencies['ROL'] = array(2, ',', '.');          //  Romania, Old Leu
        $currencies['RUB'] = array(2, ',', '.');          //  Russian Ruble
        $currencies['SAR'] = array(2, '.', ',');          //  Saudi Riyal
        $currencies['SGD'] = array(2, '.', ',');          //  Singapore Dollar
        $currencies['SKK'] = array(2, ',', ' ');          //  Slovak Koruna
        $currencies['SIT'] = array(2, ',', '.');          //  Slovenia, Tolar
        $currencies['ZAR'] = array(2, '.', ' ');          //  South Africa, Rand
        $currencies['KRW'] = array(0, '', ',');           //  South Korea, Won
        $currencies['SZL'] = array(2, '.', ', ');         //  Swaziland, Lilangeni
        $currencies['SEK'] = array(2, ',', '.');          //  Swedish Krona
        $currencies['CHF'] = array(2, '.', '\'');         //  Swiss Franc
        $currencies['TZS'] = array(2, '.', ',');          //  Tanzanian Shilling
        $currencies['THB'] = array(2, '.', ',');          //  Thailand, Baht
        $currencies['TOP'] = array(2, '.', ',');          //  Tonga, Paanga
        $currencies['AED'] = array(2, '.', ',');          //  UAE Dirham
        $currencies['UAH'] = array(2, ',', ' ');          //  Ukraine, Hryvnia
        $currencies['USD'] = array(2, '.', ',');          //  US Dollar
        $currencies['VUV'] = array(0, '', ',');           //  Vanuatu, Vatu
        $currencies['VEF'] = array(2, ',', '.');          //  Venezuela Bolivares Fuertes
        $currencies['VEB'] = array(2, ',', '.');          //  Venezuela, Bolivar
        $currencies['VND'] = array(0, '', '.');           //  Viet Nam, Dong
        $currencies['ZWD'] = array(2, '.', ' ');          //  Zimbabwe Dollar

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


        if ($curr == "INR") {
            return formatinr($floatcurr);
        } else {
            return number_format($floatcurr, $currencies[$curr][0], $currencies[$curr][1], $currencies[$curr][2]);
        }
    }
}

if (!function_exists("formatdateindonesia")) {
    function formatdateindonesia($yyymmdd)
    {
        $bulan['01'] = 'Januari';
        $bulan['02'] = 'Februari';
        $bulan['03'] = 'Maret';
        $bulan['04'] = 'April';
        $bulan['05'] = 'Mei';
        $bulan['06'] = 'Juni';
        $bulan['07'] = 'Juli';
        $bulan['08'] = 'Agustus';
        $bulan['09'] = 'September';
        $bulan['10'] = 'Oktober';
        $bulan['11'] = 'November';
        $bulan['12'] = 'Desember';
        $date = explode(' ', $yyymmdd);
        $_tmp = explode('-', $date[0]);
        $tahun = $_tmp[0];
        $bulan = $bulan[$_tmp[1]];
        $tanggal = $_tmp[2];
        if(substr($tanggal,0,1) == "0") {
            $tanggal = substr($tanggal,1,1);
        }
        return $tanggal . ' ' . $bulan . ' ' . $tahun;
    }
}

if (!function_exists("isAccountExpired")) {
    function isAccountExpired()
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $isExpired = $CI->session->userdata('is_expired');
        return $isExpired;
    }
}

// is active menu
if (!function_exists("is_active")) {
    function is_active( $menu, $menu_name )
    {
        if ( $menu == $menu_name  ) {
            echo "active";
        }
    }
	// set jadi null jika tidak ada value
	function set_null($str,$isstring=true) {
		$str = trim($str);

		if(strlen($str) == 0) {
			if(empty($isstring))
				return null;
			else
				return 'null';
		}
			return $str;
	}
	// get number format
	function format_number($num,$dec=null,$ceknull=false) {
		if($ceknull and !isset($num))
			return null;

		list(,$left) = explode('.',strval($num));

		$left = (float)$left;
		if(empty($left))
			$len = 0;
		else
			$len = strlen($left);

		if(!isset($dec))
			$dec = $len;

		return number_format($num,$dec,',','.');
	}
}
