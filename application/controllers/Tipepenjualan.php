<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tipepenjualan extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('NutaQuery');
        $this->load->model('Userperusahaancabang');
        $this->load->model('Perusahaanmodel');
        $this->load->model('Masteropsimakan');
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

    public function index()
    {
        $availableOutlets = $this->GetOutletTanpaSemua();
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
        $data['page_part'] = 'tipepenjualan/index';
        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_form",
            "features/js/js_form_validation",
            'tipepenjualan/js_tipepenjualan',
            'features/js/js_datatable'
        );
        $data['js_chart'] = array();
        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $selected_outlet,
        );
        $data['totalOpsiMakan'] = $this->Masteropsimakan->getTotalOpsiMakan($params);
        $data['rs_opsimakan'] = $this->Masteropsimakan->getOpsiMakan($params);
        $data['rs_account'] = $this->Masteropsimakan->getCashBankAccountPerusahaan($params);
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();

        $this->load->model('Options');
        $Options = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $selected_outlet));
        $data['Options'] = $Options;
        $data['SharedRevenueSt'] = $Options->CreatedVersionCode >= 319 || $Options->EditedVersionCode >= 319 ? 'true' : 'false';
        // $data['SharedRevenueSt'] = 'true';
        $data['menu'] = "produk";
        $data['title'] = "Nuta Cloud - Items - Tipe Penjualan";
        $this->load->view('main_part', $data);
    }

    public function ajax_validation_exist()
    {
        $Outlet = $this->input->get_post('Outlet');
        $field = $this->input->get_post('field');
        $value = $this->input->get_post('value');
        $id = $this->input->get_post('id');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            $field => $value
        );

        if ($id) {
            $where['OpsiMakanID <> '] = $id;
        }

        $result = $this->Masteropsimakan->isExistField($where);

        echo json_encode(array('valid' => !$result));
    }

    public function ajax_insert_tipepenjualan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $NamaOpsiMakan = $this->input->get_post('NamaOpsiMakan');
        $OjekOnline = $this->input->get_post('OjekOnline');
        $Account = $this->input->get_post('Account');
        $MarkupPersen = $this->input->get_post('MarkupPersen');
        $ShareRevenue = $this->input->get_post('ShareRevenue');
        $MarkupRounding = $this->input->get_post('MarkupRounding');
        $MarkupRoundingType = $this->input->get_post('MarkupRoundingType');
        $MarkupRoundingValue = $this->input->get_post('MarkupRoundingValue');

        $OjekOnline = $OjekOnline == 'on' ? 1 : 0;
        $MarkupRounding = $MarkupRounding == 'on' ? 1 : 0;

        $MarkupPersen = str_replace('%', '', $MarkupPersen);
        $MarkupPersen = str_replace(' ', '', $MarkupPersen);
        $MarkupPersen = str_replace('.', '', $MarkupPersen);
        $MarkupPersen = str_replace(',', '.', $MarkupPersen);

        $ShareRevenue = str_replace('%', '', $ShareRevenue);
        $ShareRevenue = str_replace(' ', '', $ShareRevenue);
        $ShareRevenue = str_replace('.', '', $ShareRevenue);
        $ShareRevenue = str_replace(',', '.', $ShareRevenue);

        $this->load->model('Options');
        $Options = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $Outlet));

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
        );

        $OpsiMakanID = $this->Masteropsimakan->getNewOpsiMakanID($where);

        $params = array(
            'OpsiMakanID' => $OpsiMakanID,
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'DeviceNo' => 0,
            'NamaOpsiMakan' => $NamaOpsiMakan,
            'TipeOpsiMakan' => 3,
            'OjekOnline' => $OjekOnline,
            'MarkupPersen' => $MarkupPersen,
            'ShareRevenue' => !$ShareRevenue ? 0 : $ShareRevenue,
            'IsDetailsSaved' => 1,
            'IsActive' => 1,
            'AccountID' => 0,
            'AccountDeviceNo' => 0,
            'CreatedBy' => getLoggedInUsername(),
            'CreatedDate' => date('Y-m-d'),
            'CreatedTime' => date('H:i:s'),
            'CreatedVersionCode' => $Options->CreatedVersionCode,
            'EditedVersionCode' => $Options->EditedVersionCode,
            'Varian' => $Options->Varian,
        );

        if ($MarkupRounding == 1) {
            $params['MarkupRounding'] = $MarkupRoundingValue . '#' . $MarkupRoundingType;
        }

        if ($OjekOnline == 1) {
            $expAccount = explode('#', $Account);
            $params['AccountID'] = intval($expAccount[0]);
            $params['AccountDeviceNo'] = intval($expAccount[1]);
        }

        $insert = $this->Masteropsimakan->insertOpsiMakan($params);

        if ($insert == true) {
            $response = array(
                'status' => 200,
                'message' => 'Tipe Penjualan Berhasil Disimpan'
            );
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Tipe Penjualan Gagal Disimpan'
            );
        }

        echo json_encode($response);
        exit();
    }

    public function ajax_update_tipepenjualan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $OpsiMakanID = $this->input->get_post('OpsiMakanID');
        $DeviceNo = $this->input->get_post('DeviceNo');
        $NamaOpsiMakan = $this->input->get_post('NamaOpsiMakan');
        $OjekOnline = $this->input->get_post('OjekOnline');
        $Account = $this->input->get_post('Account');
        $MarkupPersen = $this->input->get_post('MarkupPersen');
        $ShareRevenue = $this->input->get_post('ShareRevenue');
        $MarkupRounding = $this->input->get_post('MarkupRounding');
        $MarkupRoundingType = $this->input->get_post('MarkupRoundingType');
        $MarkupRoundingValue = $this->input->get_post('MarkupRoundingValue');

        $OjekOnline = $OjekOnline == 'on' ? 1 : 0;
        $MarkupRounding = $MarkupRounding == 'on' ? 1 : 0;

        $MarkupPersen = str_replace('%', '', $MarkupPersen);
        $MarkupPersen = str_replace(' ', '', $MarkupPersen);
        $MarkupPersen = str_replace('.', '', $MarkupPersen);
        $MarkupPersen = str_replace(',', '.', $MarkupPersen);

        $ShareRevenue = str_replace('%', '', $ShareRevenue);
        $ShareRevenue = str_replace(' ', '', $ShareRevenue);
        $ShareRevenue = str_replace('.', '', $ShareRevenue);
        $ShareRevenue = str_replace(',', '.', $ShareRevenue);

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'OpsiMakanID' => $OpsiMakanID,
            'DeviceNo' => $DeviceNo
        );

        $OpsiMakan = $this->Masteropsimakan->getDetailOpsiMakan($where);

        $params = array(
            'NamaOpsiMakan' => $NamaOpsiMakan,
            'OjekOnline' => $OjekOnline,
            'MarkupPersen' => $MarkupPersen,
            'ShareRevenue' => $ShareRevenue,
            'IsDetailsSaved' => 1,
            'EditedBy' => getLoggedInUsername(),
            'EditedDate' => date('Y-m-d'),
            'EditedTime' => date('H:i:s'),
            'RowVersion' => (intval($OpsiMakan['RowVersion']) + 1)
        );

        if ($MarkupRounding == 1) {
            $params['MarkupRounding'] = $MarkupRoundingValue . '#' . $MarkupRoundingType;
        } else {
            $params['MarkupRounding'] = '';
        }

        if ($OjekOnline == 1) {
            $expAccount = explode('#', $Account);
            $params['AccountID'] = intval($expAccount[0]);
            $params['AccountDeviceNo'] = intval($expAccount[1]);
        } else {
            $params['AccountID'] = 0;
            $params['AccountDeviceNo'] = 0;
        }

        $update = $this->Masteropsimakan->updateOpsiMakan($params, $where);

        if ($update == true) {
            $response = array(
                'status' => 200,
                'message' => 'Tipe Penjualan Berhasil Disimpan'
            );
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Tipe Penjualan Gagal Disimpan'
            );
        }

        echo json_encode($response);
        exit();
    }

    public function ajax_update_isactive_tipepenjualan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $OpsiMakanID = $this->input->get_post('OpsiMakanID');
        $DeviceNo = $this->input->get_post('DeviceNo');
        $IsActive = $this->input->get_post('IsActive');
        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'OpsiMakanID' => $OpsiMakanID,
            'DeviceNo' => $DeviceNo
        );

        $OpsiMakan = $this->Masteropsimakan->getDetailOpsiMakan($where);

        $params = array(
            'IsActive' => $IsActive,
            'EditedBy' => getLoggedInUsername(),
            'EditedDate' => date('Y-m-d'),
            'EditedTime' => date('H:i:s'),
            'RowVersion' => (intval($OpsiMakan['RowVersion']) + 1)
        );

        $update = $this->Masteropsimakan->updateOpsiMakan($params, $where);

        if ($update == true) {
            $response = array(
                'status' => 200,
                'message' => $IsActive == 1 ? 'Tipe Penjualan Berhasil Diaktifkan.' : 'Tipe Penjualan Berhasil Dinon-aktifkan.'
            );
        } else {
            $response = array(
                'status' => 400,
                'message' => $IsActive == 1 ? 'Tipe Penjualan Gagal Diaktifkan.' : 'Tipe Penjualan Gagal Dinon-aktifkan.'
            );
        }

        echo json_encode($response);
        exit();
    }

    public function ajax_delete_tipepenjualan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $OpsiMakanID = $this->input->get_post('OpsiMakanID');
        $DeviceNo = $this->input->get_post('DeviceNo');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'OpsiMakanID' => $OpsiMakanID,
            'DeviceNo' => $DeviceNo
        );

        $delete = $this->Masteropsimakan->deleteOpsiMakan($where);

        if ($delete) {
            $response = array(
                'status' => 200,
                'message' => 'Tipe Penjualan Berhasil Dihapus'
            );
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Tipe Penjualan Gagal Dihapus'
            );
        }

        echo json_encode($response);
    }

    public function ajax_get_detail_tipepenjualan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $OpsiMakanID = $this->input->get_post('OpsiMakanID');
        $DeviceNo = $this->input->get_post('DeviceNo');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'OpsiMakanID' => $OpsiMakanID,
            'DeviceNo' => $DeviceNo
        );

        $result = $this->Masteropsimakan->getDetailOpsiMakan($where);
        $result['MarkupPersen'] = str_replace('.', ',', $result['MarkupPersen']);
        $result['ShareRevenue'] = str_replace('.', ',', $result['ShareRevenue']);

        echo json_encode($result);
    }
}
