<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//echo $heading; 
$a = str_replace("<p>", "", $message);
$b = explode("</p>", $a);

echo $b[0] . "\n" . $b[1];
