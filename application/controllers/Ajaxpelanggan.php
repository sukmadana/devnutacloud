<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajaxpelanggan extends MY_Controller
{

    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();
        $this->load->library('NutaQuery');
        $this->load->model('Userperusahaancabang');
        $this->load->model('Perusahaanmodel');
        $devid = getLoggedInUserID();
        $result = $this->Perusahaanmodel->get_perusahaanno_by_devid($devid);
        $noperusahaan = 1;
        if (count($result) >= 0) {
            $noperusahaan = $result[0]->PerusahaanNo;
        }
        $this->nutaquery->setDeviceID(getLoggedInRegisterWithDeviceID());
        $this->DevIDAtauIDPerusahaan = $devid;
        $this->nutaquery->setPerusahaaanID($this->DevIDAtauIDPerusahaan, $noperusahaan);
        $cabangs = $this->Userperusahaancabang->getListCabang(getLoggedInUsername(), $this->DevIDAtauIDPerusahaan);
        $this->nutaquery->setCabangs($cabangs);
        $this->load->library('CurrencyFormatter');
        $this->load->helper('nuta_helper');
    }


    public function ajaxgetpelanggan()
    {
        $selected_outlet = $this->input->get('outlet');
        if (isNotEmpty($selected_outlet)) {
            $this->nutaquery->SetOutlet($selected_outlet);
        }
        $this->db->where('DeviceID', $this->nutaquery->getOutlet());
        $this->db->select('CustomerName AS Nama,CustomerEmail as Email,CustomerPhone as Phone,Birthday,CustomerAddress AS Alamat');
        $query_master_pelanggan = $this->db->get('mastercustomer');
        $result = $query_master_pelanggan->result();
        $fields = $query_master_pelanggan->field_data();
        $json_fields = array();
        foreach ($fields as $f) {
            array_push($json_fields, $f->name);
        }
        $data = array('fields' => json_encode($json_fields), 'result' => json_encode($result));
        header('Content-Type: application/json');
        echo json_encode($data);


    }

    public function savesingleoutlet()
    {
        $outlets = $this->input->post('outlets');//dari angular formatnya: 350,306,307
        $old_nama = $this->input->post('old_nama');
        $nama = $this->input->post('nama');
        $alamat = $this->input->post('alamat');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $note = $this->input->post('note');
        $birthday = $this->input->post('birthday');
        $this->load->model('Mastercustomer');
        $status = false;
        $exploded_outlets = explode(',', $outlets);
        $eror_message = $this->Mastercustomer->NO_ERROR_MESSAGE;
        if (isNotEmpty($old_nama)) {
            $eror_message = $this->Mastercustomer->update($old_nama, $nama, $alamat, $phone, $email, $note, $birthday, $exploded_outlets[0]);
        } else {
            $eror_message = $this->Mastercustomer->create($nama, $alamat, $phone, $email, $note, $birthday, $exploded_outlets[0]);
        }
        if ($eror_message === $this->Mastercustomer->NO_ERROR_MESSAGE) {
            $status = true;
        }

        header('Content-Type: application/json');
        echo json_encode(array('status' => $status, 'message' => $eror_message));
    }

    public function deletebyname()
    {
        $outlets = $this->input->post('outlets');//dari agular formatnya: 350,306,307
        $nama = $this->input->post('nama');
        $this->load->model('Mastercustomer');
        $status = false;
        $exploded_outlets = explode(',', $outlets);
        $eror_message = $this->Mastercustomer->deleteByName($nama, $exploded_outlets[0]);
        if ($eror_message === $this->Mastercustomer->NO_ERROR_MESSAGE) {
            $status = true;
        }

        header('Content-Type: application/json');
        echo json_encode(array('status' => $status, 'message' => $eror_message));
    }


}
