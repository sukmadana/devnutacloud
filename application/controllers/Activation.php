<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activation extends MY_Controller
{

    var $theDB;
    var $DevIDAtauIDPerusahaan = '';
    var $registeredDeviceID;
    var $Cabangs;
    var $TglMulai;
    var $TglSampai;

    function __construct()
    {

        parent::__construct();
    }

    public function index()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->DevIDAtauIDPerusahaan = getLoggedInUserID();
        $this->registeredDeviceID = getLoggedInRegisterWithDeviceID();
        $this->load->model('Userperusahaancabang');
        $this->Cabangs = $this->Userperusahaancabang->getListCabang(getLoggedInUsername(), $this->DevIDAtauIDPerusahaan);
        $this->load->library('CurrencyFormatter');

        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        log_message('error','Activation index availableOutlets : '. var_export($availableOutlets,true));
        list($get_outlet, $get_masaaktif, $get_fitur) = $this->get_query_string();
        if (isNotEmpty($get_outlet)) {
            $this->default_outlet_id = $get_outlet;
        }
        log_message('error','Activation index pada outlet : '. $get_outlet);


        $this->load->model('Options');
        $option = $this->Options->get_by_devid($this->default_outlet_id);
        if(empty($option->CompanyEmail) || !isset($option->CompanyEmail) || trim($option->CompanyEmail) == '') {
            $this->load->model('Userperusahaan');
            $option->CompanyEmail = $this->Userperusahaan->getEmailPasswordByPerusahaanUsername($option->PerusahaanID,getLoggedInUsername())['email'];
        }
        if(empty($option->CompanyEmail) || !isset($option->CompanyEmail) || trim($option->CompanyEmail) == '') {
            $this->load->model('Perusahaanmodel');
            $option->CompanyEmail = $this->Perusahaanmodel->GetEmailPerusahaan($option->PerusahaanID);
        }
        $info = $this->get_outlet_info($this->default_outlet_id);
        $data['info'] = $info;

        $exp = $info->exp;
        $strDateNow = date('Y-m-d');
        $dateNow = strtotime($strDateNow);
        $expRef = $exp;
        if (strtotime($exp) <= $dateNow) {
            $expRef = $strDateNow;
            //$dateTglExpired = $dateNow;
        }
        $plus_exp = strtotime(date("Y-m-d", strtotime($expRef)) . " +" . $get_masaaktif . " month");
        $perpanjangan = date("Y-m-d", $plus_exp);

        $this->load->model('Outlet');
        $has_new_aktivasi = $this->Outlet->hasNewAktivasi(getLoggedInUserID(), $this->default_outlet_id);
        $existing_aktivasi = ['status' => '', 'kode' => '', 'total' => '', 'token' => ''];
        if ($has_new_aktivasi['has']) {
            $existing_aktivasi = $this->Outlet->getAktivasi(getLoggedInUserID(), $has_new_aktivasi['kode']);
        }

        $data['amount'] = $get_masaaktif;
        $data['has_new_aktivasi'] = false;//$has_new_aktivasi['has'];
        $data['existing_aktivasi'] = $existing_aktivasi;

        $data['fitur'] = $get_fitur;
        $data['until'] = $perpanjangan;
        $data['outlets'] = $availableOutlets;
        $data['selected_outlet'] = $this->default_outlet_id;
        $data['option'] = $option;
        $data['page_part'] = 'activation/activation';
        $data['js_part'] = array('features/js/js_payment', 'features/js/js_form');
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['isDiningTableVisible'] = $this->IsDiningTableVisible();
        $data['skipCekExpired'] = true;
        $this->load->view('main_part', $data);
    }

    //finish payment
    public function finish()
    {
        ifNotAuthenticatedRedirectToLogin();

        $this->load->model('Outlet');
        $has_new_aktivasi = $this->Outlet->hasNewAktivasi(getLoggedInUserID());
        echo var_dump($has_new_aktivasi);
        if ($has_new_aktivasi['has']) {
            $aktivasi = $this->Outlet->getAktivasi(getLoggedInUserID(), $has_new_aktivasi['kode']);
            if ($aktivasi['status'] === 'New') {
                $id = $aktivasi['kode'];
                $jumlah = $aktivasi['total'];
                $by = 'Nutacloud Midtrans';
                $purpose = 1;//1 = Client, 2= Developer, 3= Sales;
                $this->konfirmasiPembayaran($id, $by, $jumlah, $purpose);
                $this->Outlet->setStatusAktivasi('Payment Successful', $aktivasi['outletid']);
                redirect(base_url('activation/index'));
            } else {
                show_404();
            }
        }
    }

    //
    public function unfinish()
    {
        echo 'Tidak selesai';
    }

    //payment error
    public function error()
    {
        echo 'Error';
    }

    /*
     * REST access
     * */
    public function request()
    {
        $nama = $this->input->post('nama');
        $outletid = $this->input->post('outletid');
        $alamat = $this->input->post('alamat');
        $email = $this->input->post('email');
        $masaaktif = $this->input->post('masaaktif');
        $fitur = $this->input->post('fitur');
        $voucher = $this->input->post('voucher');
        $this->load->library('Payment');
        $this->load->model('Outlet');

        $this->load->model('Options');
        $option = $this->Options->get_by_devid($outletid);
        $aktivasi_json = $this->requestAktivasi($nama, $email, $alamat, $masaaktif, $fitur, $outletid, $option->TglExpired, $voucher);

        $aktivasi = json_decode($aktivasi_json);

        if ($aktivasi->status === "OK") {
            $transaction_id = $aktivasi->kodebooking;

            //flag aktivasi
            $this->Outlet->setKodeAktivasi($transaction_id, $outletid);
            $this->Outlet->setStatusAktivasi('New', $outletid);
            $this->Outlet->setTotalHargaAktivasi($aktivasi->total, $outletid);

            $this->db->where(array('OutletID' => $outletid));
            $this->db->update('options', array('KodeBooking' => $transaction_id));

            try {
                $token = $this->payment->get_snap_token($transaction_id, $aktivasi->total);
                $this->Outlet->setSnapTokenAktivasi($token, $outletid);
                echo json_encode(['status' => $aktivasi->status, 'token' => $token, 'kodeaktivasi' => $transaction_id,
                    'total' => $aktivasi->total, 'totalharga' => number_format($aktivasi->total,0,',','.')]);
            } catch (Exception $e) {
                $nama = $this->input->post('nama');
                $outletid = $this->input->post('outletid');
                $alamat = $this->input->post('alamat');
                $email = $this->input->post('email');
                $masaaktif = $this->input->post('masaaktif');
                $fitur = $this->input->post('fitur');
                $voucher = $this->input->post('voucher');
                $this->load->library('Payment');
                $this->load->model('Outlet');

                $this->load->model('Options');
                $option = $this->Options->get_by_devid($outletid);
                $aktivasi_json = $this->requestAktivasi($nama, $email, $alamat, $masaaktif, $fitur, $outletid, $option->TglExpired, $voucher, true);

                $aktivasi = json_decode($aktivasi_json);

                if ($aktivasi->status === "OK") {
                    $transaction_id = $aktivasi->kodebooking;

                    //flag aktivasi
                    $this->Outlet->setKodeAktivasi($transaction_id, $outletid);
                    $this->Outlet->setStatusAktivasi('New', $outletid);
                    $this->Outlet->setTotalHargaAktivasi($aktivasi->total, $outletid);

                    $this->db->where(array('OutletID' => $outletid));
                    $this->db->update('options', array('KodeBooking' => $transaction_id));

                    try {
                        $token = $this->payment->get_snap_token($transaction_id, $aktivasi->total);
                        $this->Outlet->setSnapTokenAktivasi($token, $outletid);
                        echo json_encode(['status' => $aktivasi->status, 'token' => $token, 'kodeaktivasi' => $transaction_id,
                            'total' => $aktivasi->total, 'totalharga' => number_format($aktivasi->total,0,',','.')]);
                    } catch (Exception $e) {
                        log_message('error', 'gagal aktivasi ' . $e->getMessage());
                        echo json_encode(['status' => 'Error', 'message' => 'Gagal Aktivasi, hubungi tim technical support nutapos support@nutapos.com']);
                    }
                } else {
                    echo json_encode(['status' => $aktivasi->status, 'message' => $aktivasi->pesan]);
                }
            }
        } else {
            echo json_encode(['status' => $aktivasi->status, 'message' => $aktivasi->pesan]);
        }
    }


    protected function requestAktivasi($nama, $email, $alamat, $masaaktif, $kodefitur, $devid, $tglexpired, $voucher, $force = false)
    {
        $curl = curl_init();
        $url = 'http://visitnutapos.com/Aktivasi/PesanAktivasi';
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($kodefitur != 2) {
            if($force == false) {
                $data = ['nama' => $nama, 'email' => $email,
                    'alamat' => $alamat, 'masaaktif' => $masaaktif,
                    'kodefitur' => $kodefitur, 'devid' => $devid,
                    'tglExpired' => $tglexpired, 'kupon' => '', 'voucher' => $voucher,
                    'sumber' => $this->config->item('base_url')
                ];
            } else {
                $data = ['nama' => $nama, 'email' => $email,
                    'alamat' => $alamat, 'masaaktif' => $masaaktif,
                    'kodefitur' => $kodefitur, 'devid' => $devid,
                    'tglExpired' => $tglexpired, 'kupon' => '', 'voucher' => $voucher,
                    'sumber' => $this->config->item('base_url'),
                    'force' => true
                ];
            }
        } else {
            $url = 'http://visitnutapos.com/Aktivasi/PesanFitur';
            $info = $this->get_outlet_info($devid);
            log_message('error',var_export($info, true));
            $data = ['nama' => $nama, 'email' => $email,
                'alamat' => $alamat, 'masaaktif' => $masaaktif,
                'kodefitur' => $kodefitur, 'devid' => $devid,
                'fituraktifsampai' => $info->FiturMejaAktifSampai,
                'tglExpired' => $info->exp, 'kupon' => '', 'voucher' => $voucher,
                'sumber' => $this->config->item('base_url')
            ];
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS,
            http_build_query($data));

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ));

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);


        curl_close($curl);

        return $result;
    }

    protected function konfirmasiPembayaran($id, $by, $jumlah, $purpose)
    {
        $curl = curl_init();
        $url = 'http://visitnutapos.com/Aktivasi/KonfirmasiPembayaran';
        curl_setopt($curl, CURLOPT_POST, 1);

        $data = ['id' => $id, 'name' => $by, 'purpose' => $purpose, 'jumlah_transfer' => $jumlah];

        curl_setopt($curl, CURLOPT_POSTFIELDS,
            http_build_query($data));

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ));

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);


        curl_close($curl);

        return $result;
    }

    private function get_outlet_info($devid)
    {
        $this->load->model('Outlet');
        $outlet_info = $this->Outlet->get_outlet_info($devid);
        return $outlet_info;
    }

    /**
     * @return array
     */
    private function get_query_string()
    {
        $get_outlet = $this->input->get('outlet');
        $get_masaaktif = $this->input->get('amount');
        $get_fitur = $this->input->get('fitur');
        if (!isset($get_masaaktif))
            $get_masaaktif = 1;
        if (!isset($get_fitur))
            $get_fitur = 1;
        return array($get_outlet, $get_masaaktif, $get_fitur);
    }

    public function pushfb()
    {
        $this->load->model('Firebasemodel');
        $this->load->model('Outlet');
        $last_insert_data =  array(
            "table"     => "Aktivasi",
            "column"    => array(
                "TglExpired" => $this->input->post('aktifsampai')
            )
        );
        $perusahaanno = $this->Outlet->getPerusahaanNoByOutlet($this->input->post('outletid'));
        $this->Firebasemodel->push_firebase($this->input->post('outletid'),$last_insert_data, 1, 0, $perusahaanno, 0);
    }
}
