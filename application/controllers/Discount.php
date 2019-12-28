<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Discount extends MY_Controller
{

    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();
        $this->load->library('NutaQuery');
        $this->load->model('Userperusahaancabang');
        $this->load->model('Perusahaanmodel');
        $this->load->model('Masterdiscount');
        $devid = getLoggedInUserID();
        $result = $this->Perusahaanmodel->get_perusahaanno_by_devid($devid);
        // $result = $queryNo->result();
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
        $this->load->model('masteritem');
        if (count($availableOutlets) > 1) {
            $this->nutaquery->SetOutlet(-999);
        } else {
            $this->setDefaultOutletId($availableOutlets);
        }
        $selected_outlet = $this->input->get('outlet');
        if (isNotEmpty($selected_outlet)) {
            $this->nutaquery->SetOutlet($selected_outlet);
        } else if (count($availableOutlets) == 1) {
            $this->nutaquery->SetOutlet($this->default_outlet_id);
        }

        $outletids = array();
        foreach ($availableOutlets as $k => $v) {
            array_push($outletids, $k);
        }
        $data['outletids'] = $outletids;
        $data['outlets'] = $availableOutlets;
        $selected_outlet = $this->nutaquery->getOutlet();
        $data['selected_outlet'] = $selected_outlet;
        $data['page_part'] = 'discount/index';
        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_form",
            'features/js/js_form_validation',
            'discount/js_discount',
            'features/js/js_datatable',
        );

        // Get data
        $where = array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $selected_outlet);
        $data['totalDiscount'] = $this->Masterdiscount->getTotalDiscount($where);

        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['menu'] = "produk";
        $data['title'] = "Nuta Cloud - Items - Diskon";
        $this->load->view('main_part', $data);
    }

    // AJAX
    public function ajaxdiscount()
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
        $result = $this->Masterdiscount->getDatatablesDiscount($params, $this->visibilityMenu);

        echo json_encode($result);
    }

    public function ajax_get_detail_discount()
    {
        $Outlet = $this->input->get_post('Outlet');
        $DiscountID = $this->input->get_post('DiscountID');
        $DeviceNo = $this->input->get_post('DeviceNo');

        $where = array(
            getPerusahaanNo(), $Outlet, $DiscountID, $DeviceNo
        );

        $result = $this->Masterdiscount->getDetailMasterDiscount($where);
        echo json_encode($result);
    }

    public function ajax_update_discount()
    {

        $Outlet = $this->input->get_post('Outlet');
        $DiscountID = $this->input->get_post('DiscountID');
        $DeviceNo = $this->input->get_post('DeviceNo');
        $DiscountName = $this->input->get_post('DiscountName');
        $Discount = $this->input->get_post('Discount');
        $Percent = $this->input->get_post('Percent');

        $Discount = str_replace('%', '', $Discount);
        $Discount = str_replace('Rp', '', $Discount);
        $Discount = str_replace(' ', '', $Discount);
        $Discount = str_replace('.', '', $Discount);
        $Discount = str_replace(',', '.', $Discount);
        $Discount = $Percent == 'yes' ? $Discount . '%' : $Discount;

        $MasterDiscount = $this->Masterdiscount->getDetailMasterDiscount(array(getPerusahaanNo(), $Outlet, $DiscountID, $DeviceNo));

        $params = array(
            'DiscountName' => $DiscountName,
            'Discount' => $Discount,
            'RowVersion' => ($MasterDiscount['RowVersion'] + 1)
        );

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'DiscountID' => $DiscountID,
            'DeviceNo' => $DeviceNo
        );

        if ($this->Masterdiscount->updateMasterDiscount($params, $where)) {
            $response = array(
                'status' => 200,
                'message' => 'Diskon Berhasil Disimpan'
            );
            echo json_encode($response);
            exit();
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Diskon Gagal Disimpan'
            );
            echo json_encode($response);
            exit();
        }
    }

    public function ajax_insert_discount()
    {

        $Outlet = $this->input->get_post('Outlet');
        $DiscountName = $this->input->get_post('DiscountName');
        $Discount = $this->input->get_post('Discount');
        $Percent = $this->input->get_post('Percent');

        $Discount = str_replace('%', '', $Discount);
        $Discount = str_replace('Rp', '', $Discount);
        $Discount = str_replace(' ', '', $Discount);
        $Discount = str_replace('.', '', $Discount);
        $Discount = str_replace(',', '.', $Discount);
        $Discount = $Percent == 'yes' ? $Discount . '%' : $Discount;

        $this->load->model('Options');
        $Options = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $Outlet));

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet
        );

        $DiscountID = $this->Masterdiscount->getNewDiscountID($where);

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'DeviceNo' => 0,
            'DiscountID' => $DiscountID,
            'DiscountName' => $DiscountName,
            'Discount' => $Discount,
            'RowVersion' => 1,
            'CreatedVersionCode' => $Options->CreatedVersionCode,
            'EditedVersionCode' => $Options->EditedVersionCode,
            'Varian' => $Options->Varian,

        );

        if ($this->Masterdiscount->insertMasterDiscount($params)) {
            $response = array(
                'status' => 200,
                'message' => 'Diskon Berhasil Disimpan'
            );
            echo json_encode($response);
            exit();
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Diskon Gagal Disimpan'
            );
            echo json_encode($response);
            exit();
        }
    }

    public function ajax_delete_discount()
    {
        $Outlet = $this->input->get_post('Outlet');
        $DiscountID = $this->input->get_post('DiscountID');
        $DeviceNo = $this->input->get_post('DeviceNo');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'DiscountID' => $DiscountID,
            'DeviceNo' => $DeviceNo
        );

        if ($this->Masterdiscount->deleteMasterDiscount($where)) {
            $response = array(
                'status' => 200,
                'message' => 'Diskon Berhasil Dihapus'
            );
            echo json_encode($response);
            exit();
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Diskon Gagal Dihapus'
            );
            echo json_encode($response);
            exit();
        }
    }
}
