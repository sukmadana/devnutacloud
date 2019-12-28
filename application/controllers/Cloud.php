<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class cloud extends MY_Controller
{

    var $theDB;
    var $DevIDAtauIDPerusahaan = '';
    var $registeredDeviceID;
    var $Cabangs;
    var $Outlet;
    var $TglMulai;
    var $TglSampai;

    function __construct()
    {
        parent::__construct();
        $this->SelectedOutlet = 'Semua';
        $this->TglMulai = date('Y-m-d');
        $this->TglSampai = date('Y-m-d');
    }

    public function index()
    {
        redirect(base_url() . 'cloud/main');
    }
    public function help()
    {
        $data['page_part'] = 'cloud/help';
		$this->load->view('main_part', $data);

    }
    public function main()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->DevIDAtauIDPerusahaan = getLoggedInUserID();
        $this->registeredDeviceID = getLoggedInRegisterWithDeviceID();
        $this->load->model('Userperusahaancabang');
        $this->Cabangs = $this->Userperusahaancabang->getListCabang(getLoggedInUsername(), $this->DevIDAtauIDPerusahaan);
        $this->load->library('CurrencyFormatter');

        $get_tglmulai = $this->input->get('date_start');
        $get_tglselesai = $this->input->get('date_end');
        $get_outlet = $this->input->get('outlet');

        if (isNotEmpty($get_tglmulai)) {
            $this->TglMulai = $get_tglmulai;
        }
        if (isNotEmpty($get_tglselesai)) {
            $this->TglSampai = $get_tglselesai;
        }
        if (isNotEmpty($get_outlet)) {
            $this->SelectedOutlet = $get_outlet;
        }

        # LIMIT filter hanya 366 hari
        if (isNotEmpty($get_tglmulai) && isNotEmpty($get_tglselesai)) {
            if (( (strtotime($this->TglSampai) - strtotime($this->TglMulai)) / (24 * 60 * 60 )) > 366){
                log_message('error',"dashboard $this->DevIDAtauIDPerusahaan - $get_outlet lebih dari setahun");
                redirect('cloud/main');
            }
        }

        $data['outlets'] = $this->GetOutlet();
//        echo var_dump($data_outlet_terlaris);exit;
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['page_part'] = 'cloud/dashboard_me';
        if ($this->visibilityMenu['Dashboard']) {
        $data['js_part'] = array('features/js/js_chart', 'features/js/js_form', 'features/filters/filter_date_mulai_sampai_horizontal_js');
            $data['js_chart'] = array(
                'features/js/js_total_penjualan',
                'features/js/js_total_transaksi',
                'features/js/js_ratarata_transaksi',
                'features/js/js_total_laba',
                'features/js/js_total_biaya',
                'features/js/js_chart_penjualan_bulan_ini_me',
                'features/js/js_chart_pengunjung_bulan_ini_me',
                'features/js/js_chart_penjualan_terlaris_me',
                'features/js/js_chart_rekap_pembayaran_me',
                'features/js/js_chart_outlet_terlaris_me',
            );
        }
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['isDiningTableVisible'] = $this->IsDiningTableVisible();
        $data['selected_outlet'] = $this->SelectedOutlet;
        $data['selected_datestart'] = $this->TglMulai;
        $data['selected_dateend'] = $this->TglSampai;

        $data['bulan'] = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );
        $this->load->view('main_part_me', $data);
    }

    public function login()
    {
//        $this->load->view('login');
        redirect(base_url() . 'cloud/trial');
    }


    public function trial()
    {
        redirect(base_url() . 'authentication/loginv2');
        $cID = $this->input->get('i');
        if ($this->isNotEmpty($cID)) {
            $try = $this->doLogin($cID);
            $isAuth = $try['exist'];
            if ($isAuth) {
                $this->setLoginSession($cID, $try['name']);
                $redirect = $this->input->get('r');
                if ($this->isNotEmpty($redirect)) {
                    redirect($redirect);
                } else {
                    redirect(base_url() . 'cloud/main');
                }
            }

        }
        $deviceID = $this->input->get_post('devid');
        if ($this->isNotEmpty($deviceID)) {
            $tryLogin = $this->doLogin($deviceID);
            $isAuthenticated = $tryLogin['exist'];
            if ($isAuthenticated) {
                $this->setLoginSession($deviceID, $tryLogin['name']);
                redirect(base_url() . 'cloud/main');
            } else {
                $this->load->view('cloud/login_trial', array('msg' => 'Device ID not found :('));
            }
        } else {
            $this->load->view('cloud/login_trial', array('msg' => ''));
        }
    }

    public function datagrid()
    {
        $this->load->library('NutaQuery');
        $queryStr = $this->nutaquery->get_query_penjualan();
        $this->load->database();
        $query = $this->db->query($queryStr);
        $result = $query->result();
    }

    protected function isNotEmpty($value)
    {
        return isset($value) && trim($value) != "";
    }

    protected function doLogin($deviceid)
    {
        $this->load->model('Options');
        return $this->Options->IsDeviceIDexist($deviceid);
    }

    protected function GetOutlet()
    {
        $id = $this->db->escape($this->DevIDAtauIDPerusahaan);
        $username = $this->db->escape(getLoggedInUsername());
        $where = "";
        if (getLoggedInNamaPerusahaan() === "Individual") {
            $where = " deviceid=" . $username . " ";
        } else {
            $where = " perusahaanid = " . $id . " ";
        }
        $this->db->where($where);
        $query = $this->db->get('outlet');
        $result = $query->result();
//        dump::me($where);exit;
//        if (count($result) <= 0) {
//            $this->load->model('Perusahaanmodel');
//            $id = $this->Perusahaanmodel->GetRegisterDeviceID($this->DevIDAtauIDPerusahaan);
//            $id = $this->db->escape($id);
//            $where = "(deviceid=" . $id . " OR perusahaanid = " . $id . " ) ";
//            $this->db->where($where);
//            $query = $this->db->get('options');
//            $result = $query->result();
//        }
        $retval = array();
        if (count($result) > 1) {
            $retval['Semua'] = "Semua";
        }

        $this->load->model('Userperusahaan');
        if (getLoggedInNamaPerusahaan() != "Individual") {
            $isOwner = $this->Userperusahaan->isUserOwner(getLoggedInUserID(), getLoggedInUsername());
        } else {
            $isOwner = true;
        }
        if ($isOwner) {
            foreach ($result as $row) {
                $retval[$row->OutletID] = $row->NamaOutlet . ' ' . $row->AlamatOutlet;
            }
        } else {
            $this->load->model('Userperusahaancabang');
            $availableCabangs = $this->Userperusahaancabang->getListCabang(getLoggedInUsername(), getLoggedInUserID());
            foreach ($result as $row) {
                foreach ($availableCabangs as $cabang) {
                    if ($cabang->OutletID == $row->OutletID) {
                        $retval[$row->OutletID] = $row->NamaOutlet . ' ' . $row->AlamatOutlet;
                    }
                }

            }
        }
        return $retval;
    }

    public function account()
    {

        ifNotAuthenticatedRedirectToLogin();
        $this->output->set_header("HTTP/1.0 200 OK");
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        $mode = $this->session->flashdata('m');
        $error = $this->session->flashdata('error');

        $this->load->model('Perusahaanmodel');
        $this->load->model('Userperusahaan');

        if (getLoggedInNamaPerusahaan() != "Individual") {
            $isOwner = $this->Userperusahaan->isUserOwner(getLoggedInUserID(), getLoggedInUsername());
        } else {
            $isOwner = true;
        }

        if ($isOwner) {
            $daftardevice = $this->Perusahaanmodel->getDaftarDevice(getLoggedInUserID(), getLoggedInUsername());
        } else {
            $this->load->model('Userperusahaancabang');
            $daftardevice = $this->Perusahaanmodel->getDaftarDeviceNonOwner(getLoggedInUserID(), getLoggedInUsername());
        }

        $data['outlet_count'] = count($daftardevice);
        $data['page_part'] = 'cloud/akun_saya';
        $data['js_part'] = array(
            'features/js/js_chart',
            'features/js/js_form',
            'features/js/js_form_validation',
            'features/js/js_akun_saya'
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['mode'] = $mode;
        $data['error'] = $error;


        $query_nama = $this->db->get_where('perusahaan', array('perusahaanid' => getLoggedInUserID()));
        $row_nama = $query_nama->row();
        $data['nama_perusahaan'] = $row_nama->namaperusahaan;
        $this->load->model('Outlet');
        $info_outlet = $this->Outlet->get_outlet_expired_date_by_perusahaanid($this->db->escape(getLoggedInUserID()));
        $data['list_outlet'] = $info_outlet;
        $this->load->model('Userperusahaan');
        $fotoAndEmail = $this->Userperusahaan->getUrlFotoAndEmail(getLoggedInUserID(), getLoggedInUsername());
        $urlFoto = $fotoAndEmail['UrlFoto'];
        $email = $fotoAndEmail['Email'];
        $data['email'] = $email;
        $data['password'] = $this->Userperusahaan->getEmailPasswordByPerusahaanUsername(getLoggedInUserID(), getLoggedInUsername());
        if (!isNotEmpty($urlFoto)) {
            $url = base_url('images/user.png');
        } else {
            $url = base_url() . $urlFoto;
        }
        $data['urlfoto'] = $url;
        $this->load->view('main_part_perusahaan', $data);
    }

    public function accountpost()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $oldpassword = $this->input->get_post('passwordlama');
            $newpassword = $this->input->get_post('passwordbaru');
            $confirmnewpassword = $this->input->get_post('confirmpassword');
            $newemail = $this->input->get_post('emailbaru');
            $ext = $this->input->get_post('ext');
            $hapus_foto = $this->input->get_post('hapus_foto');
            $this->load->model('Userperusahaan');
            if (isNotEmpty($newpassword)) {
                if ($newpassword != $confirmnewpassword) {
                    $this->session->set_flashdata('m',1);
                    redirect(base_url('cloud/account'));
                } else {

                    $result = $this->Userperusahaan->changePassword(getLoggedInUserID(), getLoggedInUsername(), $oldpassword, $newpassword);
                    if ($result === 'OK') {

                    } else {
                        $this->session->set_flashdata('error','Gagal mengubah password.');
                        $this->session->set_flashdata('m',3);
                        redirect(base_url('cloud/account'));
                    }
                }
            }
            if (isNotEmpty(trim($newemail))) {
                $result = $this->Userperusahaan->changeEmail(getLoggedInUserID(), getLoggedInUsername(), $oldpassword, $newemail);
                if ($result === 'OK') {

                } else {
                    if ($result === 'Password lama salah'){
                        $this->session->set_flashdata('error','Password lama salah.');
                        $this->session->set_flashdata('m',3);
                        redirect(base_url('cloud/account'));
                    }else{
                        $this->session->set_flashdata('m',4);
                        redirect(base_url('cloud/account'));
                    }
                }
            }
            if (isNotEmpty(trim($ext))) {
                #upload foto
                $config['upload_path'] = 'uploaded_photos/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = 5*1024;
                $config['overwrite'] = TRUE;
                $config['file_name'] = getLoggedInUserID() . '-' . getLoggedInUsername() . '.' . $ext;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('foto')) {
                    $this->session->set_flashdata('error',$this->upload->display_errors());
                    $this->session->set_flashdata('m',3);
                    redirect(base_url('cloud/account'));
                } else {
                    $data = $this->upload->data();
                    $this->Userperusahaan->updateImageFoto('uploaded_photos/' . $data['file_name'], getLoggedInUserID(), getLoggedInUsername());
                    $fotoAndEmail = $this->Userperusahaan->getUrlFotoAndEmail(getLoggedInUserID(), getLoggedInUsername());
                    $this->session->set_userdata('foto', base_url($fotoAndEmail['UrlFoto']));
                }

            }
            if (isNotEmpty($hapus_foto)){
                $this->Userperusahaan->updateImageFoto(null, getLoggedInUserID(), getLoggedInUsername());
                $this->session->set_userdata('foto', base_url('images/ukuran-foto-cloud-nav.png'));
            }

            $this->session->set_flashdata('m',2);
            redirect(base_url('cloud/account'));
        }
    }

    public function changepassword()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $oldpassword = $this->input->get_post('oldpassword');
            $newpassword = $this->input->get_post('newpassword');
            $confirmnewpassword = $this->input->get_post('confirmpassword');
            $this->load->model('Userperusahaan');
            if (isNotEmpty($newpassword)) {
                $result = $this->Userperusahaan->changePassword(getLoggedInUserID(), getLoggedInUsername(), $oldpassword, $newpassword);
                if ($result !== 'OK') {
                    echo json_encode(['status' => 'error', 'field' => 'oldpassword', 'message' => 'Password lama anda salah.']);
                    exit;
                }

                if ($newpassword != $confirmnewpassword) {
                    echo json_encode(['status' => 'error', 'field' => 'confirmpassword', 'message' => 'Konfirmasi Password Baru Tidak Sama']);
                    exit;
                }

                echo json_encode(['status' => 'success', 'message' => base_url('cloud/account?m=2')]);
                exit;
            }
        }
    }

    /* TODO Validation OLD EMAIL & Error Call to a member function changeEmail() on null */
    public function changeemail()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $oldpassword = $this->input->get_post('oldpassword');
            $newemail = $this->input->get_post('newemail');
            $this->load->model('Userperusahaan');
            if (isNotEmpty(trim($newemail))) {
                $result = $this->Userperusahaan->changeEmail(getLoggedInUserID(), getLoggedInUsername(), $oldpassword, $newemail);
                if ($result === 'OK') {
                    $this->session->set_flashdata('m',2);
                    echo json_encode(['status' => 'success', 'message' => base_url('cloud/account')]);
                    exit;
                } else {
                    if ($result === 'Password lama salah') {
                        echo json_encode(['status' => 'error', 'field' => 'oldpassword', 'message' => 'Password lama anda salah.']);
                        exit;
                    }
                    else {
                        echo json_encode(['status' => 'error', 'field' => 'newemail', 'message' => 'Email sudah terdaftar']);
                        exit;
                    }
                }
            }
        }
    }

    public function cektablet()
    {
        $this->load->view('cloud/cek_tablet');
    }

    public function finfini() {
        $this->load->library('finfini');
        $a = $this->finfini->account_list();
        printf(json_encode($a));
    }
    public function reconnect() {
        $this->load->library('finfini');
        $a = $this->finfini->reconnect();
        printf(json_encode($a));
    }

    public function delete() {
        $this->load->library('finfini');
        $a = $this->finfini->delete_account(1830);
        printf(json_encode($a));
    }

    public function createuserfinfini() {
        $this->load->library('finfini');
        $a = $this->finfini->create_user();
        printf(json_encode($a));
    }

    public function getvendors() {
        $this->load->library('finfini');
        $a = $this->finfini->get_vendor();
        if ($a->status == 'success') {
            $this->db->trans_begin();

            foreach ($a->data as $key => $value) {
                if ($value->status == 'active') {
                    $data = array(
                        'bank_name' => $value->name,
                        'vendor_id' => $value->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'type' => $value->vendor_type
                    );
                    $this->db->insert('masterbank', $data);
                }
            }

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
            }
            else
            {
                $this->db->trans_commit();
            }
        }
        var_dump($a);
    }

    public function syncAccount() {
        $this->load->library('finfini');
        $this->db->where('account_id is not null');
        $this->db->where('connected', 1);
        $hasil = $this->db->get('mastercashbankaccount')->result();
        if (sizeof($hasil) > 0) {
            foreach ($hasil as $k => $v) {
                $a = $this->finfini->account_sync($v->account_id);
                if ($a->request_code == 400 || $a->request_code == "400") {
                    $b = $this->finfini->account_sync($v->account_id);
                    if ($b->request_code == 400 || $b->request_code == "400") {
                        $dataUpdate = array(
                            'connected' => 0,
                            'tries' => 2
                        );
                        $this->db->where('account_id', $v->account_id);
                        $this->db->update('mastercashbankaccount', $dataUpdate);
                    }
                }
            }
        }
        $return = ["status" => true];
        echo json_encode($return);
    }

    function syncbyacct() {
        $acctSplit = explode(".", $this->input->post('acct'));
        $this->load->library('finfini');
        $this->db->where('account_id is not null');
        $this->db->where('connected', 1);
        $this->db->where('AccountID', $acctSplit[0]);
        $this->db->where('DeviceID', $this->input->post('o'));
        $hasil = $this->db->get('mastercashbankaccount')->result();
        if (sizeof($hasil) > 0) {
            foreach ($hasil as $k => $v) {
                $a = $this->finfini->account_sync($v->account_id);
                if ($a->request_code == 400 || $a->request_code == "400") {
                    $b = $this->finfini->account_sync($v->account_id);
                    if ($b->request_code == 400 || $b->request_code == "400") {
                        $dataUpdate = array(
                            'connected' => 0,
                            'tries' => 2
                        );
                        $this->db->where('account_id', $v->account_id);
                        $this->db->update('mastercashbankaccount', $dataUpdate);
                        $ret= ["status" => false, "message" => "silahkann update password dan username anda"];
                        echo json_encode($ret);
                        exit;
                    } else {
                        $ret= ["status" => true];
                        echo json_encode($ret);
                        exit;
                    }
                } else {
                    $ret= ["status" => true];
                    echo json_encode($ret);
                    exit;
                }
            }
        } else {
            $ret= ["status" => false, "message" => "silahkann update password dan username anda"];
            echo json_encode($ret);
        }
    }
}
