<?php
/*
 * This file created by Em Husnan
 * Copyright 2015
 */
$d = getLoggedInNamaPerusahaan();
$n = getLoggedInUsername();
if (trim($n) === '') {
    $n = 'Single Outlet';
}
if ($d == "Individual") {
    $d = "Single Outlet";
}
