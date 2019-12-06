<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . "third_party/veritrans-php-master/Veritrans.php";

/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 03/06/17
 * Time: 11:34
 */
class Payment
{
    function __construct()
    {
        //Set Your server key
        Veritrans_Config::$serverKey = "VT-server-LqN415PzmdG_XVQJmbGnT67N";

// Uncomment for production environment
        Veritrans_Config::$isProduction = true;
        
        Veritrans_Config::$isSanitized = true;
        Veritrans_Config::$is3ds = true;

    }

    function get_snap_token($order_id, $amount)
    {
        $transaction = array(
            'transaction_details' => array(
                'order_id' => strval($order_id),
                'gross_amount' => $amount // no decimal allowed
            )
        );

        $snapToken = Veritrans_Snap::getSnapToken($transaction);
        return $snapToken;
    }

    function generate_order_id()
    {
        return $this->generate_unique_id(50);
    }


    protected function generate_unique_id($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max - 1)];
        }

        return $token;
    }

    protected function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int)($log / 8) + 1; // length in bytes
        $bits = (int)$log + 1; // length in bits
        $filter = (int)(1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }
}
