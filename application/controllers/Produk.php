<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class produk extends MY_Controller
{

    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();
        $this->load->library('NutaQuery');
        $this->load->model('Userperusahaancabang');
        $this->load->model('Perusahaanmodel');
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
        $data['page_part'] = 'produk/list_produk';
        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_form",
            "features/js/js_form_validation",
            'produk/js_produk',
            'features/js/js_datatable'
        );
        $data['js_chart'] = array();
        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $selected_outlet,
        );
        $data['totalItems'] = $this->Masteritem->getTotalItems($params);
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['menu'] = "produk";
        $data['title'] = "Nuta Cloud - Items - Produk";
        $this->load->view('main_part', $data);
    }

    public function itemAdd()
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
        $data['page_part'] = 'produk/add_produk';
        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_form",
            "features/js/js_form_validation",
            'produk/js_add_produk',
            'features/js/js_datatable'
        );
        $data['js_chart'] = array();
        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $selected_outlet,
        );
        $rs_kategori = $this->Masteritem->getMasterCategory($params);
        $arr_kategori = array_map(function ($kategori) {
            $arr_kategori['id'] = $kategori['CategoryName'];
            $arr_kategori['text'] = $kategori['CategoryName'];
            return $arr_kategori;
        }, $rs_kategori);
        $data['rs_kategori'] = json_encode($arr_kategori);
        $rs_bahan = $this->Masteritem->getMasterItem(array_merge($params, array('IsProduct' => 'false')));
        $data['rs_bahan'] = array_map(function ($bahan) {
            $rs_bahan['DeviceNo'] = $bahan['DeviceNo'];
            $rs_bahan['ItemID'] = $bahan['ItemID'];
            $rs_bahan['ItemName'] = str_replace("'", "\'", $bahan['ItemName']);
            $rs_bahan['Unit'] = $bahan['Unit'];
            $rs_bahan['PurchasePrice'] = $bahan['PurchasePrice'];
            return $rs_bahan;
        }, $rs_bahan);
        // $data['rs_modifier'] = $this->Masteritem->getMasterModifier($params);
        $data['ws_host'] = $this->config->item('ws_base_url');
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();

        $this->load->model('Options');
        $data['Options'] = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $selected_outlet));

        $data['menu'] = "produk";
        $data['title'] = "Nuta Cloud - Items - Produk";
        $this->load->view('main_part', $data);
    }

    public function ajaxCreateItems()
    {
        $SelectedOutlet = $this->input->get_post('SelectedOutlet');
        $Outlet = $this->input->get_post('Outlet');
        $ItemName = $this->input->get_post('ItemName');
        $Barcode = $this->input->get_post('Barcode');
        $Category = $this->input->get_post('Category');
        $Unit = $this->input->get_post('Unit');
        $SellPrice = $this->input->get_post('SellPrice');
        $PurchasePrice = $this->input->get_post('PurchasePrice');

        // Check Kategori
        $CategoryID = '';
        $CategoryDeviceNo = '';
        $insertCategory = $this->createNewCategory($Outlet, $Category, 'Dapur');
        if ($insertCategory['status'] == 200) {
            $CategoryID = $insertCategory['CategoryID'];
            $CategoryDeviceNo = $insertCategory['CategoryDeviceNo'];
        } else {
            $response = array(
                "status" => 400,
                "message" => 'Produk gagal disimpan : <br> <small>Kategori baru gagal disimpan.</small>'
            );
            echo json_encode($response);
            exit();
        }

        // Variasi Harga
        $rsVarianName = $this->input->get_post('VarianName');
        $rsVarianSellPrice = $this->input->get_post('VarianSellPrice');
        $arrVariant = array();

        if (count($rsVarianSellPrice) > 1) {
            $SellPrice = min($rsVarianSellPrice);

            foreach ($rsVarianName as $i => $VarianName) {
                $arrVariant[$i]['VarianName'] = $VarianName;
                $arrVariant[$i]['SellPrice'] = $rsVarianSellPrice[$i];
                $arrVariant[$i]['VarianKey'] = $VarianName . '#' . $rsVarianSellPrice[$i];
                $arrVariant[$i]['IsReguler'] = $rsVarianSellPrice[$i] == $SellPrice ? 1 : 0;
            }
        } else {
            $SellPrice = $this->number_db_format($SellPrice);
        }

        // Bahan Penyusun
        $rsIngredientsName = $this->input->get_post('IngredientsName');
        $rsIngredientsQty = $this->input->get_post('IngredientsQty');
        $rsIngredientsUnit = $this->input->get_post('IngredientsUnit');
        $rsIngredientsPurchasePrice = $this->input->get_post('IngredientsPurchasePrice');
        $arrIngredients = array();
        if ($rsIngredientsName) {
            foreach ($rsIngredientsName as $i => $IngredientsName) {
                $arrIngredients[$i]['ItemName'] = $IngredientsName;
                $arrIngredients[$i]['Unit'] = $rsIngredientsUnit[$i];
                $arrIngredients[$i]['PurchasePrice'] = $rsIngredientsPurchasePrice[$i];
                $arrIngredients[$i]['QtyNeed'] = $rsIngredientsQty[$i];
            }
        }

        // Pilihan Ekstra
        $rsModifierID = $this->input->get_post('ModifierID');
        $rsModifierDeviceNo = $this->input->get_post('ModifierDeviceNo');
        $arrModifier = array();
        if ($rsModifierID) {
            foreach ($rsModifierID as $i => $ModifierID) {
                $arrModifier[$i]['ModifierID'] = $ModifierID;
                $arrModifier[$i]['ModifierDeviceNo'] = $rsModifierDeviceNo[$i];
                $arrModifier[$i]['ModifierKey'] = $rsModifierDeviceNo[$i] . '#' . $ModifierID;
            }
        }

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'ItemName' => $ItemName,
            'Barcode' => !$Barcode ? '' : $Barcode,
            'Unit' => $Unit,
            'CategoryID' => $CategoryID,
            'CategoryDeviceNo' => $CategoryDeviceNo,
            'SellPrice' => $SellPrice,
            'PurchasePrice' => $this->number_db_format($PurchasePrice),
            'IsProduct' => 'true'
        );

        $insert = $this->Masteritem->insertMasterItem($params, $arrVariant, $arrIngredients, $arrModifier, $SelectedOutlet);

        echo json_encode($insert);
        exit();
    }

    public function itemEdit()
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

        $ItemID = $this->input->get('ItemID');
        $DeviceNo = $this->input->get('DeviceNo');


        $outletids = array();
        foreach ($availableOutlets as $k => $v) {
            array_push($outletids, $k);
        }
        $data['outletids'] = $outletids;

        $data['outlets'] = $availableOutlets;
        $selected_outlet = $this->nutaquery->getOutlet();
        $data['selected_outlet'] = $selected_outlet;
        $where = array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $selected_outlet, 'ItemID' => $ItemID, 'DeviceNo' => $DeviceNo);
        $data['resultItem'] = $this->Masteritem->getDetailMasterItem($where);
        $data['rsVarian'] = $this->Masteritem->getMasterVariant($where);
        $data['rsBahan'] = $this->Masteritem->getMasterItemDetailIngredients($where);
        $data['page_part'] = 'produk/edit_produk';
        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_form",
            "features/js/js_form_validation",
            'produk/js_edit_produk',
            'features/js/js_datatable'
        );
        $data['js_chart'] = array();
        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $selected_outlet,
        );
        $rs_kategori = $this->Masteritem->getMasterCategory($params);
        $arr_kategori = array_map(function ($kategori) {
            $arr_kategori['id'] = $kategori['CategoryName'];
            $arr_kategori['text'] = $kategori['CategoryName'];
            return $arr_kategori;
        }, $rs_kategori);
        $data['rs_kategori'] = json_encode($arr_kategori);
        $rs_bahan = $this->Masteritem->getMasterItem(array_merge($params, array('IsProduct' => 'false')));
        $data['rs_bahan'] = array_map(function ($bahan) {
            $rs_bahan['DeviceNo'] = $bahan['DeviceNo'];
            $rs_bahan['ItemID'] = $bahan['ItemID'];
            $rs_bahan['ItemName'] = str_replace("'", "\'", $bahan['ItemName']);
            $rs_bahan['Unit'] = $bahan['Unit'];
            $rs_bahan['PurchasePrice'] = $bahan['PurchasePrice'];
            return $rs_bahan;
        }, $rs_bahan);
        // $data['rs_modifier'] = $this->Masteritem->getMasterModifier($params);
        $data['ws_host'] = $this->config->item('ws_base_url');
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();

        $this->load->model('Options');
        $data['Options'] = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $selected_outlet));

        $data['menu'] = "produk";
        $data['title'] = "Nuta Cloud - Items - Produk";
        $this->load->view('main_part', $data);
    }

    public function ajaxUpdateItems()
    {
        $SelectedOutlet = $this->input->get_post('SelectedOutlet');
        $Outlet = $this->input->get_post('Outlet');
        $ItemName = $this->input->get_post('ItemName');
        $OldItemName = $this->input->get_post('OldItemName');
        $Barcode = $this->input->get_post('Barcode');
        $Category = $this->input->get_post('Category');
        $Unit = $this->input->get_post('Unit');
        $SellPrice = $this->input->get_post('SellPrice');
        $PurchasePrice = $this->input->get_post('PurchasePrice');
        $fotoStatus = $this->input->get_post('fotoStatus');

        // Check Kategori
        $CategoryID = '';
        $CategoryDeviceNo = '';
        $insertCategory = $this->createNewCategory($Outlet, $Category, 'Dapur');
        if ($insertCategory['status'] == 200) {
            $CategoryID = $insertCategory['CategoryID'];
            $CategoryDeviceNo = $insertCategory['CategoryDeviceNo'];
        } else {
            $response = array(
                "status" => 400,
                "message" => 'Produk gagal disimpan : <br> <small>Kategori baru gagal disimpan.</small>'
            );
            echo json_encode($response);
            exit();
        }

        // Variasi Harga
        $rsVarianName = $this->input->get_post('VarianName');
        $rsOldVarianName = $this->input->get_post('OldVarianName');
        $rsVarianSellPrice = $this->input->get_post('VarianSellPrice');
        $arrVariant = array();

        if (count($rsVarianSellPrice) > 1) {
            $SellPrice = min($rsVarianSellPrice);

            foreach ($rsVarianName as $i => $VarianName) {
                $arrVariant[$i]['VarianName'] = $VarianName;
                $arrVariant[$i]['OldVarianName'] = $rsOldVarianName[$i];
                $arrVariant[$i]['SellPrice'] = $rsVarianSellPrice[$i];
                $arrVariant[$i]['VarianKey'] = $VarianName . '#' . $rsVarianSellPrice[$i];
                $arrVariant[$i]['IsReguler'] = $rsVarianSellPrice[$i] == $SellPrice ? 1 : 0;
            }
        } else {
            $SellPrice = $this->number_db_format($SellPrice);
        }

        // Bahan Penyusun
        $rsIngredientsName = $this->input->get_post('IngredientsName');
        $rsIngredientsQty = $this->input->get_post('IngredientsQty');
        $rsIngredientsUnit = $this->input->get_post('IngredientsUnit');
        $rsIngredientsPurchasePrice = $this->input->get_post('IngredientsPurchasePrice');
        $arrIngredients = array();
        if ($rsIngredientsName) {
            foreach ($rsIngredientsName as $i => $IngredientsName) {
                $arrIngredients[$i]['ItemName'] = $IngredientsName;
                $arrIngredients[$i]['Unit'] = $rsIngredientsUnit[$i];
                $arrIngredients[$i]['PurchasePrice'] = $rsIngredientsPurchasePrice[$i];
                $arrIngredients[$i]['QtyNeed'] = $rsIngredientsQty[$i];
            }
        }

        // Pilihan Ekstra
        $rsModifierID = $this->input->get_post('ModifierID');
        $rsModifierDeviceNo = $this->input->get_post('ModifierDeviceNo');
        $arrModifier = array();
        if ($rsModifierID) {
            foreach ($rsModifierID as $i => $ModifierID) {
                $arrModifier[$i]['ModifierID'] = $ModifierID;
                $arrModifier[$i]['ModifierDeviceNo'] = $rsModifierDeviceNo[$i];
                $arrModifier[$i]['ModifierKey'] = $rsModifierDeviceNo[$i] . '#' . $ModifierID;
            }
        }

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'ItemName' => $ItemName,
            'Barcode' => !$Barcode ? '' : $Barcode,
            'Unit' => $Unit,
            'CategoryID' => $CategoryID,
            'CategoryDeviceNo' => $CategoryDeviceNo,
            'SellPrice' => $SellPrice,
            'PurchasePrice' => $this->number_db_format($PurchasePrice),
            'IsProduct' => 'true'
        );

        $insert = $this->Masteritem->updateMasterItem($params, $OldItemName, $fotoStatus, $arrVariant, $arrIngredients, $arrModifier, $SelectedOutlet);

        echo json_encode($insert);
        exit();
    }

    public function ajaxPushImageItem()
    {

        $DeviceID = $this->input->get_post('Outlet');
        $ItemID = $this->input->get_post('ItemID');
        $DeviceNo = $this->input->get_post('DeviceNo');

        $this->Masteritem->pushImageItem($DeviceID, $ItemID, getPerusahaanNo(), $DeviceNo);
    }

    private function createNewCategory($Outlet, $CategoryName, $IPPrinter)
    {

        if ($CategoryName == '' || empty($CategoryName)) {
            return array(
                "status" => 200,
                "message" => 'Kategori Berhasil Disimpan',
                "CategoryID" => 0,
                "CategoryDeviceNo" => 0
            );
        }
        $this->load->model('Kategori');
        $Category = $this->Kategori->getDetailCategoryDynamic(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $Outlet, 'CategoryName' => $CategoryName));
        if (!empty($Category)) {
            return array(
                "status" => 200,
                "message" => 'Kategori Berhasil Disimpan',
                "CategoryID" => $Category['CategoryID'],
                "CategoryDeviceNo" => $Category['DeviceNo']
            );
        } else {
            $insert = $this->Kategori->insertKategori($Outlet, $CategoryName, $IPPrinter, getPerusahaanNo());
            if ($insert['status'] == 200) {
                return array(
                    "status" => 200,
                    "message" => $insert['message'],
                    "CategoryID" => $insert['CategoryID'],
                    "CategoryDeviceNo" => $insert['CloudDevNo']
                );
            } else {
                return $insert;
            }
        }
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
            $field => $value,
            'IsProduct' => 'true'
        );

        if ($id) {
            $where['ItemID <> '] = $id;
        }

        $result = $this->Masteritem->isExistField($where);

        echo json_encode(array('valid' => !$result));
    }

    public function ajaxItems()
    {
        $Outlet = $this->input->get_post('Outlet', true);
        $draw = $this->input->post('draw', true);
        $start = (int) $this->input->post('start', true);
        $length = (int) $this->input->post('length', true);
        $order = $this->input->post('order', true);
        $search = $this->input->post('search', true);
        $DetailBahan = $this->input->get_post('DetailBahan', true);

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'IsProduct' => 'true',
            'draw' => $draw,
            'start' => $start,
            'length' => $length,
            'order' => $order,
            'search' => $search,
            'DetailBahan' => $DetailBahan = '' ? 'false' : $DetailBahan
        );
        $result = $this->Masteritem->getDatatablesItems($params, $this->visibilityMenu);

        echo json_encode($result);
    }

    public function ajax_varian_by_item()
    {
        $Outlet = $this->input->get_post('OutletID');
        $ItemID = $this->input->get_post('ItemID');
        $ItemDeviceNo = $this->input->get_post('ItemDeviceNo');

        $where = array(
            getPerusahaanNo(), $Outlet, $ItemID, $ItemDeviceNo
        );
        $ItemVariant = $this->Masteritem->getItemVariant($where);
        echo json_encode($ItemVariant);
    }

    public function ajaxmodifier()
    {
        $Outlet = $this->input->get_post('Outlet', true);
        $ItemID = $this->input->get_post('ItemID', true);
        $ItemDeviceNo = $this->input->get_post('ItemDeviceNo', true);
        $draw = $this->input->post('draw', true);
        $start = (int) $this->input->post('start', true);
        $length = (int) $this->input->post('length', true);
        $order = $this->input->post('order', true);
        $search = $this->input->post('search', true);

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'ItemID' => $ItemID,
            'ItemDeviceNo' => $ItemDeviceNo,
            'draw' => $draw,
            'start' => $start,
            'length' => $length,
            'order' => $order,
            'search' => $search,
        );

        if ($ItemID != '' && $ItemDeviceNo != '') {
            $result = $this->Masteritem->getDatatablesModifierEdit($params, $this->visibilityMenu);
        } else {
            $result = $this->Masteritem->getDatatablesModifier($params, $this->visibilityMenu);
        }
        echo json_encode($result);
    }

    public function ajax_detail_by_modifier()
    {
        $ModifierID = $this->input->get_post('ModifierID');
        $DeviceNo = $this->input->get_post('DeviceNo');
        $OutletID = $this->input->get_post('OutletID');

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $OutletID,
            'ModifierDeviceNo' => $DeviceNo,
            'ModifierID' => $ModifierID
        );

        $rs_detail = $this->Masteritem->getModifierDetail($params);
        echo json_encode($rs_detail);
    }

    public function ajax_create_modifier()
    {
        $this->load->model('Mastermodifier');
        $OutletID = $this->input->post('Outlet');
        $ModifierName = $this->input->post('ModifierName');
        $ChooseOneOnly = $this->input->post('ChooseOneOnly');
        $CanAddQuantity = $this->input->post('CanAddQuantity');
        $ChoiceName = $this->input->post('ChoiceName');
        $QtyNeed = $this->input->post('QtyNeed');
        $ChoicePrice = $this->input->post('ChoicePrice');

        $where = array(
            "DeviceID" => $OutletID,
            "PerusahaanNo" => getPerusahaanNo(),
            "ModifierName" => $ModifierName
        );

        if ($this->Mastermodifier->isExistModifierName($where)) {
            $response =  array(
                "status" => 400,
                "message" => "Nama Pilihan Ekstra sudah digunakan"
            );
            echo json_encode($response);
            exit();
        }

        if (empty(array_filter($ChoiceName))) {
            $response =  array(
                "status" => 400,
                "message" => "Detail Pilihan Ekstra belum diisi"
            );
            echo json_encode($response);
            exit();
        }

        // create array detail
        $params = array(
            "ModifierName" => $ModifierName,
            "ChooseOneOnly" => $ChooseOneOnly,
            "CanAddQuantity" => !$CanAddQuantity ? 0 : $CanAddQuantity,
            "DeviceID" => $OutletID,
            "PerusahaanNo" => getPerusahaanNo()
        );

        $rs_detail = array();
        foreach ($ChoiceName as $key => $value) {
            if ($value == '' || empty($value)) {
                continue;
            }

            if ($ChoicePrice[$key] == '') {
                continue;
            }

            $price = $ChoicePrice[$key];
            $price = str_replace('Rp', '', $price);
            $price = str_replace(' ', '', $price);
            $price = str_replace('.', '', $price);
            $price = str_replace(',', '.', $price);

            $rs_detail[$key]['ChoiceName'] = $value;
            $rs_detail[$key]['ChoicePrice'] = $price;
            $rs_detail[$key]['QtyNeed'] = $QtyNeed[$key] == '' ? 0 : $QtyNeed[$key];
            $rs_detail[$key]['DeviceID'] = $OutletID;
            $rs_detail[$key]['PerusahaanNo'] = getPerusahaanNo();
            $rs_detail[$key]['Varian'] = 'Nuta';
        }

        $insert = $this->Mastermodifier->insertModifier($params, $rs_detail);
        if ($insert) {
            $response =  array(
                "status" => 200,
                "message" => "Pilihan Ekstra berhasil disimpan"
            );
            echo json_encode($response);
            exit();
        } else {
            $response =  array(
                "status" => 400,
                "message" => "Pilihan Ekstra gagal disimpan"
            );
            echo json_encode($response);
            exit();
        }
    }

    public function ajax_validation_modifier_exist()
    {
        $this->load->model('Mastermodifier');
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
            $where['ModifierID <> '] = $id;
        }

        $result = $this->Mastermodifier->isExistField($where);

        echo json_encode(array('valid' => !$result));
    }

    public function ajax_get_detail_modifier()
    {
        $this->load->model('Mastermodifier');
        $Outlet = $this->input->get_post('Outlet');
        $ModifierID = $this->input->get_post('ModifierID');
        $DeviceNo = $this->input->get_post('DeviceNo');

        $where = array(
            "DeviceID" => $Outlet,
            "PerusahaanNo" => getPerusahaanNo(),
            "ModifierID" => $ModifierID,
            "DeviceNo" => $DeviceNo
        );
        $Modifier = $this->Mastermodifier->getModifierOnlyByID($where);
        $Modifier['rs_detail'] = $this->Mastermodifier->getListDetailByModifier($ModifierID, $DeviceNo, $Outlet);
        echo json_encode($Modifier);
    }

    public function ajax_update_modifier()
    {
        $this->load->model('Mastermodifier');
        $OutletID = $this->input->post('Outlet');
        $DeviceNo = $this->input->post('DeviceNo');
        $ModifierID = $this->input->post('ModifierID');
        $ModifierName = $this->input->post('ModifierName');
        $ChooseOneOnly = $this->input->post('ChooseOneOnly');
        $CanAddQuantity = $this->input->post('CanAddQuantity');
        $ChoiceName = $this->input->post('ChoiceName');
        $QtyNeed = $this->input->post('QtyNeed');
        $ChoicePrice = $this->input->post('ChoicePrice');

        $where = array(
            "DeviceID" => $OutletID,
            "PerusahaanNo" => getPerusahaanNo(),
            "ModifierID <> " => $ModifierID,
            "ModifierName" => $ModifierName
        );

        if ($this->Mastermodifier->isExistModifierName($where)) {
            $response =  array(
                "status" => 400,
                "message" => "Nama Pilihan Ekstra sudah digunakan"
            );
            echo json_encode($response);
            exit();
        }

        if (empty(array_filter($ChoiceName))) {
            $response =  array(
                "status" => 400,
                "message" => "Detail Pilihan Ekstra belum diisi"
            );
            echo json_encode($response);
            exit();
        }

        // create array detail
        $params = array(
            "ModifierName" => $ModifierName,
            "ChooseOneOnly" => $ChooseOneOnly,
            "CanAddQuantity" => !$CanAddQuantity ? 0 : $CanAddQuantity,
            "DeviceID" => $OutletID,
            "PerusahaanNo" => getPerusahaanNo()
        );

        $where = array(
            "ModifierID" => $ModifierID,
            "DeviceID" => $OutletID,
            "DeviceNo" => $DeviceNo,
            "PerusahaanNo" => getPerusahaanNo()
        );

        $rs_detail = array();
        foreach ($ChoiceName as $key => $value) {
            if ($value == '' || empty($value)) {
                continue;
            }

            if ($ChoicePrice[$key] == '') {
                continue;
            }

            $QtyNeed[$key] = $CanAddQuantity == '1' ? $QtyNeed[$key] : '';

            $price = $ChoicePrice[$key];
            $price = str_replace('Rp', '', $price);
            $price = str_replace(' ', '', $price);
            $price = str_replace('.', '', $price);
            $price = str_replace(',', '.', $price);

            $rs_detail[$key]['ChoiceName'] = $value;
            $rs_detail[$key]['ChoicePrice'] = $price;
            $rs_detail[$key]['QtyNeed'] = $QtyNeed[$key] == '' ? 0 : $QtyNeed[$key];
            $rs_detail[$key]['DeviceID'] = $OutletID;
            $rs_detail[$key]['PerusahaanNo'] = getPerusahaanNo();
            $rs_detail[$key]['Varian'] = 'Nuta';
        }

        $update = $this->Mastermodifier->updateModifier2($params, $where, $rs_detail);
        if ($update) {
            $response =  array(
                "status" => 200,
                "message" => "Pilihan Ekstra berhasil diubah"
            );
            echo json_encode($response);
            exit();
        } else {
            $response =  array(
                "status" => 400,
                "message" => "Pilihan Ekstra gagal diubah"
            );
            echo json_encode($response);
            exit();
        }
    }

    public function itemform()
    {

        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        $selected_outlet = $this->input->get('outlet');

        if (isNotEmpty($selected_outlet)) {
            $this->nutaquery->setOutlet($selected_outlet);
        }
        $this->load->model('masteritem');
        $this->load->model('Satuan');
        $this->load->model('Kategori');
        $this->load->model('Mastervarian');
        $this->load->model('Perusahaanmodel');
        $list_kategori = $this->Kategori->getDaftarKategori($this->nutaquery->getOutlet());
        $list_satuan = $this->Satuan->getDaftarSatuan($this->nutaquery->getOutlet());
        $list_variasi_harga = [];
        //        $list_variasi_harga = [
        //            ['VarianName' => 'Reguler', 'SellPrice' => 0, 'IsReguler' => 1, 'Placeholder' => 'Reguler'],
        //            ['VarianName' => '', 'SellPrice' => 0, 'IsReguler' => 0, 'Placeholder' => 'misal: Cup Ukuran Kecil'],
        //            ['VarianName' => '', 'SellPrice' => 0, 'IsReguler' => 0, 'Placeholder' => 'misal: Cup Ukuran Besar']
        //        ];
        $pilihan_ekstra = [
            'NamaPilihanEkstra' => '',
            'PlaceholderPilihan' => 'Misal: Toping',
            'HanyaBisaPilihSatu' => 'false',
            'BisaMenambahJumlahPerPilihan' => 'false',
            'Pilihan' => [
                ['NamaPilihan' => '', 'Harga' => 0, 'QtyDibutuhkan' => 0, 'Satuan' => '', 'PlaceholderPilihan' => 'misal: Keju'],
                ['NamaPilihan' => '', 'Harga' => 0, 'QtyDibutuhkan' => 0, 'Satuan' => '', 'PlaceholderPilihan' => 'misal: Coklat'],
                ['NamaPilihan' => '', 'Harga' => 0, 'QtyDibutuhkan' => 0, 'Satuan' => '', 'PlaceholderPilihan' => 'misal: Kacang']
            ]
        ];

        $this->load->model('Mastermodifier');
        $list_pilihan_ekstra = $this->Mastermodifier->getModifier($this->nutaquery->getOutlet());
        $index = 0;
        foreach ($list_pilihan_ekstra as $ekstra) {
            $pilihans = $this->Mastermodifier->getPilihan($ekstra['ModifierID'] . "." . $ekstra['DeviceNo'], $this->nutaquery->getOutlet());
            if ($ekstra['CanAddQuantity'] == 1) {
                $y = 0;
                foreach ($pilihans as $pilihan) {
                    $itempilihan = $this->masteritem->getByName($pilihan['NamaPilihan'], $this->nutaquery->getOutlet());
                    $pilihans[$y]['Satuan'] = isset($itempilihan) ? $itempilihan->Unit : '';
                    $y++;
                }
                $y = 0;
            }
            $list_pilihan_ekstra[$index]['Pilihan'] = $pilihans;
            $index++;
        }
        $namaitem = $this->input->get('id');
        $mode = isNotEmpty($namaitem) ? 'edit' : 'new';
        $item_name = '';
        $kategori = '';
        $satuan = '';
        $harga_jual = '';
        $harga_beli = '';
        $is_produk_bahan = '';
        $is_punya_bahan = '';
        $urlfoto = '';
        $bahans = array();
        $autocompletebahan = $this->masteritem->getAutocompleteBahan($this->nutaquery->getOutlet());

        if ($mode == 'edit') {
            //Outletnya sesuai item
            $outletsByItem = $this->FilterOutletWithItem($availableOutlets, $namaitem);
            if (isNotEmpty($selected_outlet)) {
                $this->nutaquery->setOutlet($selected_outlet);
            } else {
                $this->setDefaultOutletId($availableOutlets);
            }

            $item = $this->masteritem->getByName($namaitem, $this->nutaquery->getOutlet());
            $iditem = $item->ItemID . "." . $item->DeviceNo;
            $item_name = $item->ItemName;
            $kategori = $item->CategoryID . "." . $item->CategoryDeviceNo;
            $harga_jual = $item->SellPrice;
            $harga_beli = $item->PurchasePrice;
            $is_produk_bahan = $item->IsProduct;
            $is_punya_bahan = $item->IsProductHasIngredients;
            $satuan = $item->Unit;
            $variasi_harga_di_database = $this->Mastervarian->getVariasiHarga($iditem, $this->nutaquery->getOutlet());
            //$list_variasi_harga[0]['SellPrice'] = $harga_jual;
            if (count($variasi_harga_di_database) > 0) {
                $list_variasi_harga = $variasi_harga_di_database;
            }
            $urlfoto = base_url('images/no-image-with-text.png');
            if (isNotEmpty($item->OnlineImagePath)) {
                $urlfoto = $this->config->item('ws_base_url') . $item->OnlineImagePath;
            }
            //load bahan
            $str_query_bahan = $this->nutaquery->get_query_bahan_item($iditem);
            $query_bahan = $this->db->query($str_query_bahan);
            $bahans = $query_bahan->result();
            $data['iditem'] = $iditem;
            $linkedModifiers = $this->masteritem->getModifiersItem($iditem, $this->nutaquery->getOutlet());
            $index = 0;
            foreach ($list_pilihan_ekstra as $e) {

                foreach ($linkedModifiers as $d) {
                    if ($e['ModifierID'] == $d->ModifierID && $e['DeviceNo'] == $d->ModifierDeviceNo) {
                        $list_pilihan_ekstra[$index]['Selected'] = true;
                        break;
                    }
                }
                $index++;
            }
        }
        $outletids = array();
        foreach ($availableOutlets as $k => $v) {
            array_push($outletids, $k);
        }
        $data['outletids'] = $outletids;


        $data['ws_host'] = $this->config->item('ws_base_url');
        $data['urlfoto'] = $urlfoto;
        $data['autocompletebahan'] = $autocompletebahan;
        $data['form']['nama item'] = $item_name;
        $data['form']['kategori'] = $kategori;
        $data['form']['satuan'] = $satuan;
        $data['form']['harga jual'] = $harga_jual;
        $data['form']['harga beli'] = $harga_beli;
        $data['form']['jenis item'] = $is_produk_bahan;
        $data['form']['punya bahan'] = $is_punya_bahan;
        $data['form']['bahans'] = $bahans;
        $data['outlets'] = $availableOutlets;
        $data['outlets_by_item'] = isset($outletsByItem) ? $outletsByItem : $availableOutlets;

        $data['modeform'] = $mode;
        $data['kategories'] = $list_kategori;
        $data['satuans'] = $list_satuan;
        $data['variasi_harga'] = $list_variasi_harga;
        $data['pilihan_ekstra'] = $pilihan_ekstra;
        $data['list_pilihan_ekstra'] = $list_pilihan_ekstra;
        $data['selected_outlet'] = $this->nutaquery->getOutlet();
        $data['page_part'] = 'produk/form_produk';
        $data['js_part'] = array(
            'features/js/js_socket',
            'features/js/js_ajax_switch',
            'features/js/js_form',
            'features/js/js_form_validation',
            'features/js/js_grid_item',
            'features/js/js_datatable',
            'features/js/js_dialog_kategori',
            'features/js/js_dialog_satuan',
            'features/js/js_dialog_hapus_kategori',
            'features/js/js_dialog_hapus_item',
            'features/js/js_form_produk',
            'features/js/js_dialog_simpan_beberapa_outlet',
            'features/js/js_dialog_hapus_satuan',
            'features/js/js_dialog_variasi_harga',
            'features/js/js_dialog_pilihan_ekstra',
        );
        $data['js_chart'] = array();
        $data['datagrid'] = array('fields' => array('Foto', 'Item', 'Kategori', 'Satuan', 'Harga Jual', 'Ada Bahan'));
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();

        $query_option = $this->db->query($this->nutaquery->get_query_modul_outlet());
        $option = $query_option->row();
        $data['option'] = $option;
        $this->load->view('main_part', $data);
    }

    public function tesmodifier()
    {
        $this->load->model('Mastermodifier');
        $this->Mastermodifier->tesmodifier('Topping', 2678);
    }

    public function import()
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

        $CategoryCount = $this->check_new_outlet($selected_outlet);
        if ($CategoryCount > 0) {
            redirect(base_url('produk/import_wizard?outlet='.$selected_outlet), 'refresh');
        }

        $data['outlets'] = $availableOutlets;
        $selected_outlet = $this->nutaquery->getOutlet();
        $data['selected_outlet'] = $selected_outlet;
        $data['statusimport'] = $this->input->get_post('statusimport');

        $data['page_part'] = 'produk/import_produk';
        $data['js_part'] = array('features/js/js_import');
        $data['js_chart'] = array();

        $data['rs_history'] = $this->Masteritem->getImportItemHistory($selected_outlet);
        $data['ws_host'] = $this->config->item('ws_base_url');
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();

        $data['menu'] = "produk";
        $data['title'] = "Nuta Cloud - Items - Import Produk";

        $this->load->view('main_part', $data);
    }

    public function import_upload()
    {
        $Outlet = $this->input->get_post('Outlet');
        if ($Outlet > 0) {
            $file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
                $arr_file = explode('.', $_FILES['file']['name']);
                $file_name = $arr_file[0];
                $extension = end($arr_file);

                $arrProduk = array();
                $arrBahan = array();
                $arrModifier = array();
                if ($extension == 'xlsx') {
                    if ($file_name == 'Template Produk Dengan Bahan') {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
                        // $sheetData = $spreadsheet->getActiveSheet()->toArray();
                        $sheetProduk = $spreadsheet->getSheetByName('Produk');
                        if ($sheetProduk) {
                            $sheetProdukArray = $sheetProduk->toArray();
                        } else {
                            $sheetProdukArray = array();
                        }

                        $sheetModifierItems = $spreadsheet->getSheetByName('Komposisi Pilihan Ekstra');
                        if ($sheetModifierItems) {
                            $sheetModifierItemsArray = $sheetModifierItems->toArray();
                        } else {
                            $sheetModifierItemsArray = array();
                        }

                        $sheetItemIngredients = $spreadsheet->getSheetByName('Komposisi Bahan');
                        if ($sheetItemIngredients) {
                            $sheetItemIngredientsArray = $sheetItemIngredients->toArray();
                        } else {
                            $sheetItemIngredientsArray = array();
                        }

                        if ($sheetProdukArray) {
                            foreach ($sheetProdukArray as $i => $row) {
                                if ($i == 0) {
                                    continue;
                                }

                                if (strlen(trim($row[0])) == 0) {
                                    continue;
                                }

                                $found = array_search($row[0], array_column($arrProduk, 'ItemName'));
                                if (strval($found) != '') {
                                    array_push($arrProduk[$found]['VarianName'], $row[1]);
                                    array_push($arrProduk[$found]['VarianSellPrice'], $row[6]);

                                    $min = min($arrProduk[$found]['VarianSellPrice']);
                                    $arrProduk[$found]['SellPrice'] = $min;

                                    if (strlen(trim($row[2]) > 0)) {
                                        $arrProduk[$found]['Foto'] = $row[2];
                                    }
                                } else {

                                    $params = array(
                                        'ItemName' => $row[0],
                                        'Category' => $row[3],
                                        'Unit' => $row[4],
                                        'Barcode' => $row[5],
                                        'SellPrice' => $row[6],
                                        'PurchasePrice' => $row[7],
                                        'VarianName' => array(),
                                        'VarianSellPrice' => array(),
                                        'IngredientsName' => array(),
                                        'IngredientsQty' => array(),
                                        'IngredientsUnit' => array(),
                                        'IngredientsPurchasePrice' => array(),
                                        'ModifierName' => array(),
                                        'IsProduct' => 'true',
                                        'Foto' => $row[2]
                                    );

                                    // Check varian
                                    if (strlen(trim($row[1])) > 0) {
                                        array_push($params['VarianName'], $row[1]);
                                        array_push($params['VarianSellPrice'], $row[6]);
                                    }

                                    // Check Komposisi Bahan
                                    if ($row[8] == 'Ya') {
                                        if ($sheetItemIngredientsArray) {
                                            $ItemName = $row[0];
                                            foreach ($sheetItemIngredientsArray as $Ingredients) {
                                                if ($Ingredients[0] == $ItemName) {
                                                    array_push($params['IngredientsName'], $Ingredients[1]);
                                                    array_push($params['IngredientsQty'], $Ingredients[2]);
                                                    array_push($params['IngredientsUnit'], $Ingredients[3]);
                                                    array_push($params['IngredientsPurchasePrice'], $Ingredients[4]);
                                                }
                                            }
                                        }
                                    }

                                    // Check Komposisi Pilihan Ekstra
                                    if ($row[9] == 'Ya') {
                                        if ($sheetModifierItemsArray) {
                                            $ItemName = $row[0];
                                            foreach ($sheetModifierItemsArray as $Modifier) {
                                                if ($Modifier[0] == $ItemName) {
                                                    array_push($params['ModifierName'], $Modifier[1]);
                                                }
                                            }
                                        }
                                    }


                                    array_push($arrProduk, $params);
                                }
                            }
                        }

                        // Bahan
                        $sheetBahan = $spreadsheet->getSheetByName('Bahan');
                        if ($sheetBahan) {
                            $sheetBahanArray = $sheetBahan->toArray();
                        } else {
                            $sheetBahanArray = array();
                        }
                        if ($sheetBahanArray) {
                            foreach ($sheetBahanArray as $i => $row) {
                                if ($i == 0) {
                                    continue;
                                }

                                if (strlen(trim($row[0])) == 0) {
                                    continue;
                                }

                                $params = array(
                                    'ItemName' => $row[0],
                                    'Category' => $row[2],
                                    'Unit' => $row[3],
                                    'PurchasePrice' => $row[4],
                                    'foto' => $row[1],
                                    'IsProduct' => 'false'
                                );

                                array_push($arrBahan, $params);
                            }
                        }

                        // Pilihan Ekstra / Modifier
                        $sheetModifier = $spreadsheet->getSheetByName('Pilihan Ekstra');
                        if ($sheetModifier) {
                            $sheetModifierArray = $sheetModifier->toArray();
                        } else {
                            $sheetModifierArray = array();
                        }
                        if ($sheetModifierArray) {
                            foreach ($sheetModifierArray as $i => $row) {
                                if ($i == 0) {
                                    continue;
                                }

                                if (strlen(trim($row[0])) == 0) {
                                    continue;
                                }

                                $found = array_search($row[0], array_column($arrModifier, 'ModifierName'));
                                if (strval($found) != '') {
                                    array_push($arrModifier[$found]['ChoiceName'], $row[3]);
                                    array_push($arrModifier[$found]['ChoicePrice'], $row[4]);
                                    array_push($arrModifier[$found]['QtyNeed'], $row[5]);
                                } else {
                                    $params = array(
                                        'ModifierName' => $row[0],
                                        'ChooseOneOnly' => $row[1] == 'Ya' ? 1 : 0,
                                        'CanAddQuantity' => $row[2] == 'Ya' ? 1 : 0,
                                        'ChoiceName' => array($row[3]),
                                        'ChoicePrice' => array($row[4]),
                                        'QtyNeed' => array($row[5])
                                    );

                                    array_push($arrModifier, $params);
                                }
                            }
                        }


                        $response = array(
                            'status' => 200,
                            'message' => 'Upload File Berhasil.',
                            'progress' => (100 / (count($arrProduk) + count($arrBahan) + count($arrModifier))),
                            'AdaBahan' => 'true',
                            'fileName' => $file_name . '.' . $extension,
                            'data' => array(
                                'arrProduk' => $arrProduk,
                                'arrBahan' => $arrBahan,
                                'arrModifier' => $arrModifier
                            )
                        );

                        echo json_encode($response);
                        exit();
                    } elseif ($arr_file[0] == 'Template Produk Tanpa Bahan') {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
                        $sheetProduk = $spreadsheet->getSheet(0)->toArray();
                        if ($sheetProduk) {
                            foreach ($sheetProduk as $i => $row) {
                                if ($i == 0) {
                                    continue;
                                }

                                if (strlen(trim($row[0])) == 0) {
                                    continue;
                                }

                                $found = array_search($row[0], array_column($arrProduk, 'ItemName'));
                                if (strval($found) != '') {
                                    array_push($arrProduk[$found]['VarianName'], $row[1]);
                                    array_push($arrProduk[$found]['VarianSellPrice'], $row[6]);

                                    $min = min($arrProduk[$found]['VarianSellPrice']);
                                    $arrProduk[$found]['SellPrice'] = $min;

                                    if (strlen(trim($row[2]) > 0)) {
                                        $arrProduk[$found]['Foto'] = $row[2];
                                    }
                                } else {

                                    $params = array(
                                        'ItemName' => $row[0],
                                        'Category' => $row[3],
                                        'Unit' => $row[4],
                                        'Barcode' => $row[5],
                                        'SellPrice' => $row[6],
                                        'PurchasePrice' => $row[7],
                                        'VarianName' => array(),
                                        'VarianSellPrice' => array(),
                                        'IngredientsName' => array(),
                                        'IngredientsQty' => array(),
                                        'IngredientsUnit' => array(),
                                        'IngredientsPurchasePrice' => array(),
                                        'ModifierName' => array(),
                                        'IsProduct' => 'true',
                                        'Foto' => $row[2]
                                    );

                                    // Check varian
                                    if (strlen(trim($row[1])) > 0) {
                                        array_push($params['VarianName'], $row[1]);
                                        array_push($params['VarianSellPrice'], $row[6]);
                                    }

                                    array_push($arrProduk, $params);
                                }
                            }
                        }

                        $response = array(
                            'status' => 200,
                            'message' => 'Upload File Berhasil.',
                            'progress' => (100 / (count($arrProduk))),
                            'AdaBahan' => 'false',
                            'fileName' => $file_name . '.' . $extension,
                            'data' => array(
                                'arrProduk' => $arrProduk,
                                'arrBahan' => array(),
                                'arrModifier' => array()
                            )
                        );

                        echo json_encode($response);
                        exit();
                    } else {
                        $response = array(
                            'status' => 400,
                            'message' => 'Upload File Gagal : Nama file tidak boleh diubah.'
                        );

                        echo json_encode($response);
                        exit();
                    }
                } else {
                    $response = array(
                        'status' => 400,
                        'message' => 'Upload File Gagal : Type file tidak diijinkan.'
                    );

                    echo json_encode($response);
                    exit();
                }
            } else {
                $response = array(
                    'status' => 400,
                    'message' => 'Upload File Gagal : Type file tidak diijinkan.'
                );

                echo json_encode($response);
                exit();
            }
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Upload File Gagal : Outlet belum dipilih.'
            );

            echo json_encode($response);
            exit();
        }
    }

    public function import_wizard()
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

        $CategoryCount = $this->check_new_outlet($selected_outlet);
        if ($CategoryCount == 0) {
            redirect(
                base_url(
                    'produk/import?outlet='.$selected_outlet
                ),
                'refresh'
            );
        }

        $data['outlets'] = $availableOutlets;
        $data['selected_outlet'] = $this->nutaquery->getOutlet();
        $data['ws_host'] = $this->config->item('ws_base_url');

        $data['page_part'] = 'produk/import_wizard';
        $data['js_part'] = array('features/js/js_import_wizard');
        $data['js_chart'] = array();

        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();

        $this->load->view('main_part', $data);
    }

    public function import_act()
    {
        $this->load->library('Excel');

        $outlet = $this->input->post('outlet');

        $tmpfile = $_FILES['fContent']['tmp_name'];
        try {
            $inputFileType = PHPExcel_IOFactory::identify($tmpfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($tmpfile);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($tmpfile,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        $sheet = $objPHPExcel->getSheet(0); // selalu fetch data dari sheet pertama
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        try {
            $perusahaanNo = getPerusahaanNo();
            $this->db->trans_begin();

            for ($i = 2; $i <= $highestRow; $i++) {
                $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, FALSE);
                $rowData = $rowData[0];

                foreach ($rowData as $key => $value) {
                    if (is_null($value)) {
                        $rowData[$key] = "";
                    }
                }

                $qC = $this->db->query("SELECT CONCAT(CategoryID, '.', DeviceNo) CategoryID FROM mastercategory WHERE CategoryName = ? AND DeviceID = ? AND PerusahaanNo = ? ORDER BY DeviceNo ASC", array($rowData[2], $outlet, getPerusahaanNo()));

                if (!$qC) throw new Exception($this->db->_error_message(), $this->db->_error_number());
                $c = $qC->row_array();
                if (is_null($c["CategoryID"])) {
                    $categoryid = $this->createCategory($rowData[2], $outlet, $perusahaanNo);
                    $c["CategoryID"] = $categoryid;
                }

                $itemid = $this->createItem($rowData[0], $rowData[1], $c["CategoryID"], $rowData[3], $outlet, $perusahaanNo, strtolower(trim($rowData[10])) == "produk", strtolower(trim($rowData[12])) == "ya", strlen($rowData[5]) == 0 ? 0 : $rowData[5], strlen($rowData[11]) == 0 ? 0 : $rowData[11]);

                // variant 1
                if (strlen($rowData[4]) > 0) {

                    $this->createVarian($itemid, $rowData[4], $rowData[5], $outlet, $perusahaanNo, true);
                }

                // variant 2
                if (strlen($rowData[6]) > 0) {
                    $this->createVarian($itemid, $rowData[6], $rowData[7], $outlet, $perusahaanNo);
                }

                // variant 3
                if (strlen($rowData[8]) > 0) {
                    $this->createVarian($itemid, $rowData[8], $rowData[9], $outlet, $perusahaanNo);
                }

                // ingredients
                if (strtolower(trim($rowData[12])) == "ya") {
                    $this->createIngredient($itemid, $rowData[13], $rowData[15], $rowData[14], $outlet, $perusahaanNo, 1);
                    $this->createIngredient($itemid, $rowData[16], $rowData[18], $rowData[17], $outlet, $perusahaanNo, 2);
                    $this->createIngredient($itemid, $rowData[19], $rowData[21], $rowData[20], $outlet, $perusahaanNo, 3);
                    $this->createIngredient($itemid, $rowData[22], $rowData[24], $rowData[23], $outlet, $perusahaanNo, 4);
                    $this->createIngredient($itemid, $rowData[25], $rowData[27], $rowData[26], $outlet, $perusahaanNo, 5);
                    $this->createIngredient($itemid, $rowData[28], $rowData[30], $rowData[29], $outlet, $perusahaanNo, 6);
                    $this->createIngredient($itemid, $rowData[31], $rowData[33], $rowData[32], $outlet, $perusahaanNo, 7);
                    $this->createIngredient($itemid, $rowData[34], $rowData[36], $rowData[35], $outlet, $perusahaanNo, 8);
                    $this->createIngredient($itemid, $rowData[37], $rowData[39], $rowData[38], $outlet, $perusahaanNo, 9);
                    $this->createIngredient($itemid, $rowData[40], $rowData[42], $rowData[41], $outlet, $perusahaanNo, 10);
                }

                if (strlen($rowData[43]) > 0) {
                    $details = array();

                    if (strlen($rowData[45]) > 0)
                        array_push($details, array("ChoiceName" => $rowData[45], "ChoicePrice" => strlen($rowData[46]) > 0 ? $rowData[46] : 0));
                    if (strlen($rowData[47]) > 0)
                        array_push($details, array("ChoiceName" => $rowData[47], "ChoicePrice" => strlen($rowData[48]) > 0 ? $rowData[48] : 0));
                    if (strlen($rowData[49]) > 0)
                        array_push($details, array("ChoiceName" => $rowData[49], "ChoicePrice" => strlen($rowData[50]) > 0 ? $rowData[50] : 0));

                    $a = $this->createModifier($itemid, $rowData[43], strtolower(trim($rowData[44])) == "ya", $details, $outlet, $perusahaanNo);
                    //
                }
                if (strlen($rowData[51]) > 0) {
                    $details = array();

                    if (strlen($rowData[53]) > 0)
                        array_push($details, array("ChoiceName" => $rowData[53], "ChoicePrice" => strlen($rowData[54]) > 0 ? $rowData[54] : 0));
                    if (strlen($rowData[55]) > 0)
                        array_push($details, array("ChoiceName" => $rowData[55], "ChoicePrice" => strlen($rowData[56]) > 0 ? $rowData[56] : 0));
                    if (strlen($rowData[57]) > 0)
                        array_push($details, array("ChoiceName" => $rowData[57], "ChoicePrice" => strlen($rowData[58]) > 0 ? $rowData[58] : 0));

                    $this->createModifier($itemid, $rowData[51], strtolower(trim($rowData[52])) == "ya", $details, $outlet, $perusahaanNo);
                }
            }

            $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', sprintf('%s : %s : DB transaction failed. Error no: %s, Error msg:%s, Last query: %s', __CLASS__, __FUNCTION__, $e->getCode(), $e->getMessage(), print_r($this->db->last_query(), TRUE)));

            echo "<h1>Cannot import anydata to Product. Please check excel again!</h1>";
        }

        redirect('produk/index');
    }

    public function ajaxImportBahan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $ItemName = $this->input->get_post('ItemName');
        $Category = $this->input->get_post('Category');
        $PurchasePrice = $this->input->get_post('PurchasePrice');
        $Unit = $this->input->get_post('Unit');
        $Foto = $this->input->get_post('Foto');

        $fileFoto = '';
        $extFoto = '';
        if ($Foto != '') {
            $extFoto = pathinfo($Foto, PATHINFO_EXTENSION);
            $fileFoto = base64_encode(file_get_contents($Foto));
        }

        $PerusahaanNo = getPerusahaanNo();
        $DeviceID = $Outlet;

        // Check Kategori
        $CategoryID = '';
        $CategoryDeviceNo = '';
        $insertCategory = $this->createNewCategory($Outlet, $Category, 'Dapur');
        if ($insertCategory['status'] == 200) {
            $CategoryID = $insertCategory['CategoryID'];
            $CategoryDeviceNo = $insertCategory['CategoryDeviceNo'];
        } else {
            $response = array(
                "status" => 400,
                "message" => 'Bahan gagal disimpan : <br> <small>Kategori gagal disimpan.</small>'
            );
            echo json_encode($response);
            // exit();
        }

        // Insert Bahan
        $IdBahan = $this->Masteritem->createItemBahan($ItemName, $Unit, $DeviceID, $PerusahaanNo, $PurchasePrice, $CategoryID, $CategoryDeviceNo);
        if ($IdBahan != '') {
            $response = array(
                'id' => $IdBahan,
                'Outlet' => $DeviceID,
                'ext' => $extFoto,
                'foto' => $fileFoto
            );

            echo json_encode($response);
            exit();
        } else {
            echo json_encode(array());
            exit();
        }
    }

    public function ajaxImportModifier()
    {
        $this->load->model('Mastermodifier');

        $Outlet = $this->input->get_post('Outlet');
        $ModifierName = $this->input->get_post('ModifierName');
        $ChooseOneOnly = $this->input->get_post('ChooseOneOnly');
        $CanAddQuantity = $this->input->get_post('CanAddQuantity');
        $ChoiceName = $this->input->get_post('ChoiceName');
        $QtyNeed = $this->input->get_post('QtyNeed');
        $ChoicePrice = $this->input->get_post('ChoicePrice');

        $where = array(
            "DeviceID" => $Outlet,
            "PerusahaanNo" => getPerusahaanNo(),
            "ModifierName" => $ModifierName
        );

        if ($this->Mastermodifier->isExistModifierName($where)) {
            $response =  array(
                "status" => 400,
                "message" => "Nama Pilihan Ekstra sudah digunakan"
            );
            echo json_encode($response);
            exit();
        }

        if (empty(array_filter($ChoiceName))) {
            $response =  array(
                "status" => 400,
                "message" => "Detail Pilihan Ekstra belum diisi"
            );
            echo json_encode($response);
            exit();
        }

        // create array detail
        $params = array(
            "ModifierName" => $ModifierName,
            "ChooseOneOnly" => $ChooseOneOnly,
            "CanAddQuantity" => !$CanAddQuantity ? 0 : $CanAddQuantity,
            "DeviceID" => $Outlet,
            "PerusahaanNo" => getPerusahaanNo()
        );

        $rs_detail = array();
        foreach ($ChoiceName as $key => $value) {
            if ($value == '' || empty($value)) {
                continue;
            }

            if ($ChoicePrice[$key] == '') {
                continue;
            }

            $price = $ChoicePrice[$key];
            $price = str_replace('Rp', '', $price);
            $price = str_replace(' ', '', $price);
            $price = str_replace('.', '', $price);
            $price = str_replace(',', '.', $price);

            $rs_detail[$key]['ChoiceName'] = $value;
            $rs_detail[$key]['ChoicePrice'] = $price;
            $rs_detail[$key]['QtyNeed'] = $QtyNeed[$key] == '' ? 0 : $QtyNeed[$key];
            $rs_detail[$key]['DeviceID'] = $Outlet;
            $rs_detail[$key]['PerusahaanNo'] = getPerusahaanNo();
            $rs_detail[$key]['Varian'] = 'Nuta';
        }

        $insert = $this->Mastermodifier->insertModifier($params, $rs_detail);
        if ($insert) {
            $response =  array(
                "status" => 200,
                "message" => "Pilihan Ekstra berhasil disimpan"
            );
            echo json_encode($response);
            exit();
        } else {
            $response =  array(
                "status" => 400,
                "message" => "Pilihan Ekstra gagal disimpan"
            );
            echo json_encode($response);
            exit();
        }
    }

    public function ajaxImportItems()
    {
        $SelectedOutlet = $this->input->get_post('SelectedOutlet');
        $Outlet = $this->input->get_post('Outlet');
        $ItemName = $this->input->get_post('ItemName');
        $Barcode = $this->input->get_post('Barcode');
        $Category = $this->input->get_post('Category');
        $Unit = $this->input->get_post('Unit');
        $SellPrice = $this->input->get_post('SellPrice');
        $PurchasePrice = $this->input->get_post('PurchasePrice');
        $Foto = $this->input->get_post('Foto');

        $fileFoto = '';
        $extFoto = '';
        if ($Foto != '') {
            $extFoto = pathinfo($Foto, PATHINFO_EXTENSION);
            $fileFoto = base64_encode(file_get_contents($Foto));
        }

        // Check Kategori
        $CategoryID = '';
        $CategoryDeviceNo = '';
        $insertCategory = $this->createNewCategory($Outlet, $Category, 'Dapur');
        if ($insertCategory['status'] == 200) {
            $CategoryID = $insertCategory['CategoryID'];
            $CategoryDeviceNo = $insertCategory['CategoryDeviceNo'];
        } else {
            $response = array(
                "status" => 400,
                "message" => 'Produk gagal disimpan : <br> <small>Kategori baru gagal disimpan.</small>'
            );
            echo json_encode($response);
            exit();
        }

        // Variasi Harga
        $rsVarianName = $this->input->get_post('VarianName');
        $rsVarianSellPrice = $this->input->get_post('VarianSellPrice');
        $arrVariant = array();

        if (count($rsVarianSellPrice) > 1) {
            $SellPrice = min($rsVarianSellPrice);

            foreach ($rsVarianName as $i => $VarianName) {
                $arrVariant[$i]['VarianName'] = $VarianName;
                $arrVariant[$i]['SellPrice'] = $rsVarianSellPrice[$i];
                $arrVariant[$i]['VarianKey'] = $VarianName . '#' . $rsVarianSellPrice[$i];
                $arrVariant[$i]['IsReguler'] = $rsVarianSellPrice[$i] == $SellPrice ? 1 : 0;
            }
        } else {
            $SellPrice = $this->number_db_format($SellPrice);
        }

        // Bahan Penyusun
        $rsIngredientsName = $this->input->get_post('IngredientsName');
        $rsIngredientsQty = $this->input->get_post('IngredientsQty');
        $rsIngredientsUnit = $this->input->get_post('IngredientsUnit');
        $rsIngredientsPurchasePrice = $this->input->get_post('IngredientsPurchasePrice');
        $arrIngredients = array();
        if ($rsIngredientsName) {
            foreach ($rsIngredientsName as $i => $IngredientsName) {
                $arrIngredients[$i]['ItemName'] = $IngredientsName;
                $arrIngredients[$i]['Unit'] = $rsIngredientsUnit[$i];
                $arrIngredients[$i]['PurchasePrice'] = $rsIngredientsPurchasePrice[$i];
                $arrIngredients[$i]['QtyNeed'] = $rsIngredientsQty[$i];
            }
        }

        // Pilihan Ekstra
        $rsModifierName = $this->input->get_post('ModifierName');
        $arrModifier = array();
        if ($rsModifierName) {
            $this->load->model("Mastermodifier");
            foreach ($rsModifierName as $i => $ModifierName) {
                $Modifier = $this->Mastermodifier->getDetailModifierArray(array('DeviceID' => $Outlet, 'ModifierName' => $ModifierName));
                if ($Modifier) {
                    $arrModifier[$i]['ModifierID'] = $Modifier['ModifierID'];
                    $arrModifier[$i]['ModifierDeviceNo'] = $Modifier['DeviceNo'];
                    $arrModifier[$i]['ModifierKey'] = $Modifier['DeviceNo'] . '#' . $Modifier['ModifierID'];
                }
            }
        }

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'ItemName' => $ItemName,
            'Barcode' => !$Barcode ? '' : $Barcode,
            'Unit' => $Unit,
            'CategoryID' => $CategoryID,
            'CategoryDeviceNo' => $CategoryDeviceNo,
            'SellPrice' => $SellPrice,
            'PurchasePrice' => $this->number_db_format($PurchasePrice),
            'IsProduct' => 'true'
        );

        $insert = $this->Masteritem->insertMasterItem($params, $arrVariant, $arrIngredients, $arrModifier, $SelectedOutlet);
        $insert['dataFoto'] = array(
            'ext' => $extFoto,
            'foto' => $fileFoto
        );
        echo json_encode($insert);
        exit();
    }

    public function ajaxInsertLogImport()
    {
        $Outlet = $this->input->get_post('Outlet');
        $fileName = $this->input->get_post('fileName');
        $arrDetail = $this->input->get_post('Details');
        $success  = $this->input->get_post('success');
        $failed  = $this->input->get_post('failed');

        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'OutletID' => $Outlet,
            'FileName' => $fileName
        );

        if ($arrDetail) {
            $params['ImportStatus'] = 'Gagal';
            $params['Note'] = 'Terdapat kesalahan pada beberapa baris. Silakan lihat detail.';
        } else {
            $params['ImportStatus'] = 'Berhasil';
            $params['Note'] = $success . ' produk berhasil diupload.';
        }

        $insertID = $this->Masteritem->insertImportItemHistory($params);
        if ($insertID != '') {
            if ($arrDetail) {
                foreach ($arrDetail as $Detail) {
                    $this->Masteritem->insertImportItemHistoryDetail(array('HistoryID' => $insertID, 'NoteDetail' => $Detail));
                }

                $response = array(
                    'status' => 200,
                    'message' => 'Import Produk Selesai. Riwayat Import Produk Tersimpan.'
                );
                echo json_encode($response);
                exit();
            }

            $response = array(
                'status' => 200,
                'message' => 'Import Produk Selesai. Riwayat Import Produk Tersimpan.'
            );
            echo json_encode($response);
            exit();
        }
    }

    public function ajaxDeleteMasterItem()
    {
        $Outlet = $this->input->get_post('Outlet');
        $ItemID = $this->input->get_post('ItemID');
        $DeviceNo = $this->input->get_post('DeviceNo');
        $IsProduct = $this->input->get_post('IsProduct');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'ItemID' => $ItemID,
            'DeviceNo' => $DeviceNo,
            'IsProduct' => $IsProduct
        );

        $delete =  $this->Masteritem->deleteMasterItem($where);
        if ($delete == true) {
            $response = array('status' => 200);
        } else {
            $response = array('status' => 400);
        }

        echo json_encode($response);
        exit();
    }

    public function ajax_get_produk_by_outlet()
    {
        $Outlet = $this->input->get_post('Outlet');

        $result = $this->Masteritem->getMasterItem(array('DeviceID' => $Outlet));
        echo json_encode($result);
        exit();
    }

    public function ajax_get_history_import()
    {
        $Outlet = $this->input->get_post('Outlet');

        $result = $this->Masteritem->getImportItemHistory($Outlet);
        echo json_encode($result);
        exit();
    }

    public function ajax_get_history_detail()
    {
        $HistoryID = $this->input->get_post('HistoryID');

        $result = $this->Masteritem->getImportItemHistoryDetail($HistoryID);
        echo json_encode($result);
        exit();
    }

    private function createItem($itemname, $imagelink, $categoryid, $satuan, $outlet, $perusahaanno, $isproduct, $isproducthasingredients, $sellprice, $purchaseprice)
    {
        $this->load->model('Masteritem');

        $tmp = explode('.', $categoryid);
        $categoryid = $tmp[0];
        $categoryDeviceNo = $tmp[1];

        $deviceno = 0; // from web

        $itemid = $this->Masteritem->getMaxItemID($outlet);
        $res = $this->db->query("INSERT INTO masteritem (ItemID, ItemName, ImageLink, CategoryID, CategoryDeviceNo, Unit, DeviceID, DeviceNo, PerusahaanNo, IsDetailSaved, IsProduct, IsProductHasIngredients, SellPrice, PurchasePrice, Barcode, Stock, BeginningStock, BeginningCogs, TaxPercent, SellPriceIncludeTax, SplitPosition, OnlineImagePath, Varian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'true', ?, ?, ?, ?, '', ?, 0, 0, 0, 'false', 1, '', 'Nuta')", array(
            $itemid,
            $itemname,
            $imagelink,
            $categoryid,
            $categoryDeviceNo,
            $satuan,
            $outlet,
            $deviceno,
            $perusahaanno,
            $isproduct ? 'true' : 'false',
            $isproducthasingredients ? 'true' : 'false',
            $sellprice,
            $purchaseprice,
            $isproducthasingredients ? 'true' : 'false'
        ));
        if (!$res) {
            $db_error = $this->db->error();
            if (!empty($db_error)) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        }

        return $itemid . "." . $deviceno;
    }

    private function createCategory($categoryname, $outlet, $perusahaanno)
    {
        $this->load->model('Kategori');

        $deviceno = 0; // web

        $categoryid = $this->Kategori->getMaxCategoriID($outlet, $perusahaanno);
        $tabNumber = $this->Kategori->getMaxTabNumberInSale($outlet, $perusahaanno);
        $res = $this->db->query("INSERT INTO mastercategory (CategoryID, CategoryName, TabNumberInSale, Varian, DeviceID, DeviceNo, PerusahaanNo) VALUES (?, ?, ?, 'Nuta', ?, ?, ?)", array(
            $categoryid,
            $categoryname,
            $tabNumber,
            $outlet,
            $deviceno,
            $perusahaanno
        ));
        if (!$res) {
            $db_error = $this->db->error();
            if (!empty($db_error)) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        }

        return $categoryid . "." . $deviceno;
    }

    private function createVarian($id, $varianname, $sellprice, $outlet, $perusahaanno, $isreguler = false)
    {
        $this->load->model('Mastervarian');

        $tmp = explode('.', $id);
        $itemid = $tmp[0];
        $itemdeviceno = $tmp[1];

        $deviceno = 0; // from web

        $varianid = $this->Mastervarian->generateNewID($outlet);
        $res = $this->db->query("INSERT INTO mastervariant (VarianID, ItemID, ItemDeviceNo, VarianName, SellPrice, DeviceID, DeviceNo, PerusahaanNo, IsReguler, Varian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Nuta')", array(
            $varianid,
            $itemid,
            $itemdeviceno,
            $varianname,
            $sellprice,
            $outlet,
            $deviceno,
            $perusahaanno,
            $isreguler ? 1 : 0
        ));
        if (!$res) {
            $db_error = $this->db->error();
            if (!empty($db_error)) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        }

        return $varianid;
    }

    private function createIngredient($id, $ingredientname, $ingredientunit, $qty, $outlet, $perusahaanno, $number)
    {
        $this->load->model('Masteritem');

        $tmp = explode('.', $id);
        $itemid = $tmp[0];
        $itemdeviceno = $tmp[1];

        $itemdetailingredientsid = $this->Masteritem->getMaxIngredientDetailID($outlet);

        $ingredientid = $this->createItem($ingredientname, '', '0.0', $ingredientunit, $outlet, $perusahaanno, false, false, 0, 0);
        $tmp = explode('.', $ingredientid);
        $ingredientid = $tmp[0];
        $ingredientdeviceno = $tmp[1];

        $deviceno = 0; // from web

        $res = $this->db->query("INSERT INTO masteritemdetailingredients (DetailID, ItemID, ItemDeviceNo, IngredientsID, IngredientsDeviceNo, QtyNeed, Varian, DeviceID, DeviceNo, PerusahaanNo, DetailNumber) VALUES (?, ?, ?, ?, ?, ?, 'Varian', ?, ?, ?, ?)", array(
            $itemdetailingredientsid,
            $itemid,
            $itemdeviceno,
            $ingredientid,
            $ingredientdeviceno,
            (float) $qty,
            $outlet,
            $deviceno,
            $perusahaanno,
            $number
        ));
        if (!$res) {
            $db_error = $this->db->error();
            if (!empty($db_error)) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        }
    }

    private function createModifier($id, $modifiername, $chooseOnlyOne, $details, $outlet, $perusahaanno)
    {
        $this->load->model('Mastermodifier');
        $this->load->model('Masteritem');

        $tmp = explode('.', $id);
        $itemid = $tmp[0];
        $itemdeviceno = $tmp[1];

        $modifierid = $this->Mastermodifier->generateModifierID($outlet);
        $modifierdeviceno = 0; // from web
        $detailid = $this->Mastermodifier->generatePilihanID($outlet);
        $itemdetailmodifierid = $this->Masteritem->getMaxModifierDetailID($outlet);

        $deviceno = 0; // from web

        $res = $this->db->query("INSERT INTO mastermodifier (ModifierID, ModifierName, ChooseOneOnly, Varian, DeviceID, DeviceNo, PerusahaanNo, IsDetailsSaved) VALUES (?, ?, ?, 'Nuta', ?, ?, ?, 1)", array(
            $modifierid,
            $modifiername,
            $chooseOnlyOne ? 1 : 0,
            $outlet,
            $deviceno,
            $perusahaanno
        ));

        if(!$res) {
            $db_error = $this->db->error();
            if (!empty($db_error)) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        }

        for ($i=0; $i<count($details); $i++) {
            $d = $details[$i];
            $res = $this->db->query("INSERT INTO mastermodifierdetail (DetailID, ModifierID, ModifierDeviceNo, ChoiceName, ChoicePrice, Varian, DeviceID, DeviceNo, PerusahaanNo) VALUES (?, ?, ?, ?, ?, 'Nuta', ?, ?, ?)", array(
                $detailid++,
                $modifierid,
                $modifierdeviceno,
                $d["ChoiceName"],
                $d["ChoicePrice"],
                $outlet,
                $deviceno,
                $perusahaanno
            ));
            if (!$res) {
                $db_error = $this->db->error();
                if (!empty($db_error)) {
                    throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
                }
            }
        }

        $res = $this->db->query("INSERT INTO masteritemdetailmodifier (DetailID, ItemID, ItemDeviceNo, ModifierID, ModifierDeviceNo, Varian, DeviceID, DeviceNo, PerusahaanNo) VALUES (?, ?, ?, ?, ?, 'Nuta', ?, ?, ?)", array(
            $itemdetailmodifierid,
            $itemid,
            $itemdeviceno,
            $modifierid,
            $modifierdeviceno,
            $outlet,
            $deviceno,
            $perusahaanno
        ));

        if (!$res) {
            $db_error = $this->db->error();
            if (!empty($db_error)) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        }
    }
    public function item_export_template($OutletID){
        $this->load->model('masteritem');
        $this->load->model('Masteritemingredients');
        $this->load->model('Mastermodifier');

        $itemData = $this->masteritem->getMasterItemExport($OutletID);
        $ingredientsData = $this->Masteritemingredients->getItemsIngredientExport($OutletID);
        $komposisiIngredientsData = $this->masteritem->getKomposisiItemExport($OutletID);
        $modifierData = $this->Mastermodifier->getDetailModifierExport($OutletID);
        $komposisiModifierData = $this->Mastermodifier->getKomposisiModifierExport($OutletID);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Nuta Cloud')
            ->setLastModifiedBy('Nuta Cloud')
            ->setTitle('Nuta Cloud Export Document')
            ->setSubject('Nuta Cloud Export Document')
            ->setDescription('Nuta Cloud')
            ->setKeywords('Nuta Cloud')
            ->setCategory('Nuta Cloud');
        $sheetIndex = 0;

        if (!empty($itemData)) {
            $spreadsheet->setActiveSheetIndex($sheetIndex)
                ->setCellValue('A1', 'Nama Produk')
                ->setCellValue('B1', 'Nama Varian')
                ->setCellValue('C1', 'Foto')
                ->setCellValue('D1', 'Kategori')
                ->setCellValue('E1', 'Satuan')
                ->setCellValue('F1', 'Barcode')
                ->setCellValue('G1', 'Harga Jual')
                ->setCellValue('H1', 'Harga Beli')
                ->setCellValue('I1', 'Mengandung Bahan')
                ->setCellValue('J1', 'Mengandung Pilihan Ekstra');
            $row = 2;
            foreach ($itemData as $item) {
                $spreadsheet->setActiveSheetIndex($sheetIndex)
                    ->setCellValue('A'.$row, $item['ItemName'])
                    ->setCellValue('B'.$row, $item['VarianName'])
                    ->setCellValue('C'.$row, $item['OnlineImagePath'])
                    ->setCellValue('D'.$row, $item['CategoryName'])
                    ->setCellValue('E'.$row, $item['Unit'])
                    ->setCellValue('F'.$row, $item['Barcode'])
                    ->setCellValue('G'.$row, $item['SellPrice'])
                    ->setCellValue('H'.$row, $item['PurchasePrice'])
                    ->setCellValue('I'.$row, $item['HasIngredients'])
                    ->setCellValue('J'.$row, $item['IsProductHasModifiers']);
                $row++;
            }
            $spreadsheet->getActiveSheet()->setTitle('Produk');
            $sheetIndex++;
        }

        if (!empty($ingredientsData)) {
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($sheetIndex)
                ->setCellValue('A1', 'Nama Bahan')
                ->setCellValue('B1', 'Foto')
                ->setCellValue('C1', 'Kategori')
                ->setCellValue('D1', 'Satuan')
                ->setCellValue('E1', 'Harga Beli');
            $row = 2;
            foreach ($ingredientsData as $ingredients) {
                $spreadsheet->setActiveSheetIndex($sheetIndex)
                    ->setCellValue('A'.$row, $ingredients['Bahan'])
                    ->setCellValue('B'.$row, $ingredients['OnlineImagePath'])
                    ->setCellValue('C'.$row, $ingredients['CategoryName'])
                    ->setCellValue('D'.$row, $ingredients['Unit'])
                    ->setCellValue('E'.$row, $ingredients['PurchasePrice']);
                $row++;
            }
            $spreadsheet->getActiveSheet()->setTitle('Bahan');
            $sheetIndex++;
        }

        if (!empty($komposisiIngredientsData)) {
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($sheetIndex)
                ->setCellValue('A1', 'Nama Produk')
                ->setCellValue('B1', 'Nama Bahan')
                ->setCellValue('C1', 'Qty Dibutuhkan')
                ->setCellValue('D1', 'Satuan')
                ->setCellValue('E1', 'Harga Beli')
                ->setCellValue('F1', 'Sub Total');
            $sheet = $spreadsheet->getActiveSheet();
            $row = 2;
            foreach($komposisiIngredientsData as $index => $value){
                $sheet->setCellValue('A' . $row, $value['NamaProduk']);
                $sheet->setCellValue('B' . $row, $value['NamaBahan']);
                $sheet->setCellValue('C' . $row, $value['Quantity']);
                $sheet->setCellValue('D' . $row, $value['Unit']);
                $sheet->setCellValue('E' . $row, $value['PurchasePrice']);
                $sheet->setCellValue('F' . $row, $value['Quantity'] * $value['PurchasePrice']);

                $row++;
            }
            $spreadsheet->getActiveSheet()->setTitle('Komposisi Bahan');
            $sheetIndex++;
        }

        if (!empty($modifierData)) {
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($sheetIndex)
                ->setCellValue('A1', 'Nama Kelompok Pilihan Ekstra')
                ->setCellValue('B1', 'Pelanggan hanya bisa pilih satu ekstra')
                ->setCellValue('C1', 'Pelanggan bisa menambah jumlah per pilihan')
                ->setCellValue('D1', 'Nama Pilihan')
                ->setCellValue('E1', 'Harga Pilihan')
                ->setCellValue('F1', 'Qty Dibutuhkan')
                ->setCellValue('G1', 'Satuan');
            $sheet = $spreadsheet->getActiveSheet();
            $row = 2;
            foreach($modifierData as $index => $value){
                $sheet->setCellValue('A' . $row, $value['ModifierName']);
                $sheet->setCellValue('B' . $row, $value['ChooseOneOnly']);
                $sheet->setCellValue('C' . $row, $value['CanAddQuantity']);
                $sheet->setCellValue('D' . $row, $value['ChoiceName']);
                $sheet->setCellValue('E' . $row, $value['ChoicePrice']);
                $sheet->setCellValue('F' . $row, $value['QtyNeed']);
                $sheet->setCellValue('G' . $row, $value['Unit']);

                $row++;
            }
            $spreadsheet->getActiveSheet()->setTitle('Pilihan Extra');
            $sheetIndex++;
        }

        if (!empty($komposisiModifierData)) {
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($sheetIndex)
                ->setCellValue('A1', 'Nama Produk')
                ->setCellValue('B1', 'Pilihan Ekstra');
            $sheet = $spreadsheet->getActiveSheet();
            $row = 2;
            foreach($komposisiModifierData as $index => $value){
                $sheet->setCellValue('A' . $row, $value['ItemName']);
                $sheet->setCellValue('B' . $row, $value['ModifierName']);

                $row++;
            }
            $spreadsheet->getActiveSheet()->setTitle('Komposisi Pilihan Ekstra');
            $sheetIndex++;
        }

        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Template Produk Dengan Bahan.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function number_db_format($number)
    {
        if ($number == '') {
            return 0;
        }
        $number = str_replace('Rp', '', $number);
        $number = str_replace(' ', '', $number);
        $number = str_replace('.', '', $number);
        $number = str_replace(',', '.', $number);

        return $number;
    }

    private function check_new_outlet($OutletID){
        $this->load->model('Kategori');
        $CategoryCount = $this->Kategori->countKategoriByOutlet($OutletID);
        return $CategoryCount;
    }
}
