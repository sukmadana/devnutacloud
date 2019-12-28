<?php

class Perusahaan extends MY_Controller
{

    var $DevID = '';

    public function __construct()
    {
        parent::__construct();
        $this->DevID = getLoggedInUserID();
    }

    public function gate()
    {
        //jika sudah terdaftar id perusahaan maka ke halaman akun saya
        //jika belum ke registrasi
        $this->load->model('User');
        $this->load->model('Perusahaanmodel');
        $emailBelumKonfirm = $this->Perusahaanmodel->GetIdPerusahaanBelumConfirm(getLoggedInUserID());
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) { //belum daftar perusahaan
            redirect(base_url() . 'perusahaan/registrasi');
        } else if (isset($emailBelumKonfirm)) { // belum konfirmasi

            redirect(base_url() . 'perusahaan/kirimidperusahaan?e=' . base64_encode($emailBelumKonfirm));
        } else {
            redirect(base_url() . 'perusahaan/akunsaya');
        }
    }

    protected function doRegistrasi($postData)
    {

        $errors = array();
        if (isNotEmpty($postData['namaperusahaan']) == false) {
            array_push($errors, 'Nama Perusahaan tidak boleh kosong');
        }
        if (isNotEmpty($postData['pemilik']) == false) {
            array_push($errors, 'Nama Pemilik tidak boleh kosong');
        }
        if (isNotEmpty($postData['email']) == false) {
            array_push($errors, 'Email tidak boleh kosong');
        }
        if (isNotEmpty($postData['registerwithdeviceid'] == false)) {
            array_push($errors, 'ID tidak boleh kosong');
        }
        if (count($errors) > 0) {
            $this->load->model('Perusahaanmodel');
            $emailSudahDipakai = $this->Perusahaanmodel->isEmailPerusahaanExist($postData['email']);
            if ($emailSudahDipakai) {
                array_push($errors, 'Email sudah terdaftar');
            }
        }
        if (count($errors) > 0) {
            $data['page_part'] = 'perusahaan/registrasi_perusahaan';
            $data['js_part'] = array(
                "features/js/js_form_validation",
                'features/js/js_form_validation_registrasi_perusahaan'
            );
            $data['js_chart'] = array();
            $data['visibilityMenu'] = $this->visibilityMenu;
            $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
            $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
            $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
            $data['isUsePushNotification'] = $this->IsUsePushNotification();
            $data['email'] = $postData['email'];
            $data['error'] = $errors;
            $this->load->view('main_part_perusahaan', $data);
            return;
        } else {
            $this->load->helper('hashids_helper');
            $this->load->model('perusahaanmodel');
            $time = time();
            $idperusahaan = $this->perusahaanmodel->generatePerusahaanID(
                $postData['namaperusahaan'],
                $postData['email'],
                $postData['pemilik'],
                $postData['registerwithdeviceid']
            );
            $postData['PerusahaanID'] = $idperusahaan;
            $postData['time'] = $time;
            $this->perusahaanmodel->Create($postData);
            $this->load->model('User');
            $this->User->UpdatePerusahaanID($postData['registerwithdeviceid'], $idperusahaan);

            $this->load->library('email');
            $this->email->from('no-reply@nutacloud.com', 'no-reply@nutacloud.com');
            $this->email->to($postData['email']);

            $url = base_url() . "perusahaan/pass?a=" . base64_encode($this->DevID . "#" . $time . "#" . $idperusahaan);
            $subject = "ID Perusahaan anda";
            $this->email->subject($subject);
            $message = $this->load->view('mail/mail_registrasi_perusahaan', array('url' => $url, 'subject' => $subject, 'namaperusahaan' => $postData['namaperusahaan']), true);
            $this->email->message($message);
            $a = $this->email->send();
            redirect(base_url() . 'perusahaan/kirimidperusahaan?e=' . base64_encode($postData['email']));
        }
    }

    public function Kirimidperusahaan()
    {
        ifNotAuthenticatedRedirectToLogin();
        $viewData['page_part'] = "perusahaan/kirim_idperusahaan";
        $viewData['js_part'] = array("features/js/js_form_validation");
        $viewData['js_chart'] = array();
        $viewData['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $viewData['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $viewData['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $viewData['email'] = base64_decode($this->input->get('e'));
        $viewData['visibilityMenu'] = $this->visibilityMenu;
        $this->load->view('main_part_perusahaan', $viewData);
    }

    public function Registrasi()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $postNamaPerusahaan = $this->input->get_post('namaperusahaan');
            $postEmail = $this->input->get_post('email');
            $postNamaPemilik = $this->input->get_post('namapemilik');
            $this->doRegistrasi(array(
                'namaperusahaan' => $postNamaPerusahaan, 'email' => $postEmail,
                'pemilik' => $postNamaPemilik,
                'registerwithdeviceid' => $this->DevID
            ));
            return;
        }
        $this->load->model('User');
        $isTidakPunyaPerusahaanID = $this->User->TidakPunyaPerusahaanID(getLoggedInUserID());
        $punyaPerusahaanID = !$isTidakPunyaPerusahaanID;
        if ($punyaPerusahaanID) {
            if (getLoggedInNamaPerusahaan() != "Individual") {
                redirect(base_url() . 'perusahaan/akunsaya');
            } else {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        $data['page_part'] = 'perusahaan/registrasi_perusahaan';
        $data['js_part'] = array("features/js/js_form_validation", 'features/js/js_dialog', 'features/js/js_form_validation_registrasi_perusahaan');
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['email'] = $this->User->getEmailByUsername(getLoggedInUsername());
        $data['error'] = array();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function Akunsaya()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }
        $this->load->model('Perusahaanmodel');
        $daftardevice = $this->Perusahaanmodel->getDaftarDevice(getLoggedInUserID());
        log_message('error', "mengakses perusahaan/akunsaya : " . getLoggedInUserID() . " " . getLoggedInUsername());
        $this->load->model('Userperusahaan');
        $listUser = $this->Userperusahaan->getListUser(getLoggedInUserID());
        $data['daftaruser'] = $listUser;
        $data['page_part'] = 'perusahaan/perusahaan_saya';
        $data['daftardevice'] = $daftardevice;
        $data['modulperusahaan'] = $this->Perusahaanmodel->getModulPerusahaan(getLoggedInUserID());
        $data['js_part'] = array(
            "features/js/js_form",
            "features/js/js_ajax_switch",
            'features/js/js_ajax_switch_modul_perusahaan',
            'features/js/js_alert_nonaktif_outlet',
            'features/js/js_alert_delete_outlet'
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function Outlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('Outlet');
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

        $this->load->model('Userperusahaan');
        $this->load->model('Usertablet');
        $listUser = $this->Userperusahaan->getListUser(getLoggedInUserID());
        $data['daftaruser'] = $listUser;
        $data['page_part'] = 'outlet/index';
        $data['daftardevice'] = $daftardevice;
        $data['modulperusahaan'] = $this->Perusahaanmodel->getModulPerusahaan(getLoggedInUserID());
        $data['js_part'] = array(
            "features/js/js_form",
            "features/js/js_ajax_switch",
            'features/js/js_ajax_switch_modul_perusahaan',
            'features/js/js_alert_nonaktif_outlet',
            'features/js/js_datatables',
            'features/js/js_outlet_index',
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['OutletEdit'] = $data['visibilityMenu']['OutletEdit'];
        $data['OutletNew'] = $data['visibilityMenu']['OutletNew'];

        $options = $this->db->order_by('TglExpired', 'DESC')->get_where('options', ['OutletID' => $OutletID])->result();
        $deviceid = [];
        foreach ($options as $o) {
            $deviceid[] = $o->DeviceID;
        }
        if (!$deviceid) {
            $data['detail_user'] = [];
        } else {
            $data['detail_user'] = $this->db->where_in('DeviceID', $deviceid)->get('usertablet')->result();
        }
        $this->load->view('main_part_perusahaan', $data);
    }

    public function Perangkat()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        $data['js_part'] = array(
            'features/js/js_datatable',
            'features/js/js_perangkat_list',
        );

        $this->load->model('DeviceApp');

        $data['page_part'] = 'perusahaan/perangkat_index';

        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function ajaxgetperangkat()
    {
        $draw = $this->input->post('draw', true);
        $start = (int) $this->input->post('start', true);
        $length = (int) $this->input->post('length', true);
        $order = $this->input->post('order', true);
        $search = $this->input->post('search', true);

        $sql = "SELECT COUNT(*) AS recordsTotal FROM device_app DA WHERE DA.PerusahaanNo = ?";
        $q = $this->db->query($sql, array(getPerusahaanNo()));
        $recordsTotal = (int) $q->row()->recordsTotal;

        if ($length <= 0) {
            $length = $recordsTotal;
        }

        $sql2 = "
    SELECT
    OU.PerusahaanNo,
    OU.OutletID,
    OU.NamaOutlet,
    OU.AlamatOutlet,
    DA.DeviceNo,
    (SELECT MAX(CreatedDate) FROM sale WHERE DeviceID = OU.OutletID) as 'TerakhirDipakai',
    DA.IsActive
    FROM device_app DA
    JOIN outlet OU ON OU.OutletID = DA.OutletID
    WHERE
    DA.PerusahaanNo = ?
    AND (
        OU.NamaOutlet LIKE ?
        OR OU.AlamatOutlet LIKE ?
        )
        " . $this->sqlOrder($order) . "
        LIMIT ?,?
        ";
        $q2 = $this->db->query($sql2, array(
            getPerusahaanNo(),
            $search["value"] . "%",
            $search["value"] . "%",
            $start,
            $length,
        ));
        $data = $q2->result();

        $recordFiltered = count($data);

        $r = new stdClass;
        $r->draw = $draw;
        $r->recordsTotal = (int) $recordsTotal;
        $r->recordsFiltered = (int) $recordFiltered;
        $r->data = $data;
        $r->sql = $this->sqlOrder($order);

        echo json_encode($r);
    }

    private function sqlOrder($order)
    {
        if (!is_array($order)) {
            return "";
        }

        $columns = [
            'NamaOutlet',
            'AlamatOutlet',
            'DeviceNo',
            'TerakhirDipakai',
            'IsActive',
        ];
        $dirs = array("asc" => "ASC", "ASC" => "ASC", "desc" => "DESC", "DESC" => "DESC");

        $data = array();
        foreach ($order as $o) {
            array_push($data, $columns[$o["column"]] . " " . $dirs[$o["dir"]]);
        }

        return "ORDER BY " . implode(",", $data);
    }

    public function ajaxtindakan()
    {
        $perusahaanNo = $this->input->post('PerusahaanNo', true);
        $outletID = $this->input->post('OutletID', true);
        $deviceNo = $this->input->post('DeviceNo', true);
        $isActive = $this->input->post('IsActive', true);

        $this->load->model('DeviceApp');

        $this->DeviceApp->updateStatus($perusahaanNo, $outletID, $deviceNo, $isActive === "true");

        echo "200";
    }

    public function user()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        // Cek user role
        $role = $this->input->get('role');
        if ($role) {
            switch ($role) {
                case 'cloud':
                    redirect('perusahaan/usercloud');

                default:
                    redirect('perusahaan/usertablet');
            }
        }

        $this->load->model('Userperusahaan');
        $this->load->model('Perusahaanmodel');
        $listUser = $this->Userperusahaan->getListUser(getLoggedInUserID());
        $data['page_part'] = 'perusahaan/user_perusahaan';
        $data['daftaruser'] = $listUser;

        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function usercloud()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet) {
            $selected_outlet = 0;
        }

        $data['page_part'] = 'perusahaan/user_cloud';
        $data['outlets'] = $availableOutlets;
        $data['selected_outlet'] = $selected_outlet;
        $listUser = $this->Userperusahaan->getListUser(getLoggedInUserID());
        $data['totaluser'] = count($listUser);

        $data['js_part'] = array(
            'features/js/js_socket',
            'features/js/js_usercloud_list',
            'features/js/js_datatable',
            "features/js/js_alert_delete_user",
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function ajaxusercloud()
    {
        $this->load->model('Userperusahaan');
        $draw = $this->input->post('draw', true);
        $start = (int) $this->input->post('start', true);
        $length = (int) $this->input->post('length', true);
        $order = $this->input->post('order', true);
        $search = $this->input->post('search', true);

        $perusahaanID = getLoggedInUserID();
        $params = array(
            'PerusahaanID' => $perusahaanID,
            'draw' => $draw,
            'start' => $start,
            'length' => $length,
            'order' => $order,
            'search' => $search,
        );
        $result = $this->Userperusahaan->getDatatablesUserCloud($params, $this->visibilityMenu);

        echo json_encode($result);
    }

    public function usercloudform()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        $this->load->model('Perusahaanmodel');
        $this->load->model('Userperusahaan');

        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        $daftardevice = $this->Perusahaanmodel->getDaftarDevice(getLoggedInUserID(), $username);
        $cabang = array();
        foreach ($daftardevice as $d) {
            array_push($cabang, array(
                'allow' => false, 'outletid' => $d->OutletID,
                'namacabang' => $d->NamaOutlet, 'pemilik' => $d->Username,
                'alamat' => $d->AlamatOutlet
            ));
        }

        $data['page_part'] = 'perusahaan/form_user_cloud';
        $data['daftardevice'] = $cabang;
        $data['rs_hak_akses'] = $this->Userperusahaan->getAllHakAkses(array());
        $data['allowDailyRerport'] = 'checked';
        $data['js_part'] = array(
            "features/js/js_usercloud",
            "features/js/js_datatable",
            "features/js/js_form_validation",
            "features/js/js_form_validation_user_perusahaan",
            "features/js/js_form",
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function usercloudformprocess()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        }

        $selecteduser = $this->input->get_post('selecteduser');
        $postUsername = $this->input->get_post('username');
        $postPassword = $this->input->get_post('password');
        $postEmail = $this->input->get_post('email');
        $postJabatan = $this->input->get_post('jabatan');


        $this->load->model('Userperusahaan');
        if (!$selecteduser) {
            // Add New
            $SentDailyReport = $this->input->get_post('SentDailyReport');
            $rs_allowakses = $this->input->get_post('allowakses');
            $rs_allowoutlet = $this->input->get_post('allowoutlet');
            $params = array(
                'username' => $postUsername,
                'password' => $postPassword,
                'email' => $postEmail,
                'PerusahaanID' => getLoggedInUserID(),
                'PerusahaanNo' => getPerusahaanNo(),
                'jabatan' => $postJabatan,
                'IsOwner' => 0, 'SudahKonfirmasi' => 1, 'SentDailyReport' => ($SentDailyReport == 'on' ? 1 : 0)
            );
            $resultIsTrue = $this->Userperusahaan->insertUserperusahaan($params, $rs_allowakses, $rs_allowoutlet);
            if ($resultIsTrue) {
                redirect(base_url() . 'perusahaan/usercloud');
            } else {
                redirect(base_url() . 'perusahaan/usercloudform');
            }
        } else {
            // Update
            $params = array(
                'username' => $postUsername,
                'password' => $postPassword,
                'email' => $postEmail,
                'jabatan' => $postJabatan
            );
            $where = array(
                'username' => $selecteduser
            );
            if ($this->Userperusahaan->Update($params, $where)) {
                redirect(base_url() . 'perusahaan/userclouddetail?user=' . $postUsername);
            } else {
                redirect(base_url() . 'perusahaan/usercloud');
            }
        }
    }

    public function userclouddetail()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        $this->load->model('Perusahaanmodel');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        // DATA
        $username = $data['selecteduse'] = $this->input->get('user');
        $this->load->model('Userperusahaan');
        $userperusahaan = $this->Userperusahaan->getUserByUsernameAndPerusahaanID(getLoggedInUserID(), $username);
        $jenisUser = $userperusahaan->IsOwner == '1' ? 'Administrator' : 'Biasa';
        $data['form'] = array(
            'username' => $userperusahaan->username,
            'password' => $userperusahaan->password,
            'email' => $userperusahaan->email,
            'TglUserDibuat' => $userperusahaan->TglJamUpdate,
            'jabatan' => $userperusahaan->Jabatan, 'jenis_user' => $jenisUser, 'IsOwner' => $userperusahaan->IsOwner
        );
        $aksescloud = $this->Useraksescloud->getAkses($username, getLoggedInUserID());
        $data['rs_hak_akses'] = $this->Userperusahaan->getAllHakAkses($aksescloud);
        $daftardevice = $this->Perusahaanmodel->getDaftarDevice(getLoggedInUserID(), $username);
        $this->load->model('Userperusahaancabang');
        $cabang = array();
        foreach ($daftardevice as $d) {
            $isallowcabang = $this->Userperusahaancabang->isUserAllowAksesCabang($d->OutletID, $username, getLoggedInUserID());
            array_push($cabang, array(
                'allow' => $isallowcabang, 'outletid' => $d->OutletID,
                'namacabang' => $d->NamaOutlet,
                'pemilik' => $d->Username,
                'alamat' => $d->AlamatOutlet
            ));
        }

        $data['page_part'] = 'perusahaan/detail_user_cloud';
        $data['daftardevice'] = $cabang;
        $selected_user = $this->Userperusahaan->getUserByUsernameAndPerusahaanID(getLoggedInUserID(), $username);
        $data['selectedusername'] = $username;
        $data['selecteduser'] = $selected_user;
        $data['allowDailyRerport'] = $selected_user->SentDailyReport == 1 ? "checked" : "";
        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_usercloud",
            "features/js/js_datatable",
            "features/js/js_form_validation",
            "features/js/js_form_validation_user_perusahaan",
            "features/js/js_form", "features/js/js_ajax_switch_user_akses",
            "features/js/js_ajax_switch_user_cabang",
            "features/js/js_ajax_switch_user_daily_report",
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function userform()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        }
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $postUsername = $this->input->get_post('username');
            $postPassword = $this->input->get_post('password');
            $postEmail = $this->input->get_post('email');
            $this->load->model('Userperusahaan');
            $resultIsTrue = $this->Userperusahaan->Create(array(
                'username' => $postUsername, 'password' => $postPassword,
                'email' => $postEmail,
                'PerusahaanID' => getLoggedInUserID(),
                'IsOwner' => 0, 'SudahKonfirmasi' => 1
            ));
            if ($resultIsTrue) {
                $this->load->model('Useraksescloud');
                $this->Useraksescloud->Create(array('PerusahaanID' => getLoggedInUserID(), 'Username' => $postUsername));
                redirect(base_url() . 'perusahaan/userdetail?x=' . $postUsername);
            } else {
                redirect(base_url() . 'perusahaan/userform');
            }
        }
        $this->load->model('Perusahaanmodel');
        $daftardevice = $this->Perusahaanmodel->getDaftarDevice(getLoggedInUserID());

        $data['page_part'] = 'perusahaan/user_perusahaan_daftar';
        $data['daftardevice'] = $daftardevice;

        $data['js_part'] = array(
            "features/js/js_form_validation",
            "features/js/js_form_validation_user_perusahaan",
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function usertablet()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        $this->load->library('NutaQuery');

        $selected_outlet = $this->input->get('outlet');
        if (isNotEmpty($selected_outlet)) {
            $this->nutaquery->SetOutlet($selected_outlet);
        } else if (count($availableOutlets) == 1) {
            $this->nutaquery->SetOutlet($this->default_outlet_id);
        }

        $selected_outlet = $this->nutaquery->getOutlet();

        $this->load->model('Usertablet');
        $listUser = $this->Usertablet->getListUser(getPerusahaanNo(), $selected_outlet);

        $data['page_part'] = 'perusahaan/user_tablet';
        $data['outlets'] = $availableOutlets;
        $data['selected_outlet'] = $selected_outlet;
        $data['daftaruser'] = $listUser;

        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_form",
            "features/js/js_usertablet",
            "features/js/js_usertablet_list",
            "features/js/js_datatable",
            "features/js/js_alert_delete_usertablet",
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function usertabletform()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        $this->load->model('Usertablet');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        $selectedoutlet = $this->input->get('outlet');
        if (!$selectedoutlet) {
            redirect(base_url() . 'perusahaan/usertablet');
        }

        $data['page_part'] = 'perusahaan/form_user_tablet';
        $data['selectedoutlet'] = $selectedoutlet;
        $data['rs_hak_akses'] = $this->Usertablet->getAllHakAkses(array());
        $data['js_part'] = array(
            "features/js/js_form_validation",
            "features/js/js_form_validation_user_tablet",
            "features/js/js_form",
            "features/js/js_ajax_switch_usertablet_akses",
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function usertabletdetail()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        $this->load->model('Usertablet');
        $this->load->model('Options');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        $user = $this->input->get('user');
        $outlet = $this->input->get('outlet');
        $usertablet = $this->Usertablet->getUser($user, getPerusahaanNo(), $outlet);
        $data['page_part'] = 'perusahaan/detail_user_tablet';
        $data['form'] = $usertablet;
        $data['options'] = $this->Options->get_by_devid($outlet);
        $data['selecteduser'] = $user;
        $data['selectedoutlet'] = $data['selected_outlet'] = $outlet;
        $data['rs_hak_akses'] = $this->Usertablet->getAllHakAkses($usertablet);
        $data['js_part'] = array(
            "features/js/js_usertablet",
            "features/js/js_form",
            "features/js/js_ajax_switch_usertablet_akses",
            "features/js/js_form_validation",
            "features/js/js_form_validation_user_tablet",
            "features/js/js_alert_delete_usertablet",
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part_perusahaan', $data);
    }

    public function usertabletformprocess()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        }

        $getOutlet = (int) $this->input->get('outlet');
        $postUsername = $this->input->get_post('username');
        $postPassword = $this->input->get_post('password');
        $postEmail = $this->input->get_post('email');
        $postJabatan = $this->input->get_post('jabatan');
        $postAllowAkses = $this->input->get_post('allowakses');
        $this->load->model('Usertablet');
        $lastID = $this->Usertablet->getLastID();
        $UserID = ($lastID + 1);
        $params = array(
            'UserID' => $UserID,
            'Username' => $postUsername,
            'Password' => $postPassword,
            'Email' => $postEmail,
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $getOutlet,
            'jabatan' => $postJabatan,
            'Level' => 888,
            'CreatedVersionCode' => '0',
            'EditedVersionCode' => '0',
            'RowVersion' => 1,
            'AllowUserDanHakAkses' => 0,
            'Varian' => 'Nuta'
        );
        $resultIsTrue = $this->Usertablet->Create($params);
        if ($resultIsTrue) {
            foreach ($postAllowAkses as $key => $akses) {
                if ($akses == "off") {
                    $this->Usertablet->updateHakAkses($key, 0, $postUsername, getPerusahaanNo(), $getOutlet, $push = FALSE);
                }
            }

            // Push to firebase
            $this->Usertablet->pushFirebaseCreateOrUpdate($UserID, getPerusahaanNo(), $getOutlet);

            redirect(base_url() . 'perusahaan/usertablet?outlet=' . $getOutlet);
        } else {
            redirect(base_url() . 'perusahaan/usertabletform');
        }
    }

    public function pass()
    {
        $a = $this->input->get('a');
        if (isNotEmpty($a)) {
            $decode_a = base64_decode($a);
            $ex = explode('#', $decode_a);
            //        echo var_dump($ex);
            $deviceid = $ex[0];
            $time = $ex[1];
            $idperusahaan = $ex[2];
            //        $sessionDevid = getLoggedInUserID();
            //        if (!isNotEmpty($sessionDevid)) {
            //            redirect(base_url() . 'authentication/autoauth/trial?i=' . $deviceid . '&r=' . $this->current_url());
            //        }
            //nutacloud.com/perusahaan/pass?a=deviceid#time#idperusahaan
            $this->load->model('User');
            $user = $this->User->getUsernamePassword($deviceid);
            $this->load->model('Userperusahaan');
            $this->Userperusahaan->Create(
                array(
                    'username' => $user['username'],
                    'password' => $user['password'],
                    'email' => $user['email'],
                    'IsOwner' => 1,
                    'PerusahaanID' => $idperusahaan,
                    'registerwithdeviceid' => $deviceid
                )
            );
            $this->load->model('Useraksescloud');
            $this->Useraksescloud->Create(array('PerusahaanID' => $idperusahaan, 'Username' => $user['username'], 'HapusData' => 1));
            $this->load->model('Perusahaanmodel');
            $this->Perusahaanmodel->UpdateKonfirm($deviceid, $idperusahaan, $time);

            $data['page_part'] = 'perusahaan/konfirmasi_perusahaan';
            $data['js_part'] = array("features/js/js_form_validation");
            $data['js_chart'] = array();
            $data['visibilityMenu'] = $this->visibilityMenu;
            $data['idperusahaan'] = $idperusahaan;
            $this->load->view('perusahaan/blank_part', $data);
        } else {
            show_404();
        }
    }

    protected function current_url()
    {
        $CI = &get_instance();

        $url = $CI->config->site_url($CI->uri->uri_string());
        return $_SERVER['QUERY_STRING'] ? $url . '?' . $_SERVER['QUERY_STRING'] : $url;
    }

    public function userdetail()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }
        $this->load->model('Perusahaanmodel');
        $this->load->model('Useraksescloud');
        $username = $data['selecteduser'] = $this->input->get('x');
        $daftardevice = $this->Perusahaanmodel->getDaftarDevice(getLoggedInUserID());
        $this->load->model('Userperusahaancabang');
        $cabang = array();
        foreach ($daftardevice as $d) {
            $isallowcabang = $this->Userperusahaancabang->isUserAllowAksesCabang($d->OutletID, $username, getLoggedInUserID());
            array_push($cabang, array(
                'allow' => $isallowcabang, 'outletid' => $d->OutletID,
                'namacabang' => $d->NamaOutlet,
                'alamat' => $d->AlamatOutlet
            ));
        }
        $aksescloud = $this->Useraksescloud->getAkses($username, getLoggedInUserID());
        $data['allowLaporanPenjualan'] = $aksescloud->LaporanPenjualan == 1 ? "checked" : "";
        $data['allowLaporanRekapPenjualan'] = $aksescloud->LaporanRekapPenjualan == 1 ? "checked" : "";
        $data['allowLaporanRekapPenjualanPerKategori'] = $aksescloud->LaporanRekapPenjualanPerKategori == 1 ? "checked" : "";
        $data['allowLaporanRekapPembayaran'] = $aksescloud->LaporanRekapPembayaran == 1 ? "checked" : "";
        $data['allowLaporanPembelian'] = $aksescloud->LaporanPembelian == 1 ? "checked" : "";
        $data['allowLaporanRekapPembelian'] = $aksescloud->LaporanRekapPembelian == 1 ? "checked" : "";
        $data['allowLaporanSaldoKasRekening'] = $aksescloud->LaporanSaldoKasRekening == 1 ? "checked" : "";
        $data['allowLaporanStok'] = $aksescloud->LaporanStok == 1 ? "checked" : "";
        $data['allowLaporanKartuStok'] = $aksescloud->LaporanKartuStok == 1 ? "checked" : "";
        $data['allowLaporanRekapMutasiStok'] = $aksescloud->LaporanRekapMutasiStok == 1 ? "checked" : "";
        $data['allowLaporanLaba'] = $aksescloud->LaporanLaba == 1 ? "checked" : "";
        $data['allowHapusData'] = $aksescloud->HapusData == 1 ? "checked" : "";
        $data['allowLaporanPengeluaran'] = $aksescloud->LaporanPengeluaran == 1 ? "checked" : "";

        $dataHakAkses = array();
        $pekerjaan = new pekerjaandanakses("Produk", "ItemView", $aksescloud->ItemView, "");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Tambah Produk", "ItemAdd", $aksescloud->ItemAdd, "ItemView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Edit Produk", "ItemEdit", $aksescloud->ItemEdit, "ItemView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Hapus Produk", "ItemDelete", $aksescloud->ItemDelete, "ItemView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;

        $pekerjaan = new pekerjaandanakses("Pelanggan", "CustomerView", $aksescloud->CustomerView, "");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Tambah Pelanggan", "CustomerAdd", $aksescloud->CustomerAdd, "CustomerView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Edit Pelanggan", "CustomerEdit", $aksescloud->CustomerEdit, "CustomerView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Hapus Pelanggan", "CustomerDelete", $aksescloud->CustomerDelete, "CustomerView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;

        $pekerjaan = new pekerjaandanakses("Supplier", "SupplierView", $aksescloud->SupplierView, "");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Tambah Supplier", "SupplierAdd", $aksescloud->SupplierAdd, "SupplierView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Edit Supplier", "SupplierEdit", $aksescloud->SupplierEdit, "SupplierView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Hapus Supplier", "SupplierDelete", $aksescloud->SupplierDelete, "SupplierView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;

        $pekerjaan = new pekerjaandanakses("Pembelian", "PurchaseView", $aksescloud->PurchaseView, "");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Tambah Pembelian", "PurchaseAdd", $aksescloud->PurchaseAdd, "PurchaseView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Edit Pembelian", "PurchaseEdit", $aksescloud->PurchaseEdit, "PurchaseView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Hapus Pembelian", "PurchaseDelete", $aksescloud->PurchaseDelete, "PurchaseView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;

        $pekerjaan = new pekerjaandanakses("Stok (Stok Masuk, Stok Keluar, Koreksi Stok)", "StockView", $aksescloud->StockView, "");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Tambah Stok", "StockAdd", $aksescloud->StockAdd, "StockView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Edit Stok", "StockEdit", $aksescloud->StockEdit, "StockView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Hapus Stok", "StockDelete", $aksescloud->StockDelete, "StockView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;

        $pekerjaan = new pekerjaandanakses("Rekening Bank", "DataRekeningView", $aksescloud->DataRekeningView, "");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Tambah Rekening", "DataRekeningAdd", $aksescloud->DataRekeningAdd, "DataRekeningView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Edit Rekening", "DataRekeningEdit", $aksescloud->DataRekeningEdit, "DataRekeningView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Hapus Rekening", "DataRekeningDelete", $aksescloud->DataRekeningDelete, "DataRekeningView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;

        $pekerjaan = new pekerjaandanakses("Uang (Uang Masuk, Uang Keluar)", "MoneyView", $aksescloud->MoneyView, "");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Tambah Uang", "MoneyAdd", $aksescloud->MoneyAdd, "MoneyView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Edit Uang", "MoneyEdit", $aksescloud->MoneyEdit, "MoneyView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;
        $pekerjaan = new pekerjaandanakses("└ Hapus Uang", "MoneyDelete", $aksescloud->MoneyDelete, "MoneyView");
        $dataHakAkses[$pekerjaan->key] = $pekerjaan;

        $data['pekerjaan'] = $dataHakAkses;

        $data['page_part'] = 'perusahaan/user_perusahaan_detail';
        $data['daftardevice'] = $cabang;
        $this->load->model('Userperusahaan');
        $selected_user = $this->Userperusahaan->getUserByUsernameAndPerusahaanID(getLoggedInUserID(), $username);
        $data['selectedusername'] = $username;
        $data['selecteduser'] = $selected_user;
        $data['allowDailyRerport'] = $selected_user->SentDailyReport == 1 ? "checked" : "";
        $data['js_part'] = array(
            "features/js/js_form", "features/js/js_ajax_switch_user_akses",
            "features/js/js_ajax_switch_user_cabang",
            "features/js/js_ajax_switch_user_daily_report",
            "features/js/js_alert_delete_user"
        );
        $data['js_chart'] = array();

        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $this->load->view('main_part_perusahaan', $data);
    }

    public function usertabletdetail_()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }
        $this->load->model('Perusahaanmodel');
        $this->load->model('Usertablet');

        $username = $data['selecteduser'] = $this->input->get('x');
        $selected_outlet = $data['outlet'] = $this->input->get('outlet');

        $akses = $this->Usertablet->getUser($username, getPerusahaanNo(), $selected_outlet);

        $data['akses'] = array(
            'AllowKasir' => $akses->AllowKasir == 1 ? "checked" : "",
            'AllowEditNamaStand' => $akses->AllowEditNamaStand == 1 ? "checked" : "",
            'AllowTambahMenu' => $akses->AllowTambahMenu == 1 ? "checked" : "",
            'AllowEditMenu' => $akses->AllowEditMenu == 1 ? "checked" : "",
            'AllowHapusMenu' => $akses->AllowHapusMenu == 1 ? "checked" : "",
            'AllowEditPenjualan' => $akses->AllowEditPenjualan == 1 ? "checked" : "",
            'AllowHapusPenjualan' => $akses->AllowHapusPenjualan == 1 ? "checked" : "",
            'AllowHapusOrder' => $akses->AllowHapusOrder == 1 ? "checked" : "",
            'AllowTambahDataRekening' => $akses->AllowTambahDataRekening == 1 ? "checked" : "",
            'AllowEditDataRekening' => $akses->AllowEditDataRekening == 1 ? "checked" : "",
            'AllowHapusDataRekening' => $akses->AllowHapusDataRekening == 1 ? "checked" : "",
            'AllowTambahUangMasuk' => $akses->AllowTambahUangMasuk == 1 ? "checked" : "",
            'AllowEditUangMasuk' => $akses->AllowEditUangMasuk == 1 ? "checked" : "",
            'AllowHapusUangMasuk' => $akses->AllowHapusUangMasuk == 1 ? "checked" : "",
            'AllowTambahUangKeluar' => $akses->AllowTambahUangKeluar == 1 ? "checked" : "",
            'AllowEditUangKeluar' => $akses->AllowEditUangKeluar == 1 ? "checked" : "",
            'AllowHapusUangKeluar' => $akses->AllowHapusUangKeluar == 1 ? "checked" : "",
            'AllowLaporanPenjualan' => $akses->AllowLaporanPenjualan == 1 ? "checked" : "",
            'AllowLaporanRekapPenjualan' => $akses->AllowLaporanRekapPenjualan == 1 ? "checked" : "",
            'AllowLaporanRekapPembayaran' => $akses->AllowLaporanRekapPembayaran == 1 ? "checked" : "",
            'AllowLaporanSaldoKasRekening' => $akses->AllowLaporanSaldoKasRekening == 1 ? "checked" : "",
            'AllowLaporanLaba' => $akses->AllowLaporanLaba == 1 ? "checked" : "",
            'AllowLaporanPembelian' => $akses->AllowLaporanPembelian == 1 ? "checked" : "",
            'AllowLaporanRekapPembelian' => $akses->AllowLaporanRekapPembelian == 1 ? "checked" : "",
            'AllowLaporanStok' => $akses->AllowLaporanStok == 1 ? "checked" : "",
            'AllowLaporanKartuStok' => $akses->AllowLaporanKartuStok == 1 ? "checked" : "",
            'AllowLaporanRekapMutasiStok' => $akses->AllowLaporanRekapMutasiStok == 1 ? "checked" : "",
            'AllowLaporanAwan' => $akses->AllowLaporanAwan == 1 ? "checked" : "",
            'AllowPengaturan' => $akses->AllowPengaturan == 1 ? "checked" : "",
            'AllowAktivasi' => $akses->AllowAktivasi == 1 ? "checked" : "",
            'AllowHapusDataTransaksi' => $akses->AllowHapusDataTransaksi == 1 ? "checked" : "",
            'AllowDownloadDataAwan' => $akses->AllowDownloadDataAwan == 1 ? "checked" : "",
            'AllowZoomIn' => $akses->AllowZoomIn == 1 ? "checked" : "",
            'AllowZoomOut' => $akses->AllowZoomOut == 1 ? "checked" : "",
            'AllowDaftarPenjualan' => $akses->AllowDaftarPenjualan == 1 ? "checked" : "",
            'AllowPrINTDaftarPenjualan' => $akses->AllowPrINTDaftarPenjualan == 1 ? "checked" : "",
            'AllowPromo' => $akses->AllowPromo == 1 ? "checked" : "",
            'AllowPembelian' => $akses->AllowPembelian == 1 ? "checked" : "",
            'AllowTambahItemPembelian' => $akses->AllowTambahItemPembelian == 1 ? "checked" : "",
            'AllowEditItemPembelian' => $akses->AllowEditItemPembelian == 1 ? "checked" : "",
            'AllowHapusItemPembelian' => $akses->AllowHapusItemPembelian == 1 ? "checked" : "",
            'AllowEditPembelian' => $akses->AllowEditPembelian == 1 ? "checked" : "",
            'AllowHapusPembelian' => $akses->AllowHapusPembelian == 1 ? "checked" : "",
            'AllowTambahSupplier' => $akses->AllowTambahSupplier == 1 ? "checked" : "",
            'AllowEditSupplier' => $akses->AllowEditSupplier == 1 ? "checked" : "",
            'AllowHapusSupplier' => $akses->AllowHapusSupplier == 1 ? "checked" : "",
            'AllowKoreksiStok' => $akses->AllowKoreksiStok == 1 ? "checked" : "",
            'AllowTambahItemStok' => $akses->AllowTambahItemStok == 1 ? "checked" : "",
            'AllowEditItemStok' => $akses->AllowEditItemStok == 1 ? "checked" : "",
            'AllowHapusItemStok' => $akses->AllowHapusItemStok == 1 ? "checked" : "",
            'AllowEditKoreksiStok' => $akses->AllowEditKoreksiStok == 1 ? "checked" : "",
            'AllowHapusKoreksiStok' => $akses->AllowHapusKoreksiStok == 1 ? "checked" : "",
        );

        $data['page_part'] = 'perusahaan/user_tablet_detail';

        $data['js_part'] = array(
            "features/js/js_form",
            "features/js/js_ajax_switch_usertablet_akses",
            "features/js/js_alert_delete_usertablet",
        );
        $data['js_chart'] = array();

        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $this->load->view('main_part_perusahaan', $data);
    }

    public function newoutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('Userperusahaan');
        $error = array();
        $namaoutlet = '';
        $alamatoutlet = '';
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $postNamaOutlet = $this->input->get_post('namaoutlet');
            $postAlamatOutlet = $this->input->get_post('alamatoutlet');
            $postBisaDownload = $this->input->get_post('bisadownload');

            $PurchaseModule = $this->input->get_post('modulpembelian') == 'on' ? 'true' : 'false';
            $StockModule = $this->input->get_post('modulstok') == 'on' ? 'true' : 'false';
            $MenuRacikan = ($this->input->get_post('modulstokbahan') == 'on' && $this->input->get_post('modulstok') == 'on') ? 'true' : 'false';
            $PriceVariation = $this->input->get_post('modulvariasiharga') == 'on' ? 1 : 0;
            $StockModifier = ($this->input->get_post('modulstokmodifier') == 'on' && $this->input->get_post('modulstok') == 'on') ? 1 : 0;
            $SendReceiptToCustomerViaEmail = $this->input->get_post('strukviaemail') == 'on' ? 1 : 0;
            $DiningTable = $this->input->get_post('fiturmeja') == 'on' ? 1 : 0;
            $SupportBarcode = $this->input->get_post('supportbarcode') == 'on' ? 'true' : 'false';

            $bisadownload = $postBisaDownload == 'on' ? 1 : 0;
            $this->load->model('Outlet');
            $namaoutlet = $postNamaOutlet;
            $alamatoutlet = $postAlamatOutlet;

            $insert_outlet_data = array(
                'nama_outlet' => $this->input->post('namaoutlet'),
                'alamat_outlet' => $this->input->post('alamatoutlet'),
                'id_perusahaan' => getLoggedInUserID(),
                'allow_download_data' => $bisadownload,
                'nomor_perusahaan' => getPerusahaanNo(),
                'provinsi_outlet' => $this->input->post('provinsioutlet'),
                'kota_outlet' => $this->input->post('kotaoutlet'),
                'notelp_outlet' => $this->input->post('notelpoutlet'),
                'pemilik_outlet' => $this->input->post('pemilikoutlet')
            );
            $id = $this->Outlet->createNewOutlet($insert_outlet_data);
            if (gettype($id) === "string") {
                array_push($error, $id);
            } else {
                $this->Outlet->updateSudahKonfirmasi(getLoggedInUserID(), $id);
                $this->load->model('Userperusahaan');
                $email = $this->Userperusahaan->getEmailPasswordByPerusahaanUsername(getLoggedInUserID(), getLoggedInUsername())['email'];
                $this->load->model('Options');
                $id = $this->Options->createorupdate_by_devid(
                    $id,
                    $PurchaseModule,
                    $StockModule,
                    $MenuRacikan,
                    $PriceVariation,
                    $StockModifier,
                    $SendReceiptToCustomerViaEmail,
                    $DiningTable,
                    $SupportBarcode,
                    $postNamaOutlet,
                    $postAlamatOutlet,
                    $email,
                    $this->input->post('notelpoutlet')
                );
                if (gettype($id) === "string") {
                    array_push($error, $id);
                } else {
                    redirect(base_url() . 'perusahaan/outlet');
                    return;
                }
            }
        }
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            redirect(base_url() . 'perusahaan/registrasi');
        } else {
            if (getLoggedInNamaPerusahaan() == "Individual") {
                redirect(base_url() . 'cloud/main');
                exit;
            }
        }

        $data['js_part'] = array(
            "features/js/js_form",
            "features/js/js_form_validation",
            "features/js/js_form_validation_registrasi_outlet",
            "features/js/js_ajax_provinsi_kota"
        );
        $data['js_chart'] = array();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['page_part'] = 'outlet/add';
        $data['error'] = $error;
        $data['form_mode'] = 'Tambah Outlet';
        $data['nama_outlet'] = $namaoutlet;
        $data['alamat_outlet'] = $alamatoutlet;
        $data['id_outlet'] = '';
        $data['action_url'] = base_url() . 'perusahaan/newoutlet';
        $data['bisadownload'] = 0;
        $data['PurchaseModule'] = 0;
        $data['StockModule'] = 0;
        $data['MenuRacikan'] = 0;
        $data['PriceVariation'] = 0;
        $data['StockModifier'] = 0;
        $data['SendReceiptToCustomerViaEmail'] = 0;
        $data['DiningTable'] = 0;
        $data['userperusahaan'] = $this->Userperusahaan->getListUser(getLoggedInUserID());
        $this->load->view('main_part_perusahaan', $data);
    }

    public function outletdetail()
    {
        ifNotAuthenticatedRedirectToLogin();
        $error = array();
        $namaoutlet = '';
        $alamatoutlet = '';
        $bisadownload = '';
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $idoutlet = $this->input->get_post('id');
            if (isNotEmpty($idoutlet)) {
                $postNamaOutlet = $this->input->get_post('namaoutlet');
                $postAlamatOutlet = $this->input->get_post('alamatoutlet');
                $postBisaDownload = $this->input->get_post('bisadownload');
                $PurchaseModule = $this->input->get_post('modulpembelian') == 'on' ? 'true' : 'false';
                $StockModule = $this->input->get_post('modulstok') == 'on' ? 'true' : 'false';
                $MenuRacikan = ($this->input->get_post('modulstokbahan') == 'on' && $this->input->get_post('modulstok') == 'on') ? 'true' : 'false';
                $PriceVariation = $this->input->get_post('modulvariasiharga') == 'on' ? 1 : 0;
                $StockModifier = ($this->input->get_post('modulstokmodifier') == 'on' && $this->input->get_post('modulstok') == 'on') ? 1 : 0;
                $SendReceiptToCustomerViaEmail = $this->input->get_post('strukviaemail') == 'on' ? 1 : 0;
                $DiningTable = $this->input->get_post('fiturmeja') == 'on' ? 1 : 0;
                $SupportBarcode = $this->input->get_post('supportbarcode') == 'on' ? 'true' : 'false';
                $namaoutlet = $postNamaOutlet;
                $alamatoutlet = $postAlamatOutlet;
                $bisadownload = $postBisaDownload == 'on' ? 1 : 0;
                $this->load->model('Outlet');
                $retval = $this->Outlet->updateOutlet($idoutlet, $postNamaOutlet, $postAlamatOutlet, getLoggedInUserID(), $bisadownload);
                if (gettype($retval) === "string") {
                    array_push($error, $retval);
                } else {
                    $this->load->model('Userperusahaan');
                    $email = $this->Userperusahaan->getEmailPasswordByPerusahaanUsername(getLoggedInUserID(), getLoggedInUsername())['email'];
                    $this->load->model('Options');
                    $retval = $this->Options->createorupdate_by_devid(
                        $idoutlet,
                        $PurchaseModule,
                        $StockModule,
                        $MenuRacikan,
                        $PriceVariation,
                        $StockModifier,
                        $SendReceiptToCustomerViaEmail,
                        $DiningTable,
                        $SupportBarcode,
                        $postNamaOutlet,
                        $postAlamatOutlet,
                        $email
                    );
                    if (gettype($retval) === "string") {
                        array_push($error, $retval);
                    } else {
                        redirect(base_url() . 'perusahaan/outlet');
                        return;
                    }
                }
            }
        }
        $outletid = $this->input->get('x');
        $this->load->model('Outlet');
        $this->load->model('Options');
        $outlet_permission = $this->Outlet->get_outlet_user_permission(
            getLoggedInUserID(),
            getLoggedInUsername()
        );
        $OutletNew = $outlet_permission['OutletNew'];
        $OutletView = $outlet_permission['OutletView'];
        $OutletEdit = $outlet_permission['OutletEdit'];
        $OutletDelete = $outlet_permission['OutletDelete'];
        $outlet = $this->Outlet->getById(getLoggedInUserID(), $outletid);
        $option = $this->Options->get_by_devid($outletid);
        if ($namaoutlet === '') {
            $namaoutlet = $outlet->NamaOutlet;
        }
        if ($alamatoutlet === '') {
            $alamatoutlet = $outlet->AlamatOutlet;
        }
        if ($bisadownload === '') {
            $bisadownload = $outlet->DataMasterBisaDiambil;
        }

        $data['js_part'] = array("features/js/js_form");
        $data['js_chart'] = array();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['page_part'] = 'perusahaan/outlet_form';
        $data['error'] = $error;
        $data['form_mode'] = 'Pengaturan Outlet';
        $data['nama_outlet'] = $namaoutlet;
        $data['id_outlet'] = $outletid;
        $data['alamat_outlet'] = $alamatoutlet;
        $data['bisadownload'] = $bisadownload;

        if (isset($option)) {
            $data['PurchaseModule'] = $option->PurchaseModule == 'true' ? 1 : 0;
            $data['StockModule'] = $option->StockModule == 'true' ? 1 : 0;
            $data['MenuRacikan'] = $option->MenuRacikan == 'true' ? 1 : 0;
            $data['PriceVariation'] = $option->PriceVariation;
            $data['StockModifier'] = $option->StockModifier;
            $data['SendReceiptToCustomerViaEmail'] = $option->SendReceiptToCustomerViaEmail;
            $data['DiningTable'] = $option->DiningTable;
        } else {
            $data['PurchaseModule'] = 0;
            $data['StockModule'] = 0;
            $data['MenuRacikan'] = 0;
            $data['PriceVariation'] = 0;
            $data['StockModifier'] = 0;
            $data['SendReceiptToCustomerViaEmail'] = 0;
            $data['DiningTable'] = 0;
        }

        $data['action_url'] = base_url() . 'perusahaan/outletdetail?x=' . $outletid;
        $data['OutletEdit'] = $OutletEdit;
        $this->load->view('main_part_perusahaan', $data);
    }

    public function dummydetail($OutletID = null)
    {
        $this->load->model('Outlet');

        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['page_part'] = 'outlet/detail';
        $data['js_part'] = array("features/js/js_outlet_detail");
        $data['detail_outlet'] = $this->Outlet->getOutletByIdOnly($OutletID);
        $options = $this->db->order_by('TglExpired', 'DESC')->get_where('options', ['OutletID' => $OutletID])->result();
        $data['detail_options'] = $options[0];
        $deviceid = [];
        foreach ($options as $o) {
            $deviceid[] = $o->DeviceID;
        }
        if (!$deviceid) {
            $data['detail_user'] = [];
        } else {
            $data['detail_user'] = $this->db->where_in('DeviceID', $deviceid)->get('usertablet')->result();
        }
        $data['count_user_nuta'] = $this->Outlet->countNutaUser($OutletID);
        $data['detail_user_nuta'] = $this->Outlet->getNutaUser($OutletID);
        $data['OutletEdit'] = $data['visibilityMenu']['OutletEdit'];
        $data['OutletDelete'] = $data['visibilityMenu']['OutletDelete'];
        $this->load->view('main_part_perusahaan', $data);
    }

    public function dummyhistory($OutletID = null)
    {
        $data['detail_outlet'] = $this->db->get_where('outlet', ['OutletID' => $OutletID])->row();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['page_part'] = 'outlet/history';
        $this->load->view('main_part_perusahaan', $data);
    }

    public function dummyinvoice()
    {
        $this->load->view('outlet/invoice');
    }

    public function dummymodule($OutletID = null)
    {
        $this->load->model('Outlet');
        $outlet_permission = $this->Outlet->get_outlet_user_permission(
            getLoggedInUserID(),
            getLoggedInUsername()
        );
        $OutletEdit = $outlet_permission['OutletEdit'];
        if ($OutletEdit != 1) {
            redirect('perusahaan/outletdetailinfo/' . $OutletID, 'refresh');
        }
        $this->load->model('Outlet');
        $data['detail_outlet'] = $this->Outlet->getOutletByIdOnly($OutletID);
        $data['outlet_options'] = $this->Outlet->getOutletOptions($OutletID);
        $data['js_part'] = array(
            "features/js/js_form",
            "outlet/js/module",

        );
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['page_part'] = 'outlet/module';
        $this->load->view('main_part_perusahaan', $data);
    }

    public function editoutlet($OutletID = null)
    {
        $this->load->model('Outlet');
        $outlet_permission = $this->Outlet->get_outlet_user_permission(
            getLoggedInUserID(),
            getLoggedInUsername()
        );
        $OutletEdit = $outlet_permission['OutletEdit'];
        if ($this->input->post('outlet_id')) {
            if ($OutletEdit) {
                $update_outlet_data = array(
                    'id_outlet' => $this->input->post('outlet_id'),
                    'nama_outlet' => $this->input->post('namaoutlet'),
                    'alamat_outlet' => $this->input->post('alamatoutlet'),
                    'id_perusahaan' => getLoggedInUserID(),
                    'nomor_perusahaan' => getPerusahaanNo(),
                    'provinsi_outlet' => $this->input->post('provinsioutlet'),
                    'kota_outlet' => $this->input->post('kotaoutlet'),
                    'notelp_outlet' => $this->input->post('notelpoutlet'),
                    'pemilik_outlet' => $this->input->post('pemilikoutlet')
                );
                $this->Outlet->update_outlet_data($update_outlet_data);
            }
            redirect('perusahaan/outletdetailinfo/' . $this->input->post('outlet_id'), 'refresh');
        }
        if ($OutletEdit != 1) {
            $this->Outlet->update_outlet_data($update_outlet_data);
        }
        $this->load->model('Userperusahaan');
        $data['detail_outlet'] = $this->Outlet->getOutletByIdOnly($OutletID);
        $data['userperusahaan'] = $this->Userperusahaan->getListUser(getLoggedInUserID());
        $data['js_part'] = array(
            "features/js/js_form",
            "features/js/js_form_validation",
            "features/js/js_form_validation_registrasi_outlet",
            "features/js/js_outlet_edit"
        );
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['page_part'] = 'outlet/edit';
        $this->load->view('main_part_perusahaan', $data);
    }

    public function get_selected_province($OutletID)
    {
        $this->db->select('Propinsi');
        $this->db->from('outlet');
        $this->db->where('OutletID', $OutletID);
        $query = $this->db->get();
        $province = $query->row_array();
        $provinces = json_decode($provinces, true);
        header('Content-Type: application/json');
        echo json_encode($province);
    }

    public function get_selected_city($OutletID)
    {
        $this->db->select('Kota');
        $this->db->from('outlet');
        $this->db->where('OutletID', $OutletID);
        $query = $this->db->get();
        $city = $query->row_array();
        header('Content-Type: application/json');
        echo json_encode($city);
    }

    public function hapusoutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outletid = $this->input->get_post('i');
            if (isNotEmpty($outletid)) {
                $this->load->model('Outlet');
                $this->Outlet->delete($outletid, getLoggedInUserID());
                redirect(base_url('perusahaan/outlet'));
            }
        }
    }

    public function update_outlet_module()
    {
        $error = [];
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $bisadownload = $this->input->get_post('bisadownload') == 'on' ? 1 : 0;
            $PurchaseModule = $this->input->get_post('modulpembelian') == 'on' ? 'true' : 'false';
            $StockModule = $this->input->get_post('modulstok') == 'on' ? 'true' : 'false';
            $MenuRacikan = ($this->input->get_post('modulstokbahan') == 'on' && $this->input->get_post('modulstok') == 'on') ? 'true' : 'false';
            $PriceVariation = $this->input->get_post('modulvariasiharga') == 'on' ? 1 : 0;
            $StockModifier = ($this->input->get_post('modulstokmodifier') == 'on' && $this->input->get_post('modulstok') == 'on') ? 1 : 0;
            $SendReceiptToCustomerViaEmail = $this->input->get_post('strukviaemail') == 'on' ? 1 : 0;
            $DiningTable = $this->input->get_post('fiturmeja') == 'on' ? 1 : 0;
            $SupportBarcode = $this->input->get_post('supportbarcode') == 'on' ? 'true' : 'false';

            $this->load->model('Outlet');
            $namaoutlet = $postNamaOutlet;
            $alamatoutlet = $postAlamatOutlet;
            $id = $this->input->get_post('OutletID');
            $OutletID = $id;
            if (empty($id)) {
                array_push($error, $id);
            } else {
                $this->Outlet->update_download_module($id, $bisadownload);
                $this->Outlet->updateSudahKonfirmasi(getLoggedInUserID(), $id);
                $this->load->model('Userperusahaan');
                $email = $this->Userperusahaan->getEmailPasswordByPerusahaanUsername(getLoggedInUserID(), getLoggedInUsername())['email'];
                $this->load->model('Options');
                $id = $this->Options->createorupdate_by_devid(
                    $id,
                    $PurchaseModule,
                    $StockModule,
                    $MenuRacikan,
                    $PriceVariation,
                    $StockModifier,
                    $SendReceiptToCustomerViaEmail,
                    $DiningTable,
                    $SupportBarcode,
                    $postNamaOutlet,
                    $postAlamatOutlet,
                    $email
                );
                if (gettype($id) === "string") {
                    array_push($error, $id);
                } else {
                    redirect(base_url() . 'perusahaan/outletsetting/' . $OutletID);
                    return;
                }
            }
        }
    }

    public function get_province_ajax()
    {
        $response = file_get_contents('https://x.rajaapi.com/poe');
        $response = json_decode($response);
        $token = '';

        if ($response->code == '200' and $response->success == '1') {
            $token = $response->token;
        }

        if (!empty($token)) {
            $provinces = file_get_contents('https://x.rajaapi.com/MeP7c5ne' . $token . '/m/wilayah/provinsi');
            $provinces = json_decode($provinces, true);
            header('Content-Type: application/json');
            echo json_encode($provinces['data']);
        }

        return FALSE;
    }

    public function get_city_ajax($city = '')
    {
        if ($city == '') {
            return FALSE;
        } else {
            $response = file_get_contents('https://x.rajaapi.com/poe');
            $response = json_decode($response);
            $token = '';

            if ($response->code == '200' and $response->success == '1') {
                $token = $response->token;
            }

            if (!empty($token)) {
                $cities = file_get_contents('https://x.rajaapi.com/MeP7c5ne' . $token . '/m/wilayah/kabupaten?idpropinsi=' . $city);
                $cities = json_decode($cities, true);
                header('Content-Type: application/json');
                echo json_encode($cities['data']);
            }

            return FALSE;
        }
    }
}


class pekerjaandanakses
{
    public $name = "";
    public $key = "";
    public $isAllow = true;
    public $parentkey = "";

    public function __construct($n, $k, $i, $p)
    {
        $this->name = $n;
        $this->key = $k;
        $this->isAllow = $i;
        $this->parentkey = $p;
    }

    public function do_foo()
    {
        echo "Doing foo.";
    }

    public function isVisible($dataAll)
    {
        if ($this->parentkey == "") {
            return true;
        } else {
            return $dataAll[$this->parentkey]->isAllow;
        }
    }
}
