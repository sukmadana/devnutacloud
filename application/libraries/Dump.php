<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . "/third_party/kint-master/Kint.class.php";

/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 23/01/2016
 * Time: 15:20
 */
class Dump
{

    public static function me($it_is, $message = null)
    {
        if ($message != null) {
            echo "<div class='kint'>" . $message . "</div>";
        }
        Kint::dump($it_is);
    }

}