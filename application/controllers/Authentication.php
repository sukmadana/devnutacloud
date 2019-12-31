<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('nuta_helper');
    }

    public function index()
    {
        redirect(preventXSS(base_url() . 'authentication/gate'));
    }

    public function loginv2()
    {
        $this->load->library('session');

//        $sessid=$this->session->userdata('id');
        //        if(isNotEmpty($sessid)) {
        //            redirect(base_url() . 'cloud/main');
        //        }
        $messageError = array(
            'e=0' => '',
            'e=1' => 'Username tidak terdaftar.',
            'e=2' => 'Password salah.',
            'e=3' => 'Nama Perusahaan tidak terdaftar.',
            'e=4' => 'Username ini belum konfirmasi email. Periksa kembali email anda.',
            'e=5' => 'User ini tidak aktif.',
            'e=6' => 'Tidak bisa login karena tidak mempunyai akses.',
        );
        $error = $this->input->get('e');
        $errorperusahaan = $this->input->get('t') == 1;
        $idperusahaan = $this->input->get('v');
        $username = $this->input->get('u');
        $forgotPwUrl = '';
        if (!isNotEmpty($error)) {
            $error = 0;
        } else {
            $this->load->library('encryption');
            $encPerusahaan = $this->encrypt->encode($idperusahaan);
            $encUsername = $this->encrypt->encode($username);
            $encP = str_replace(array('+', '/', '='), array('-', '_', '~'), $encPerusahaan);
            $encU = str_replace(array('+', '/', '='), array('-', '_', '~'), $encUsername);
            $_forgotPwUrl = 'account/forgot?p=' . $encP . '&i=' . $encU;
            $forgotPwUrl = base_url($_forgotPwUrl);
        }
        $this->load->library('user_agent');
        $this->load->view('authentication/login', array(
            'error' => preventXSS($error),
            'errorperusahaan' => preventXSS($errorperusahaan),
            'idperusahaan' => preventXSS($idperusahaan),
            'msg' => preventXSS($messageError["e=" . $error]),
            'username' => preventXSS($username),
            'forgotpwurl' => $forgotPwUrl
        ));
    }

    public function gate()
    {
        $devid = $this->input->get('i');
        $this->load->model('User');
        //Sudah Pernah Daftar?
        $sudahPernahDaftar = $this->User->IsTerdaftar($devid);

        if ($sudahPernahDaftar) {
            redirect(preventXSS(base_url() . 'authentication/loginv2'));
        } else {
            redirect(preventXSS(base_url() . 'authentication/register?i=' . $devid));
        }
    }

    public function auth()
    {
        $urlRedirect = "";
        $urlRedirectError = array(
            'error_user' => preventXSS(base_url() . 'authentication/loginv2?e=1'),
            'error_password' => preventXSS(base_url() . 'authentication/loginv2?e=2'),
            'error_idperusahaan' => preventXSS(base_url() . 'authentication/loginv2?e=3'),
            'error_konfirmasi' => preventXSS(base_url() . 'authentication/loginv2?e=4'),
            'error_belumaktif' => preventXSS(base_url() . 'authentication/loginv2?e=5'),
            'error_cabang' => preventXSS(base_url() . 'authentication/loginv2?e=6'),
        );
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $namaperusahaan = $this->input->get_post('idperusahaan');
            $idperusahaan = str_replace(' ', '', trim($namaperusahaan));
            $username = $this->input->get_post('username');
            $password = $this->input->get_post('password');
            $this->load->model('User');
            $this->load->model('Perusahaanmodel');
            $loginIndividual = !isset($idperusahaan);
            if ($loginIndividual) {
                $isUserExist = $this->User->IsUsernameExist($username);
                if ($isUserExist) {
                    //cek sudah konfirmasi atau belum
                    $isConfirm = $this->User->isUserConfirmed($username);
                    if ($isConfirm) {
                        $AuthObject = $this->User->authIndividual($username, $password);
                        if ($AuthObject['isAuth']) {
                            $AuthObject['namaperusahaan'] = 'Individual';
                            $AuthObject['registerwithdeviceid'] = $AuthObject['id'];
                            $AuthObject['hascabang'] = true;
                            $ismenuvisible = $this->Perusahaanmodel->IsMenuPerusahaanVisible($AuthObject['registerwithdeviceid']);
                            $this->setLoginSession($AuthObject['id'], $username, $AuthObject['namaperusahaan'], $AuthObject['registerwithdeviceid'], $ismenuvisible);
                            redirect(preventXSS(base_url() . 'cloud/main'));
                        } else {
                            redirect(preventXSS($urlRedirectError['error_password']));
                        }
                    } else {
                        redirect(preventXSS($urlRedirectError['error_konfirmasi']));
                    }

                } else {
                    redirect(preventXSS($urlRedirectError['error_user']));
                }
            } else {
                $isIdperusahaanExist = $this->Perusahaanmodel->isPerusahaanExist($idperusahaan);
                if ($isIdperusahaanExist) {
                    $isUserPerusahaanExist = $this->User->isUserPerusahaanExist($idperusahaan, $username);
                    if ($isUserPerusahaanExist) {
                        $isUserAktif = $this->User->isUserPerusahaanAktif($idperusahaan, $username);
                        if ($isUserAktif) {
                            $AuthObject = $this->User->authPerusahaan($idperusahaan, $username, $password);
                            if ($AuthObject['isAuth']) {
                                $np = $this->Perusahaanmodel->GetNamaPerusahaan($idperusahaan);
                                $AuthObject['namaperusahaan'] = $np;
                                $AuthObject['registerwithdeviceid'] = $AuthObject['regid'];
                                $this->load->model('Userperusahaancabang');
                                $AuthObject['hascabang'] = $this->Userperusahaancabang->isUserHasAksesCabang($username, $idperusahaan);

                                $ismenuvisible = FALSE;
                                //$this->Perusahaanmodel->IsMenuPerusahaanVisible($AuthObject['registerwithdeviceid']);
                                $this->load->model('Userperusahaan');
                                $isUserOwner = $this->Userperusahaan->isUserOwner($idperusahaan, $username);
                                if ($isUserOwner == TRUE) {
                                    $AuthObject['hascabang'] = true;
                                    $ismenuvisible = true;
                                }
                                if ($AuthObject['hascabang']) {
                                    $saleTables = $this->getSaleTables($idperusahaan);
                                    $perusahaans = $this->getPerusahaanNo($idperusahaan);
                                    $this->setLoginSession($AuthObject['id'], $username, $AuthObject['namaperusahaan'], $AuthObject['registerwithdeviceid'], $ismenuvisible, $saleTables->TabelSale, $saleTables->TabelSaleItemDetail, $saleTables->TabelSaleItemDetailIngredients, $perusahaans->PerusahaanNo);
                                    redirect(preventXSS(base_url() . 'cloud/main'));
                                } else {
                                    redirect(preventXSS($urlRedirectError['error_cabang']) . "&t=1&v=" . preventXSS($namaperusahaan) . '&u=' . preventXSS($username));
                                }
                            } else {
                                redirect(preventXSS($urlRedirectError['error_password']) . "&t=1&v=" . preventXSS($namaperusahaan) . '&u=' . preventXSS($username));
                            }
                        } else {
                            redirect(preventXSS($urlRedirectError['error_belumaktif']) . "&t=1&v=" . preventXSS($namaperusahaan) . '&u=' . preventXSS($username));
                        }
                    } else {
                        redirect(preventXSS($urlRedirectError['error_user']) . "&t=1&v=" . preventXSS($namaperusahaan) . '&u=' . preventXSS($username));
                    }
                } else {
                    redirect(preventXSS($urlRedirectError['error_idperusahaan']) . "&t=1&v=" . preventXSS($namaperusahaan) . '&u=' . preventXSS($username));
                }
            }
        } else {
            show_404();
        }
    }

    public function logout()
    {
        $this->removeLoginSession();
    }

    public function autoauth()
    {
        //todo:for auto auth and redirect
        $cID = $this->input->get('i');
        $this->load->model('User');
        $this->load->model('Perusahaanmodel');
        if (isNotEmpty($cID)) {
            $isAuth = $this->User->authIndividualByDeviceID($cID);

            if ($isAuth['isAuth']) {
                $ismenuvisible = $this->Perusahaanmodel->IsMenuPerusahaanVisible($isAuth['registerwithdeviceid']);
                if ($isAuth['namaperusahaan'] != 'Individual') {
                    $ismenuvisible = true;
                }
                $this->setLoginSession($isAuth['id'], $isAuth['Username'], $isAuth['namaperusahaan'],
                    $isAuth['registerwithdeviceid'], $ismenuvisible);

                $redirect = $this->input->get('r');
                if (isNotEmpty($redirect)) {
                    redirect($redirect);
                } else {
                    redirect(base_url() . 'cloud/main');
                }

            } else {
                redirect(base_url() . 'authentication/loginv2');
            }
        }
    }

    public function register()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $username = $this->input->get_post('username');
            $password = $this->input->get_post('password');
            $confirmPassword = $this->input->get_post('confirmPassword');
            $email = $this->input->get_post('email');
            $nohp = $this->input->get_post('nohp');
            $devid = $this->input->get_post('id');
            //second validation
            $isUsernameEmpty = !isNotEmpty($username);
            $isPasswordEmpty = !isNotEmpty($password);
            $isPasswordEqual = ($password === $confirmPassword);
            $isEmailEmpty = !isNotEmpty($email);
            $isNoHpEmpty = !isNotEmpty($nohp);
            $isDevidempty = !isNotEmpty($devid);
            $this->load->model('User');
            $emailSudahDipakai = $this->User->isEmailExist($email);
            $errors = array();
            if ($isUsernameEmpty) {
                array_push($errors, 'Username tidak boleh kosong');
            }
            if ($isPasswordEmpty) {
                array_push($errors, 'Password tidak boleh kosong');
            }
            if (!$isPasswordEqual) {
                array_push($errors, 'Password tidak sama');
            }
            if ($isEmailEmpty) {
                array_push($errors, 'Email tidak boleh kosong');
            }
            if ($isNoHpEmpty) {
                array_push($errors, 'No. Handphone tidak boleh kosong');
            }
            if ($emailSudahDipakai) {
                array_push($errors, 'Email sudah dipakai');
            }
            if ($isDevidempty) {
                array_push($errors, 'Hubungi admin');
            }
            $tidakadaerrordidata = (count($errors) == 0);
            $iduser = -1;
            if ($tidakadaerrordidata) {
                $iduser = $this->saveUserToDB($username, $password, $nohp, $email, $devid);
                if ($iduser == -1) {
                    array_push($errors, 'Username sudah terdaftar, ganti username lain.');
                }
            }

            $isDataError = count($errors) > 0;
            if ($isDataError) {
                $this->load->view('authentication/registrasi_individual', array('mode' => 'form', 'email' => null, 'error' => $errors, 'id' => $devid));
            } else {
                $this->sentEmailKonfirmasiIndividual($email, $iduser);
                $this->load->view('authentication/registrasi_individual', array('mode' => 'konfirmasi', 'email' => $email, 'error' => $errors, 'id' => null));
                return;
            }

        } else {
            $this->load->model('User');
            $devid = $this->input->get('i');
            //Sudah Pernah Daftar?
            $sudahPernahDaftar = $this->User->IsTerdaftar($devid);

            if ($sudahPernahDaftar) {
                redirect(base_url() . 'authentication/loginv2');
            } else {
                $this->load->view('authentication/registrasi_individual', array('mode' => 'form', 'email' => null, 'error' => array(), 'id' => null));
            }

        }
    }

    public function konfirmasi()
    {
        $this->load->helper('hashids_helper');
        $encIdUser = $this->input->get('a');
        if (!isset($encIdUser)) {
            show_404();
        }
        $IdUser = hashids_decrypt($encIdUser, "bnV0YXBvc2tleWVuY3J5cHRpbmRpdmlkdWFs");
        $this->load->model('User');
        $this->User->SudahKonfirmasi($IdUser);
        redirect(base_url() . 'authentication/loginv2?k=1');
    }

    /**
     * @param $username
     * @param $password
     * @param $nohp
     * @param $email
     */
    private function saveUserToDB($username, $password, $nohp, $email, $devid)
    {
        $this->load->model('User');
        if ($this->User->IsUsernameExist($username)) {
            return -1;
        } else {
            return $this->User->Create(array('username' => $username,
                'password' => $password, 'nohp' => $nohp, 'email' => $email,
                'deviceid' => $devid));
        }
    }

    /**
     * @param $email
     * @param $iduser
     */
    private function sentEmailKonfirmasiIndividual($email, $iduser)
    {
        $this->load->library('email');
        $this->load->helper('hashids_helper');
        $this->load->model('User');
        $user = $this->User->getUsernamePasswordByUserID($iduser);
        $this->email->from('no-reply@nutacloud.com', 'no-reply@nutacloud.com');
        $this->email->to(array($email));
        $encIdUser = hashids_encrypt("bnV0YXBvc2tleWVuY3J5cHRpbmRpdmlkdWFs", $iduser, 7);
        $url = base_url() . "authentication/konfirmasi?a=" . $encIdUser;
        $subject = "Akun nutacloud anda";
        $this->email->subject($subject);
        $message = $this->load->view('mail/mail_registrasi_individual', array('url' => $url, 'subject' => $subject, 'username' => $user['username'], 'password' => $user['password']), true);
        $this->email->message($message);
        $this->email->send();
    }

    protected function isPOST()
    {
        return ($this->input->server('REQUEST_METHOD') == 'POST');
    }

    protected function setLoginSession($idperusahaanAtauDeviceID, $username, $namaperusahaan, $registerdeviceid, $ismenu
        , $tableSale, $tableSaleItemDetail, $tableSaleItemDetailIngredients, $nomorperusahaan)
    {
        $this->load->library('session');
        $this->load->model('Userperusahaan');
        $urlFotoAndEmail = $this->Userperusahaan->getUrlFotoAndEmail($idperusahaanAtauDeviceID, $username);
        $urlFoto = $urlFotoAndEmail['UrlFoto'];
        if (!isNotEmpty($urlFoto)) {
            $url = base_url('images/ukuran-foto-cloud-nav.png');
        } else {
            $url = base_url() . '/' . $urlFoto;
        }

        $isExpired = $this->isUserExpired($username, $registerdeviceid, $nomorperusahaan, $idperusahaanAtauDeviceID);
        $authenticatedData = array(
            'id' => $idperusahaanAtauDeviceID,
            'username' => $username,
            'namaperusahaan' => $namaperusahaan,
            'nomorperusahaan' => $nomorperusahaan,
            'registerwithdeviceid' => $registerdeviceid,
            'ismenuperusahaanvisible' => $ismenu,
            'foto' => $url,
            'nama_tabel_sale' => $tableSale,
            'nama_tabel_sale_detail' => $tableSaleItemDetail,
            'nama_tabel_sale_detail_bahan' => $tableSaleItemDetailIngredients,
            'is_expired' => $isExpired
        );

        $this->session->set_userdata($authenticatedData);
    }

    protected function removeLoginSession()
    {
        $this->load->library('session');
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('namaperusahaan');
        $this->session->unset_userdata('nomorperusahaan');
        $this->session->unset_userdata('registerwithdeviceid');
        $this->session->unset_userdata('ismenuperusahaanvisible');
        $this->session->unset_userdata('is_expired');
        redirect(base_url() . 'authentication/loginv2');
    }

    protected function getSaleTables($perusahaanID)
    {
        $this->load->database();
        $queryStr = "SELECT * FROM perusahaantablemapping WHERE PerusahaanAfterNo=
(SELECT MAX(PerusahaanAfterNo) FROM perusahaantablemapping WHERE PerusahaanAfterNo<
(SELECT PerusahaanNo FROM perusahaan WHERE PerusahaanID=" . $this->db->escape($perusahaanID) . ")
)";
        $queryNo = $this->db->query($queryStr);
        $result = $queryNo->result();
        if (count($result) >= 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    protected function getPerusahaanNo($perusahaanID)
    {
        $this->load->database();
        $queryStr = "SELECT PerusahaanNo FROM perusahaan WHERE PerusahaanID=" . $this->db->escape($perusahaanID);
        $queryNo = $this->db->query($queryStr);
        $result = $queryNo->result();
        if (count($result) >= 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    protected function isUserExpired($username, $registerdeviceid, $nomerPerusahaan, $idperusahaan)
    {
        $this->load->model('Userperusahaancabang');
        $this->load->database();
        $cabangs = $this->Userperusahaancabang->getListCabang($username, $idperusahaan);

        $strCabangs = '';
        foreach ($cabangs as $cabang) {
            $strCabangs = $strCabangs . "," . $this->db->escape($cabang->OutletID) . "";
        }
        if (strlen($strCabangs) > 0) {
            $strCabangs = substr($strCabangs, 1);
            $Condition = " o.PerusahaanNo = " . $nomerPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->db->escape($idperusahaan) . "";
        } else {
            $Condition = " o.PerusahaanNo = " . $nomerPerusahaan . " AND o.PerusahaanID = " . $this->db->escape($idperusahaan) . "";
        }

        $queryString = "
            SELECT SUM(TotalOutlet) TotalOutlet, SUM(TotalExpired) TotalExpired
            FROM
            (
                SELECT 1 AS TotalOutlet, CASE WHEN TglExpired<" . $this->db->escape(date('Y-m-d')) . " THEN 1 ELSE 0 END TotalExpired
                FROM options o
                WHERE
                " . $Condition . "
            ) X";


        $query = $this->db->query($queryString);
        $row = $query->row();

        return $row->TotalOutlet === $row->TotalExpired;
    }

}
