<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pajak extends MY_Controller
{

    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();
        $this->load->library('NutaQuery');
        $this->load->model('Userperusahaancabang');
        $this->load->model('Perusahaanmodel');
        $this->load->model('Mastertax');
        $this->load->model('Kategori');
        $this->load->model('Masteritem');
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
        //}
    }

    public function index()
    {
        $availableOutlets = $this->GetOutletTanpaSemua();
        if (count($availableOutlets) > 1) {
            $this->nutaquery->SetOutlet(-999);
        } else {
            $this->setDefaultOutletId($availableOutlets);
        }
        $selected_outlet = $this->input->get('outlet') ? $this->input->get('outlet') : 0;

        $data['outlets'] = $availableOutlets;
        $data['selected_outlet'] = $selected_outlet;
        $data['pajak'] = $this->Mastertax->getAllTaxByOutlet($selected_outlet);
        $data['kategori'] = $this->Kategori->getKategoriByOutlet($selected_outlet);
        $data['item'] = $this->Masteritem->getMasterItemByOutlet($selected_outlet);
        $data['page_part'] = 'pajak/list_pajak';
        $data['js_part'] = array(
            'features/js/js_socket',
            'features/js/js_form',
            'features/js/js_form_validation',
            'features/js/js_datatable',
            'pajak/js/js_pajak'
        );
        $data['js_chart'] = array();
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['menu'] = "produk";
        $this->load->view('main_part', $data);
    }

    // AJAX
    public function ajaxpajak()
    {
        $Outlet = $this->input->get_post('Outlet', true);
        $draw = $this->input->post('draw', true);
        $start = (int) $this->input->post('start', true);
        $length = (int) $this->input->post('length', true);
        $order = $this->input->post('order', true);
        $search = $this->input->post('search', true);

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'draw' => $draw,
            'start' => $start,
            'length' => $length,
            'order' => $order,
            'search' => $search,
        );
        $result = $this->Mastertax->getDatatablespajak($params, $this->visibilityMenu);

        echo json_encode($result);
    }

    public function ajax_get_detail_pajak()
    {
        $Outlet = $this->input->get_post('Outlet');
        $pajakID = $this->input->get_post('pajakID');

        $where = array(
            getPerusahaanNo(), $Outlet, $pajakID
        );

        $result = $this->Mastertax->getDetailMastertax($where);
        echo json_encode($result);
    }

    public function ajax_update_tax()
    {

        $Outlet = $this->input->get_post('Outlet');
        $TaxID = $this->input->get_post('TaxID');
        $TaxName = $this->input->get_post('TaxName');
        $TaxPercent = $this->input->get_post('TaxPercent');
        $PriceIncludeTax = $this->input->get_post('PriceIncludeTax');
        $ApplyToAllItems = $this->input->get_post('ApplyToAllItems');
        $ApplicableCategories = $this->input->post('ApplicableCategories');
        $ApplicableItems = $this->input->post('ApplicableItems');

        if ($ApplyToAllItems == 'on') {
            $ApplyToAllItems = 1;
            $ApplicableCategories = " ";
            $ApplicableItems = " ";
        } else {
            $ApplyToAllItems = 0;
            if (!empty($ApplicableItems)) {
                $ApplicableItems = implode(",",$ApplicableItems);
            } else {
                $ApplicableItems = " ";
            }

            if (!empty($ApplicableCategories)) {
                $ApplicableCategories = implode(",",$ApplicableCategories);
            } else {
                $ApplicableCategories = " ";
            }
        }

        $TaxPercent = str_replace(',', '.', $TaxPercent);

        $Mastertax = $this->Mastertax->getDetailMasterTax(array(getPerusahaanNo(), $Outlet, $TaxID));
        $this->load->model('Options');
        $Options = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $Outlet));

        $params = array(
            'TaxName' => $TaxName,
            'TaxPercent' => $TaxPercent,
            'PriceIncludeTax' => $PriceIncludeTax,
            'TaxPercent' => $TaxPercent,
            'ApplyToAllItems' => $ApplyToAllItems,
            'RowVersion' => 1,
            'CreatedVersionCode' => $Options->CreatedVersionCode,
            'EditedVersionCode' => $Options->EditedVersionCode,
            'Varian' => $Options->Varian,
            'ApplicableItems' => $ApplicableItems,
            'ApplicableCategories' => $ApplicableCategories
        );

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'TaxID' => $TaxID
        );

        if ($this->Mastertax->updateMasterTax($params, $where)) {
            $response = array(
                'status' => 200,
                'message' => 'Pajak Berhasil Disimpan'
            );
            echo json_encode($response);
            exit();
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Pajak Gagal Disimpan'
            );
            echo json_encode($response);
            exit();
        }
    }

    public function ajax_insert_tax()
    {

        $Outlet = $this->input->post('Outlet');
        $TaxName = $this->input->post('TaxName');
        $TaxPercent = $this->input->post('TaxPercent');
        $PriceIncludeTax = $this->input->post('PriceIncludeTax');
        $ApplyToAllItems = $this->input->post('ApplyToAllItems');
        $ApplicableCategories = $this->input->post('ApplicableCategories');
        $ApplicableItems = $this->input->post('ApplicableItems');

        if ($ApplyToAllItems == 'on') {
            $ApplyToAllItems = 1;
            $ApplicableCategories = " ";
            $ApplicableItems = " ";
        } else {
            $ApplyToAllItems = 0;
            if (!empty($ApplicableItems)) {
                $ApplicableItems = implode(",",$ApplicableItems);
            } else {
                $ApplicableItems = " ";
            }

            if (!empty($ApplicableCategories)) {
                $ApplicableCategories = implode(",",$ApplicableCategories);
            } else {
                $ApplicableCategories = " ";
            }
        }

        $TaxPercent = str_replace(',', '.', $TaxPercent);

        $this->load->model('Options');
        $Options = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $Outlet));

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet
        );

        $TaxID = $this->Mastertax->getNewTaxID($where);

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'TaxID' => $TaxID,
            'TaxName' => $TaxName,
            'TaxPercent' => $TaxPercent,
            'PriceIncludeTax' => $PriceIncludeTax,
            'TaxPercent' => $TaxPercent,
            'ApplyToAllItems' => $ApplyToAllItems,
            'RowVersion' => 1,
            'CreatedVersionCode' => $Options->CreatedVersionCode,
            'EditedVersionCode' => $Options->EditedVersionCode,
            'Varian' => $Options->Varian,
            'ApplicableItems' => $ApplicableItems,
            'ApplicableCategories' => $ApplicableCategories,

        );

        if ($this->Mastertax->insertMasterTax($params)) {
            $response = array(
                'status' => 200,
                'message' => 'Pajak Berhasil Disimpan'
            );
            echo json_encode($response);
            exit();
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Pajak Gagal Disimpan'
            );
            echo json_encode($response);
            exit();
        }
    }

    public function ajax_delete_tax()
    {
        $Outlet = $this->input->get_post('Outlet');
        $pajakID = $this->input->get_post('TaxID');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'TaxID' => $pajakID
        );

        if ($this->Mastertax->deleteMasterTax($where)) {
            $response = array(
                'status' => 200,
                'message' => 'Pajak Berhasil Dihapus'
            );
            echo json_encode($response);
            exit();
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Pajak Gagal Dihapus'
            );
            echo json_encode($response);
            exit();
        }
    }
}
