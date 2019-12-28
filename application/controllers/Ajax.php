<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ajax extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function getitembyoutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outlet = $this->input->get_post('o');

            $query = $this->db->get_where('masteritem', array('deviceid' => $outlet, 'Stock' => 'true'));
            $result = $query->result();
            $items = array();
            foreach ($result as $row) {
                $a = array();
                $a['id'] = $row->ItemID . "." . $row->DeviceNo;
                $a['name'] = $row->ItemName;

                array_push($items, $a);
            }
            echo json_encode($items);
        } else {
            echo "404";
        }
    }

    public function getrekeningbyoutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outlet = $this->input->get_post('o');
            $query_string = "
SELECT
AccountID,DeviceNo,
    CASE AccountType
        WHEN 1 THEN AccountName
        WHEN 2 THEN CONCAT(BankName , ' ', AccountNumber, ' ', AccountName)
    END AS AccountName
FROM
    nutacloud.mastercashbankaccount
WHERE AccountType=2 AND
    DeviceID = " . $this->db->escape($outlet);
            $query_mastercashbank = $this->db->query($query_string);
            $result = array();
            foreach ($query_mastercashbank->result_array() as $key => $value) {
                $result[] = array(
                    "AccountID" => $value['AccountID'] . "." . $value['DeviceNo'],
                    "AccountName" => $value['AccountName']
                );
            }
            echo json_encode((object) $result);
        } else {
            echo "404";
        }
    }

    public function getitembyoutlet2()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outlet = $this->input->get_post('o');

            $checkpoint = "select InsertedDate from checkpoint
                        where PerusahaanNo=" . getPerusahaanNo() . " and DeviceID=" . $outlet .
                " ORDER BY InsertedDate DESC LIMIT 0,1";
            $query_checkpoint = $this->db->query($checkpoint);
            $result_checkpoint = $query_checkpoint->result();
            $result_checkpoint = json_decode(json_encode($result_checkpoint), true);
            if (sizeof($result_checkpoint) > 0) {
                $minDate = $result_checkpoint[0]['InsertedDate'];
            } else {
                $minDate = false;
            }
            $this->load->library('NutaQuery');
            log_message('error', 'mulai get stok by outlet ' . microtime());
            $querystok = $this->nutaquery->get_query_stok_byoutlet(getPerusahaanNo(), $outlet, $minDate);
            $stocks = $this->db->query($querystok)->result();
            log_message('error', 'selesai get stok by outlet ' . microtime());

            $query = $this->db->query("SELECT ItemID, DeviceNo, ItemName, PurchasePrice, Unit FROM masteritem
 WHERE deviceid=" . $outlet . " AND PerusahaanNo=" . getPerusahaanNo() .
                " AND (IsProduct='false' OR (IsProduct='true' AND IsProductHasIngredients='false')) ORDER BY ItemName");
            //            $this->db->select('ItemID, ItemName, PurchasePrice, Unit');
            //            $query = $this->db->get_where('masteritem', array('deviceid' => $outlet, 'Stock' => 'true'));
            $result = array();
            foreach ($query->result_array() as $value) {
                $sistemqty = 0;
                foreach ($stocks as $stock) {
                    if ($stock->ItemID == $value['ItemID'] && $stock->ItemDeviceNo == $value['DeviceNo']) {
                        $sistemqty = $stock->Qty;
                        break;
                    }
                }
                $result[] = array(
                    "ItemID" => $value['ItemID'],
                    "ItemDeviceNo" => $value['DeviceNo'],
                    "ItemName" => $value['ItemName'],
                    "PurchasePrice" => $value['PurchasePrice'],
                    "Unit" => $value['Unit'],
                    "SistemQty" => $sistemqty,
                    //                    "SistemQty" => $this->get_sistem_qty($outlet, $value['ItemID']),
                );
            }
            // log_message('error', var_export($result,true));
            echo json_encode((object) $result);
        } else {
            echo "404";
        }
    }

    protected function get_sistem_qty($outlet, $item_id)
    {
        $this->load->library('NutaQuery');
        $querystok = $this->nutaquery->get_query_stok_single(getPerusahaanNo(), $outlet, $item_id);
        return $this->db->query($querystok)->result()[0]->Qty;
    }

    public function getkasrekeningbyoutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outlet = $this->input->get_post('o');
            $query = $this->db->get_where('mastercashbankaccount', array('deviceid' => strval($outlet)));
            $result = $query->result();
            $items = array();
            foreach ($result as $row) {
                $a = array();
                $a['id'] = $row->AccountID . "." . $row->DeviceNo;
                $a['name'] = $row->BankName . ' ' . $row->AccountNumber . ' ' . $row->AccountName;

                array_push($items, $a);
            }
            echo json_encode($items);
        } else {
            echo "404";
        }
    }

    //AJAX save checkbox IsUserAktif
    public function changeuserisaktif()
    {
        ifNotAuthenticatedRedirectToLogin();
        $this->load->model('User');
        if ($this->User->TidakPunyaPerusahaanID(getLoggedInUserID())) {
            //redirect(base_url() . 'perusahaan/registrasi');
            echo "404";
            exit;
        }
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $postUsername = $this->input->get_post('username');
            $isaktif = $this->input->get_post('isaktif');
            $val = 0;
            $this->load->model('Userperusahaan');
            if ($isaktif === "true") {
                $val = 1;
            }

            $this->Userperusahaan->changeUserAktif(getLoggedInUserID(), $postUsername, $val);
        }
    }
    //AJAX check username, dipakai di
    // - perusahaan/userform
    public function validateusername()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->get_post('username');
            $this->load->model('Userperusahaan');
            $userSudahDipakai = $this->Userperusahaan->isUsernameExist($username, getLoggedInUserID());
            echo json_encode(array(
                'valid' => !$userSudahDipakai,
            ));
        } else {
            echo "404";
        }
    }
    public function validateusernameedit()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $selectedusername = $this->input->get_post('selectedusername');
            $username = $this->input->get_post('username');
            $this->load->model('Userperusahaan');
            $userSudahDipakai = $this->Userperusahaan->isUsernameExistEdit($selectedusername, $username, getLoggedInUserID());
            echo json_encode(array(
                'valid' => !$userSudahDipakai,
            ));
        } else {
            echo "404";
        }
    }

    //AJAX check username, dipakai di
    // - perusahaan/userform
    public function validateusernametablet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->get_post('username');
            $this->load->model('Usertablet');
            $userSudahDipakai = $this->Usertablet->isUsernameExist($username, getPerusahaanNo());
            echo json_encode(array(
                'valid' => !$userSudahDipakai,
            ));
        } else {
            echo "404";
        }
    }

    //AJAX check email, dipakai di
    // - perusahaan/userform
    public function validateemail()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $email = $this->input->get_post('email');
            $this->load->model('Userperusahaan');
            $emailSudahDipakai = $this->Userperusahaan->isEmailExist($email, getLoggedInUserID());
            echo json_encode(array(
                'valid' => !$emailSudahDipakai,
            ));
        } else {
            echo "404";
        }
    }

    public function validateemailedit()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $email = $this->input->get_post('email');
            $selectedusername = $this->input->get_post('selectedusername');
            $this->load->model('Userperusahaan');
            $emailSudahDipakai = $this->Userperusahaan->isEmailExistEdit($selectedusername, $email, getLoggedInUserID());
            echo json_encode(array(
                'valid' => !$emailSudahDipakai,
            ));
        } else {
            echo "404";
        }
    }
    //AJAX check email, dipakai di
    // - perusahaan/userform
    public function validateemailtablet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $email = $this->input->get_post('email');
            $this->load->model('Usertablet');
            $emailSudahDipakai = $this->Usertablet->isEmailExist($email, getPerusahaanNo());
            echo json_encode(array(
                'valid' => !$emailSudahDipakai,
            ));
        } else {
            echo "404";
        }
    }

    //AJAX check matching
    public function validatematch()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $source = trim($this->input->get_post('source'));
            $duplicate = trim($this->input->get_post('duplicate'));
            if ($source != $duplicate) {
                echo json_encode(array(
                    'valid' => FALSE,
                ));
            } else {
                echo json_encode(array(
                    'valid' => TRUE,
                ));
            }
        } else {
            echo "404";
        }
    }

    //AJAX uopdate switch akses cloud
    public function updateaksescloud()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->get_post('username');
            $kolom = $this->input->get_post('kol');
            $value = $this->input->get_post('isaktif') == "true" ? 1 : 0;
            $this->load->model('Useraksescloud');
            $this->Useraksescloud->updateHakAkses($kolom, $value, getLoggedInUserID(), $username);
            echo 200;
        } else {
            echo "404";
        }
    }

    public function updateaksesusertablet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outlet = $this->input->get_post('outlet');
            $username = $this->input->get_post('username');
            $kolom = $this->input->get_post('kol');
            $value = $this->input->get_post('isaktif') == "true" ? 1 : 0;
            $this->load->model('Usertablet');
            $this->Usertablet->updateHakAkses($kolom, $value, $username, getPerusahaanNo(), $outlet);
            echo 200;
        } else {
            echo "404";
        }
    }

    public function updatepasswordusertablet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outlet = $this->input->get_post('outlet');
            $username = $this->input->get_post('username');
            $old_password = trim($this->input->get_post('old_password'));
            $password = trim($this->input->get_post('password'));
            $konfirmasi_password = trim($this->input->get_post('konfirmasi_password'));
            $this->load->model('Usertablet');
            if ($old_password == "") {
                echo json_encode(array(
                    "status" => 400,
                    "message" => "Password Lama harus diisi."
                ));
                exit();
            }

            if (!$this->Usertablet->checkOldPassword($old_password, $username, getPerusahaanNo(), $outlet)) {
                echo json_encode(array(
                    "status" => 400,
                    "message" => "Password Lama salah."
                ));
                exit();
            }

            if ($password == "") {
                echo json_encode(array(
                    "status" => 400,
                    "message" => "Password Baru harus diisi."
                ));
                exit();
            }

            if ($password != $konfirmasi_password) {
                echo json_encode(array(
                    "status" => 400,
                    "message" => "Konfirmasi Password salah."
                ));
                exit();
            }

            $this->Usertablet->updateHakAkses("password", $password, $username, getPerusahaanNo(), $outlet);
            $this->Usertablet->updateRowVersion($username, getPerusahaanNo(), $outlet);
            echo json_encode(array(
                "status" => 200,
                "message" => "Penyimpanan Berhasil."
            ));
            exit();
        } else {
            echo json_encode(array(
                "status" => 404,
                "message" => "Penyimpanan Gagal."
            ));
            exit();
        }
    }

    //AJAX update user cabang
    public function updateusercabang()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->get_post('username');
            $devid = $this->input->get_post('id');
            $value = $this->input->get_post('isaktif') == "true";
            $this->load->model('Userperusahaancabang');
            if ($value) {
                $this->Userperusahaancabang->Create(array(
                    "PerusahaanID" => getLoggedInUserID(),
                    "Username" => $username,
                    "OutletID" => $devid,
                    "IsAktif" => 1
                ));
            } else {
                $this->Userperusahaancabang->Delete(getLoggedInUserID(), $username, $devid);
            }
            echo "200";
        } else {
            echo "404";
        }
    }

    //    public function updatecabang()
    //    {
    //        ifNotAuthenticatedRedirectToLogin();
    //        if ($this->input->server('REQUEST_METHOD') == 'POST') {
    //            $devid = $this->input->get_post('id');
    //            $value = $this->input->get_post('isaktif') == "true" ? 1 : 0;
    //            $this->load->model('Userperusahaancabang');
    //            if ($value) {
    //                $this->Userperusahaancabang->Create(array(
    //                    "PerusahaanID" => getLoggedInUserID(),
    //                    "Username" => $username,
    //                    "DeviceID" => $devid,
    //                    "IsAktif" => 1));
    //            } else {
    //                $this->Userperusahaancabang->Delete(getLoggedInUserID(), $username, $devid);
    //            }
    //            echo "200";
    //        } else {
    //            echo "404";
    //        }
    //    }

    //AJAX delete user
    public function deleteuser()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->get_post('username');
            $this->load->model('Userperusahaan');
            $ret = $this->Userperusahaan->Delete($username, getLoggedInUserID());

            echo json_encode(array('code' => $ret));
        } else {
            echo "404";
        }
    }

    public function deleteusertablet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->get_post('username');
            $outlet = $this->input->get_post('outlet');
            $this->load->model('Usertablet');
            $ret = $this->Usertablet->delete($username, getPerusahaanNo(), $outlet);

            echo json_encode(array('code' => 200));
        } else {
            echo "404";
        }
    }

    public function getoutletbyvarian()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $varian = $this->input->get_post('o');
            $semua = $this->input->get_post('a') == "all";
            $this->load->model('Userperusahaancabang');
            $Cabangs = $this->Userperusahaancabang->getListCabang(getLoggedInUsername(), getLoggedInUserID());
            $this->load->model('Userperusahaan');
            $isOwner = $this->Userperusahaan->isUserOwner(getLoggedInUserID(), getLoggedInUsername());
            $retval = $this->GetOutlet($varian, $Cabangs, $isOwner, $semua);
            echo json_encode($retval);
        } else {
            echo "404";
        }
    }

    protected function GetOutlet($varian, $availableCabangs, $isOwner, $withsemua = TRUE)
    {
        $id = $this->db->escape(getLoggedInUserID());
        $where = "(deviceid=" . $id . " OR perusahaanid = " . $id . " ) AND Varian=" . $this->db->escape($varian);
        $this->db->where($where);
        $query = $this->db->get('options');
        $result = $query->result();

        if (count($result) <= 0) {
            $this->load->model('Perusahaanmodel');
            $id = $this->Perusahaanmodel->GetRegisterDeviceID(getLoggedInUserID());
            $id = $this->db->escape($id);
            $where = "(deviceid=" . $id . " OR perusahaanid = " . $id . " ) AND Varian=" . $this->db->escape($varian);
            $this->db->where($where);
            $query = $this->db->get('options');
            $result = $query->result();
        }
        $retval = array();
        if ($withsemua) {
            array_push($retval, array('id' => "Semua", 'name' => 'Semua'));
        }
        if ($isOwner) {
            foreach ($result as $row) {
                $add = array();
                $add['id'] = $row->DeviceID;
                $add['name'] = $row->CompanyName . ' ' . $row->CompanyAddress;
                array_push($retval, $add);
            }
        } else {
            foreach ($result as $row) {
                foreach ($availableCabangs as $cabang) {
                    if ($cabang->DeviceID == $row->DeviceID) {
                        $add = array();
                        $add['id'] = $row->DeviceID;
                        $add['name'] = $row->CompanyName . ' ' . $row->CompanyAddress;
                        array_push($retval, $add);
                    }
                }
            }
        }
        return $retval;
    }

    public function checkidperusahaan()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $id = $this->input->get_post('i');
            $query = $this->db->get_where('perusahaan', array('PerusahaanID' => $id));
            $result = $query->result();
            $count = count($result);
            $retval = array();
            if ($count == 1) {
                $namaperusahaan = $result[0]->namaperusahaan;
                $retval['kode'] = 200;
                $retval['msg'] = "Selamat, tablet ini telah terdaftar sebagai salah satu outlet dari perusahaan " . $namaperusahaan . ".\nSilahkan melanjutkan aktivitas seperti biasanya.";
            } else {
                $retval['kode'] = 404;
                $retval['msg'] = " ID perusahaan tidak valid";
            }
            echo json_encode($retval);
        }
    }
    //AJAX check username, dipakai di
    // - authetication/register
    public function validateusernameindividual()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->get_post('username');
            $this->load->model('User');
            $userSudahDipakai = $this->User->IsUsernameExist($username);
            echo json_encode(array(
                'valid' => !$userSudahDipakai,
            ));
        } else {
            echo "404";
        }
    }

    //AJAX check email, dipakai di
    // - authentication/register
    public function validateemailindividual()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $email = $this->input->get_post('email');
            $this->load->model('User');
            $emailSudahDipakai = $this->User->isEmailExist($email);
            echo json_encode(array(
                'valid' => !$emailSudahDipakai,
            ));
        } else {
            echo "404";
        }
    }

    //digunakan di registrasi perusahaan
    public function validateemailperusahaan()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $email = $this->input->get_post('email');
            $this->load->model('Perusahaanmodel');
            $emailSudahDipakai = $this->Perusahaanmodel->isEmailPerusahaanExist($email);
            echo json_encode(array(
                'valid' => !$emailSudahDipakai,
            ));
        } else {
            echo "404";
        }
    }

    //digunakan di perusahaan/akunsaya
    public function updatemodulperusahaan()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $kol = $this->input->get_post('kol');
            $aktif = $this->input->get_post('isaktif');
            $this->load->model('Perusahaanmodel');
            $kolom = 'IsUseStockModule';
            if ($kol == 'Pembelian') {
                $kolom = 'IsUsePurchaseModule';
            } else if ($kol == 'Pajak') {
                $kolom = 'IsUseTaxModule';
            } else if ($kol == 'VariasiItemHarga') {
                $kolom = 'IsUseVarianAndPrice';
            }
            $a = $this->Perusahaanmodel->Update(array('PerusahaanID' => getLoggedInUserID(), $kolom => $aktif == 'true' ? 1 : 0));
            //            echo var_dump($a);
            echo "200";
        } else {
            echo "404";
        }
    }

    public function validatenamaoutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $namaoutlet = $this->input->get_post('namaoutlet');
            $this->load->model('Outlet');
            $valid = $this->Outlet->isOutletNameHasBeenRegisteredInCompany(getLoggedInUserID(), $namaoutlet);
            echo json_encode(array(
                'valid' => !$valid,
            ));
        } else {
            echo "404";
        }
    }

    public function setoutletnonaktif()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $idoutlet = $this->input->get_post('id');
            $this->load->model('Outlet');
            $this->Outlet->setNonAktif(getLoggedInUserID(), $idoutlet);
            echo json_encode(array(
                'code' => 200,
            ));
        } else {
            echo "404";
        }
    }

    public function createnewkategori()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $newkategori = $this->input->get_post('namakategori');
            $outlet = $this->input->get_post('idoutlet');
            $this->load->model('Kategori');
            $id = $this->Kategori->createKategori($outlet, $newkategori, getPerusahaanNo());

            echo json_encode(array(
                'msg' => $id,
            ));
        } else {
            echo "404";
        }
    }

    public function editkategori()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $newkategori = $this->input->get_post('newnamakategori');
            $oldnamakategori = $this->input->get_post('oldnamakategori');
            $outlet = $this->input->get_post('idoutlet');
            $this->load->model('Kategori');
            $idkategori = explode(
                ".",
                $this->Kategori->getKategoriIDByName($outlet, $oldnamakategori, getPerusahaanNo())
            );

            $msg = $this->Kategori->editKategori($idkategori[0], $idkategori[1], $outlet, $oldnamakategori, $newkategori);

            echo json_encode(array(
                'msg' => $msg,
            ));
        } else {
            echo "404";
        }
    }

    public function deletekategori()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $namakategori = $this->input->get_post('namakategori');
            $arrayOfidOutlet = $this->input->get_post('idoutlet');

            $this->load->model('Kategori');
            $id = $this->Kategori->deleteKategori($namakategori, $arrayOfidOutlet, getPerusahaanNo());

            echo json_encode(array(
                'msg' => $id,
            ));
        } else {
            echo "404";
        }
    }

    public function createnewsatuan()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $newsatuan = $this->input->get_post('namasatuan');
            $outlet = $this->input->get_post('idoutlet');
            $this->load->model('Satuan');
            $id = $this->Satuan->createsatuan($outlet, $newsatuan, getPerusahaanNo());

            echo json_encode(array(
                'msg' => $id,
            ));
        } else {
            echo "404";
        }
    }

    public function editsatuan()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $newsatuan = $this->input->get_post('newnamasatuan');
            $oldnamasatuan = $this->input->get_post('oldnamasatuan');

            $outlet = $this->input->get_post('idoutlet');

            $this->load->model('Satuan');
            $id = $this->Satuan->editsatuan($outlet, $oldnamasatuan, $newsatuan, getPerusahaanNo());
            echo json_encode(array(
                'msg' => $id,
            ));
        } else {
            echo "404";
        }
    }

    public function deletesatuan()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $idsatuan = $this->input->get_post('idsatuan');
            $arrayOfidOutlet = $this->input->get_post('idoutlet');

            $this->load->model('Satuan');
            $id = $this->Satuan->deletesatuan($idsatuan, $arrayOfidOutlet);

            echo json_encode(array(
                'msg' => $id,
            ));
        } else {
            echo "404";
        }
    }

    var $isItemBahanExist = false;
    var $isModifierExist = false;

    public function savemasteritem()
    {
        $response = array();
        $post_mode = $this->input->post('mode');
        if (isNotEmpty($post_mode)) {
            $mode = $post_mode;
            $post_item_name = $this->input->post('itemname');
            $oldnamakategori = $this->input->post('oldnamakategori');
            $namakategori = $this->input->post('namakategori');
            $namasatuan = $this->input->post('namasatuan');
            $post_harga_jual = $this->input->post('hargajual');
            $post_harga_beli = $this->input->post('hargabeli');
            $post_is_produk = $this->input->post('isproduk');
            $post_punya_bahan = $this->input->post('punyabahan');
            $outlet_ids = $this->input->post('idoutlets');
            $post_bahans = $this->input->post('bahans');
            $post_namamodifiers = $this->input->post('modifiers');
            $original_save_outletid = $this->input->post('selectedoutlet');
            $namaitem = '';
            $hargajual = 0;
            $hargabeli = 0;
            $idkategori = array("0", "0");
            $idsatuan = 0;
            $isProduct = 'true';
            $punyabahan = 'false';
            $perusahaanNo = getPerusahaanNo();
            $idoutlet = -1;
            if (isNotEmpty($post_item_name)) {
                $namaitem = $post_item_name;
            } else {
                $response['msg'] = 'Item tidak boleh kosong';
                echo json_encode($response);
                return;
            }
            if (isNotEmpty($post_harga_jual)) {
                $hargajual = $post_harga_jual;
            }
            if (isNotEmpty($post_harga_beli)) {
                $hargabeli = $post_harga_beli;
            }
            if (isNotEmpty($post_is_produk)) {
                $isProduct = $post_is_produk;
            }
            if (isNotEmpty($post_punya_bahan)) {
                $punyabahan = $post_punya_bahan;
            }

            $hapusgambar = $this->input->post('deletegambar') == 'true';

            $this->load->model('Masteritem');
            $this->load->model('Kategori');
            $this->load->model('Satuan');
            $this->load->library('NutaQuery');
            $index = 0;
            foreach ($outlet_ids as $idoutlet) {
                $itemid = "-1.0";
                if (isNotEmpty($namakategori)) {
                    if ($oldnamakategori != $namakategori && $original_save_outletid != $idoutlet) { //outlet  pertama kategorinya sudah diupdate by ajax
                        $idkategori = explode(
                            ".",
                            $this->Kategori->getKategoriIDByName($idoutlet, $oldnamakategori, $perusahaanNo)
                        );
                        $this->Kategori->editKategori($idkategori[0], $idkategori[1], $idoutlet, $oldnamakategori, $namakategori);
                    } else {
                        $idkategori = explode(".", $this->Kategori->getKategoriIDByName($idoutlet, $namakategori, $perusahaanNo));
                    }
                } else {
                    $namakategori = "";
                }
                if ($mode == 'new') {
                    $itemid = $this->Masteritem->createNewItem($namaitem, $idkategori[0], $idkategori[1], $namasatuan, $hargajual, $hargabeli, $isProduct, $punyabahan, $idoutlet, $perusahaanNo);
                    if ($itemid == "Item ini sudah ada") {
                        $response['msg'] = "Item " . $namaitem . " sudah ada, silakan input Item lain.";
                        echo json_encode(array('status' => false, 'data' => array_values($response), 'message' => $response['msg']));
                        return;
                    }
                    if ($punyabahan == 'true') {
                        foreach ($post_bahans as $k => $bahan) {
                            if (isNotEmpty($bahan['nama'])) {
                                $oldbahan = $this->Masteritem->getByName($bahan['nama'], $idoutlet);
                                if (!isset($oldbahan)) {
                                    //1. Simpan bahan Master Item
                                    $idbahan = $this->Masteritem->createItemBahan($bahan['nama'], $bahan['satuan'], $idoutlet, $perusahaanNo);

                                    //2. Insert ke MasterItemDetailIngredients
                                    $detailnumber = $k + 1;
                                    $idmdi = $this->Masteritem->createNewLinkItemBahan($itemid, $idbahan, $detailnumber, $bahan['qty'], $idoutlet, $perusahaanNo);
                                } else {
                                    $detailnumber = $k + 1;
                                    $idmdi = $this->Masteritem->createNewLinkItemBahan(
                                        $itemid,
                                        $oldbahan->ItemID . "." . $oldbahan->DeviceNo,
                                        $detailnumber,
                                        $bahan['qty'],
                                        $idoutlet,
                                        $perusahaanNo
                                    );
                                }
                            }
                        }
                    }
                    if (count($post_namamodifiers) > 0) {
                        foreach ($post_namamodifiers as $nama) {
                            if ($nama != '') {
                                $idmodifier = $this->Masteritem->getModifierIDByName($idoutlet, $nama);
                                $this->Masteritem->createNewLinkModifier($itemid, $idmodifier, $idoutlet);
                            }
                        }
                    }
                } else if ($mode == 'edit') {
                    //handle update Item
                    $oldName = $this->input->post('olditemname');
                    $referensi = 'line 591';
                    $itemid = $this->Masteritem->updateByName($oldName, $namaitem, $idkategori[0], $idkategori[1], $hargajual, $hargabeli, $isProduct, $punyabahan, $idoutlet, $namasatuan, $referensi, false);
                    //handle update Gambar
                    if ($hapusgambar) {
                        $this->Masteritem->hapusGambar($namaitem, $idoutlet);
                    }

                    $this->nutaquery->setOutlet($idoutlet);

                    $str_query_bahan = $this->nutaquery->get_query_bahan_item($itemid);
                    $query_bahan = $this->db->query($str_query_bahan);
                    $oldBahans = $query_bahan->result();

                    //handle bahan yang diubah
                    if (count($post_bahans) > 0) {
                        $detnumber = 0;
                        foreach ($post_bahans as $k => $bahan) {
                            if (isNotEmpty($bahan['nama'])) {
                                $detnumber = $detnumber + 1;

                                $is_bahan_existing = false;
                                $isNamaBahanChanged = false;
                                $isQtyBahanChanged = false;
                                $isSatuanChanged = false;
                                $isHargaChanged = false;

                                foreach ($oldBahans as $k => $oldBahan) {
                                    if ($bahan['nama'] == $oldBahan->ItemName) {
                                        $is_bahan_existing = true;
                                        $isNamaBahanChanged = ($oldBahan->ItemName != $bahan['nama']);
                                        $isQtyBahanChanged = ($oldBahan->QtyNeed != $bahan['qty']);
                                        $isSatuanChanged = ($oldBahan->Satuan != $bahan['satuan']);
                                        $isHargaChanged = ($oldBahan->hargabeli != $bahan['hargabeli']);
                                        break;
                                    }
                                }

                                if ($is_bahan_existing == false) {
                                    $oldbahan = $this->Masteritem->getByName($bahan['nama'], $idoutlet);
                                    if (!isset($oldbahan)) {
                                        //1. Simpan bahan Master Item
                                        $idbahan = $this->Masteritem->createItemBahan($bahan['nama'], $bahan['satuan'], $idoutlet, $perusahaanNo);

                                        //2. Insert ke MasterItemDetailIngredients
                                        $detailnumber = $detnumber;
                                        $idmdi = $this->Masteritem->createNewLinkItemBahan($itemid, $idbahan, $detailnumber, $bahan['qty'], $idoutlet, $perusahaanNo);
                                    } else {
                                        $detailnumber = $detnumber;
                                        $idmdi = $this->Masteritem->createNewLinkItemBahan(
                                            $itemid,
                                            $oldbahan->ItemID . "." . $oldbahan->DeviceNo,
                                            $detailnumber,
                                            $bahan['qty'],
                                            $idoutlet,
                                            $perusahaanNo
                                        );
                                    }
                                } else {
                                    //echo ">_ Bahan " . $bahan['nama'] . ($is_bahan_existing ? " sudah ada" : " gak ada");

                                    if ($is_bahan_existing) {
                                        if ($isSatuanChanged || $isHargaChanged) {
                                            // Update Item
                                            //echo ">_ Bahan " . $bahan['nama'] . ($isNamaBahanChanged ? " nama diubah, " : "") . ($isSatuanChanged ? " satuan diubah." : "");
                                            $referensi = 'line 634, bahan dari item : ' . $namaitem;
                                            $this->Masteritem->updateByName($oldBahan->ItemName, $bahan['nama'], 0, 1, 0, $bahan['hargabeli'], 'false', 'false', $idoutlet, $bahan['satuan'], $referensi, true);
                                        }
                                        if ($isQtyBahanChanged) {
                                            //echo ">_ Bahan " . $bahan['nama'] . ($isQtyBahanChanged ? " jumlah diubah, " : "");
                                            //Update Link Bahan
                                            $srcitembahan = $this->Masteritem->getByName($bahan['nama'], $idoutlet);
                                            $this->Masteritem->updateLinkBahan(
                                                $itemid,
                                                $srcitembahan->ItemID . "." . $srcitembahan->DeviceNo,
                                                $bahan['qty'],
                                                $idoutlet,
                                                $perusahaanNo
                                            );
                                        }
                                    } else {
                                        //echo ">_ Bahan nya ga ada jadi buat baru";
                                        //Buat Bahan
                                        $idbahan = $this->Masteritem->createItemBahan($bahan['nama'], $bahan['satuan'], $idoutlet, $perusahaanNo);
                                        //Link ke Item
                                        $detailnumber = $detnumber;
                                        $this->Masteritem->createNewLinkItemBahan($itemid, $idbahan, $detailnumber, $bahan['qty'], $idoutlet, $perusahaanNo);
                                    }
                                }
                            }
                        }
                    }

                    //handle bahan yang dihapus
                    $tobe_deleted = array();

                    foreach ($oldBahans as $oldBahan) {
                        $this->isItemBahanExist = false;
                        foreach ($post_bahans as $bahan) {
                            $namabahan = $bahan['nama'];
                            $oldNamaBahan = $oldBahan->ItemName;
                            if ($namabahan == $oldNamaBahan) {
                                $this->isItemBahanExist = true;
                                break;
                            }
                        }

                        if (!$this->isItemBahanExist) {
                            log_message('error', var_export($oldBahan, true));
                            array_push($tobe_deleted, $oldBahan);
                        }
                    }

                    $delete_fail_use_in_order_sale = array();

                    foreach ($tobe_deleted as $d) {
                        $is_used_in_pending_sale = $this->Masteritem->isItemUseInPendingSale($itemid, $idoutlet);
                        if ($is_used_in_pending_sale) {
                            array_push($delete_fail_use_in_order_sale, $d);
                        }
                    }

                    if (count($delete_fail_use_in_order_sale) == 0) {
                        foreach ($tobe_deleted as $d) {
                            $this->Masteritem->hapusBahan($d->DetailID, $idoutlet, $d->DeviceNo, $perusahaanNo);
                        }
                        $response['msg'] = $itemid;
                    } else {
                        #Generate Pesan eror
                        $msg = "";
                        $itemnames = "";
                        if (count($delete_fail_use_in_order_sale) > 0) {
                            $msg = " * Item";
                            $itemnames = "";
                            foreach ($delete_fail_use_in_order_sale as $d) {
                                $itemnames .= " " . $d->ItemName . ",";
                            }
                            $msg .= rtrim($itemnames, ",");
                            $msg .= " tidak dapat dihapus karena sedang dipesan oleh pelanggan";
                        }
                        $response['msg'] = $msg;
                    }

                    $oldModifiers = $this->Masteritem->getModifiersItem($itemid, $idoutlet);

                    //handle jika ada input
                    if (isset($post_namamodifiers) && count($post_namamodifiers) > 0) {
                        foreach ($post_namamodifiers as $m) {
                            $this->isModifierExist = false;
                            $idmodifier = $this->Masteritem->getModifierIDByName($idoutlet, $m);
                            foreach ($oldModifiers as $om)
                                if ($om->ModifierID == $idmodifier) {
                                    $this->isModifierExist = true;
                                    break;
                                }
                            //                        echo ">_ Hmmm... Modifier " . $m . ($this->isModifierExist ? " ada " : "ga ada");
                            if (!$this->isModifierExist) {
                                $this->Masteritem->createNewLinkModifier($itemid, $idmodifier, $idoutlet);
                            }
                        }

                        //handle jika dihapus
                        foreach ($oldModifiers as $om) {
                            $this->isModifierExist = false;
                            if (count($post_namamodifiers) > 0) {
                                foreach ($post_namamodifiers as $m) {
                                    $idmodifier = $this->Masteritem->getModifierIDByName($idoutlet, $m);
                                    if ($om->ModifierID == $idmodifier) {
                                        $this->isModifierExist = true;
                                        break;
                                    }
                                }
                            }
                            if (!$this->isModifierExist) {
                                $this->Masteritem->deleteModifierItem($itemid, $idoutlet, $om->DetailID, $om->DeviceNo, $perusahaanNo);
                            }
                        }
                    }
                }
                array_push($response, array('outlet' => $idoutlet, 'saved_id' => $itemid));


                $realitemid = explode(".", $itemid)[0];
                $devno = explode(".", $itemid)[1];
                $options = $this->Options->get_by_devid($idoutlet);
                if ($options->CreatedVersionCode >= 200 || $options->EditedVersionCode >= 200) {
                    $this->Firebasemodel->push_firebase(
                        $idoutlet,
                        array(
                            'table' => 'pleaseUpdateMasterItem',
                            'column' => array('ItemID' => $realitemid, 'DeviceNo' => $devno)
                        ),
                        $realitemid,
                        $devno,
                        $perusahaanNo,
                        0
                    );
                }
                array_push($response, array('outlet' => $idoutlet, 'saved_id' => $itemid));

                $index++;
            }
        } else {
            $response['msg'] = 'Mode ?';
        }
        //        log_message('error', var_export($response, true));
        echo json_encode(array('status' => true, 'data' => array_values($response), 'message' => $itemid));
    }

    public
    function deletemasteritem()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $iditem = $this->input->get_post('iditem');
            $arrayOfidOutlet = $this->input->get_post('idoutlet');
            $this->load->model('Masteritem');
            $id = $this->Masteritem->deleteItem($iditem, $arrayOfidOutlet);

            echo json_encode(array(
                'msg' => $id,
            ));
        } else {
            echo "404";
        }
    }

    public
    function isiteminmultioutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $itemname = $this->input->get_post('itemname');
            $this->load->model('Masteritem');
            $result = $this->Masteritem->isInMultiOutlet($itemname, getLoggedInUserID());
            echo json_encode(array(
                'msg' => $result,
            ));
        } else {
            echo "404";
        }
    }

    public
    function ismodifierinmultioutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $itemname = $this->input->get_post('itemname');
            $this->load->model('Mastermodifier');
            $result = $this->Mastermodifier->isInMultiOutlet($itemname, getLoggedInUserID());
            echo json_encode(array(
                'msg' => $result,
            ));
        } else {
            echo "404";
        }
    }

    public
    function issatuaninmultioutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $itemname = $this->input->get_post('satuanname');
            $this->load->model('Satuan');
            $result = $this->Satuan->isSatuanInMultiOutlet($itemname, getLoggedInUserID());
            echo json_encode(array(
                'msg' => $result,
            ));
        } else {
            echo "404";
        }
    }

    public
    function iskategoriinmultioutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $itemname = $this->input->get_post('kategoriname');
            $this->load->model('Kategori');
            $result = $this->Kategori->isKategoriInMultiOutlet($itemname, getLoggedInUserID());
            echo json_encode(array(
                'msg' => $result,
            ));
        } else {
            echo "404";
        }
    }

    public
    function getrincianhpp()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outlet = $this->input->get_post('o');
            $detailid = $this->input->get_post('i');
            $perusahaanno = $this->getPerusahaanNo($outlet);
            $this->load->library('NutaQuery');
            $str_query_bahan = $this->nutaquery->get_query_rincian_hpp($outlet, $detailid, $perusahaanno);
            $query = $this->db->query($str_query_bahan);
            $result = $query->result_array();
            //            $items = array();
            //            foreach ($result as $row) {
            //                $a = array();
            //                $a['id'] = $row->ItemID;
            //                $a['name'] = $row->ItemName;
            //
            //                array_push($items, $a);
            //            }
            echo json_encode($result);
        } else {
            echo "404";
        }
    }

    public
    function updateuserdailyreport()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->get_post('username');
            $isaktif = $this->input->get_post('isaktif') === 'true';
            $allowDailyReport = 0;
            if ($isaktif)
                $allowDailyReport = 1;

            $this->load->model('Userperusahaan');

            $this->Userperusahaan->updateDailyReport(getLoggedInUserID(), $username, $allowDailyReport);

            echo "200";
        } else {
            echo "404";
        }
    }

    public
    function savevariasiharga()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $variasi_harga = $this->input->get_post('variasiharga');
            $outletdanitem = $this->input->get_post('outletdanitem');
            $this->load->model('Mastervarian');

            if (count($variasi_harga > 0)) {
                $perusahaanNo = getPerusahaanNo();
                log_message('error', var_export($outletdanitem, true));
                //                log_message('error', "variasi harga");
                //                log_message('error', var_export($variasi_harga,true));
                foreach ($outletdanitem as $item) {
                    $devid = $item['outlet'];
                    $itemid = $item['saved_id'];
                    $oldvarians = $this->Mastervarian->getVariasiHargaArray($itemid, $devid);
                    foreach ($variasi_harga as $variasi) {

                        $namaVariasi = $variasi['nama'];
                        $sellPrice = $variasi['harga'];
                        $oldName = $variasi['oldname'];
                        $isReguler = $variasi['reguler'];

                        $is_varian_existing = false;
                        $isNamavarianChanged = false;
                        $isQtyvarianChanged = false;
                        $isSatuanChanged = false;

                        foreach ($oldvarians as $oldvarian) {
                            if ($variasi['nama'] == $oldvarian['VarianName']) {
                                $is_varian_existing = true;
                                $isNamavarianChanged = ($oldvarian['VarianName'] != $variasi['nama']);
                                $isQtyvarianChanged = ($oldvarian['SellPrice'] != $variasi['harga']);
                                $isSatuanChanged = false;
                                break;
                            }
                        }

                        if ($is_varian_existing) {

                            if ($isNamavarianChanged || $isSatuanChanged) {
                                // Update Variant
                                $this->Mastervarian->updateVariasiHarga($devid, $itemid, $namaVariasi, $namaVariasi, $sellPrice, $isReguler, $perusahaanNo);
                            }
                            if ($isQtyvarianChanged) {
                                //Update variant by id
                                //                                    $this->Mastervarian->updateVariasiHargaByID($devid, $itemid, $oldName, $namaVariasi, $sellPrice, $isReguler);
                                $this->Mastervarian->updateVariasiHarga($devid, $itemid, $namaVariasi, $namaVariasi, $sellPrice, $isReguler, $perusahaanNo);
                            }
                        } else {
                            $this->Mastervarian->createVariasiharga($devid, $itemid, $namaVariasi, $sellPrice, $isReguler, $perusahaanNo);
                        }
                    }

                    //handle varian yang dihapus
                    $tobe_deleted = array();

                    foreach ($oldvarians as $oldvarian) {
                        $this->isItemvarianExist = false;
                        foreach ($variasi_harga as $varian) {
                            $namavarian = $varian['nama'];
                            $oldNamavarian = $oldvarian['VarianName'];
                            if ($namavarian == $oldNamavarian) {
                                $this->isItemvarianExist = true;
                                break;
                            }
                        }

                        if (!$this->isItemvarianExist) {
                            array_push($tobe_deleted, $oldvarian);
                        }
                    }

                    foreach ($tobe_deleted as $d) {
                        $this->Mastervarian->hapusVariasiHarga($devid, $itemid, $d['VarianName'], $perusahaanNo);
                    }
                }
            }
            echo json_encode(array('status' => true));
        } else {
            echo "404";
        }
    }

    public
    function getvariasiharga()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $itemid = $this->input->get_post('itemid');
            $devid = $this->input->get_post('outlet');
            $this->load->model('Mastervarian');
            $val = $this->Mastervarian->getVariasiHarga($itemid, $devid);
            echo json_encode($val);
        } else {
            echo "404";
        }
    }

    public
    function getvariasiharga2()
    {
        //        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $itemid = $this->input->get_post('itemid');
            $devid = $this->input->get_post('outlet');
            $this->load->model('Mastervarian');
            $val = $this->Mastervarian->getVariasiHarga($itemid, $devid);
            var_dump($val);
            //            echo json_encode($val);
        } else {
            $itemid = $this->input->get('itemid');
            $devid = $this->input->get('outlet');
            $this->load->model('Mastervarian');
            $val = $this->Mastervarian->getVariasiHarga($itemid, $devid);
            //var_dump($val);
            var_dump($val[0]['VarianName']);
            echo "404";
        }
    }

    public
    function hapusvariasiharga()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $itemid = $this->input->get_post('itemid');
            $devid = $this->input->get_post('outlet');
            $namavariasiharga = $this->input->get_post('nama');
            if (isNotEmpty($namavariasiharga) && $itemid != 'new') {
                $this->load->model('Mastervarian');
                $val = $this->Mastervarian->hapusVariasiHarga($devid, $itemid, $namavariasiharga, getPerusahaanNo());
            }
            echo "200";
        } else {
            echo "404";
        }
    }

    public function getCopyBahan()
    {

        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->load->model('Masteritem');
            $namabahan = $this->input->get_post('namabahan');
            log_message('error', var_export($namabahan, true));
            $itemX = $this->Masteritem->getByName($namabahan[0]['nama'], $namabahan[0]['outlet']);
            //load bahan
            $this->load->library('NutaQuery');
            $this->nutaquery->setOutlet($namabahan[0]['outlet']);
            $str_query_bahan = $this->nutaquery->get_query_bahan_item($itemX->ItemID . "." . $itemX->DeviceNo);
            $query_bahan = $this->db->query($str_query_bahan);
            $bahans = $query_bahan->result_array();
            $jsonresponse = json_encode(array('data' => $bahans));
            log_message('error', var_export($jsonresponse, true));
            echo $jsonresponse;
        } else {
            echo "404";
        }
    }

    public
    function savepilihanekstra()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $bunchOfModifiers = $this->input->get_post('modifiers');
            $this->load->model('Mastermodifier');
            $response = [];
            $whatIwant = [
                'outlet' => 1,
                'modifiers' => [
                    [
                        'nama' => 'Toping',
                        'saved_id' => "1.0",
                    ]
                ]
            ];
            $perusahaanNo = getPerusahaanNo();
            foreach ($bunchOfModifiers as $mod) {

                $outlet_ids = $mod['outlets'];
                $namaModifier = $mod['ModifierName'];
                $canAddQuantity = $mod['CanAddQuantity'];
                $chooseOnlyOne = $mod['ChooseOnlyOne'];
                $modifierOp = $mod['operation'];
                $oldModifierName = $mod['oldName'];
                if ($canAddQuantity == 1) {
                    $this->load->model('Masteritem');
                    $this->load->model('Satuan');
                }
                foreach ($outlet_ids as $idoutlet) {
                    $idmodifier = "-1.0";
                    $NO_MODIFIER_CANT_UPDATE = false;
                    if ($modifierOp == 'delete') {
                        $this->load->model('Mastermodifier');
                        $deletedModifierID = $this->Mastermodifier->hapusModifier($idoutlet, $namaModifier, $perusahaanNo);
                        $this->Mastermodifier->hapusPilihanByModifierID($idoutlet, $deletedModifierID, $perusahaanNo);
                        $this->load->model('Masteritem');
                        $this->Masteritem->deleteModifierLinks($idoutlet, $deletedModifierID, $perusahaanNo);

                        if (!array_key_exists($idoutlet, $response)) {
                            $response[$idoutlet] = array();
                        }
                        array_push($response[$idoutlet], array('nama' => $namaModifier, 'saved_id' => $deletedModifierID, 'op' => 'delete'));
                    } else {
                        $pilihans = $mod['Pilihan'];
                        $idmodifier = $this->Mastermodifier->getModifierIDByName($idoutlet, $namaModifier);
                        $createorupdate = 'create';
                        if ($idmodifier == "NO_MODIFIER_CANT_UPDATE") {
                            $idmodifier = $this->Mastermodifier->createModifier($idoutlet, $namaModifier, $chooseOnlyOne, $canAddQuantity, $perusahaanNo);
                            $createorupdate = 'create';
                        } else {
                            $this->Mastermodifier->updateModifier($idoutlet, $namaModifier, $namaModifier, $chooseOnlyOne, $canAddQuantity, $perusahaanNo);
                            $createorupdate = 'update';
                        }

                        $oldpilihans = $this->Mastermodifier->getPilihan($idmodifier, $idoutlet);

                        foreach ($pilihans as $pilihan) {
                            $namaPilihan = $pilihan['NamaPilihan'];
                            $harga = $pilihan['Harga'];
                            $qty = $pilihan['QtyDibutuhkan'];
                            $namaSatuan = $pilihan['Satuan'];
                            if (!isNotEmpty($harga)) {
                                $harga = 0;
                            }

                            if (isNotEmpty($namaPilihan) && isNotEmpty($harga)) {
                                $is_pilihan_existing = false;
                                $isNamapilihanChanged = false;
                                $isQtypilihanChanged = false;
                                $isHargaChanged = false;

                                foreach ($oldpilihans as $oldpilihan) {
                                    if ($namaPilihan == $oldpilihan['NamaPilihan']) {
                                        $is_pilihan_existing = true;
                                        $isNamapilihanChanged = ($oldpilihan['NamaPilihan'] != $namaPilihan);
                                        $isQtypilihanChanged = ($oldpilihan['QtyDibutuhkan'] != $qty);
                                        $isHargaChanged = ($oldpilihan['Harga'] != $harga);
                                        break;
                                    }
                                }

                                if ($is_pilihan_existing) {
                                    if ($isQtypilihanChanged || $isHargaChanged) {
                                        $this->Mastermodifier->updatePilihan($idoutlet, $idmodifier, $namaPilihan, $namaPilihan, $harga, $qty, $perusahaanNo);
                                        if ($canAddQuantity == 1) {
                                            $referensi = 'line 1176, pilihan extra: ' . $namaModifier;
                                            $resultItem = $this->Masteritem->updateByName($namaPilihan, $namaPilihan, 0, 1, 0, 0, 'false', 'false', $idoutlet, $namaSatuan, $referensi, true);
                                            if ($resultItem == 'NO_ITEM_TO_BE_UPDATE') {
                                                $this->Masteritem->createItemBahan($namaPilihan, $namaSatuan, $idoutlet, getPerusahaanNo());
                                            }
                                        }
                                    }
                                } else {
                                    $idpilihan = $this->Mastermodifier->createPilihan($idoutlet, $idmodifier, $namaPilihan, $harga, $qty, getPerusahaanNo());
                                    if ($canAddQuantity == 1) {
                                        $idbahan = $this->Masteritem->createItemBahan($namaPilihan, $namaSatuan, $idoutlet, getPerusahaanNo());
                                    }
                                }
                            }
                        }
                        //handle pilihan yang dihapus
                        $tobe_deleted = array();

                        foreach ($oldpilihans as $oldpilihan) {
                            $this->isPilihanExist = false;
                            foreach ($pilihans as $pilihan) {
                                $namapilihan = $pilihan['NamaPilihan'];
                                $oldNamapilihan = $oldpilihan['NamaPilihan'];
                                if ($namapilihan == $oldNamapilihan) {
                                    $this->isPilihanExist = true;
                                    break;
                                }
                            }

                            if (!$this->isPilihanExist) {
                                array_push($tobe_deleted, $oldpilihan);
                            }
                        }

                        foreach ($tobe_deleted as $d) {
                            $this->Mastermodifier->hapusPilihanByDetailID($idoutlet, $d['DetailID'] . "." . $d['DeviceNo'], $perusahaanNo);
                        }

                        if (!array_key_exists($idoutlet, $response)) {
                            $response[$idoutlet] = array();
                        }
                        array_push($response[$idoutlet], array('nama' => $namaModifier, 'saved_id' => $idmodifier, 'op' => $createorupdate));
                    }
                }
            }
            $result = array();

            foreach ($response as $k => $r) {
                array_push($result, array('outlet' => $k, 'modifiers' => $r));
            }
            echo json_encode(array('status' => true, 'data' => $result, 'message' => ''));
        } else {
            echo "404";
        }
    }

    function isOutletExistOnArray($result, $value)
    {
        $index = 0;
        foreach ($result as $r) {
            if ($r['outlet'] == $value)
                return $index;
            $index++;
        }
        return -1;
    }

    public
    function hapusmodifier()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $devid = $this->input->get_post('outlet');
            $namamodifier = $this->input->get_post('nama');
            $this->load->model('Mastermodifier');
            $deletedModifierID = $this->Mastermodifier->hapusModifier($devid, $namamodifier, getPerusahaanNo());
            $this->Mastermodifier->hapusPilihanByModifierID($devid, $deletedModifierID, getPerusahaanNo());
            $this->load->model('Masteritem');
            $this->Masteritem->deleteModifierLinks($devid, $deletedModifierID);
            echo "200";
        } else {
            echo "404";
        }
    }

    public
    function hapusmodifiermultioutlet()
    { }

    public
    function hapuspilihanekstra()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $devid = $this->input->get_post('outlet');
            $namapilihan = $this->input->get_post('nama');
            $modifierid = $this->input->get_post('modifierid');
            $this->load->model('Mastermodifier');
            $val = $this->Mastermodifier->hapusPilihan($devid, $namapilihan, $modifierid, getPerusahaanNo());

            echo "200";
        } else {
            echo "404";
        }
    }

    public function isBahanUsedByModifier()
    { }

    function getPerusahaanNo($deviceid)
    {
        //1.7++
        $query = $this->db->get_where('outlet', array( //making selection
            'OutletID' => $deviceid,
        ));
        $count = $query->num_rows(); //counting result from query
        if ($count > 0) {
            $res = $query->result();
            return $res[0]->PerusahaanNo;
        }
        return 0;
    }

    public function saveuangmasuk()
    {
        $retval = array();
        $post_mode = $this->input->post('mode');
        if (isNotEmpty($post_mode)) {
            $mode = $post_mode;
            $post_account = $this->input->post('masukKe');
            $post_dari = $this->input->post('dari');
            $post_jumlah = $this->input->post('jumlah');
            $post_keterangan = $this->input->post('keterangan');
            $post_jenis = $this->input->post('jenis');
            $post_outletid = $this->input->post('idoutlet');
            $masukKe = 0;
            $dari = '';
            $jumlah = '';
            $jenis = '';
            $idoutlet = -1;
            if (isNotEmpty($post_dari)) {
                $dari = $post_dari;
            } else {
                $retval['msg'] = 'Dari tidak boleh kosong';
                echo json_encode($retval);
                return;
            }
            if (isNotEmpty($post_account)) {
                $masukKe = $post_account;
            }
            if (isNotEmpty($post_jumlah)) {
                $jumlah = $post_jumlah;
            } else {
                $retval['msg'] = 'Jumlah tidak boleh kosong';
                echo json_encode($retval);
                return;
            }
            if (isNotEmpty($post_jenis)) {
                $jenis = $post_jenis;
            }
            if (isNotEmpty($post_keterangan)) {
                $keterangan = $post_keterangan;
            }
            if (isNotEmpty($post_outletid)) {
                $idoutlet = $post_outletid;
            }

            $this->load->model('UangmasukModel');
            if ($mode == 'new') {
                $namatrans = $this->UangmasukModel->createNewCashIn($masukKe, $dari, $jumlah, $keterangan, $jenis, $idoutlet);
                $retval['msg'] = $namatrans;
            } else if ($mode == 'edit') {
                $oldcash = $this->input->post('oldcash');
                $oldTransactionID = $this->UangmasukModel->updateByCash($oldcash, $masukKe, $dari, $jumlah, $keterangan, $jenis, $idoutlet);
                $this->load->library('NutaQuery');
                $this->nutaquery->setOutlet($idoutlet);
                $retval['msg'] = $oldTransactionID;
            }
        } else {
            $retval['msg'] = 'Mode ?';
        }
        echo json_encode($retval);
    }

    public function savemasterpromooutletsxxxx()
    {
        $this->load->model('Firebasemodel');
        $this->Firebasemodel->notifToWeb($this->input->post('token'), 'preparing', 0);
        $post_mode = $this->input->post('mode');

        if (isNotEmpty($post_mode)) {
            //log_message('error', 'mulai simpan promo ' . microtime());
            $mode = $post_mode;
            $perusahaanno = getPerusahaanNo();
            $namapromo = $this->input->post('namapromo');
            $idoutlet = $this->input->post('idoutlet');
            $jenispromo = $this->input->post('jenispromo');
            $date = array($this->input->post('datestart'), $this->input->post('dateend'));
            $time = array($this->input->post('jamstart'), $this->input->post('jamend'));
            $hari = $this->input->post('hari');
            $term = array($this->input->post('termqty'), $this->input->post('termitems'), $this->input->post('termcategory'), $this->input->post('termtotal'));
            $termitemnames = $this->input->post('termitemnames');
            $termcategorynames = $this->input->post('termcategorynames');
            $get = array($this->input->post('getdiscounttype'), $this->input->post('getdiscountvalue'), $this->input->post('getitemqty'), $this->input->post('getitemid'));
            $apply = $this->input->post('multiple');
            $this->load->model('MasterPromo');
            $this->load->model('Masteritem');
            $this->load->model('Outlet');
            $this->load->model('Kategori');
            $this->db->where_in('OutletID', $idoutlet);
            $q = $this->db->get('device_app')->result();
            $jumlahDevice = count($q);
            $jumlahSemuaProses = ((count($termitemnames) + count($termcategorynames)) * count($idoutlet)) + count($idoutlet) + $jumlahDevice;
            $proses = 1;
            if ($mode == 'new') {
                if (count($idoutlet) > 0) {
                    $ids = array();
                    if (count($idoutlet) == 1) {
                        if ($termitemnames != null && ($jenispromo == 1 || $jenispromo == 3)) {
                            if (is_array($idoutlet))
                                $idoutlet = $idoutlet[0];
                            $outletid = $idoutlet;
                            $termitems = array();
                            $termcategories = array();
                            if (is_array($termitemnames)) {
                                foreach ($termitemnames as $itemname) {
                                    $pros = $proses / $jumlahSemuaProses * 100;
                                    $this->Firebasemodel->notifToWeb($this->input->post('token'), 'pengecekan item ' . $itemname . " pada outlet " . $outletid, $pros);
                                    $item = $this->Masteritem->getByName($itemname, $outletid);
                                    if ($item != null) {
                                        array_push($termitems, $item->ItemID . '.' . $item->DeviceNo);
                                    }
                                    $pros++;
                                }
                            }
                            if (is_array($termcategorynames)) {
                                foreach ($termcategorynames as $catname) {
                                    $pros = $proses / $jumlahSemuaProses * 100;
                                    $this->Firebasemodel->notifToWeb($this->input->post('token'), 'pengecekan kategori ' . $catname . " pada outlet " . $outletid, $pros);
                                    $category = $this->Kategori->getByName($catname, $outletid);
                                    if ($category != null) {
                                        array_push($termcategories, $category->CategoryID . '.' . $category->DeviceNo);
                                    }
                                    $proses++;
                                }
                            }
                            $term = array($this->input->post('termqty'), implode(',', $termitems), implode(',', $termcategories), $this->input->post('termtotal'));
                            if ($jenispromo == 3) {
                                $item = $this->Masteritem->getByName($this->input->post('getitemname'), $outletid);
                                if ($item != null) {
                                    $get = array($this->input->post('getdiscounttype'), $this->input->post('getdiscountvalue'), $this->input->post('getitemqty'), $item->ItemID . '.' . $item->DeviceNo);
                                } else {
                                    echo json_encode(array(
                                        'msg' => 'Produk ' . $this->input->post('getitemname') . ' tidak ada di salah satu outlet'
                                    ));
                                    return;
                                }
                            }
                        }
                        $pros = $proses / $jumlahSemuaProses * 100;
                        $this->Firebasemodel->notifToWeb($this->input->post('token'), 'menyimpan promo pada outlet ' . $outletid, $pros);
                        $proses++;
                        $promoid = $this->MasterPromo->createNewPromo($perusahaanno, $namapromo, $idoutlet, $jenispromo, $date, $time, $hari, $term, $get, $apply);
                        if (is_numeric($promoid)) {
                            array_push($ids, array('PromoID' => $promoid, 'DeviceID' => $idoutlet));
                            $pros = $proses / $jumlahSemuaProses * 100;
                            $this->Firebasemodel->notifToWeb($this->input->post('token'), 'mengirim notifikasi pada perangkat', $pros);
                            $this->MasterPromo->pushFirebaseCreateOrUpdate($perusahaanno, $idoutlet, $promoid);
                            $proses++;
                        } else {
                            echo json_encode(array(
                                'msg' => $promoid
                            ));
                            return;
                        }
                    } else {
                        $this->MasterPromo->begintrans();
                        foreach ($idoutlet as $outletid) {
                            log_message('error', 'mulai simpan promo ' . $outletid . " " . microtime());
                            if ($jenispromo == 1 || $jenispromo == 3) {
                                $termitems = array();
                                $termcategories = array();
                                $strTermCategoryNames = "";
                                $strTermItemNames = "";
                                $strTermCategoryAndItem = "";
                                if (is_array($termitemnames)) {
                                    foreach ($termitemnames as $itemname) {
                                        $pros = $proses / $jumlahSemuaProses * 100;
                                        $this->Firebasemodel->notifToWeb($this->input->post('token'), 'pengecekan item ' . $itemname . " pada outlet " . $outletid, $pros);
                                        $item = $this->Masteritem->getByName($itemname, $outletid);
                                        log_message('error', 'selesai getByName ' . $itemname . " " . microtime());
                                        if ($item != null) {
                                            array_push($termitems, $item->ItemID . '.' . $item->DeviceNo);
                                        }
                                        $proses++;
                                    }
                                    $strTermItemNames = implode(", ", $termitemnames);
                                    $strTermCategoryAndItem = " dan Produk " . $strTermItemNames;
                                }
                                if (is_array($termcategorynames)) {
                                    foreach ($termcategorynames as $catname) {
                                        $pros = $proses / $jumlahSemuaProses * 100;
                                        $this->Firebasemodel->notifToWeb($this->input->post('token'), 'pengecekan kategori ' . $catname . " pada outletid ", $pros);
                                        $category = $this->Kategori->getByName($catname, $outletid);
                                        if ($category != null) {
                                            array_push($termcategories, $category->CategoryID . '.' . $category->DeviceNo);
                                        }
                                        $proses++;
                                    }
                                    $strTermCategoryNames = implode(", ", $termcategorynames);
                                    $strTermCategoryAndItem = $strTermCategoryAndItem . " dan Kategori " . $strTermCategoryNames;
                                }
                                if (!empty($strTermCategoryAndItem)) {
                                    $strTermCategoryAndItem = substr($strTermCategoryAndItem, 5);
                                }
                                $term = array($this->input->post('termqty'), implode(',', $termitems), implode(',', $termcategories), $this->input->post('termtotal'));
                                if ($jenispromo == 3) {
                                    $item = $this->Masteritem->getByName($this->input->post('getitemname'), $outletid);
                                    if ($item != null) {
                                        $get = array($this->input->post('getdiscounttype'), $this->input->post('getdiscountvalue'), $this->input->post('getitemqty'), $item->ItemID . '.' . $item->DeviceNo);
                                    } else {
                                        $this->MasterPromo->rollbacktrans();
                                        $curroutlet = $this->Outlet->getOutletByIdOnly($outletid);
                                        $this->session->set_userdata('aaa', 'error');
                                        echo json_encode(array(
                                            'msg' => 'Promo gagal disimpan.<br>Produk ' . $this->input->post('getitemname')
                                                . ' tidak ada di outlet ' . $curroutlet->NamaOutlet . ' - ' . $curroutlet->AlamatOutlet
                                                . '<br>Silahkan pilih produk lain atau jangan centang outlet tersebut.'
                                        ));
                                        return;
                                    }
                                }
                            }
                            $pros = $proses / $jumlahSemuaProses * 100;
                            $this->Firebasemodel->notifToWeb($this->input->post('token'), 'menyimpan promo pada outlet ' . $outletid, $pros);
                            $proses++;
                            $promoid = $this->MasterPromo->createNewPromo($perusahaanno, $namapromo, $outletid, $jenispromo, $date, $time, $hari, $term, $get, $apply);
                            log_message('error', 'selesai simpan promo ' . $outletid . " " . microtime());
                            if (is_numeric($promoid)) {
                                array_push($ids, array('PromoID' => $promoid, 'DeviceID' => $outletid));
                            } else {
                                $this->MasterPromo->rollbacktrans();
                                $curroutlet = $this->Outlet->getOutletByIdOnly($outletid);
                                if (strpos($promoid, 'judul lain') !== false) {
                                    echo json_encode(array(
                                        'msg' => 'Promo gagal disimpan.<br>Promo dengan judul ' . $namapromo
                                            . ' sudah ada pada outlet ' . $curroutlet->NamaOutlet . ' - ' . $curroutlet->AlamatOutlet
                                            . '<br>Silahkan pakai judul lain atau jangan centang outlet tersebut.'
                                    ));
                                } else if (strpos($promoid, 'tidak boleh kosong') !== false) {
                                    echo json_encode(array(
                                        'msg' => 'Promo gagal disimpan.<br>' . $strTermCategoryAndItem
                                            . ' tidak ada pada outlet ' . $curroutlet->NamaOutlet . ' - ' . $curroutlet->AlamatOutlet
                                            . '<br>Silahkan pilih produk lain atau jangan centang outlet tersebut.'
                                    ));
                                } else {
                                    echo json_encode(array(
                                        'msg' => $promoid
                                    ));
                                }
                                return;
                            }
                        }
                        $this->MasterPromo->committrans();
                        foreach ($ids as $o) {
                            //$pros = $proses/$jumlahSemuaProses * 100;
                            //$this->Firebasemodel->notifToWeb($this->input->post('token'), 'pengirim notifikasi ke perangkat', $pros);
                            $proses = $this->MasterPromo->pushFirebaseCreateOrUpdateWithProgress($perusahaanno, $o['DeviceID'], $o['PromoID'], $this->input->post('token'), $proses, $jumlahSemuaProses);
                            //$proses++;
                        }
                    }
                    $this->Firebasemodel->notifToWeb($this->input->post('token'), 'promo berasil disimpan', 100);
                    echo json_encode(array(
                        'msg' => 'OK',
                        'ids' => $ids
                    ));
                }
            } else {
                if (count($idoutlet) > 0) {
                    $ids = array();
                    if (count($idoutlet) == 1) {
                        if (is_array($idoutlet))
                            $idoutlet = $idoutlet[0];
                        $outletid = $idoutlet;
                        if ($termitemnames != null && ($jenispromo == 1 || $jenispromo == 3)) {
                            $termitems = array();
                            $termcategories = array();
                            if (is_array($termitemnames)) {
                                foreach ($termitemnames as $itemname) {
                                    $pros = $proses / $jumlahSemuaProses * 100;
                                    $this->Firebasemodel->notifToWeb($this->input->post('token'), 'pengecekan item ' . $itemname . " pada outlet " . $outletid, $pros);
                                    $item = $this->Masteritem->getByName($itemname, $outletid);
                                    if ($item != null) {
                                        array_push($termitems, $item->ItemID . '.' . $item->DeviceNo);
                                    }
                                    $proses++;
                                }
                            }
                            if (is_array($termcategorynames)) {
                                foreach ($termcategorynames as $catname) {
                                    $pros = $proses / $jumlahSemuaProses * 100;
                                    $this->Firebasemodel->notifToWeb($this->input->post('token'), 'pengecekan kategori ' . $catname . " pada outlet " . $outletid, $pros);
                                    $category = $this->Kategori->getByName($catname, $outletid);
                                    if ($category != null) {
                                        array_push($termcategories, $category->CategoryID . '.' . $category->DeviceNo);
                                    }
                                    $proses++;
                                }
                            }
                            $term = array($this->input->post('termqty'), implode(',', $termitems), implode(',', $termcategories), $this->input->post('termtotal'));
                            if ($jenispromo == 3) {
                                $item = $this->Masteritem->getByName($this->input->post('getitemname'), $outletid);
                                if ($item != null) {
                                    $get = array($this->input->post('getdiscounttype'), $this->input->post('getdiscountvalue'), $this->input->post('getitemqty'), $item->ItemID . '.' . $item->DeviceNo);
                                } else {
                                    echo json_encode(array(
                                        'msg' => 'Produk ' . $this->input->post('getitemname') . ' tidak ada di salah satu outlet'
                                    ));
                                    return;
                                }
                            }
                        }
                        $oldpromotitle = $this->input->post('oldname');
                        $pros = $proses / $jumlahSemuaProses * 100;
                        $this->Firebasemodel->notifToWeb($this->input->post('token'), 'menyimpan promosi pada outlet ' . $outletid, $pros);
                        $proses++;
                        $oldpromoid = $this->MasterPromo->updateByName($oldpromotitle, $perusahaanno, $namapromo, $idoutlet, $jenispromo, $date, $time, $hari, $term, $get, $apply);
                        if (is_numeric($oldpromoid)) {
                            array_push($ids, array('ItemID' => $oldpromoid, 'DeviceID' => $idoutlet));
                            $pros = $proses / $jumlahSemuaProses * 100;
                            $this->Firebasemodel->notifToWeb($this->input->post('token'), 'mengirim notifikasi ke perangkat', $pros);
                            $this->MasterPromo->pushFirebaseCreateOrUpdate($perusahaanno, $idoutlet, $oldpromoid);
                            $proses++;
                        } else {
                            echo json_encode(array(
                                'msg' => $oldpromoid
                            ));
                            return;
                        }
                    } else {
                        $this->MasterPromo->begintrans();
                        foreach ($idoutlet as $outletid) {
                            if ($jenispromo == 1 || $jenispromo == 3) {
                                $termitems = array();
                                $termcategories = array();
                                $strTermCategoryNames = "";
                                $strTermItemNames = "";
                                $strTermCategoryAndItem = "";
                                if (is_array($termitemnames)) {
                                    foreach ($termitemnames as $itemname) {
                                        $pros = $proses / $jumlahSemuaProses * 100;
                                        $this->Firebasemodel->notifToWeb($this->input->post('token'), 'pengecekan item ' . $itemname . " pada outlet " . $outletid, $pros);
                                        $item = $this->Masteritem->getByName($itemname, $outletid);
                                        if ($item != null) {
                                            array_push($termitems, $item->ItemID . '.' . $item->DeviceNo);
                                        }
                                        $proses++;
                                    }
                                    $strTermItemNames = implode(", ", $termitemnames);
                                    $strTermCategoryAndItem = " dan Produk " . $strTermItemNames;
                                }
                                if (is_array($termcategorynames)) {
                                    foreach ($termcategorynames as $catname) {
                                        $pros = $proses / $jumlahSemuaProses * 100;
                                        $this->Firebasemodel->notifToWeb($this->input->post('token'), 'pengecekan kategori ' . $catname . " pada outlet " . $outletid, $pros);
                                        $category = $this->Kategori->getByName($catname, $outletid);
                                        if ($category != null) {
                                            array_push($termcategories, $category->CategoryID . '.' . $category->DeviceNo);
                                        }
                                        $proses++;
                                    }
                                    $strTermCategoryNames = implode(", ", $termcategorynames);
                                    $strTermCategoryAndItem = " dan Kategori " . $strTermCategoryNames;
                                }
                                if (!empty($strTermCategoryAndItem)) {
                                    $strTermCategoryAndItem = substr($strTermCategoryAndItem, 5);
                                }
                                $term = array($this->input->post('termqty'), implode(',', $termitems), implode(',', $termcategories), $this->input->post('termtotal'));
                                if ($jenispromo == 3) {
                                    $item = $this->Masteritem->getByName($this->input->post('getitemname'), $outletid);
                                    if ($item != null) {
                                        $get = array($this->input->post('getdiscounttype'), $this->input->post('getdiscountvalue'), $this->input->post('getitemqty'), $item->ItemID . '.' . $item->DeviceNo);
                                    } else {
                                        $this->MasterPromo->rollbacktrans();
                                        $curroutlet = $this->Outlet->getOutletByIdOnly($outletid);
                                        echo json_encode(array(
                                            'msg' => 'Promo gagal disimpan.<br>Produk ' . $this->input->post('getitemname')
                                                . ' tidak ada di outlet ' . $curroutlet->NamaOutlet . ' - ' . $curroutlet->AlamatOutlet
                                                . '<br>Silahkan pilih produk lain atau jangan centang outlet tersebut.'
                                        ));
                                        return;
                                    }
                                }
                            }
                            $oldpromotitle = $this->input->post('oldname');
                            $pros = $proses / $jumlahSemuaProses * 100;
                            $this->Firebasemodel->notifToWeb($this->input->post('token'), 'melakukan pembaharuan promo pada outlet ' . $outletid, $pros);
                            $proses++;
                            $oldpromoid = $this->MasterPromo->updateByName($oldpromotitle, $perusahaanno, $namapromo, $outletid, $jenispromo, $date, $time, $hari, $term, $get, $apply);
                            if (is_numeric($oldpromoid)) {
                                array_push($ids, array('ItemID' => $oldpromoid, 'DeviceID' => $outletid));
                            } else {
                                $this->MasterPromo->rollbacktrans();
                                $curroutlet = $this->Outlet->getOutletByIdOnly($outletid);
                                if (strpos($oldpromoid, 'judul') !== false) {
                                    echo json_encode(array(
                                        'msg' => 'Promo gagal disimpan.<br>Promo dengan judul ' . $namapromo
                                            . ' sudah ada pada outlet ' . $curroutlet->NamaOutlet . ' - ' . $curroutlet->AlamatOutlet
                                            . '<br>Silahkan pakai judul lain atau jangan centang outlet tersebut.'
                                    ));
                                } else if (strpos($oldpromoid, 'tidak boleh kosong') !== false) {
                                    echo json_encode(array(
                                        'msg' => 'Promo gagal disimpan.<br>' . $strTermCategoryAndItem
                                            . ' tidak ada pada outlet ' . $curroutlet->NamaOutlet . ' - ' . $curroutlet->AlamatOutlet
                                            . '<br>Silahkan pilih produk lain atau jangan centang outlet tersebut.'
                                    ));
                                } else {
                                    echo json_encode(array(
                                        'msg' => $promoid
                                    ));
                                }
                                return;
                            }
                        }
                        $this->MasterPromo->committrans();
                        foreach ($ids as $o) {
                            // $pros = $proses/$jumlahSemuaProses * 100;
                            // $this->Firebasemodel->notifToWeb($this->input->post('token'), 'mengirim notifikasi ke perangkat', $pros);
                            // $this->MasterPromo->pushFirebaseCreateOrUpdate($perusahaanno,$o['DeviceID'],$o['ItemID']);
                            $proses = $this->MasterPromo->pushFirebaseCreateOrUpdateWithProgress($perusahaanno, $o['DeviceID'], $o['ItemID'], $this->input->post('token'), $proses, $jumlahSemuaProses);
                            // $proses++;
                        }
                    }
                    $this->Firebasemodel->notifToWeb($this->input->post('token'), 'promo berasil disimpan', 100);
                    echo json_encode(array(
                        'msg' => 'OK',
                        'ids' => $ids
                    ));
                }
            }
        }
    }

    public function deletemasterpromo()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $idproduk = $this->input->get_post('idpromo');
            $arrayOfidOutlet = $this->input->get_post('idoutlet');
            $this->load->model('MasterPromo');
            $id = $this->MasterPromo->deletepromo($idproduk, $arrayOfidOutlet);

            echo json_encode(array(
                'msg' => $id,
            ));
        } else {
            echo "404";
        }
    }

    public function ispromoinmultioutlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $promotitle = $this->input->get_post('promotitle');
            $this->load->model('MasterPromo');
            $result = $this->MasterPromo->isInMultiOutlet($promotitle, getLoggedInUserID());
            echo json_encode(array(
                'msg' => $result,
            ));
        } else {
            echo "404";
        }
    }

    public function savesupplier()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->load->model('Suppliermodel');

            $nama = $this->input->post('nama');
            $alamat = $this->input->post('alamat');
            $telepon = $this->input->post('telepon');
            $email = $this->input->post('email');
            $catatan = $this->input->post('catatan');
            $idoutlet = $this->input->post('idoutlet');
            $mode = $this->input->post('mode');
            $id_supp = $this->input->post('id_supp');
            $devno = $this->input->post('devno');

            if ($mode == "edit") {
                $result = $this->Suppliermodel->updateSupplier($nama, $alamat, $telepon, $email, $catatan, $idoutlet, $id_supp, $devno);
            } else if ($mode == "new") {
                $result = $this->Suppliermodel->createNewSupplier($nama, $alamat, $telepon, $email, $catatan, $idoutlet);
            }
            if ($result) {
                echo json_encode(array("status" => "OK"));
            }
        } else {
            echo "404";
        }
    }

    public function get_supplier()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->load->model('Suppliermodel');
            $id_supp = $this->input->post('id_supp');

            $realsuppid = explode(".", $id_supp)[0];
            $devno = explode(".", $id_supp)[1];
            $id_outlet = $this->input->post('idoutlet');
            $result = $this->Suppliermodel->getByName($realsuppid, $devno, $id_outlet);
            if ($result) {
                echo json_encode($result);
            } else {
                echo '404';
            }
        }
    }

    public function get_supplier_outlet()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->load->model('Supplier');
            $id_outlet = $this->input->post('idoutlet');

            $result = $this->db->query($this->Supplier->supplier($id_outlet))->result();
            if ($result) {
                echo json_encode($result);
            } else {
                echo '404';
            }
        }
    }

    public function delete_supplier()
    {
        $this->load->model('Suppliermodel');
        if (!array_key_exists($this->input->post('outlet'), $this->GetOutletTanpaSemua())) {
            echo '404';
        } else {
            $clause = array(
                'SupplierID' => $this->input->post('id'),
                'DeviceNo' => $this->input->post('devno'),
                'DeviceID' => $this->input->post('outlet')
            );
            $this->Suppliermodel->deleting($clause);
            echo json_encode(array("status" => "OK"));
        }
    }

    public function delete_item_stok()
    {

        $outlet = $this->input->post('o');
        $transaksi_id = $this->input->post('t');
        $item_stok = $this->input->post('i');


        if (isset($item_stok) && isset($outlet) && isset($transaksi_id)) {
            $this->load->model('Stockopnamedetail');

            $is_item_exist = false;
            foreach ($this->Stockopnamedetail->get_item_stok($outlet, $transaksi_id) as $key => $value) {
                if ($value->DetailID == $item_stok) {
                    $is_item_exist = true;
                }
            }

            if (!$is_item_exist) {
                echo '404';
            } else {
                $clause = array(
                    'DeviceID' => $outlet,
                    'PerusahaanNo' => getPerusahaanNo(),
                    'PerusahaanID' => getLoggedInUserID(),
                    'TransactionID' => $transaksi_id,
                    'DetailID' => $item_stok,
                );

                $this->Stockopnamedetail->delete($clause);
                echo json_encode(array("status" => "OK"));
            }
        }
    }

    public function delete_purchase_detail()
    {

        $outlet = $this->input->post('o');
        $transaksi_id = $this->input->post('p');
        $detail_id = $this->input->post('d');


        if (isset($detail_id) && isset($outlet) && isset($transaksi_id)) {
            $this->load->model('Purchaseitemdetail');

            $attributes = [
                'DeviceID' => $outlet,
                'TransactionID' => $transaksi_id
            ];

            $is_detail_exist = false;
            foreach ($this->Purchaseitemdetail->where($attributes)->result() as $key => $value) {
                if ($value->DetailID == $detail_id) {
                    $is_detail_exist = true;
                }
            }

            if (!$is_detail_exist) {
                echo '404';
            } else {
                $clause = array(
                    'DeviceID' => $outlet,
                    'PerusahaanNo' => getPerusahaanNo(),
                    'PerusahaanID' => getLoggedInUserID(),
                    'TransactionID' => $transaksi_id,
                    'DetailID' => $detail_id,
                );

                $this->Purchaseitemdetail->delete($clause);
                echo json_encode(array("status" => "OK"));
            }
        }
    }

    public function push_imageitem()
    {

        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->load->model('Firebasemodel');
            $this->load->model('Masteritem');
            $outletdanitem = $this->input->get_post('outletdanitem');
            log_message('error', var_export($outletdanitem, true));
            foreach ($outletdanitem as $item) {
                $devid = $item['outlet'];
                $itemid = $item['saved_id'];
                $mi = $this->Masteritem->getByID($itemid, $devid);
                $fullURL = "";
                if (isNotEmpty($mi->OnlineImagePath)) {
                    $fullURL = $this->config->item('ws_base_url') . $mi->OnlineImagePath;
                }
                $realitemid = explode(".", $itemid)[0];
                $devno = explode(".", $itemid)[1];

                $this->load->model('Options');
                $options = $this->Options->get_by_devid($devid);
                if ($options->CreatedVersionCode < 103 && $options->EditedVersionCode < 103) {
                    $data_to_be_pushed = array(
                        "table" => "getimageitem",
                        "column" => array(
                            "ItemID" => $realitemid, 'DeviceNo' => $devno,
                            "OnlineImageFullPath" =>  $fullURL
                        )
                    );
                    log_message('error', $devid . " " . var_export($data_to_be_pushed, true));
                    $this->Firebasemodel->push_firebase(
                        $devid,
                        $data_to_be_pushed,
                        $realitemid,
                        $devno,
                        $this->getPerusahaanNo($devid),
                        0
                    );
                } else {
                    if ($options->CreatedVersionCode >= 200 || $options->EditedVersionCode >= 200) {
                        $this->Masteritem->updateImageLink($devid, $realitemid, $devno, $mi->RowVersion);
                    }
                    $data_to_be_pushed = array(
                        "table" => "getimageitem2",
                        "column" => array(
                            "ItemID" => $realitemid, 'DeviceNo' => $devno, 'RowVersion' => $mi->RowVersion,
                            "OnlineImageFullPath" =>  $fullURL
                        )
                    );
                    log_message('error', $devid . " " . var_export($data_to_be_pushed, true));
                    $this->Firebasemodel->push_firebase(
                        $devid,
                        $data_to_be_pushed,
                        $realitemid,
                        $devno,
                        $this->getPerusahaanNo($devid),
                        0
                    );
                }
            }
            echo json_encode(array('status' => true));
        } else {
            echo "404";
        }
    }



    public function savemasteritemgabung()
    {
        $response = array();
        $postItem = $this->input->post('items');
            $variasi_harga = $this->input->get_post('variasiharga');
            log_message('error', var_export($postItem, true));
        $post_mode = $postItem['mode'];
        if (isNotEmpty($post_mode)) {
            $mode = $post_mode;
            
            // <editor-fold defaultstate="collapsed" desc="Save Pilihan Extra">
            $bunchOfModifiers = $this->input->get_post('modifiers');
            $this->load->model('Mastermodifier');
            $this->load->model('Mastervarian');
            $response = [];
            $whatIwant = [
                'outlet' => 1,
                'modifiers' => [
                    [
                        'nama' => 'Toping',
                        'saved_id' => "1.0",
                    ]
                ]
            ];
            $perusahaanNo = getPerusahaanNo();
            if(isset($bunchOfModifiers) && !is_null($bunchOfModifiers)) {
                foreach ($bunchOfModifiers as $mod) {

                    $outlet_ids = $mod['outlets'];
                    $namaModifier = $mod['ModifierName'];
                    $canAddQuantity = $mod['CanAddQuantity'];
                    $chooseOnlyOne = $mod['ChooseOnlyOne'];
                    $modifierOp = $mod['operation'];
                    $oldModifierName = $mod['oldName'];
                    if ($canAddQuantity == 1) {
                        $this->load->model('Masteritem');
                        $this->load->model('Satuan');
                    }
                    foreach ($outlet_ids as $idoutlet) {
                        $idmodifier = "-1.0";
                        $NO_MODIFIER_CANT_UPDATE = false;
                        if ($modifierOp == 'delete') {
                            $this->load->model('Mastermodifier');
                            $deletedModifierID = $this->Mastermodifier->hapusModifier($idoutlet, $namaModifier, $perusahaanNo);
                            $this->Mastermodifier->hapusPilihanByModifierID($idoutlet, $deletedModifierID, $perusahaanNo);
                            $this->load->model('Masteritem');
                            $this->Masteritem->deleteModifierLinks($idoutlet, $deletedModifierID, $perusahaanNo);

                            if (!array_key_exists($idoutlet, $response)) {
                                $response[$idoutlet] = array();
                            }
                            array_push($response[$idoutlet], array('nama' => $namaModifier, 'saved_id' => $deletedModifierID, 'op' => 'delete'));
                        } else {
                            $pilihans = $mod['Pilihan'];
                            $idmodifier = $this->Mastermodifier->getModifierIDByName($idoutlet, $namaModifier);
                            $createorupdate = 'create';
                            if ($idmodifier == "NO_MODIFIER_CANT_UPDATE") {
                                $idmodifier = $this->Mastermodifier->createModifier($idoutlet, $namaModifier, $chooseOnlyOne, $canAddQuantity, $perusahaanNo);
                                $createorupdate = 'create';
                            } else {
                                $this->Mastermodifier->updateModifier($idoutlet, $namaModifier, $namaModifier, $chooseOnlyOne, $canAddQuantity, $perusahaanNo);
                                $createorupdate = 'update';
                            }

                            $oldpilihans = $this->Mastermodifier->getPilihan($idmodifier, $idoutlet);

                            foreach ($pilihans as $pilihan) {
                                $namaPilihan = $pilihan['NamaPilihan'];
                                $harga = $pilihan['Harga'];
                                $qty = $pilihan['QtyDibutuhkan'];
                                $namaSatuan = $pilihan['Satuan'];
                                if (!isNotEmpty($harga)) {
                                    $harga = 0;
                                }

                                if (isNotEmpty($namaPilihan) && isNotEmpty($harga)) {
                                    $is_pilihan_existing = false;
                                    $isNamapilihanChanged = false;
                                    $isQtypilihanChanged = false;
                                    $isHargaChanged = false;

                                    foreach ($oldpilihans as $oldpilihan) {
                                        if ($namaPilihan == $oldpilihan['NamaPilihan']) {
                                            $is_pilihan_existing = true;
                                            $isNamapilihanChanged = ($oldpilihan['NamaPilihan'] != $namaPilihan);
                                            $isQtypilihanChanged = ($oldpilihan['QtyDibutuhkan'] != $qty);
                                            $isHargaChanged = ($oldpilihan['Harga'] != $harga);
                                            break;
                                        }
                                    }

                                    if ($is_pilihan_existing) {
                                        if ($isQtypilihanChanged || $isHargaChanged) {
                                            $this->Mastermodifier->updatePilihan($idoutlet, $idmodifier, $namaPilihan, $namaPilihan, $harga, $qty, $perusahaanNo);
                                            if ($canAddQuantity == 1) {
                                                $referensi = 'line 1176, pilihan extra: ' . $namaModifier;
                                                $resultItem = $this->Masteritem->updateByName($namaPilihan, $namaPilihan, 0, 1, 0, 0, 'false', 'false', $idoutlet, $namaSatuan, $referensi, true);
                                                if ($resultItem == 'NO_ITEM_TO_BE_UPDATE') {
                                                    $this->Masteritem->createItemBahan($namaPilihan, $namaSatuan, $idoutlet, getPerusahaanNo());
                                                }
                                            }
                                        }
                                    } else {
                                        $idpilihan = $this->Mastermodifier->createPilihan($idoutlet, $idmodifier, $namaPilihan, $harga, $qty, getPerusahaanNo());
                                        if ($canAddQuantity == 1) {
                                            $idbahan = $this->Masteritem->createItemBahan($namaPilihan, $namaSatuan, $idoutlet, getPerusahaanNo());
                                        }
                                    }
                                }
                            }
                            //handle pilihan yang dihapus
                            $tobe_deleted = array();

                            foreach ($oldpilihans as $oldpilihan) {
                                $this->isPilihanExist = false;
                                foreach ($pilihans as $pilihan) {
                                    $namapilihan = $pilihan['NamaPilihan'];
                                    $oldNamapilihan = $oldpilihan['NamaPilihan'];
                                    if ($namapilihan == $oldNamapilihan) {
                                        $this->isPilihanExist = true;
                                        break;
                                    }
                                }

                                if (!$this->isPilihanExist) {
                                    array_push($tobe_deleted, $oldpilihan);
                                }
                            }

                            foreach ($tobe_deleted as $d) {
                                $this->Mastermodifier->hapusPilihanByDetailID($idoutlet, $d['DetailID'].".".$d['DeviceNo'], $perusahaanNo);
                            }

                            if (!array_key_exists($idoutlet, $response)) {
                                $response[$idoutlet] = array();
                            }
                            array_push($response[$idoutlet], array('nama' => $namaModifier, 'saved_id' => $idmodifier, 'op' => $createorupdate));
                        }


                    }
                }
            }
            // </editor-fold>
            
            $post_item_name = $postItem['itemname'];
            $oldnamakategori = $postItem['oldnamakategori'];
            $namakategori = $postItem['namakategori'];
            $namasatuan = $postItem['namasatuan'];
            $post_harga_jual = $postItem['hargajual'];
            $post_harga_beli = $postItem['hargabeli'];
            $post_is_produk = $postItem['isproduk'];
            $post_punya_bahan = $postItem['punyabahan'];
            $outlet_ids = $postItem['idoutlets'];
            if(isset($postItem['bahans'])) {
                $post_bahans = $postItem['bahans'];
            } else {
                $post_bahans = array();
            }
            if(isset($postItem['modifiers'])) {
                $post_namamodifiers = $postItem['modifiers'];
            } else {
                $post_namamodifiers = array();
            }
            $original_save_outletid = $postItem['selectedoutlet'];
            $namaitem = '';
            $hargajual = 0;
            $hargabeli = 0;
            $idkategori = array("0", "0");
            $idsatuan = 0;
            $isProduct = 'true';
            $punyabahan = 'false';
            $perusahaanNo = getPerusahaanNo();
            $idoutlet = -1;
            if (isNotEmpty($post_item_name)) {
                $namaitem = $post_item_name;
            } else {
                $response['msg'] = 'Item tidak boleh kosong';
                echo json_encode($response);
                return;
            }
            if (isNotEmpty($post_harga_jual)) {
                $hargajual = $post_harga_jual;
            }
            if (isNotEmpty($post_harga_beli)) {
                $hargabeli = $post_harga_beli;
            }
            if (isNotEmpty($post_is_produk)) {
                $isProduct = $post_is_produk;
            }
            if (isNotEmpty($post_punya_bahan)) {
                $punyabahan = $post_punya_bahan;
            }

            $hapusgambar = $postItem['deletegambar'] == 'true';
            
            $this->load->model('Masteritem');
            $this->load->model('Kategori');
            $this->load->model('Satuan');
            $this->load->library('NutaQuery');
            $index = 0;
            foreach ($outlet_ids as $idoutlet) {
                $itemid = "-1.0";
                if (isNotEmpty($namakategori)) {
                    if ($oldnamakategori != $namakategori && $original_save_outletid != $idoutlet) { //outlet  pertama kategorinya sudah diupdate by ajax
                        $idkategori = explode(".",
                            $this->Kategori->getKategoriIDByName($idoutlet, $oldnamakategori, $perusahaanNo));
                        $this->Kategori->editKategori($idkategori[0], $idkategori[1], $idoutlet, $oldnamakategori, $namakategori);
                    } else {
                        $idkategori = explode(".",$this->Kategori->getKategoriIDByName($idoutlet, $namakategori, $perusahaanNo));
                    }
                } else {
                    $namakategori = "";
                }

                if ($mode == 'new') {
                    $itemid = $this->Masteritem->createNewItem($namaitem, $idkategori[0], $idkategori[1], $namasatuan, $hargajual, $hargabeli, $isProduct, $punyabahan, $idoutlet, $perusahaanNo);
                    if ($itemid == "Item ini sudah ada") {
                        $response['msg'] = "Item " . $namaitem . " sudah ada, silakan input Item lain.";
                        echo json_encode(array('status' => false, 'data' => array_values($response), 'message' => $response['msg']));
                        return;
                    }
                    if ($punyabahan == 'true') {
                        foreach ($post_bahans as $k => $bahan) {
                            if (isNotEmpty($bahan['nama'])) {
                                log_message('error',"$namaitem bahan $idoutlet : " . var_export($bahan,true));
                                $oldbahan = $this->Masteritem->getByName($bahan['nama'], $idoutlet);
                                if (!isset($oldbahan)) {
                                    //1. Simpan bahan Master Item
                                    $idbahan = $this->Masteritem->createItemBahan($bahan['nama'], $bahan['satuan'], $idoutlet, $perusahaanNo,$bahan['hargabeli']);

                                    //2. Insert ke MasterItemDetailIngredients
                                    $detailnumber = $k + 1;
                                    $idmdi = $this->Masteritem->createNewLinkItemBahan($itemid, $idbahan, $detailnumber, $bahan['qty'], $idoutlet, $perusahaanNo);
                                } else {
                                    $detailnumber = $k + 1;
                                    $idmdi = $this->Masteritem->createNewLinkItemBahan(
                                        $itemid, $oldbahan->ItemID . "." . $oldbahan->DeviceNo,
                                        $detailnumber, $bahan['qty'], $idoutlet, $perusahaanNo);
                                }
                            }
                        }
                    }
                    $oldvarians = $this->Mastervarian->getVariasiHargaArray($itemid, $idoutlet);
                    foreach ($variasi_harga as $variasi) {

                        $namaVariasi = $variasi['nama'];
                        $sellPrice = $variasi['harga'];
                        $oldName = $variasi['oldname'];
                        $isReguler = $variasi['reguler'];

                        $is_varian_existing = false;
                        $isNamavarianChanged = false;
                        $isQtyvarianChanged = false;
                        $isSatuanChanged = false;

                        foreach ($oldvarians as $oldvarian) {
                            if ($variasi['nama'] == $oldvarian['VarianName']) {
                                $is_varian_existing = true;
                                $isNamavarianChanged = ($oldvarian['VarianName'] != $variasi['nama']);
                                $isQtyvarianChanged = ($oldvarian['SellPrice'] != $variasi['harga']);
                                $isSatuanChanged = false;
                                break;
                            }
                        }

                        if ($is_varian_existing) {

                            if ($isNamavarianChanged || $isSatuanChanged) {
                                // Update Variant
                                $this->Mastervarian->updateVariasiHarga($idoutlet, $itemid, $namaVariasi, $namaVariasi, $sellPrice, $isReguler, $perusahaanNo);
                            }
                            if ($isQtyvarianChanged) {
                                //Update variant by id
//                                    $this->Mastervarian->updateVariasiHargaByID($idoutlet, $itemid, $oldName, $namaVariasi, $sellPrice, $isReguler);
                                $this->Mastervarian->updateVariasiHarga($idoutlet, $itemid, $namaVariasi, $namaVariasi, $sellPrice, $isReguler, $perusahaanNo);
                            }
                        } else {
                            $this->Mastervarian->createVariasiharga($idoutlet, $itemid, $namaVariasi, $sellPrice, $isReguler, $perusahaanNo);
                        }
                    }

                    //handle varian yang dihapus
                    $vtobe_deleted = array();

                    foreach ($oldvarians as $oldvarian) {
                        $this->isItemvarianExist = false;
                        foreach ($variasi_harga as $varian) {
                            $namavarian = $varian['nama'];
                            $oldNamavarian = $oldvarian['VarianName'];
                            if ($namavarian == $oldNamavarian) {
                                $this->isItemvarianExist = true;
                                break;
                            }
                        }

                        if (!$this->isItemvarianExist) {
                            array_push($vtobe_deleted, $oldvarian);
                        }
                    }

                    foreach ($vtobe_deleted as $d) {
                        $this->Mastervarian->hapusVariasiHarga($idoutlet, $itemid, $d['VarianName'], $perusahaanNo);
                    }
                    if (count($post_namamodifiers) > 0) {
                        foreach ($post_namamodifiers as $nama) {
                            if ($nama != '') {
                                $idmodifier = $this->Masteritem->getModifierIDByName($idoutlet, $nama);
                                $this->Masteritem->createNewLinkModifier($itemid, $idmodifier, $idoutlet);
                            }
                        }
                    }
                } else if ($mode == 'edit') {
                    //handle update Item
                    $oldName = $postItem['olditemname'];
                    $referensi = 'line 591';
                    $itemid = $this->Masteritem->updateByName($oldName, $namaitem, $idkategori[0], $idkategori[1], $hargajual, $hargabeli, $isProduct, $punyabahan, $idoutlet, $namasatuan, $referensi, false);
                    //handle update Gambar
                    if ($hapusgambar) {
                        $this->Masteritem->hapusGambar($namaitem, $idoutlet);
                    }

                    $this->nutaquery->setOutlet($idoutlet);

                    $str_query_bahan = $this->nutaquery->get_query_bahan_item($itemid);
                    $query_bahan = $this->db->query($str_query_bahan);
                    $oldBahans = $query_bahan->result();
                    
                    //handle bahan yang diubah
                    if (count($post_bahans) > 0) {
                        $detnumber = 0;
                        foreach ($post_bahans as $k => $bahan) {
                            if (isNotEmpty($bahan['nama'])) {
                                log_message('error',"$namaitem bahan $idoutlet : " . var_export($bahan,true));
                                $detnumber = $detnumber + 1;
                                $is_bahan_existing = false;
                                $isNamaBahanChanged = false;
                                $isQtyBahanChanged = false;
                                $isSatuanChanged = false;
                                $isHargaBeliChanged = false;

                                foreach ($oldBahans as $k => $oldBahan) {
                                    if (strtolower($bahan['nama']) == strtolower($oldBahan->ItemName)) {
                                        $is_bahan_existing = true;
                                        $isNamaBahanChanged = ($oldBahan->ItemName != $bahan['nama']);
                                        $isQtyBahanChanged = ($oldBahan->QtyNeed != $bahan['qty']);
                                        $isSatuanChanged = ($oldBahan->Satuan != $bahan['satuan']);
                                        $isHargaBeliChanged = ($oldBahan->PurchasePrice != $bahan['hargabeli']);
                                        break;
                                    }
                                }
                                if ($is_bahan_existing == false) {
                                    $oldbahan = $this->Masteritem->getByName($bahan['nama'], $idoutlet);
                                    if (!isset($oldbahan)) {
                                        //1. Simpan bahan Master Item
                                        $idbahan = $this->Masteritem->createItemBahan($bahan['nama'], $bahan['satuan'], $idoutlet, $perusahaanNo,$bahan['hargabeli']);

                                        //2. Insert ke MasterItemDetailIngredients
                                        $detailnumber = $detnumber;
                                        $idmdi = $this->Masteritem->createNewLinkItemBahan($itemid, $idbahan, $detailnumber, $bahan['qty'], $idoutlet, $perusahaanNo);
                                    } else {
                                        $detailnumber = $detnumber;
                                        $idmdi = $this->Masteritem->createNewLinkItemBahan($itemid,
                                            $oldbahan->ItemID . "." . $oldbahan->DeviceNo, $detailnumber,
                                            $bahan['qty'], $idoutlet, $perusahaanNo);
                                    }
                                } else {
                                    //echo ">_ Bahan " . $bahan['nama'] . ($is_bahan_existing ? " sudah ada" : " gak ada");

                                    if ($is_bahan_existing) {
                                        if ($isSatuanChanged or $isHargaBeliChanged ) { // ada tambahan untuk update harga beli
                                            // Update Item
                                            //echo ">_ Bahan " . $bahan['nama'] . ($isNamaBahanChanged ? " nama diubah, " : "") . ($isSatuanChanged ? " satuan diubah." : "");
                                            
                                            $referensi = 'line 634, bahan dari item : ' . $namaitem;
                                            $this->Masteritem->updateByName($oldBahan->ItemName, $bahan['nama'], 0, 1, 0, $bahan['hargabeli'], 'false', 'false', $idoutlet, $bahan['satuan'], $referensi, true);
                        
                                        }
                                        if ($isQtyBahanChanged) {
                                            //echo ">_ Bahan " . $bahan['nama'] . ($isQtyBahanChanged ? " jumlah diubah, " : "");
                                            //Update Link Bahan
                                            $srcitembahan = $this->Masteritem->getByName($bahan['nama'], $idoutlet);
                                            $this->Masteritem->updateLinkBahan($itemid,
                                                $srcitembahan->ItemID . "." . $srcitembahan->DeviceNo,
                                                $bahan['qty'], $idoutlet, $perusahaanNo);
                                        }
                                    } else {
                                        //echo ">_ Bahan nya ga ada jadi buat baru";
                                        //Buat Bahan
                                        $idbahan = $this->Masteritem->createItemBahan($bahan['nama'], $bahan['satuan'], $idoutlet, $perusahaanNo,$bahan['hargabeli']);
                                        //Link ke Item
                                        $detailnumber = $detnumber;
                                        $this->Masteritem->createNewLinkItemBahan($itemid, $idbahan, $detailnumber, $bahan['qty'], $idoutlet, $perusahaanNo);
                                    }

                                }
                            }
                        }
                    }

                    //handle bahan yang dihapus
                    $tobe_deleted = array();

                    foreach ($oldBahans as $oldBahan) {
                        $this->isItemBahanExist = false;
                        foreach ($post_bahans as $bahan) {
                            $namabahan = $bahan['nama'];
                            $oldNamaBahan = $oldBahan->ItemName;
                            if (strtolower($namabahan) == strtolower($oldNamaBahan)) {
                                $this->isItemBahanExist = true;
                                break;
                            }
                        }

                        if (!$this->isItemBahanExist) {
                            log_message('error', var_export($oldBahan, true));
                            array_push($tobe_deleted, $oldBahan);
                        }
                    }

                    $delete_fail_use_in_order_sale = array();
                    
                    $oldvarians = $this->Mastervarian->getVariasiHargaArray($itemid, $idoutlet);
                    foreach ($variasi_harga as $variasi) {

                        $namaVariasi = $variasi['nama'];
                        $sellPrice = $variasi['harga'];
                        $oldName = $variasi['oldname'];
                        $isReguler = $variasi['reguler'];

                        $is_varian_existing = false;
                        $isNamavarianChanged = false;
                        $isQtyvarianChanged = false;
                        $isSatuanChanged = false;

                        foreach ($oldvarians as $oldvarian) {
                            if ($variasi['nama'] == $oldvarian['VarianName']) {
                                $is_varian_existing = true;
                                $isNamavarianChanged = ($oldvarian['VarianName'] != $variasi['nama']);
                                $isQtyvarianChanged = ($oldvarian['SellPrice'] != $variasi['harga']);
                                $isSatuanChanged = false;
                                break;
                            }
                        }

                        if ($is_varian_existing) {

                            if ($isNamavarianChanged || $isSatuanChanged) {
                                // Update Variant
                                $this->Mastervarian->updateVariasiHarga($idoutlet, $itemid, $namaVariasi, $namaVariasi, $sellPrice, $isReguler, $perusahaanNo);
                            }
                            if ($isQtyvarianChanged) {
                                //Update variant by id
//                                    $this->Mastervarian->updateVariasiHargaByID($idoutlet, $itemid, $oldName, $namaVariasi, $sellPrice, $isReguler);
                                $this->Mastervarian->updateVariasiHarga($idoutlet, $itemid, $namaVariasi, $namaVariasi, $sellPrice, $isReguler, $perusahaanNo);
                            }
                        } else {
                            $this->Mastervarian->createVariasiharga($idoutlet, $itemid, $namaVariasi, $sellPrice, $isReguler, $perusahaanNo);
                        }
                    }

                    //handle varian yang dihapus
                    $vtobe_deleted = array();

                    foreach ($oldvarians as $oldvarian) {
                        $this->isItemvarianExist = false;
                        foreach ($variasi_harga as $varian) {
                            $namavarian = $varian['nama'];
                            $oldNamavarian = $oldvarian['VarianName'];
                            if ($namavarian == $oldNamavarian) {
                                $this->isItemvarianExist = true;
                                break;
                            }
                        }

                        if (!$this->isItemvarianExist) {
                            array_push($vtobe_deleted, $oldvarian);
                        }
                    }

                    foreach ($vtobe_deleted as $d) {
                        $this->Mastervarian->hapusVariasiHarga($idoutlet, $itemid, $d['VarianName'], $perusahaanNo);
                    }

                    foreach ($tobe_deleted as $d) {
                        $is_used_in_pending_sale = $this->Masteritem->isItemUseInPendingSale($itemid, $idoutlet);
                        if ($is_used_in_pending_sale) {
                            array_push($delete_fail_use_in_order_sale, $d);
                        }
                    }

                    if (count($delete_fail_use_in_order_sale) == 0) {
                        foreach ($tobe_deleted as $d) {
                            $this->Masteritem->hapusBahan($d->DetailID, $idoutlet, $d->DeviceNo, $perusahaanNo);
                        }
                        $response['msg'] = $itemid;
                    } else {
                        #Generate Pesan eror
                        $msg = "";
                        $itemnames = "";
                        if (count($delete_fail_use_in_order_sale) > 0) {
                            $msg = " * Item";
                            $itemnames = "";
                            foreach ($delete_fail_use_in_order_sale as $d) {
                                $itemnames .= " " . $d->ItemName . ",";
                            }
                            $msg .= rtrim($itemnames, ",");
                            $msg .= " tidak dapat dihapus karena sedang dipesan oleh pelanggan";
                        }
                        $response['msg'] = $msg;
                    }

                    $oldModifiers = $this->Masteritem->getModifiersItem($itemid, $idoutlet);

                    //handle jika ada input
                    if (isset($post_namamodifiers) && count($post_namamodifiers) > 0) {
                        foreach ($post_namamodifiers as $m) {
                            $this->isModifierExist = false;
                            $idmodifier = $this->Masteritem->getModifierIDByName($idoutlet, $m);
                            foreach ($oldModifiers as $om)
                                if ($om->ModifierID == $idmodifier) {
                                    $this->isModifierExist = true;
                                    break;
                                }
//                        echo ">_ Hmmm... Modifier " . $m . ($this->isModifierExist ? " ada " : "ga ada");
                            if (!$this->isModifierExist) {
                                $this->Masteritem->createNewLinkModifier($itemid, $idmodifier, $idoutlet);
                            }
                        }

                        //handle jika dihapus
                        foreach ($oldModifiers as $om) {
                            $this->isModifierExist = false;
                            if (count($post_namamodifiers) > 0) {
                                foreach ($post_namamodifiers as $m) {
                                    $idmodifier = $this->Masteritem->getModifierIDByName($idoutlet, $m);
                                    if ($om->ModifierID == $idmodifier) {
                                        $this->isModifierExist = true;
                                        break;
                                    }
                                }
                            }
                            if (!$this->isModifierExist) {
                                $this->Masteritem->deleteModifierItem($itemid, $idoutlet, $om->DetailID, $om->DeviceNo, $perusahaanNo);
                            }
                        }
                    }

                }
                
                $realitemid = explode(".", $itemid)[0];
                $devno = explode(".", $itemid)[1];
                $options = $this->Options->get_by_devid($idoutlet);
                if ($options->CreatedVersionCode>=200 || $options->EditedVersionCode>=200) {
                    $this->Firebasemodel->push_firebase($idoutlet, 
                                            array('table' => 'pleaseUpdateMasterItem', 
                                                    'column' => array('ItemID' => $realitemid, 'DeviceNo' => $devno)),
                                            $realitemid, $devno, $perusahaanNo, 0);
                }
                array_push($response, array('outlet' => $idoutlet, 'saved_id' => $itemid));

                $index++;
            }

        } else {
            $response['msg'] = 'Mode ?';
        }
//        log_message('error', var_export($response, true));
        echo json_encode(array('status' => true, 'data' => array_values($response), 'message' => $itemid));
    }

    public function saveextra()
    {
        //        var_dump($this->input->post()); exit;
        $canAddQuantity = 0;
        $hanyasatu = (int) $this->input->post('cekpilihsatu');
        $this->load->model('Mastermodifier');
        $this->load->model('Masteritem');
        $perusahaanNo = getPerusahaanNo();
        $whatIwant = [
            'outlet' => 1,
            'modifiers' => [
                [
                    'nama' => $this->input->post('name'),
                    'saved_id' => "1.0",
                ]
            ]
        ];
        $arroutlet = $this->input->post('outlet');
        $namaModifier = $this->input->post('nama');
        if (count($arroutlet) > 0) {
            foreach ($arroutlet as $ko => $outlet_ids) {
                $idmodifier = $this->Mastermodifier->getModifierIDByName($outlet_ids, $namaModifier);
                $createorupdate = 'create';
                if ($idmodifier == "NO_MODIFIER_CANT_UPDATE") {
                    $idmodifier = $this->Mastermodifier->createModifier($outlet_ids, $namaModifier, $hanyasatu, 0, $perusahaanNo);
                    $createorupdate = 'create';
                } else {
                    $this->Mastermodifier->updateModifier($outlet_ids, $namaModifier, $namaModifier, $hanyasatu, 0, $perusahaanNo);
                    $createorupdate = 'update';
                }
                $detailModifier = $this->Mastermodifier->getPilihan($idmodifier, $outlet_ids);
                $arr = [];

                $pilihans = $this->input->post('pilihan');
                foreach ($pilihans as $key => $pilihan) {
                    if (!isset($pilihan['name'])) {
                        continue;
                    }
                    $namaPilihan = $pilihan['name'];
                    $harga = $pilihan['harga'];
                    $qty = 0;
                    if (!isNotEmpty($harga)) {
                        $harga = 0;
                    }
                    $idpil = $this->Mastermodifier->getPilihanbyName($outlet_ids, $namaPilihan, $idmodifier);
                    if (count($idpil) > 0) {
                        $idpilihan = $this->Mastermodifier->updatePilihan2($outlet_ids, $idmodifier, $idpil[0]->DetailID, $namaPilihan, $harga, $qty, getPerusahaanNo());
                    } else {
                        $idpilihan = $this->Mastermodifier->createPilihan($outlet_ids, $idmodifier, $namaPilihan, $harga, $qty, getPerusahaanNo());
                    }
                    $arr[] = $idpilihan;
                    $bahan = $this->input->post('bahan');
                    //                    var_dump($bahan); exit;
                    if (isset($bahan[$key])) {
                        foreach ($bahan[$key] as $k => $v) {
                            $oldbahan = $this->Masteritem->getByName($v['name'], $outlet_ids);
                            if (!isset($oldbahan)) {
                                $idbahan = $this->Masteritem->createItemBahan($v['name'], $v['satuan'], $outlet_ids, $perusahaanNo, $v['hargabeli']);
                                $idbahan = explode(".", $idbahan);
                                $idbahan = $idbahan[0];
                            } else {
                                $idbahan = $oldbahan->ItemID;
                            }

                            $cek = $this->Mastermodifier->getModifierBahanByName($outlet_ids, $idpilihan, $idbahan, $perusahaanNo, $outlet_ids, $v['qty'], $k);
                            if (count($cek) > 0) {
                                $idbahan = $this->Mastermodifier->UpdateModifierbahan($outlet_ids, $idpilihan, $idbahan, $perusahaanNo, $outlet_ids, $v['qty'], $k, $cek[0]->ID);
                            } else {
                                $idbahan = $this->Mastermodifier->createModifierbahan($outlet_ids, $idpilihan, $idbahan, $perusahaanNo, $outlet_ids, $v['qty'], $k);
                            }
                        }
                    }
                }
                if ($createorupdate == 'update') {
                    foreach ($detailModifier as $uk => $uv) {
                        $key = array_search($uv['DetailID'], $arr);
                        if (gettype($key) == 'boolean' and $key == false) {

                            $this->Mastermodifier->hapusPilihanByDetailID($outlet_ids, $uv['DetailID'] . '.' . $uv['DeviceNo'], $perusahaanNo);
                        }
                    }
                    $delete = $this->input->post('delete');
                    if (count($delete) > 0) {
                        foreach ($delete as $kd => $vd) {
                            $this->Mastermodifier->hapusBahanByID($vd, $outlet_ids);
                        }
                    }
                }
            }
        }

        echo json_encode(["status" => true]);
    }

    public function savedatarekening()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->load->model('Datarekeningmodel');

            $bankName = $this->input->post('bankName');
            $accountNumber = $this->input->post('accountNumber');
            $accountName = $this->input->post('accountName');
            $idoutlet = $this->input->post('idoutlet');
            $mode = $this->input->post('mode');
            $accountID = $this->input->post('accountID');
            $devno = $this->input->post('devno');
            $username = $this->input->post('usernameibank');
            $password = $this->input->post('passwordibank');
            $passwordasli = $this->input->post('passwordibankasli');
            if ($mode == "edit") {
                $where = ["AccountID" => $accountID,
                            "DeviceID" => $idoutlet,
                        ];
                $cek = $this->db->get_where("mastercashbankaccount", $where)->result();
                $result = $this->Datarekeningmodel->updateDataRekening($bankName, $accountNumber, $accountName, $idoutlet, $accountID, $devno, $username, $password, $passwordasli, $cek[0]->account_id);
            } else if ($mode == "new") {
                $result = $this->Datarekeningmodel->createNewDataRekening($bankName, $accountNumber, $accountName, $idoutlet, $username, $password);
            }
            if ($result) {
                echo json_encode(array("status" => "OK"));
            }
        } else {
            echo "404";
        }
    }

    public function coba()
    {
        $message = '';
        $status = 'success';
        if ($this->session->has_userdata('aaa')) {
            $message = $this->session->userdata('aaa');
            $this->session->unset_userdata('aaa');
            if ($message === 'error') {
                $status = 'failed';
            }
        }
        echo json_encode(array(
            'msg' => $message,
            'status' => $status
        ));
    }

    function gettrxbyacct()
    {
        $acct = $this->input->get_post('acct');
        $outlet = $this->input->get_post('o');
        if ($acct != "") {
            $acctsplit = explode(".", $acct);
            $account = $this->db->get_where("mastercashbankaccount", array("AccountID" => $acctsplit[0], "DeviceID" => $outlet, "DeviceNo" => $acctsplit[1]))->result();
            $dataTransaksi = [];
            $meta = json_encode([]);
            if (sizeof($account) > 0) {
                if ($account[0]->connected == 1 || $account[0]->connected == "1") {
                    $this->load->library('finfini');
                    if ($this->input->get_post('page') != '' and $this->input->get_post('page') != null) {
                        $a = $this->finfini->transaction_by_account_perpage($account[0]->account_id, $this->input->get_post('page'));
                    } else {
                        $a = $this->finfini->transaction_by_account($account[0]->account_id);
                    }
                    $meta = $a->meta;
                    if ($a->status == 'success') {
                        foreach ($a->data as $k => $v) {
                            $cekTrans = $this->db->get_where('cloud_cashbankout', array("finfini_transaction_id" => $v->id))->result();
                            $cekTrans2 = $this->db->get_where('cloud_cashbankin', array("finfini_transaction_id" => $v->id))->result();
                            if (sizeof($cekTrans) < 1 and sizeof($cekTrans2) < 1) {
                                $dataTransaksi[] = $v;
                            }
                        }
                    }
                }
            }
            $return = ["status" => true, "acct" => $acctsplit[0], "outlet" => $outlet, "transaksi" => $dataTransaksi, 'meta' => $meta];
        } else {
            $return = ["status" => false, "acct" => $acct];
        }
        echo json_encode($return);
    }

    public
    function deletemastermodifier()
    {
        ifNotAuthenticatedRedirectToLogin();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $iditem = $this->input->get_post('iditem');
            $arrayOfidOutlet = $this->input->get_post('idoutlet');
            $this->load->model('Mastermodifier');
            if (count($arrayOfidOutlet) > 0) {
                foreach ($arrayOfidOutlet as $ik => $iv) {
                    $idmodifier = $this->Mastermodifier->getModifierIDByName($iv, $iditem);
                    if ($idmodifier != "NO_MODIFIER_CANT_UPDATE") {
                        $this->Mastermodifier->hapusPilihanByModifierID($iv, $idmodifier, getPerusahaanNo());
                        $this->Mastermodifier->hapusModifier($iv, $iditem, getPerusahaanNo());
                    }
                }
            }

            echo json_encode(array(
                'status' => 'success',
                'msg' => "modifier berhasil di hapus"
            ));
        } else {
            echo "404";
        }
    }
}
