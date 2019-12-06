<?php

require_once(dirname(__FILE__) . '/application/third_party/veritrans-php-master/Veritrans.php');
Veritrans_Config::$isProduction = true;
Veritrans_Config::$serverKey = 'VT-server-LqN415PzmdG_XVQJmbGnT67N';
try {
    $notif = new Veritrans_Notification();

    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $order_id = $notif->order_id;
    $fraud = $notif->fraud_status;
}
catch (Exception $e) {
    //echo $e->getMessage() ."\n<br>";
    //echo $e->getTraceAsString() ."\n<br>";
    $transaction = 'pending';
    $type = '';
    $order_id = 'PP20000';
    $fraud = '';
}


$entityBody = file_get_contents('php://input');
//var_dump($_POST);
//var_dump($entityBody);

// foreach ($_POST as $key => $value)
//   echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

if ($transaction == 'capture') {
    // For credit card transaction, we need to check whether transaction is challenge by FDS or not
    if ($type == 'credit_card'){
        if($fraud == 'challenge'){
            // TODO set payment status in merchant's database to 'Challenge by FDS'
            // TODO merchant should decide whether this transaction is authorized or not in MAP
            echo "Transaction order_id: " . $order_id ." is challenged by FDS";
        }
        else {
            $url = 'http://visitnutapos.com/aktivasi/KonfirmasiPembayaranMidtrans';

            $data = array( "id" => $order_id);
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $result = json_decode(file_get_contents($url, false, $context));
            $pesan = "Transaksi Sukses";
            if ($result === FALSE) {
                /* Handle error */
                $pesan = "Transaksi Gagal";
            }

            if ($result->status == "OK") {
                $aktifsampai = $result->aktifsampai;

                $url = 'http://api.nutacloud.com/registration/afterpayment';

                $data = array("i" => $result->deviceid, "e" => $aktifsampai);
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data)
                    )
                );
                $context  = stream_context_create($options);
                $result = json_decode(file_get_contents($url, false, $context));

                if ($result === FALSE) { /* Handle error */
                    $pesan = $pesan."<br>gagal update masa aktif";
                }

                if ($result->status == "OK") {
                    $pesan = $pesan."<br>berhasil update masa aktif hingga " . $aktifsampai;
                } else {
                    $pesan = $pesan."<br>gagal update masa aktif";

                }

            }

            echo '<center><div class="alert alert-success" role="alert">'. $pesan .'</div></center>';
        }
    }
}
else if ($transaction == 'settlement'){
    // TODO set payment status in merchant's database to 'Settlement'
    $url = 'http://visitnutapos.com/aktivasi/KonfirmasiPembayaranMidtrans';

    $data = array( "id" => $order_id);
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = json_decode(file_get_contents($url, false, $context));
    $pesan = "Transaksi Sukses";
    if ($result === FALSE) {
        /* Handle error */
        $pesan = "Transaksi Gagal";
    }

    if ($result->status == "OK") {
        $aktifsampai = $result->aktifsampai;

        if($result->sumber == "http://dev.nutacloud.com/") {
            $url = 'http://api.dev.nutacloud.com/registration/afterpayment';
        } else if($result->sumber == "https://www.nutacloud.com/") {
            $url = 'http://api.nutacloud.com/registration/afterpayment';
        } else {
            $url = 'http://api.staging.nutacloud.com/registration/afterpayment';
        }

        $data = array("i" => $result->deviceid, "e" => $aktifsampai);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = json_decode(file_get_contents($url, false, $context));

        if ($result === FALSE) { /* Handle error */
            $pesan = $pesan."<br>gagal update masa aktif";
        }

        if ($result->status == "OK") {
            $pesan = $pesan."<br>berhasil update masa aktif hingga " . $aktifsampai;
        } else {
            $pesan = $pesan."<br>gagal update masa aktif";
        }

    }

    echo '<center><div class="alert alert-success" role="alert">'. $pesan .'</div></center>';
}
else if($transaction == 'pending'){
    // TODO set payment status in merchant's database to 'Pending'
    echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
}
else if ($transaction == 'deny') {
    // TODO set payment status in merchant's database to 'Denied'
    echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
}
else if ($transaction == 'expire') {
    // TODO set payment status in merchant's database to 'expire'
    echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
}
else if ($transaction == 'cancel') {
    // TODO set payment status in merchant's database to 'Denied'
    echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
}
?>