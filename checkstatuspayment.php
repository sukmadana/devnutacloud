<?php

require_once(dirname(__FILE__) . '/application/third_party/veritrans-php-master/Veritrans.php');
Veritrans_Config::$isProduction = true;
Veritrans_Config::$serverKey = 'VT-server-LqN415PzmdG_XVQJmbGnT67N';

$orderid_OR_transactionid = "";
if(!empty($_GET['order_id'])) {
    $orderid_OR_transactionid = $_GET['order_id'];
} else {
    $orderid_OR_transactionid = $_GET['id'];
}

$status = Veritrans_Transaction::status($orderid_OR_transactionid);

echo $status->transaction_status;
?>