<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChartofAccount extends MY_Controller {
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
        $this->load->model('masteraccount');
    }

    public function view() {
        $list = $this->masteraccount->getAllAccount(getPerusahaanNo(), true);
        $account = $this->rebuildAccountList($list);
        $account_type = $this->masteraccount->accountType();
        
        $data['js_part'] = array(
            'features/js/js_datatable',
            'features/js/js_chartofaccount_view',
        );
        
        $data['account']  = $account;
        $data['account_type'] = $account_type;
        $data['js_chart'] = array();
        $data['page_part'] = 'chartofaccount/chartofaccount_view';
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $this->load->view('main_part', $data);
    }

    public function formdata() {
        $account_type = $this->masteraccount->accountType();
        
        $id = $this->input->get('account_id');
        $mode = isNotEmpty($id) ? 'edit' : 'new';
        $page_title = 'Tambah Akun';
        $account = [];

        if ($mode == 'edit') {
            $page_title = 'Edit Akun';
            $account = $this->masteraccount->getById(getPerusahaanNo(), $id);
        }

        $data['js_part'] = array(
            'features/js/js_datatable',
            'features/js/js_chartofaccount_form',
        );
        
        $data['account_type'] = $account_type;
        $data['account'] = $account;
        $data['page_title'] = $page_title;
        

        $data['js_chart'] = array();
        $data['page_part'] = 'chartofaccount/chartofaccount_form';
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $this->load->view('main_part', $data);
    }

    public function accountType() {
        $get = $this->masteraccount->accountType();
        $list = [];
        $parameter = $this->input->post('parameter');

        foreach ($get as $key => $val) {
            if (in_array($key, $parameter)) {
                $list[$key] = $val;
            }
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode($list);
         }else{
             // do something
         }
    }

    public function accountOtherIncome() {
        $list = $this->masteraccount->accountOtherIncome(getPerusahaanNo());
        if ($this->input->is_ajax_request()) {
            echo json_encode($list);
         }else{
             // do something
         }
    }

    public function accountNonIncome() {
        $list = $this->masteraccount->accountNonIncome(getPerusahaanNo());
        if ($this->input->is_ajax_request()) {
            echo json_encode($list);
         }else{
             // do something
         }
    }

    public function accountCost() {
        $list = $this->masteraccount->accountCost(getPerusahaanNo());
        if ($this->input->is_ajax_request()) {
            echo json_encode($list);
         }else{
             // do something
         }
    }

    public function accountNonCost() {
        $list = $this->masteraccount->accountNonCost(getPerusahaanNo());
        if ($this->input->is_ajax_request()) {
            echo json_encode($list);
         }else{
             // do something
         }
    }

    public function getById() {
        $account_id = $this->input->post('id');
        $perusahaan_no = getPerusahaanNo();

        $result = $this->masteraccount->getById($perusahaan_no, $account_id);

        if ($this->input->is_ajax_request()) {
            echo json_encode($result);
         }else{
             // do something
         }
    }

    public function create() {
        $type = $this->input->post('type');
        $name = $this->input->post('name');
        $code = $this->input->post('code');

        if (is_null($this->input->post('journal_id')) || empty($this->input->post('journal_id'))) {
            $parameter = array(
                'PerusahaanNo' => getPerusahaanNo(),
                'AccountType' => $type,
                'AccountCode' => $code,
                'AccountName' => $name,
                'IsDefault' => 0,
                'TglJamUpdate' => date("Y-m-d H:i:s")
            );
            $result = $this->masteraccount->create($parameter);
        }else{
            $parameter = array(
                'AccountType' => $type,
                'AccountCode' => $code,
                'AccountName' => $name,
            );
            $result = $this->masteraccount->update(getPerusahaanNo(), $this->input->post('journal_id'), $parameter);
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode($result);
        }else{
            // do something
        }
    }

    public function save() {
        $id = $this->input->post('account_id');
        $type = $this->input->post('account_type');
        $name = $this->input->post('account_name');
        $code = $this->input->post('account_code');

        if (!empty($id)) {
            $parameter = array(
                'AccountType' => $type,
                'AccountCode' => $code,
                'AccountName' => $name,
            );
            $result = $this->masteraccount->update(getPerusahaanNo(), $id, $parameter);
        }else{
            $parameter = array(
                'PerusahaanNo' => getPerusahaanNo(),
                'AccountType' => $type,
                'AccountCode' => $code,
                'AccountName' => $name,
                'IsDefault' => 0,
                'TglJamUpdate' => date("Y-m-d H:i:s")
            );
            $result = $this->masteraccount->create($parameter);
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode($result);
        }else{
            redirect('chartofaccount/view');
        }
    }

    public function delete() {
        $account_id = $this->input->post('id');
        $perusahaan_no = getPerusahaanNo();
        
        $result = $this->masteraccount->delete($perusahaan_no, $account_id);

        if ($this->input->is_ajax_request()) {
            echo json_encode($result);
         }else{
             // do something
         }
    }

    private function rebuildAccountList($account_list) {
        $account_type = $this->masteraccount->accountType();
        $result = [];
        foreach ($account_list as $key => $val) {
            $type_id = $val->AccountType;
            $type_name = '';
            
            if (array_key_exists($type_id, $account_type)) $type_name = $account_type[$type_id];

            $result[$key] = $val;
            $result[$key]->AccountTypeName = $type_name;
        }

        return $result;
    }

}