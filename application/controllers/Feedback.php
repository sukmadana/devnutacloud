<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class feedback extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $response = $this->input->get('response');
        $imgsmall = "";
        $imglarge = "";
        $url = "";
        $greeting = "";
        $deviceid = $this->input->get('i');
        $email = $this->input->get('e');
        $saletransactionid = $this->input->get('s');
        if ($response == "good") {
            $imglarge = base_url('images/feedback/smile.png');
            $imgsmall = base_url('images/feedback/sad.png');
            $url = base_url('feedback?i=' . $deviceid . '&e=' . $email .'&s='.$saletransactionid. '&response=bad');
            $greeting = "Terima kasih!<br/>beritahu kami kepuasan anda.";
        } else if ($response === "bad") {
            $imglarge = base_url('images/feedback/sad.png');
            $imgsmall = base_url('images/feedback/smile.png');
            $url = base_url('feedback?i=' . $deviceid . '&e=' . $email .'&s='.$saletransactionid. '&response=good');
            $greeting = "Mohon maaf<br/>beritahu kami kekecewaan anda.";
        }

        $this->load->view('feedback/feedback', array('response' => $response,
                'imagelarge' => $imglarge, 'imagesmall' => $imgsmall, 'url' => $url
            , 'greeting' => $greeting, 'email' => $email, 'deviceid' => $deviceid,
                'saletransactionid' => $saletransactionid)
        );
    }

    public function postfeedback()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $post_deviceid = $this->input->post('i');
            $post_email = $this->input->post('e');
            $post_saletransactionid = $this->input->post('s');
            $post_respon = $this->input->post('r');
            $deviceid = base64_decode($post_deviceid);
            $email = base64_decode($post_email);
            $response = base64_decode($post_respon);
            $transactionid = base64_decode($post_saletransactionid);
            $subject = $this->input->post('subject');
            $keterangan = $this->input->post('keterangan');
            if(!isset($email) || trim($email)==='')
                $email = "noemail";
            $params = array(
                'Email' => $email,
                'Deviceid' => $deviceid,
                'Respon' => $response,
                'Keterangan' => $keterangan,
                'Sale Transaction ID' => $transactionid

            );
            $this->load->model('Postdata');
            $this->Postdata->set_post_data($params);
            $validate = $this->Postdata->validate();

            if (count($validate) > 0 || count($subject) == 0) {
                $this->load->view('feedback/feedback_error', array('issubjectempty' => count($subject) == 0,
                    'validate' => $validate));
            } else {
                $masterdb=$this->load->database('master',true);
                $masterdb->insert('customerfeedback', array(
                    'OutletID' => $deviceid,
                    'Email' => $email,
                    'Response' => $response,
                    'Description' => $keterangan,
                    'SaleTransactionID' => $transactionid,
                    'IsWaktuChecked' => in_array('Waktu Menunggu', $subject) ? 1 : 0,
                    'IsKualitasChecked' => in_array('Kualitas', $subject) ? 1 : 0,
                    'IsCustomerServiceChecked' => in_array('Customer Service', $subject) ? 1 : 0,
                    'IsLainnyaChecked' => in_array('Lainnya', $subject) ? 1 : 0,


                ));
                $this->load->view('feedback/feedback_success');
            }

        } else {
            redirect(base_url('feedback'));
        }
    }
}