<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ajaxdsb extends MY_Controller {
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

    public function penjualan_hari_ini() {

        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $penjualan_hari_ini = $this->Dashboard->getRpPenjualan(
                $this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);
            $penjualan_kemarin = $this->Dashboard->getRpPenjualanKemarin($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs);
            $captionWidget = "Total Penjualan";
            $isHariIni = ($TglMulai === $TglSampai) && ($TglMulai === date('Y-m-d'));
            if ($isHariIni) {
                $captionWidget = "Penjualan Hari ini";
            }
            $text = '';
            $isNaikTurun = "Sama Dengan";
            $icon = "ico-minus";
            if ($penjualan_kemarin > $penjualan_hari_ini) {
                $isNaikTurun = "Turun";
                $icon = "ico-arrow-down-long";
            } else if ($penjualan_kemarin < $penjualan_hari_ini) {
                $isNaikTurun = "Naik";
                $icon = "ico-arrow-up";
            }

            if ($penjualan_kemarin > 0) {
                if ($isNaikTurun === 'Naik') {
                    $rumus = ($penjualan_hari_ini - $penjualan_hari_ini) / $penjualan_kemarin * 100;
                    $text = 'Naik ' . round($rumus) . ' % dari kemarin';
                } else if ($isNaikTurun === 'Turun') {
                    $rumus = ($penjualan_kemarin - $penjualan_hari_ini) / $penjualan_kemarin * 100;
                    $text = 'Turun ' . round($rumus) . ' % dari kemarin';
                } else {
                    $text = 'Sama dengan hari kemarin';
                }
            }
            $captionFooter = '';
            if ($penjualan_kemarin > 0) {
                $captionFooter = '<span ><i class=".$icon." ></i ></span >' . $text;
            } else {
                $captionFooter = '&nbsp';
            }
            echo json_encode(array('captionhead' => $captionWidget, 'totalhariini' =>
                'Rp. ' . $this->currencyformatter->format($penjualan_hari_ini),
                'captionfooter' => $captionFooter, 'ishariini' => $isHariIni));
        }
    }

    public function transaksi_hari_ini() {
        $this->benchmark->mark('code_start');
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $jumlahHariIni = $this->Dashboard->getJumlahTransaksi($this->DevIDAtauIDPerusahaan,
                $Outlet, $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);
            $jumlahKemarin = $this->Dashboard->getJumlahTransaksiKemarin($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs
            );
            $isNaikTurun = "Sama Dengan";
            $icon = "ico-minus";
            if ($jumlahKemarin > $jumlahHariIni) {
                $isNaikTurun = "Turun";
                $icon = "ico-arrow-down-long";
            } else if ($jumlahKemarin < $jumlahHariIni) {
                $isNaikTurun = "Naik";
                $icon = "ico-arrow-up";
            }
            $text = '';
            if ($jumlahKemarin > 0) {
                if ($isNaikTurun === 'Naik') {
                    $rumus = ($jumlahHariIni - $jumlahKemarin) / $jumlahKemarin * 100;
                    $text = 'Naik ' . round($rumus) . ' % dari kemarin';
                } else if ($isNaikTurun === 'Turun') {
                    $rumus = ($jumlahKemarin - $jumlahHariIni) / $jumlahKemarin * 100;
                    $text = 'Turun ' . round($rumus) . ' % dari kemarin';
                } else {
                    $rumus = $jumlahHariIni;
                    $text = 'Sama dengan hari kemarin';
                }
            }
            $captionWidget = "Jumlah Transaksi";
            $isHariIni = ($TglMulai === $TglSampai) && ($TglMulai === date('Y-m-d'));
            if ($isHariIni) {
                $captionWidget = "Transaksi Hari ini";
            }
            $captionFooter = '';
            if ($isHariIni) {
                if ($jumlahKemarin > 0) {
                    $captionFooter = '<span ><i class=".$icon." ></i ></span >' . $text;
                } else {
                    $captionFooter = '&nbsp';
                }
            }

            $this->benchmark->mark('code_end');
            echo json_encode(array('captionhead' => $captionWidget, 'totalhariini' =>
                $jumlahHariIni,
                'captionfooter' => $captionFooter, 'ishariini' => $isHariIni, 'bc' => $this->benchmark->elapsed_time('code_start', 'code_end')));
        }
    }

    public function ratarata_transaksi_hari_ini() {
        $this->benchmark->mark('code_start');
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $rataHariIni = $this->Dashboard->getRataTransaksi($this->DevIDAtauIDPerusahaan,
                $Outlet, $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);
            $rataKemarin = $this->Dashboard->getRataTransaksiKemarin($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs
            );
            $isNaikTurun = "Sama Dengan";
            $icon = "ico-minus";
            if ($rataKemarin > $rataHariIni) {
                $isNaikTurun = "Turun";
                $icon = "ico-arrow-down-long";
            } else if ($rataKemarin < $rataHariIni) {
                $isNaikTurun = "Naik";
                $icon = "ico-arrow-up";
            }
            $text = '';
            if ($rataKemarin > 0) {
                if ($isNaikTurun === 'Naik') {
                    $rumus = ($rataHariIni - $rataKemarin) / $rataKemarin * 100;
                    $text = 'Naik ' . round($rumus) . ' % dari kemarin';
                } else if ($isNaikTurun === 'Turun') {
                    $rumus = ($rataKemarin - $rataHariIni) / $rataKemarin * 100;
                    $text = 'Turun ' . round($rumus) . ' % dari kemarin';
                } else {
                    $rumus = $rataHariIni;
                    $text = 'Sama dengan hari kemarin';
                }
            }
            $captionWidget = "Rata-Rata Transaksi";
            $isHariIni = ($TglMulai === $TglSampai) && ($TglMulai === date('Y-m-d'));
            if ($isHariIni) {
                $captionWidget = "Rata-Rata Transaksi Hari ini";
            }
            $captionFooter = '';
            if ($isHariIni) {
                if ($rataKemarin > 0) {
                    $captionFooter = '<span ><i class=".$icon." ></i ></span >' . $text;
                } else {
                    $captionFooter = '&nbsp';
                }
            }

            $this->benchmark->mark('code_end');
            echo json_encode(array('captionhead' => $captionWidget, 'totalhariini' =>
                'Rp. ' . $this->currencyformatter->format($rataHariIni),
                'captionfooter' => $captionFooter, 'ishariini' => $isHariIni, 'bc' => $this->benchmark->elapsed_time('code_start', 'code_end')));
        }
    }

    public function biaya_hari_ini() {
        $this->benchmark->mark('code_start');
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $biayaHariIni = $this->Dashboard->getBiaya($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai
            );
            $biayaKemarin = $this->Dashboard->getBiayaKemarin($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs
            );
            $isNaikTurun = "Sama Dengan";
            $icon = "ico-minus";
            if ($biayaKemarin > $biayaHariIni) {
                $isNaikTurun = "Turun";
                $icon = "ico-arrow-down-long";
            } else if ($biayaKemarin < $biayaHariIni) {
                $isNaikTurun = "Naik";
                $icon = "ico-arrow-up";
            }
            $text = '';
            if ($biayaKemarin > 0) {
                if ($isNaikTurun === 'Naik') {
                    $rumus = ($biayaHariIni - $biayaKemarin) / $biayaKemarin * 100;
                    $text = 'Naik ' . round($rumus) . ' % dari kemarin';
                } else if ($isNaikTurun === 'Turun') {
                    $rumus = ($biayaKemarin - $biayaHariIni) / $biayaKemarin * 100;
                    $text = 'Turun ' . round($rumus) . ' % dari kemarin';
                } else {
                    $text = 'Sama dengan hari kemarin';
                }
            }
            $captionWidget = "Total Biaya";
            $isHariIni = ($TglMulai === $TglSampai) && ($TglMulai === date('Y-m-d'));
            if ($isHariIni) {
                $captionWidget = "Biaya Hari ini";
            }
            $captionFooter = '';
            if ($isHariIni) {
                if ($biayaKemarin > 0) {
                    $captionFooter = '<span ><i class=".$icon." ></i ></span >' . $text;
                } else {
                    $captionFooter = '&nbsp';
                }
            }
            $this->benchmark->mark('code_stop');
            echo json_encode(array('captionhead' => $captionWidget, 'totalhariini' =>
                'Rp. ' . $this->currencyformatter->format($biayaHariIni),
                'captionfooter' => $captionFooter, 'ishariini' => $isHariIni,
                'bc' => $this->benchmark->elapsed_time('code_start', 'code_end')));
        }
    }

    public function laba_kotor_hari_ini() {
        $this->benchmark->mark('code_start');
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $TglMulai = $this->input->post('ds');
            $TglSampai = $this->input->post('de');
            $Outlet = $this->input->post('o');
            $penjualanHariIni = $this->Dashboard->getRpPenjualan(
                $this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai);
            $penjualanKemarin = $this->Dashboard->getRpPenjualanKemarin($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs);
            $hppHariIni = $this->Dashboard->getHppHariIni($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs,
                $TglMulai,
                $TglSampai
            );
            $hppKemarin = $this->Dashboard->getHppKemarin($this->DevIDAtauIDPerusahaan,
                $Outlet,
                $this->registeredDeviceID,
                $this->Cabangs
            );

            $labaKotorHariIni = $penjualanHariIni - $hppHariIni;
            $labaKotorKemarin = $penjualanKemarin - $hppKemarin;
            $isNaikTurun = "Sama Dengan";
            $icon = "ico-minus";
            if ($labaKotorKemarin > $labaKotorHariIni) {
                $isNaikTurun = "Turun";
                $icon = "ico-arrow-down-long";
            } else if ($labaKotorKemarin < $labaKotorHariIni) {
                $isNaikTurun = "Naik";
                $icon = "ico-arrow-up";
            }
            $text = '';
            if ($labaKotorKemarin > 0) {
                if ($isNaikTurun === 'Naik') {
                    $rumus = ($labaKotorHariIni - $labaKotorKemarin) / $labaKotorKemarin * 100;
                    $text = 'Naik ' . round($rumus) . ' % dari kemarin';
                } else if ($isNaikTurun === 'Turun') {
                    $rumus = ($labaKotorHariIni - $labaKotorKemarin) / $labaKotorKemarin * 100;
                    if ($rumus < 0) {
                        $rumus = $rumus * -1;
                    }
                    $text = 'Turun ' . round($rumus) . ' % dari kemarin';
                } else {
                    $text = 'Sama dengan hari kemarin';
                }
            }
            $captionWidget = "Laba Kotor";
            $isHariIni = ($TglMulai === $TglSampai) && ($TglMulai === date('Y-m-d'));
            if ($isHariIni) {
                $captionWidget = "Laba Kotor hari ini";
            }
            $captionFooter = '';
            if ($isHariIni) {
                if ($labaKotorKemarin > 0) {
                    $captionFooter = '<span ><i class=".$icon." ></i ></span >' . $text;
                } else {
                    $captionFooter = '&nbsp';
                }
            }
            $this->benchmark->mark('code_stop');
            echo json_encode(array('captionhead' => $captionWidget, 'totalhariini' =>
                'Rp. ' . $this->currencyformatter->format($labaKotorHariIni),
                'captionfooter' => $captionFooter, 'ishariini' => $isHariIni,
                'bc' => $this->benchmark->elapsed_time('code_start', 'code_end')));
        }
    }


}
