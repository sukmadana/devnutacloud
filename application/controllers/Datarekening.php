<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DataRekening extends MY_Controller
{
    function __construct ()
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
        $this->load->model('Datarekeningmodel');
    }

    public function index ()
    {
        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet) {
            # random number untuk memastikan data kosong jika tidak ada selected_outlet
            $selected_outlet = '-123123';
        }
        $query_datarekening = $this->Datarekeningmodel->datarekening($selected_outlet);
        $query = $this->db->query($query_datarekening);
        $result = $query->result();
        $fields = $query->field_data();

        $data['outlets'] = $availableOutlets;
        $data['js_part'] = array(
            'features/js/js_grid_item',
            'features/js/js_datatable',
            'features/js/js_datarekening'
        );
        $data['js_chart'] = array();
        $data['outlets'] = $availableOutlets;
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['selected_outlet'] = $selected_outlet;
        $data['page_part'] = 'datarekening/datarekening';
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['menu'] = 'uang';
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->model('Options');
        $options = $this->Options->get_by_devid($selected_outlet);
        $data['options'] = $options;
        $this->load->view('main_part', $data);
    }
    
    public function form ()
    {
        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        $selected_outlet = $this->input->get('outlet');
        if (isNotEmpty($selected_outlet)) {
            $this->nutaquery->SetOutlet($selected_outlet);
        } else if(count($availableOutlets)==1) {
            $this->nutaquery->SetOutlet($this->default_outlet_id);
        }
        $accountID = $this->input->get('id');
        $devno = $this->input->get('devno');

        $mode = isNotEmpty($accountID) ? 'edit' : 'new';
        $bankName = '';
        $accountNumber = '';
        $accountName = '';

        if ($mode == 'edit') {
            $availableOutlets = $this->FilterOutletWithSupplier($availableOutlets, $accountID, $devno);
            if (isNotEmpty($selected_outlet)) {
                $this->nutaquery->setOutlet($selected_outlet);
            } else {
                $this->setDefaultOutletId($availableOutlets);
            }

            $datarekening = $this->Datarekeningmodel->getByAccountID($accountID,$devno, $this->nutaquery->getOutlet());
            $accountID = $datarekening->AccountID;
            $bankName = $datarekening->BankName;
            $accountNumber = $datarekening->AccountNumber;
            $accountName = $datarekening->AccountName;

            $data['accountID'] = $accountID;
            $data['devno'] = $datarekening->DeviceNo;
        } else {
            $data['devno'] = 0;
        }
        $data['form']['accountID'] = $accountID;
        $data['form']['bankName'] = $bankName;
        $data['form']['accountNumber'] = $accountNumber;
        $data['form']['accountName'] = $accountName;
        $data['outlets'] = $availableOutlets;
        $data['page_part'] = 'datarekening/datarekening';
        $data['modeform'] = $mode;
        $data['accountID'] = $accountID;
        $data['js_part'] = array(
            "features/js/js_ajax_switch",
            "features/js/js_form",
            "features/js/js_form_validation",
            "features/js/js_datatable",
            "features/js/js_form_datarekening"
            );
        $data['js_chart'] = array();
        $data['datagrid'] = array('fields' => array('Bank', 'No.Rekening', 'Atas Nama'));
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['selected_outlet'] = $this->nutaquery->getOutlet();
        $data['page_part'] = 'datarekening/datarekening_form';
        $this->load->view('main_part', $data);
    }
    
    protected function FilterOutletWithSupplier($arrayofoutlet, $accountID, $devno)
    {
        $retval = array();
        foreach ($arrayofoutlet as $k => $outlet) {
            $this->db->where(array('DeviceID' => $k, 'AccountID' => $accountID, 'DeviceNo' => $devno));
            $query = $this->db->get('mastercashbankaccount');
            $count = $query->num_rows();
            if ($count == 1) {
                $datarekening = $query->result();
                $retval[$k] = $outlet . '#$%^' . $datarekening[0]->AccountID;
            }

        }
        return $retval;

    }

    public function destroy ()
    {
        if ($this->input->server('REQUEST_METHOD') != 'POST')
            throw new Exception("Error Processing Request", 405);

        $this->runDeleteValidation();
        
        $this->Datarekeningmodel->deleting($this->getWhere());

        return redirect('datarekening/index?outlet='.$this->getWhere()['DeviceID']);
    }

    protected function getWhere()
    {
        return [
            'AccountID' => $this->input->post('id'),
            'DeviceNo' => $this->input->post('devno'),
            'DeviceID' => $this->input->post('outlet')
        ];
    }

    protected function runDeleteValidation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('outlet', 'Outlet', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');
        ($this->form_validation->run()) ?: redirect('datarekening/index?outlet='.$this->input->post('outlet'));

        if (!array_key_exists($this->input->post('outlet'), $this->GetOutletTanpaSemua())) {
            redirect('/datarekening/index');
        }

    }
}