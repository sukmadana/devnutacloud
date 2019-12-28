<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Test extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('unit_test');
    }

    function index()
    {
        $html = $this->input->get_post('table');
        $tmpfile = 'output/' . time() . '.html';
        file_put_contents($tmpfile, $html);


        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->load($tmpfile);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'Laporan';
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        unlink($tmpfile);

    }
    
    function info() {
        phpinfo();
    }
            
    function doLogin($deviceid)
    {
        $this->load->model('Options');
        return $this->Options->IsDeviceIDexist($deviceid);
    }

    function trial($deviceID)
    {
        if ($this->isNotEmpty($deviceID)) {
            $isAuthenticated = $this->doLogin($deviceID);
            if ($isAuthenticated) {
                return "Device ID is valid";
            } else {
                return "Not found";
            }
        } else {
            return "Render view";
        }
    }

    protected function isNotEmpty($value)
    {
        return isset($value) && trim($value) != "";
    }

    public function mail()
    {
        $this->load->view('mail/mail_registrasi_perusahaan', array('url' => '#', 'subject' => 'ID Perusahaan anda', 'namaperusahaan' => 'PT Husnan', 'username' => 'PT Husnan Test Mail', 'password' => 123));
        $this->sentEmailKonfirmasiIndividual('emhusnan@gmail.com', '12311');
    }

    private function sentEmailKonfirmasiIndividual($email, $iduser)
    {
        $this->load->library('email');
        $this->load->helper('hashids_helper');
        $this->email->from('no-reply@nutacloud.com', 'no-reply@nutacloud.com');
        $this->email->to(array($email));
        $encIdUser = hashids_encrypt("bnV0YXBvc2tleWVuY3J5cHRpbmRpdmlkdWFs", $iduser, 7);
        $url = base_url() . "authentication/konfirmasi?a=" . $encIdUser;
        $subject = "Akun nutacloud anda";
        $this->email->subject($subject);
        $message = $this->load->view('mail/mail_registrasi_perusahaan', array('url' => '#', 'subject' => 'ID Perusahaan anda', 'namaperusahaan' => 'PT Husnan Test Mail'), true);
        $this->email->message($message);
        $this->email->send();
    }

    public function testcekidperusahan()
    {
        $result = $this->checkidperusahaan('KEBAB6251');
        $json = json_decode($result);
        $this->unit->run($json->kode, 200, 'Tes');
        $this->unit->run($json->namaperusahaan, 'kebab', 'Tes Nama Perusahaan');
        echo $this->unit->report();
    }

    public function cekidperusahan()
    {
        $result = $this->checkidperusahaan();
        $json = json_decode($result);
        echo $result;
        //echo $json;
    }

    public function pushnotifikasiupdateall()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $nvc = $this->input->get_post('u');
            $this->load->model('Firebasemodel');
            $outlets = $this->Firebasemodel->get_all_outlet();
            //var_dump($outlets);
            $this->load->model('Outlet');

            foreach ($outlets as $outlet) {
                $outletid = $outlet->outlet;
                $perusahaanno = $this->Outlet->getPerusahaanNoByOutlet($outletid);
                if($perusahaanno == null) {
                    continue;
                }

                $pushed_data = array(
                    "table" => "updatefornewversion",
                    "column" => array("NewestVersionCode" => $nvc)
                );

                $this->load->model('Firebasemodel');
                $this->Firebasemodel->push_firebase($outletid, $pushed_data,
                    $nvc, 0, $perusahaanno, 0);
                var_dump($pushed_data);
                echo " Outlet ID : " . $outletid . "<br>";
                //echo $outlet->outlet . "<br>";
//            $this->send_data($value->token, $table_data, $outlet);
                // $this->send_data( "/topics/testing", $table_data );
            }
        } else {
            $nvc = 103;
            $this->load->model('Firebasemodel');
            $outlets = $this->Firebasemodel->get_all_outlet();
            //var_dump($outlets);
            $this->load->model('Outlet');

            foreach ($outlets as $outlet) {
                $outletid = $outlet->outlet;
                $perusahaanno = $this->Outlet->getPerusahaanNoByOutlet($outletid);
                if($perusahaanno == null) {
                    continue;
                }

                $pushed_data = array(
                    "table" => "updatefornewversion",
                    "column" => array("NewestVersionCode" => $nvc)
                );

                $this->load->model('Firebasemodel');
                $this->Firebasemodel->push_firebase($outletid, $pushed_data,
                    $nvc, 0, $perusahaanno, 0);
                var_dump($pushed_data);
                echo "<br>";
                //echo $outlet->outlet . "<br>";
//            $this->send_data($value->token, $table_data, $outlet);
                // $this->send_data( "/topics/testing", $table_data );
            }
        }
    }

    public function getstok()
    {
        $outlet = $this->input->get_post('o');
        $this->load->model("outlet");
        $perusahaanNo = $this->outlet->getPerusahaanNoByOutlet($outlet);

        $this->load->library('NutaQuery');
        $querystok = $this->nutaquery->get_query_stok_byoutlet($perusahaanNo, $outlet);

        $stocks = $this->db->query($querystok)->result();
//            print_r(json_encode($stocks));
        print_r(json_encode(array('status' => 'OK', 'data' => $stocks)));
        //print_r(array('data' => $stocks));
    }

    public function pushnotifikasisingle()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outletid = $this->input->get_post('o');
            $nvc = $this->input->get_post('u');

            $pushed_data = array(
                "table" => "updatefornewversion",
                "column" => array("NewestVersionCode" => "141")
            );
            $this->load->model('Firebasemodel');
            $this->load->model('Outlet');
            $perusahaanno = $this->Outlet->getPerusahaanNoByOutlet($outletid);
            $this->Firebasemodel->push_firebase($outletid, $pushed_data,
                "100", 0, $perusahaanno, 0);
            var_dump($pushed_data);
        }
        //echo $json;
    }

    public function pleaseuploaddb()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outletid = $this->input->get_post('o');

            $pushed_data = array(
                "table" => "pleaseuploaddb",
                "column" => array("db" => "sqlite")
            );
            $this->load->model('Firebasemodel');
            $this->load->model('Outlet');
            $perusahaanno = $this->Outlet->getPerusahaanNoByOutlet($outletid);
            $this->Firebasemodel->push_firebase($outletid, $pushed_data,
                "100", 0, $perusahaanno, 0);
            var_dump($pushed_data);
        }
        //echo $json;
    }

    public function checkidperusahaan()
    {
        $this->load->database();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $id = $this->input->get_post('i');
            $query = $this->db->get_where('perusahaan', array('PerusahaanID' => $id));
            $result = $query->result();
            $count = count($result);
            $retval = array();
            if ($count == 1) {
                $namaperusahaan = $result[0]->namaperusahaan;
                $retval['kode'] = 200;
                $retval['msg'] = "Selamat, tablet ini telah terdaftar sebagai salah satu outlet dari
perusahaan" . $namaperusahaan .
                    "\nSilahkan melanjutkan aktivitas seperti biasanya.";
                $retval['namaperusahaan'] = $namaperusahaan;

            } else {
                $retval['kode'] = 404;
                $retval['msg'] = " ID perusahaan tidak valid";
                $retval['namaperusahaan'] = 'Eror';

            }
            return json_encode($retval);

        }


    }

    public function resetdeviceidevercossputih()
    {
        $this->initDbMaster();
        $this->_dbMaster->query(//2d640577892f99fe
            "DELETE FROM user WHERE DeviceID='2d640577892f99fe';
DELETE FROM userperusahaan WHERE RegisterWithDeviceID='2d640577892f99fe';
DELETE FROM perusahaan WHERE RegisterWithDeviceID='2d640577892f99fe';");
        echo "Device ID 2d640577892f99fe punya Evercoss Putih sudah direset";
    }

    public function resetdeviceidadvanhitam()
    {
        $this->initDbMaster();
        $this->_dbMaster->query(//2d640577892f99fe
            "DELETE FROM user WHERE DeviceID='b430e523070a5c8d';
DELETE FROM userperusahaan WHERE RegisterWithDeviceID='b430e523070a5c8d';
DELETE FROM perusahaan WHERE RegisterWithDeviceID='b430e523070a5c8d';");
        echo "Device ID b430e523070a5c8d punya Advan Hitam sudah direset";
    }


    var $_dbMaster;

    protected function initDbMaster()
    {
        $dbmasterconfig = array(
            'dsn' => '',
            'hostname' => '188.166.227.225', //master db
            'username' => 'root',
            'password' => 'Lentera1nf',
            'database' => 'nutacloud',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => TRUE,
            'cache_on' => FALSE,
            'cachedir' => '',
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci',
            'swap_pre' => '',
            'encrypt' => FALSE,
            'compress' => FALSE,
            'stricton' => FALSE,
            'failover' => array(),
            'save_queries' => TRUE
        );
        $this->_dbMaster = $this->load->database($dbmasterconfig, true);
    }

    public function crawler()
    {
        $_isEmailExist = file_get_contents(base_url() . 'ajax/validateemailindividual');
        $jsonEmailExist = json_decode($_isEmailExist);
        $isEmailExist = $jsonEmailExist['valid'];
        echo $isEmailExist;
    }

    public function tgl()
    {
        $arr = $this->getTglBetweenTwoDates('2016-01-23', '2016-02-05');
        sort($arr);
        Dump::me($arr);
    }

    public function tes1() {
        echo "hello world";
    }

    public function tes2() {
        $this->load->view('blank_part.php');
    }

    public function getTglBetweenTwoDates($dateYmdStart, $dateYmdEnd)
    {
        $datestart = strtotime($dateYmdStart);
        $dateend = strtotime($dateYmdEnd);
        $datediff = $dateend - $datestart;
        $toDateDiff = ($datediff / (60 * 60 * 24));
        $retval = array();
        for ($from = 0; $from < $toDateDiff; $from++) {
            $timeNext = strtotime(date('Y-m-d', $datestart) . "+1 day");
            $datestart = $timeNext;
            $toSplit = explode('#', date('d#m#Y', $timeNext));
            array_push($retval, array("tanggal" => $toSplit[0], 'bulan' => $toSplit[1], 'tahun' => $toSplit[2]));
        }
        return $retval;
    }

}
