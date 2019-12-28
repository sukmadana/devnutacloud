<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends MY_Controller
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
        $this->load->model('Suppliermodel');
	}

	public function index ()
	{
		$availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet) {
            $selected_outlet = 0;
        }
     	$query_supplier = $this->Suppliermodel->supplier($selected_outlet);
        $query = $this->db->query($query_supplier);
        $result = $query->result();
        $fields = $query->field_data();

        $data['outlets'] = $availableOutlets;
     	$data['js_part'] = array(
            'features/js/js_grid_item',
            'features/js/js_datatable',
            'features/js/js_supplier');
        $data['js_chart'] = array();
        $data['outlets'] = $availableOutlets;
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['selected_outlet'] = $selected_outlet;
        $data['page_part'] = 'supplier/supplier';
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['menu'] = 'stok';
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
        $namasupp = $this->input->get('id');
        $devno = $this->input->get('devno');

        $mode = isNotEmpty($namasupp) ? 'edit' : 'new';
        $nama = '';
        $alamat = '';
        $telepon = '';
        $email = '';
        $catatan = '';

        if ($mode == 'edit') {
            $availableOutlets = $this->FilterOutletWithSupplier($availableOutlets, $namasupp,$devno);
            if (isNotEmpty($selected_outlet)) {
                $this->nutaquery->setOutlet($selected_outlet);
            } else {
                $this->setDefaultOutletId($availableOutlets);
            }

            $supplier = $this->Suppliermodel->getByName($namasupp,$devno, $this->nutaquery->getOutlet());
            $suppid = $supplier->SupplierID;
            $nama = $supplier->SupplierName;
            $alamat = $supplier->SupplierAddress;
            $telepon = $supplier->SupplierPhone;
            $email = $supplier->SupplierEmail;
            $catatan = $supplier->Note;

            $data['suppid'] = $suppid;
            $data['devno'] = $supplier->DeviceNo;
        } else {
            $data['devno'] = 0;
        }
        $data['form']['nama'] = $nama;
        $data['form']['alamat'] = $alamat;
        $data['form']['telepon'] = $telepon;
        $data['form']['email'] = $email;
        $data['form']['catatan'] = $catatan;
        $data['outlets'] = $availableOutlets;
        $data['page_part'] = 'supplier/supplier';
        $data['modeform'] = $mode;
        $data['id_supp'] = $namasupp;
        $data['js_part'] = array(
            "features/js/js_ajax_switch",
            "features/js/js_form",
            "features/js/js_form_validation",
            "features/js/js_datatable",
            "features/js/js_form_supplier"
            );
        $data['js_chart'] = array();
        $data['datagrid'] = array('fields' => array('Nama', 'Alamat', 'Telepon', 'Email', 'Catatan'));
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['selected_outlet'] = $this->nutaquery->getOutlet();
        $data['page_part'] = 'supplier/supplier_form';
        $this->load->view('main_part', $data);
	}

    protected function FilterOutletWithSupplier($arrayofoutlet, $nama, $devno)
    {
        $retval = array();
        foreach ($arrayofoutlet as $k => $outlet) {
            $this->db->where(array('DeviceID' => $k, 'SupplierID' => $nama, 'DeviceNo' => $devno));
            $query = $this->db->get('mastersupplier');
            $count = $query->num_rows();
            if ($count == 1) {
                $supp = $query->result();
                $retval[$k] = $outlet . '#$%^' . $supp[0]->SupplierID;
            }

        }
        return $retval;

    }

    public function destroy ()
    {
        if ($this->input->server('REQUEST_METHOD') != 'POST')
            throw new Exception("Error Processing Request", 405);

        $this->runDeleteValidation();
        
        $this->Suppliermodel->deleting($this->getWhere());

        return redirect('supplier/index?outlet='.$this->getWhere()['DeviceID']);
    }

    protected function getWhere()
    {
        return [
            'SupplierID' => $this->input->post('id'),
            'DeviceNo' => $this->input->post('devno'),
            'DeviceID' => $this->input->post('outlet')
        ];
    }

    protected function runDeleteValidation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('outlet', 'Outlet', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');
        ($this->form_validation->run()) ?: redirect('supplier/index?outlet='.$this->input->post('outlet'));

        if (!array_key_exists($this->input->post('outlet'), $this->GetOutletTanpaSemua())) {
            redirect('/supplier/index');
        }

    }
}