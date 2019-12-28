<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelanggan extends MY_Controller
{

    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();
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
        $this->load->model('Mastercustomer');
    }

    public function daftarpelanggan()
    {

        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet) {
            $selected_outlet = 0;
        }
        
        $data['js_part'] = array(
            'features/js/js_socket', 
            'features/js/js_pelanggan_list', 
            'features/js/js_datatable'
        );
        $data['js_chart'] = array();
        $data['outlets'] = $availableOutlets;
        $data['selected_outlet'] = $selected_outlet;
        $data['page_part'] = 'pelanggan/pelanggan';
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part', $data);
    }

    public function form() {
        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        $selected_outlet = $this->input->get('outlet');
        if (isNotEmpty($selected_outlet)) {
            $this->nutaquery->SetOutlet($selected_outlet);
        } else if(count($availableOutlets)==1) {
            $this->nutaquery->SetOutlet($this->default_outlet_id);
        }

        $id = $this->input->get('id');
        $devno = $this->input->get('devno');

        $mode = isNotEmpty($id) ? 'edit' : 'new';
        $nama = '';
        $email = '';
        $nohp = '';
        $tgllahir = '';
        $alamat = '';
        $note = '';

        if ($mode == 'edit') {
            $availableOutlets = $this->FilterOutletWithSupplier($availableOutlets, $id, $devno);
            if (isNotEmpty($selected_outlet)) {
                $this->nutaquery->setOutlet($selected_outlet);
            } else {
                $this->setDefaultOutletId($availableOutlets);
            }

            $customer = $this->Mastercustomer->getByID($id,$devno, $this->nutaquery->getOutlet());
            $custid = $customer->CustomerID;
            $nama = $customer->CustomerName;
            $email = $customer->CustomerEmail;
            $nohp = $customer->CustomerPhone;
            $tgllahir = $customer->Birthday;
            $alamat = $customer->CustomerAddress;
            $note = $customer->Note;

            $data['custid'] = $custid;
            $data['devno'] = $customer->DeviceNo;
        } else {
            $data['devno'] = 0;
        }

        $data['form']['nama'] = $nama;
        $data['form']['email'] = $email;
        $data['form']['nohp'] = $nohp;
        $data['form']['tgllahir'] = $tgllahir;
        $data['form']['alamat'] = $alamat;
        $data['form']['note'] = $note;
        $data['outlets'] = $availableOutlets;
        $data['page_part'] = 'pelanggan/pelanggan';
        $data['modeform'] = $mode;
        $data['js_part'] = array(
            "features/js/js_ajax_switch",
            "features/js/js_form",
            "features/js/js_form_validation",
            "features/js/js_datatable",
            "features/js/js_form_pelanggan"
            );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['selected_outlet'] = $this->nutaquery->getOutlet();
        $data['page_part'] = 'pelanggan/pelanggan_form';
        $this->load->view('main_part', $data);
    }

    protected function FilterOutletWithSupplier($arrayofoutlet, $customerID, $devno)
    {
        $retval = array();
        foreach ($arrayofoutlet as $k => $outlet) {
            $this->db->where(array('DeviceID' => $k, 'CustomerID' => $customerID, 'DeviceNo' => $devno));
            $query = $this->db->get('mastercustomer');
            $count = $query->num_rows();
            if ($count == 1) {
                $cust = $query->result();
                $retval[$k] = $outlet . '#$%^';
            }

        }
        return $retval;

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
    
    public function tespelanggan2669(){
        
        $this->db->where('DeviceID', 2669);
        $this->db->select('CustomerName AS Nama,CustomerEmail as Email,CustomerPhone as Phone,Birthday,CustomerAddress AS Alamat');
        $query_master_pelanggan = $this->db->get('mastercustomer');
        $result = $query_master_pelanggan->row();
        $result->bar = "a";
//        $result = (array)$result;
//        $result['bar'] = 'a';
//        $result = (object)$result;
        var_dump($result);
    }
    
    public function tespelanggan2669b(){
        
        $this->db->where('DeviceID', 2669);
        $this->db->select('CustomerName AS Nama,CustomerEmail as Email,CustomerPhone as Phone,Birthday,CustomerAddress AS Alamat');
        $query_master_pelanggan = $this->db->get('mastercustomer');
        $result = $query_master_pelanggan->row();
        $result = (array)$result;
        $result['bar'] = 'a';
        $result = (object)$result;
        var_dump($result);
    }

    private function sqlOrder($order) {
        if (!is_array($order))
            return "";

        $columns = [
            'CustomerID',
            'CustomerName',
            'CustomerEmail',
            'CustomerPhone',
            'Birthday',
            'CustomerAddress',
            'Varian',
            'DeviceID',
            'PerusahaanNo',
            'DeviceNo'
        ];
        $dirs = array("asc" => "ASC", "ASC" => "ASC", "desc" => "DESC", "DESC" => "DESC");
        
        $data = array();
        foreach ($order as $o) {
            array_push($data, $columns[$o["column"]] . " " . $dirs[$o["dir"]]);
        }
        
        return "ORDER BY " . implode(",", $data);
    }

    public function ajaxgetpelanggan_v2() {
        $selected_outlet = (int)$this->input->post('outlet', TRUE);
        $draw = $this->input->post('draw', TRUE);
        $start = (int)$this->input->post('start', TRUE);
        $length = (int)$this->input->post('length', TRUE);
        $order = $this->input->post('order', TRUE);
        $search = $this->input->post('search', TRUE);

        $sql = "SELECT COUNT(*) AS recordsTotal FROM mastercustomer WHERE DeviceID = ?";
        $q = $this->db->query($sql, array($selected_outlet));
        $recordsTotal = (int)$q->row()->recordsTotal;

        if ($length <= 0)
            $length = $recordsTotal;

        $sql2 = "
            SELECT
                CustomerID,
                CustomerName,
                CustomerEmail,
                CustomerPhone,
                Birthday,
                CustomerAddress,
                Varian,
                DeviceID,
                PerusahaanNo,
                DeviceNo
            FROM mastercustomer 
            WHERE DeviceID = ? 
                AND (
                    CustomerName LIKE ? 
                    OR CustomerEmail LIKE ?
                    OR CustomerPhone LIKE ?
                    OR CustomerAddress LIKE ?
                )
            " . $this->sqlOrder($order) . "
            LIMIT ?,?
        ";
        $q2 = $this->db->query($sql2, array(
            $selected_outlet, 
            $search["value"] . "%", 
            $search["value"] . "%", 
            $search["value"] . "%", 
            $search["value"] . "%", 
            $start, 
            $length
        ));
        $data = $q2->result();
        
        $sql3 = "
            SELECT
                COUNT(*) AS recordFiltered
            FROM mastercustomer 
            WHERE DeviceID = ? 
                AND (
                    CustomerName LIKE ? 
                    OR CustomerEmail LIKE ?
                    OR CustomerPhone LIKE ?
                    OR CustomerAddress LIKE ?
                )
            " . $this->sqlOrder($order) . "
        ";
        $q3 = $this->db->query($sql3, array(
            $selected_outlet, 
            $search["value"] . "%", 
            $search["value"] . "%", 
            $search["value"] . "%", 
            $search["value"] . "%"
        ));

        $recordFiltered = $q3->row()->recordFiltered;

        $r = new stdClass;
        $r->draw = $draw;
        $r->recordsTotal = (int)$recordsTotal;
        $r->recordsFiltered = (int)$recordFiltered;
        $r->data = $data;
        $r->sql = $this->sqlOrder($order);

        echo json_encode($r);
    }
}
