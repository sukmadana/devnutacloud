<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ajaxdsbchart extends MY_Controller {
    var $DevIDAtauIDPerusahaan = '';
    var $registeredDeviceID;
    var $Cabangs;
    var $Outlet;
    var $TglMulai;
    var $TglSampai;

    function __construct() {
        parent::__construct();
        $this->Outlet = 'Semua';
        $this->TglMulai = date('Y-m-d');
        $this->TglSampai = date('Y-m-d');
        $this->DevIDAtauIDPerusahaan = getLoggedInUserID();
        $this->registeredDeviceID = getLoggedInRegisterWithDeviceID();
        $this->load->model('Userperusahaancabang');
        $this->Cabangs = $this->Userperusahaancabang->getListCabang(getLoggedInUsername(), $this->DevIDAtauIDPerusahaan);
        $this->load->library('CurrencyFormatter');
        $this->load->model('Dashboard');
        $this->load->helper('nuta_helper');
        
        $this->Dashboard->setNoPerusahaan(getPerusahaanNo());
        $this->Dashboard->setTableSale(getTableSale());
        $this->Dashboard->setTableSaleDetail(getTableSaleDetail());
        $this->Dashboard->setTableSaleDetailIngredients(getTableSaleDetailIngredients());
    }

    public function penjualan() {
        ifNotAuthenticatedRedirectToLogin();
        $this->benchmark->mark('code_start');
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $captionWidget = "Penjualan";
            $blnNow = date('m');
            $tmpStart = explode('-', $TglMulai);
            $tmpEnd = explode('-', $TglSampai);

            $bulanStart = intval($tmpStart[1]);
            $bulanEnd = intval($tmpEnd[1]);
            $isbulanini = ($bulanStart == $bulanEnd) && ($bulanStart === intval($blnNow));
            if ($isbulanini) {
                $captionWidget = "Penjualan Bulan ini";
            }

            $data_penjualan = $this->Dashboard->getPenjualanBulanIni($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);
            $jsonTicks = $data_penjualan['ticks'];
            $jsonData = $data_penjualan['data'];
            $this->benchmark->mark('code_end');
            $str = '{
            "caption":"' . $captionWidget . '",
               "ticks": ' . $jsonTicks . ',
                "data": ' . $jsonData . ',
                "bc":' . $this->benchmark->elapsed_time('code_start', 'code_end') . '
            }';

            echo $str;

        }
    }

    public function penjualan_bulan() {
        ifNotAuthenticatedRedirectToLogin();
        $this->benchmark->mark('code_start');
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $captionWidget = "Penjualan ".date("F Y", strtotime($TglMulai))." - ".date("F Y", strtotime($TglSampai));
            $blnNow = date('m');
            $tmpStart = explode('-', $TglMulai);
            $tmpEnd = explode('-', $TglSampai);

            $bulanStart = intval($tmpStart[1]);
            $bulanEnd = intval($tmpEnd[1]);
            $isbulanini = ($bulanStart == $bulanEnd) && ($bulanStart === intval($blnNow));
            if ($isbulanini) {
                $captionWidget = "Penjualan Bulan ini";
            }

            $data_penjualan = $this->Dashboard->getPenjualanBulanIni($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);
            $data_ticks =  json_decode($data_penjualan['data'], true);
            $ticks = array_map(function ($ar) {return $ar['label'];}, $data_ticks);
            $datas = array_map(function ($ar) {return $ar['data'];}, $data_ticks);
            $data = array();
            $key_data = 0;
            foreach ($datas as $key => $value) {
                array_push($data, array($key_data,array_sum(array_column($value, '1'))));
                $key_data ++;
            }
            $key_data = 0;
            $ticks_data = array();
            foreach ($ticks as $key => $value) {
                array_push($ticks_data, array($key_data,$value));
                $key_data ++;   
            }
            $this->benchmark->mark('code_end');
            $result = array('data' => $data, 'ticks' => $ticks_data, 'caption' => $captionWidget);
            header('Content-Type: application/json');
            echo json_encode($result);
        }
        
    }

    public function pengunjung() {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $captionWidget = "Pengunjung";
            $blnNow = date('m');
            $tmpStart = explode('-', $TglMulai);
            $tmpEnd = explode('-', $TglSampai);

            $bulanStart = intval($tmpStart[1]);
            $bulanEnd = intval($tmpEnd[1]);
            $isbulanini = ($bulanStart == $bulanEnd) && ($bulanStart === intval($blnNow));
            if ($isbulanini) {
                $captionWidget = "Pengunjung Bulan ini";
            }

            $data_penjualan = $this->Dashboard->getPengunjungBulanIni($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);
            $jsonTicks = $data_penjualan['ticks'];
            $jsonData = $data_penjualan['data'];
            $str = '{
            "caption":"' . $captionWidget . '",
               "ticks": ' . $jsonTicks . ',
                "data": ' . $jsonData . '
            }';
            echo $str;

        }
    }

    public function pengunjung_bulan() {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $captionWidget = "Pengunjung ".date("F Y", strtotime($TglMulai))." - ".date("F Y", strtotime($TglSampai));
            $blnNow = date('m');
            $tmpStart = explode('-', $TglMulai);
            $tmpEnd = explode('-', $TglSampai);

            $bulanStart = intval($tmpStart[1]);
            $bulanEnd = intval($tmpEnd[1]);
            $isbulanini = ($bulanStart == $bulanEnd) && ($bulanStart === intval($blnNow));
            if ($isbulanini) {
                $captionWidget = "Pengunjung Bulan ini";
            }

            $data_penjualan = $this->Dashboard->getPengunjungBulanIni($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);
            $data_ticks =  json_decode($data_penjualan['data'], true);
            $ticks = array_map(function ($ar) {return $ar['label'];}, $data_ticks);
            $datas = array_map(function ($ar) {return $ar['data'];}, $data_ticks);
            $data = array();
            $key_data = 0;
            foreach ($datas as $key => $value) {
                array_push($data, array($key_data,array_sum(array_column($value, '1'))));
                $key_data ++;
            }
            $key_data = 0;
            $ticks_data = array();
            foreach ($ticks as $key => $value) {
                array_push($ticks_data, array($key_data,$value));
                $key_data ++;   
            }
            $this->benchmark->mark('code_end');
            $result = array('data' => $data, 'ticks' => $ticks_data, 'caption' => $captionWidget);
            header('Content-Type: application/json');
            echo json_encode($result);

        }
    }

    public function terlaris() {
        $this->benchmark->mark('code_start');
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $captionWidget = "Penjualan Terlaris";


            $data_penjualan_terlaris = $this->Dashboard->getPenjualanTerlaris($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);

            /*$data_penjualan_terlaris = $this->Dashboard->getChartOutletTerlaris($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);*/

            $jsonData = $data_penjualan_terlaris;
            $this->benchmark->mark('code_end');
            $str = '{
            "caption":"' . $captionWidget . '",
                "data": ' . $jsonData . ',
                "bc":"' . $this->benchmark->elapsed_time('code_start', 'code_end') . '"
            }';

            echo $str;
        }
    }

    public function rekappembayaran() {
        $this->benchmark->mark('code_start');
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $captionWidget = "Rekap Pembayaran";


            $data_rekap_pembayaran = $this->Dashboard->getChartRekapPembayaran($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);

            $jsonData = $data_rekap_pembayaran['data'];
            $this->benchmark->mark('code_end');
            $str = '{
            "caption":"' . $captionWidget . '",
                "data": ' . $jsonData . ',
                "bc":"' . $this->benchmark->elapsed_time('code_start', 'code_end') . '"
            }';
            echo $str;
        }
    }

    public function outlet() {
        $this->benchmark->mark('code_start');
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $captionWidget = "Outlet Terlaris";


            $data_outlet_terlaris = $this->Dashboard->getChartOutletTerlaris($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);

//echo $data_outlet_terlaris;

            $jsonData = $data_outlet_terlaris['data'];
            $this->benchmark->mark('code_end');
            $str = '{
            "caption":"' . $captionWidget . '",
                "data": ' . $jsonData . ',
                 "bc":"' . $this->benchmark->elapsed_time('code_start', 'code_end') . '"
            }';
            echo $str;
        }
    }
}