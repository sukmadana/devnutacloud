<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Firebasemodel extends CI_Model
{
    var $_tableName = "firebase_device";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function get_all_outlet()
    {
        $query = $this->db->get($this->_tableName);
        return $query->result();
    }


    protected function get_token($outlet)
    {
        //$query = $this->db->get_where($this->_tableName, array('outlet' => $outlet));
        $query = $this->db->query("SELECT fd.* FROM firebase_device fd
INNER JOIN device_app da
ON da.OutletID=fd.outlet AND da.DeviceNo=fd.DeviceNo
WHERE fd.outlet=" . $outlet . " AND da.IsActive=1");
        return $query->result();
    }

    protected function send_data($to, $table_data, $todeviceno, $outlet, $tableid, $deviceno, $perusahaanno, $fromdevice)
    {
        if (!defined('API_ACCESS_KEY')) {
            if (ENVIRONMENT == 'development') {
                define('API_ACCESS_KEY', 'AAAAoIQmavg:APA91bGuOPWvccgZhPxdeLn-uWbZazZD3EicE1Ic2uiNAI2n7J5I-4UlI8UgJzrAfT8e2NeDgvx6oA310EK9z-Wyqla0CKEFpsU3ygr6W0Mi7hgJ7pxG4UEHw0ecL2Iv4CvYsd05zvH_');
            } else if (ENVIRONMENT == 'testing') {
                define('API_ACCESS_KEY', 'AAAA58dqaj0:APA91bEKLbLOzp2gi2ab6AvilLk7PBBwoElIplPCJwaWzG1LyYSmj6IBcX7TsPYd6peFuB7hKr87O8vD4iw2-ETRuqL8ZAmPFbEm-LM5a3yUCqOGEeEIFInP_zznePFSvmAA9anZnVtW');
            } else if (ENVIRONMENT == 'production') {
                define('API_ACCESS_KEY', 'AAAANlCjvuA:APA91bHjla3fjXvX9AZPc-nZgxddtJvIokI_c_x-7HXN0NC7msWScqUfH1kTXHWxEpzxJIzA3hOOwJZgd-c83C7p-4462A8hDL7wMKgT0QLDHaaXSMeeA5SJnxAPpQFcKZ4-Llqr0Wfy');
            }
        }

        $data = $table_data['column'];
        $data = array_merge($data, array("firebasetitle" => $table_data['table'], "firebasebody" => "dari nutacloud"));

        $fields = array
        (
            'to' => $to,
            'data' => $data,
            //'priority' => 'high',
        );

//        $jsonfields = json_encode( $fields );
//        log_message('error', var_export($jsonfields, true));


        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        #Send Reponse To FireBase Server
            log_message('error', 'mulai send fcm' . $outlet . "." . $todeviceno . " " . microtime());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
            log_message('error', 'selesai send fcm' . $outlet . "." . $todeviceno . " " . microtime());

        #Echo Result Of FireBase Server
        $debug = array(
            "fields" => $fields,
            "result_firebase" => $result,
        );

        //insert log firebase
        $result_firebase_array = json_decode($result);

        $data_message_id = '';


        $multicastid = "";
        $errorMsg = "";
        if (isset($result_firebase_array) && isset($result_firebase_array->multicast_id)) {
            $multicastid = $result_firebase_array->multicast_id;
            foreach ($result_firebase_array->results as $key => $value) {
                if(isset($value->message_id)) {
                    $data_message_id = $value->message_id;
                }
                if (isset ($value->error)) {
                    $errorMsg = $value->error;
                }
            }
        }
        if($data_message_id == "" || $data_message_id == null) {
            if ($errorMsg == "NotRegistered") {
                $this->db->insert('log_firebase_notsent', array(
                        'table' => $table_data['table'],
                        'multicast_id' => $multicastid,
                        'outlet' => $outlet,
                        'token' => $to,
                        'result' => var_export($result_firebase_array, true),
                        'TableID' => $tableid,
                        'DeviceNo' => $deviceno,
                        'PerusahaanNo' => $perusahaanno,
                        'FromDevice' => $fromdevice,
                        'ToDevice' => $todeviceno,
                        'Message' => json_encode($data)
                    )
                );
                $this->db->query("UPDATE device_app SET IsActive=0 WHERE OutletID=".$outlet." AND DeviceNo=".$todeviceno);
            } else {
                $this->db->insert('log_firebase', array(
                        'table' => $table_data['table'],
                        'multicast_id' => $multicastid,
                        'outlet' => $outlet,
                        'token' => $to,
                        'result' => var_export($result_firebase_array, true),
                        'TableID' => $tableid,
                        'DeviceNo' => $deviceno,
                        'PerusahaanNo' => $perusahaanno,
                        'FromDevice' => $fromdevice,
                        'ToDevice' => $todeviceno,
                        'message_id' => $data_message_id,
                        'Message' => json_encode($data)
                    )
                );
            }
        } else {
            $this->db->insert('log_firebase', array(
                    'table' => $table_data['table'],
                    'multicast_id' => $multicastid,
                    'outlet' => $outlet,
                    'token' => $to,
                    'result' => var_export($result_firebase_array, true),
                    'TableID' => $tableid,
                    'DeviceNo' => $deviceno,
                    'PerusahaanNo' => $perusahaanno,
                    'FromDevice' => $fromdevice,
                    'ToDevice' => $todeviceno,
                    'message_id' => $data_message_id,
                    'Message' => json_encode($data)
                )
            );
        }
            log_message('error', 'selesai insert log_firebase' . $outlet . "." . $todeviceno . " " . microtime());


        // print_r($msg);
        // echo json_encode($debug,JSON_PRETTY_PRINT);
    }

    public function notifToWeb($to, $msg, $progres)
    {
        if (!defined('API_ACCESS_KEY')) {
            if (ENVIRONMENT == 'development') {
                define('API_ACCESS_KEY', 'AAAAoIQmavg:APA91bGuOPWvccgZhPxdeLn-uWbZazZD3EicE1Ic2uiNAI2n7J5I-4UlI8UgJzrAfT8e2NeDgvx6oA310EK9z-Wyqla0CKEFpsU3ygr6W0Mi7hgJ7pxG4UEHw0ecL2Iv4CvYsd05zvH_');
            } else if (ENVIRONMENT == 'testing') {
                define('API_ACCESS_KEY', 'AAAA58dqaj0:APA91bEKLbLOzp2gi2ab6AvilLk7PBBwoElIplPCJwaWzG1LyYSmj6IBcX7TsPYd6peFuB7hKr87O8vD4iw2-ETRuqL8ZAmPFbEm-LM5a3yUCqOGEeEIFInP_zznePFSvmAA9anZnVtW');
            } else if (ENVIRONMENT == 'production') {
                define('API_ACCESS_KEY', 'AAAANlCjvuA:APA91bHjla3fjXvX9AZPc-nZgxddtJvIokI_c_x-7HXN0NC7msWScqUfH1kTXHWxEpzxJIzA3hOOwJZgd-c83C7p-4462A8hDL7wMKgT0QLDHaaXSMeeA5SJnxAPpQFcKZ4-Llqr0Wfy');
            }
        }

        if (ENVIRONMENT == 'production') {
            $urlFirebase = 'https://us-central1-nuta-production.cloudfunctions.net/hitPost';
        } else {
            $urlFirebase = 'https://us-central1-nuta-staging.cloudfunctions.net/hitPost';
        }

        $data = array_merge(array("firebasetitle" => 'promoadd', "firebasebody" => $msg, "prosentase" => $progres));

        $fields = array
        (
            'to' => $to,
            'token' => $to,
            'notification' => ['body' => $data],
            'priority' => 'high',
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        #Send To Firebase Function To send fcm
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
//        var_dump(json_encode($result)); exit();
//        $data = $fields;
//        $data['token'] = $data['to'];
//        $data_string = json_encode($data);
//        $ch = curl_init($urlFirebase);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//                'Content-Type: application/json',
//                'Content-Length: ' . strlen($data_string))
//        );
    }

    public function push_firebase($outlet, $table_data, $tableid, $deviceno, $perusahaanno, $fromdevice)
    {
        foreach ($this->get_token($outlet) as $key => $value) {
            $this->send_data($value->token, $table_data, $value->DeviceNo, $outlet, $tableid, $deviceno, $perusahaanno, $fromdevice);
            // $this->send_data( "/topics/testing", $table_data );
        }
    }

    public function push_firebase_withprogres($outlet, $namaoutlet, $table_data, $tableid, $deviceno, $perusahaanno, $fromdevice,$token,$proses,$jumlahSemuaProses)
    {
        foreach ($this->get_token($outlet) as $key => $value) {
            $pros = $proses/$jumlahSemuaProses * 100;
            $this->notifToWeb($token, 'sedang mengirim data ke outlet ' . $namaoutlet . ' perangkat ' . $value->DeviceNo , $pros);
            $this->send_data($value->token, $table_data, $value->DeviceNo, $outlet, $tableid, $deviceno, $perusahaanno, $fromdevice);
            $proses++;
            // $this->send_data( "/topics/testing", $table_data );
        }
        return $proses;
    }
}