<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <!-- bootstrap css -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- font awesome -->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<?php
$idpesanan = "";
if(!empty($_GET['order_id']) || !empty($_GET['id'])){
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

    //var_dump($status);

    //print_r($status);
    if( $status->transaction_status == "capture" ){

        if($status->fraud_status == "accept"){
            if(!empty($_GET['order_id'])) {
                $idpesanan = $orderid_OR_transactionid;
            } else {
                $idpesanan = $status->order_id;
            }

        } else if( $status->fraud_status == "challenge" ){
            echo '
			<center>
            <div class="alert alert-warning" role="alert">Transaksi Challenge</div>
        </center>
			';
        }
    } else if( $status->transaction_status == "settlement" ){
        if(!empty($_GET['order_id'])) {
            $idpesanan = $orderid_OR_transactionid;
        } else {
            $idpesanan = $status->order_id;
        }
    } else if( $status->transaction_status == "deny" ){
        echo '
		<center>
            <div class="alert alert-warning" role="alert">Transaksi Ditolak</div>
        </center>
		';
    } else if( $status->transaction_status == "cancel" ){
        echo '
		<center>
            <div class="alert alert-warning" role="alert">Transaksi Dibatalkan</div>
        </center>
		';
    } else if( $status->transaction_status == "pending" ){
        echo '
<nav class="top-section" style="background-color: #29c325;text-align: center;font-size: 2em;color: #fff">
    <div class="container">
        <div class="row">
        
            <p style="font-size: 1em;">Anda mempunyai waktu </p>
            <p id="waktucountdown">1 hari</p>
            <script>
                // Set the date we\'re counting down to
                var satuharilagi = new Date("'.$status->transaction_time . '");
                satuharilagi.setMinutes(satuharilagi.getMinutes() + 60*24);
                var countDownDate = satuharilagi;

                // Update the count down every 1 second
                var x = setInterval(function() {

                    // Get todays date and time
                    var now = new Date().getTime();

                    // Find the distance between now an the count down date
                    var distance = countDownDate - now;

                    // Time calculations for days, hours, minutes and seconds
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Output the result in an element with id="demo"
                    document.getElementById("waktucountdown").innerHTML = 
                    hours + " jam " + minutes + " menit " + seconds + " detik ";
                    
                    if(seconds%10==0){
                    
                        $.get("https://www.nutacloud.com/checkstatuspayment.php?order_id='.
            $orderid_OR_transactionid. '",
                        function(data, status){
                            if(data == "settlement") {
                                window.location = "https://www.nutacloud.com/finishpayment.php?order_id='.
            $orderid_OR_transactionid. '";
                            }
                            /*alert("Data: " + data + "\nStatus: " + status);*/
                        });
                    } 

                    // If the count down is over, write some text
                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("waktucountdown").innerHTML = "EXPIRED";
                    }
                }, 1000);
            </script>';

        if(isset($status->va_numbers)) {
            echo '
            <p>Untuk melakukan pembayaran ke <i>virtual account</i> nomor ' . $status->va_numbers[0]->va_number .'</p>
                ';
        }
        else if(isset($status->permata_va_number)) {
            echo '
            <p>
                Untuk melakukan pembayaran ke <br>
                Bank : Permata (kode 013)<br>
                Nomor <i>virtual account</i> : ' . $status->permata_va_number .' <br>
                Sejumlah Rp. '. number_format(floatval($status->gross_amount),0,',', '.') . '
            </p>
                ';
        }
        else if($status->payment_type== "echannel") {
            echo '
            <p>Untuk melakukan pembayaran ke <br>
                Kode Perusahaan : ' . $status->biller_code . '<br>
                Kode Pembayaran : ' . $status->bill_key . '</p>
                ';
        }
        echo '
        </div>
    </div>
</nav>
		';


        if(isset($status->va_numbers)) {
            if($status->va_numbers[0]->bank == "bca") {
                echo '
<nav style="font-family: opensans; font-size: 1.3em; background-color: #ffffff;padding: 30px;">
    <div class="container">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#atm">ATM BCA</a></li>
            <li><a data-toggle="tab" href="#ibank">Klik BCA</a></li>
            <li><a data-toggle="tab" href="#mbank">m-BCA</a></li>
        </ul>

        <div class="tab-content">
            <div id="atm" class="tab-pane fade in active">
                1. Pada menu utama, pilih Transaksi Lainnya<br>
                2. Pilih Transfer<br>
                3. Pilih Ke Rek BCA Virtual Account<br>
                4. Masukkan Nomor Rekening '. $status->va_numbers[0]->va_number .' Anda lalu tekan Benar<br>
                5. Masukkan jumlah tagihan yang akan anda bayar : '.
                    number_format(floatval($status->gross_amount),0,',', '.') . '<br>
                6. Pada halaman konfirmasi transfer akan muncul detail pembayaran Anda. Jika informasi telah sesuai tekan Ya
            </div>
            <div id="ibank" class="tab-pane fade">
                1.	Pilih menu Transfer Dana<br>
                2.	Pilih Transfer ke BCA Virtual Account<br>
                3.	Masukkan nomor BCA Virtual Account '. $status->va_numbers[0]->va_number .'<br>
                4.	Jumlah yang akan ditransfer, nomor rekening dan nama merchant akan muncul di halaman konfirmasi pembayaran, jika informasi benar klik Lanjutkan<br>
                5.	Ambil BCA Token Anda dan masukkan KEYBCA Response APPLI 1 dan Klik Submit<br>
                6.	Transaksi Anda selesai
            </div>
            <div id="mbank" class="tab-pane fade">
                1.	Pilih m-Transfer<br>
                2.	Pilih Transfer<br>
                3.	Pilih BCA Virtual Account<br>
                4.	Pilih nomor rekening yang akan digunakan untuk pembayaran<br>
                5.	Masukkan nomor BCA Virtual Account '. $status->va_numbers[0]->va_number .', lalu pilih OK<br>
                6.	Nomor BCA Virtual Account dan nomor Rekening anda akan terlihat di halaman konfirmasi rekening, kemudian pilih Kirim<br>
                7.	Pilih OK pada halaman konfirmasi pembayaran<br>
                8.	Masukan jumlah nominal yang akan ditransfer ('.
                    number_format(floatval($status->gross_amount),0,',', '.') . ') dan berita kemudan pilih OK<br>
                9.	Transaksi Anda selesai
            </div>
        </div>
    </div>
</nav>';
            } else if($status->va_numbers[0]->bank == "bni") {
                echo '
<nav style="font-family: opensans; font-size: 1.3em; background-color: #ffffff;padding: 30px;">
    <div class="container">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#atm">ATM BNI</a></li>
            <li><a data-toggle="tab" href="#ibank">Internet Banking</a></li>
            <li><a data-toggle="tab" href="#mbank">Mobile Banking</a></li>
        </ul>

        <div class="tab-content">
            <div id="atm" class="tab-pane fade in active">
                1.		Pada menu utama, pilih Menu Lainnya<br>
                2.		Pilih Transfer<br>
                3.		Pilih Rekening Tabungan<br>
                4.		Pilih Ke Rekening BNI<br>
                5.		Masukkan nomor virtual account '. $status->va_numbers[0]->va_number .' dan pilih Tekan Jika Benar<br>
                6.		Masukkan jumlah tagihan '.
                    number_format(floatval($status->gross_amount),0,',', '.') .
                    '. Pembayaran dengan jumlah tidak sesuai akan otomatis ditolak.<br>
                7.		Jumlah yang dibayarkan, nomor rekening dan nama Merchant akan ditampilkan. Jika informasi telah sesuai, tekan Ya<br>
                8.		Transaksi Anda sudah selesai
            </div>
            <div id="ibank" class="tab-pane fade">
                1.	Ketik alamat https://ibank.bni.co.id kemudian klik Masuk<br>
                2.	Silakan masukkan User ID dan Password<br>
                3.	Klik menu Transfer kemudian pilih Tambah Rekening Favorit<br>
                4.	Masukkan nama, nomor rekening '. $status->va_numbers[0]->va_number .', dan email, lalu klik Lanjut<br>
                5.	Masukkan Kode Otentikasi dari token Anda dan klik Lanjut<br>
                6.	Kembali ke menu utama dan pilih Transfer lalu Transfer Antar Rekening BNI<br>
                7.	Pilih rekening yang telah Anda favoritkan sebelumnya di Rekening Tujuan lalu lanjutkan pengisian, dan tekan Lanjut<br>
                8.	Pastikan detail transaksi Anda benar, lalu masukkan Kode Otentikasi dan tekan Lanjut
            </div>
            <div id="mbank" class="tab-pane fade">
                1.	Buka aplikasi BNI Mobile Banking dan login<br>
                2.	Pilih Transfer<br>
                3.	Pilih Antar Rekening BNI<br>
                4.	Pilih Input Rekening Baru<br>
                5.	Masukkan nomor rekening '. $status->va_numbers[0]->va_number .', lalu tekan Lanjut<br>
                6.	Masukkan Password Transaksi lalu tekan Lanjut
            </div>
        </div>
    </div>
</nav>';
            }
        }
        else if(isset($status->permata_va_number)) {
            echo '
<nav style="font-family: opensans; font-size: 1.3em; background-color: #ffffff;padding: 30px;">
    <div class="container">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#atmp">ATM Permata</a></li>
            <li><a data-toggle="tab" href="#atm">ATM Bersama</a></li>
            <li><a data-toggle="tab" href="#ibank">Prima</a></li>
            <li><a data-toggle="tab" href="#mbank">Alto</a></li>
        </ul>

        <div class="tab-content">
            <div id="atmp" class="tab-pane fade in active">
                <br>
                1.		Pada menu utama, pilih Transaksi Lainnya<br>
                2.		Pilih Pembayaran<br>
                3.		Pilih Pembayaran Lainnya<br>
                4.		Pilih Virtual Account<br>
                5.		Masukkan 16 digit No. Virtual Account ' . $status->permata_va_number . ', lalu tekan Benar<br>
                6.		Pada halaman konfirmasi transfer akan muncul jumlah yang dibayarkan, nomor rekening, & nama Merchant. Jika informasi telah sesuai tekan Benar<br>
                7.		Pilih rekening pembayaran Anda dan tekan Benar<br>
            </div>
            <div id="atm" class="tab-pane fade">
                1.		Pada menu utama, pilih Transaksi Lainnya<br>
                2.		Pilih Transfer<br>
                3.		Pilih Antar Bank Online<br>
                4.		Masukkan 013 dan 16 digit No. Rekening : 013' . $status->permata_va_number . ' <br>
                5.		Masukkan jumlah tagihan yang akan Anda bayar secara lengkap, Pembayaran dengan jumlah tidak sesuai akan otomatis ditolak<br>
                6.		Kosongkan No. Referensi, lalu tekan Benar<br>
                7.		Pada halaman konfirmasi transfer akan muncul jumlah yang dibayarkan, nomor rekening & nama Merchant. Jika informasi telah sesuai tekan Benar
            </div>
            <div id="ibank" class="tab-pane fade">
                1.		Pada menu utama, pilih Transaksi Lainnya<br>
                2.		Pilih Transfer<br>
                3.		Pilih Rek Bank Lain<br>
                4.		Masukkan nomor 013 (kode bank Permata) lalu tekan Benar<br>
                5.		Masukkan jumlah tagihan yang akan Anda bayar secara lengkap, pembayaran dengan jumlah tidak sesuai akan otomatis ditolak<br>
                6.		Masukkan 16 digit No. Rekening pembayaran (' . $status->permata_va_number . ') lalu tekan Benar<br>
                7.		Pada halaman konfirmasi transfer akan muncul jumlah yang dibayarkan, nomor rekening & nama Merchant. Jika informasi telah sesuai tekan Benar
            </div>
            <div id="mbank" class="tab-pane fade">
                1.		Pada menu utama, pilih Transaksi Lainnya<br>
                2.		Pilih Transaksi Pembayaran<br>
                3.		Pilih Lain-lain<br>
                4.		Pilih Pembayaran Virtual Account<br>
                5.		Masukkan 16 digit No. Rekening pembayaran (' . $status->permata_va_number . ') lalu tekan Benar<br>
                6.		Pada halaman konfirmasi transfer akan muncul jumlah yang dibayarkan, nomor rekening & nama Merchant. Jika informasi telah sesuai tekan Benar<br>
                7.		Pilih rekening pembayaran Anda dan tekan Benar
            </div>
        </div>
    </div>
</nav>
                ';
        }
        else if($status->payment_type== "echannel") {
            echo '
<nav style="font-family: opensans; font-size: 1.3em; background-color: #ffffff;padding: 30px;">
    <div class="container">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#atm">ATM Mandiri</a></li>
            <li><a data-toggle="tab" href="#ibank">Internet Banking</a></li>
        </ul>

        <div class="tab-content">
            <div id="atm" class="tab-pane fade in active">
                1.		Pada menu utama, pilih Bayar/Beli<br>
                2.		Pilih Lainnya<br>
                3.		Pilih Multi Payment<br>
                4.		Masukkan ' . $status->biller_code . ' (kode perusahaan Midtrans) lalu tekan Benar<br>
                5.		Masukkan Kode Pembayaran Anda ' . $status->bill_key . ' lalu tekan Benar<br>
                6.		Pada halaman konfirmasi akan muncul detail pembayaran Anda. Jika informasi telah sesuai tekan Ya
            </div>
            <div id="ibank" class="tab-pane fade">
                1.	Login ke Internet Banking Mandiri (https://ib.bankmandiri.co.id/)<br>
                2.	Pada menu utama, pilih Bayar, lalu pilih Multi Payment<br>
                3.	Pilih akun Anda di Dari Rekening, kemudian di Penyedia Jasa pilih Midtrans<br>
                4.	Masukkan Kode Pembayaran Anda ' . $status->bill_key . ' dan klik Lanjutkan<br>
                5.	Konfirmasi pembayaran Anda menggunakan Mandiri Token
            </div>
        </div>
    </div>
</nav>
                ';
        }
    }
} else {
    echo '<meta http-equiv="refresh" content="0; url=https://www.nutacloud.com" />';
}
if($idpesanan !== "") {
    $url = 'http://visitnutapos.com/aktivasi/KonfirmasiPembayaranMidtrans';

    $data = array( "id" => $idpesanan);
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = json_decode(file_get_contents($url, false, $context));
//		$pesan = "Transaksi Sukses";
    if ($result === FALSE) {
        /* Handle error */
        $pesan = $pesan."namun gagal aktivasi, hubungi CS : 085731820966 / support@nutapos.com";
    }

    $sumber = "afiliasi";
    if ($result->status == "OK") {
        $aktifsampai = $result->aktifsampai;
        $sumber = $result->sumber;
        $day2 = date('j', strtotime($aktifsampai));
        $month2 = date('m', strtotime($aktifsampai));
        $year2 = date('Y', strtotime($aktifsampai));
        $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $strAktifSampai = $day2 . " " . $months[$month2 - 1] . " " . $year2;
        if(substr($sumber,0,8) == "afiliasi") {
            $pesan = $pesan . "Aktivasi berhasil, Nuta anda aktif sampai " . $strAktifSampai;
        } else {
            $pesan = $pesan . "Nuta anda aktif sampai " . $strAktifSampai;
        }

    } else {
        $pesan = $pesan."namun gagal aktivasi, hubungi CS : 085731820966 / support@nutapos.com";
    }

    $namaperusahaan="<i>Contoh Perusahaan</i>";
    $username="<i>Contoh Username</i>";
    $password="<i>Contoh Password</i>";
    $outlet="<i>Contoh Outlet</i>";

    $url = 'http://api.nutacloud.com/registration/infologinwithexistingaccount';

    //$data = array("i" => $result->deviceid, "e" => $aktifsampai);
    $data = array("idpesanan" => $idpesanan);
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
        //$pesan = $pesan."namun gagal aktivasi, hubungi CS : 085731820966 / support@nutapos.com";
    }

    if ($result->status == "OK") {
//        var_dump($result);
        $namaperusahaan=$result->message->NamaPerusahaan;
        $username=$result->message->Username;
        $password=$result->message->Password;
        $outlet=$result->message->NamaOutlet;
    }


    if($sumber == "http://dev.nutacloud.com/") {
        echo '<nav class="top-section" style="background-color: #fff;text-align: center;font-size: 2em;color: #111111">
    <div class="container">
        <div class="row">
            <h3 style="font-size: 1.2em;line-height: 1.5em;">Terima Kasih<br>Pembayaran Anda telah Sukses</h3>
            ' . $pesan . '<br><br>
            <a href="http://dev.nutacloud.com/activation" class="btn btn-primary" style="font-size: 1.1em;">Kembali ke nutacloud</a>
            <br>
        </div>
    </div>
</nav>';
    } else if($sumber == "https://www.nutacloud.com/") {
        echo '<nav class="top-section" style="background-color: #fff;text-align: center;font-size: 2em;color: #111111">
    <div class="container">
        <div class="row">
            <h3 style="font-size: 1.2em;line-height: 1.5em;">Terima Kasih<br>Pembayaran Anda telah Sukses</h3>
            ' . $pesan . '<br><br>
            <a href="https://www.nutacloud.com/activation" style="font-size: 1.1em;">Kembali ke nutacloud</a>
            <br>
        </div>
    </div>
</nav>';
    } else {
        echo '<nav class="top-section" style="background-color: #29c325;text-align: center;font-size: 2em;color: #fff">
    <div class="container">
        <div class="row">
            <h3 style="font-size: 1.2em;line-height: 1.5em;">Terima Kasih<br>Pembayaran Anda telah Sukses</h3>
            ' . $pesan . '
            <p style="font-size: 1.1em;">Download Nuta di Playstore dari tablet Anda</p>
            <img src="assets/images/googleplay.png"><br><br>
        </div>
    </div>
</nav>
<nav style="background-color: #29c325;text-align: center;padding: 30px;font-size: 2em;font-style: italic;">
    <div class="container">
                        <span style="color: #fff">
                        Setelah berhasil install :<br><br>
                        Masuk dengan Akun yang sudah Anda daftarkan
                        </span><br>
        <img style="width: 80%;" src="assets/images/layarregistrasi1.png"><br><br>
        <span style="color: #fff">
                        Masukkan Nama Perusahaan : ' . $namaperusahaan. '<br>Tekan Lanjut
                        </span><br>
        <img style="width: 80%;" src="assets/images/layarlogin1.png"><br><br>
        <span style="color: #fff">
	        			Masukkan nama user : ' . $username. ' <br>   password : ' . $password. '<br>Tekan Lanjut<br>
                        </span>
        <img style="width: 80%;" src="assets/images/layarlogin2.png"><br><br>
        <span style="color: #fff">
	        			Pilih Outlet ' . $outlet. ', lalu tekan login<br>
                        </span>
        <img style="width: 80%;" src="assets/images/layarlogin3.png"><br><br>
    </div>
</nav>';
    }
//	echo '<center><div class="alert alert-success" role="alert">'. $pesan .'</div></center>';
}
?>


<!-- jquery-js -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- bootstrap js -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


</body>
</html>
