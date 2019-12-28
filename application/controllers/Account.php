<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('nuta_helper');
        $this->load->library('encrypt');
    }

    public function index() {
        redirect(preventXSS(base_url() . 'authentication/gate'));
    }

    public function forgot() {
        $p = $this->input->get('p');
        $i = $this->input->get('i');
        $encP = str_replace(array('-', '_', '~'), array('+', '/', '='), $p);
        $encU = str_replace(array('-', '_', '~'), array('+', '/', '='), $i);
        $perusahaan = $this->encrypt->decode($encP);
        $perusahaan = str_replace(' ','',$perusahaan);
        $username = $this->encrypt->decode($encU);
        $this->load->model('Userperusahaan');
        $_d = $this->Userperusahaan->getEmailPasswordByPerusahaanUsername($perusahaan, $username);
        $password = $_d['password'];
        $this->sentEmailPassword($_d['email'], $perusahaan, $username, $password, 'password');
        $this->load->view('account/kirim_password_berhasil', array('email' => $_d['email']));
    }

    public function forgotacc() {
        $email = $this->input->post('email');
        $error = 0;
        $msg = "OK";
        $state = 'firststep';
        $action = $this->input->post('actionbutton');
        $options_perusahaan = array();
        if ($action === 'Kirim' && isNotEmpty($email)) {
            $this->load->model('Userperusahaan');
            $listPerusahan = $this->Userperusahaan->getUserByEmail($email);
            if (count($listPerusahan) <= 0) {
                $error = 1;
                $msg = 'Email yang anda masukkan tidak ada dalam database kami';
            } else if (count($listPerusahan) > 1) {
                $state = 'secondstep';
                foreach ($listPerusahan as $p) {
                    array_push($options_perusahaan, $p->PerusahaanID);
                }
            } else if (count($listPerusahan) == 1) {
                $state = 'finish';
                $perusahaan = $listPerusahan[0]->PerusahaanID;
                $password = $listPerusahan[0]->password;
                $username = $listPerusahan[0]->username;
                $this->sentEmailPassword($email, $perusahaan, $username, $password, 'akun');
            }

        } else if ($action === 'Pilih' && isNotEmpty('perusahaan')) {
            $selectedPerusahaan = $this->input->post('perusahaan');
            $this->load->model('Userperusahaan');
            $_d = $this->Userperusahaan->getUsernamePasswordByPerusahaanEmail($selectedPerusahaan, $email);
            $this->sentEmailPassword($email, $selectedPerusahaan, $_d['username'], $_d['password'], 'akun');
            $state = 'finish';
        }

        $this->load->view('account/lupa_akun', array('email' => $email, 'error' => $error, 'msg' => $msg, 'state' => $state, 'action' => $action
        , 'options_perusahaan' => $options_perusahaan));
    }

    private function sentEmailPassword($email, $perusahaan, $username, $password, $jenislupa) {
        $this->load->library('email');
        $this->email->from('no-reply@nutacloud.com', 'no-reply@nutacloud.com');
        $this->email->to(array($email));


        $message = '';
        $subject = '';
        if ($jenislupa === 'akun') {
            $subject = "Lupa akun nutacloud";
            $message = $this->load->view('mail/mail_lupa_akun', array('subject' => 'Permintaan lupa akun', 'username' => $username, 'password' => $password, 'namaperusahaan' => $perusahaan), true);
        } else {
            $subject = "Lupa password nutacloud";
            $message = $this->load->view('mail/mail_lupa_password', array('subject' => 'Permintaan lupa akun', 'username' => $username, 'password' => $password, 'namaperusahaan' => $perusahaan), true);
        }
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();
    }
}