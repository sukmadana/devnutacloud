<?php
defined('BASEPATH') or exit('No direct script access allowed');

class bahan extends MY_Controller
{

    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();
        $this->load->library('NutaQuery');
        $this->load->model('Userperusahaancabang');
        $this->load->model('Perusahaanmodel');
        $this->load->model('Masteritemingredients');
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
        $data['page_part'] = 'bahan/list_bahan';
        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_form",
            "features/js/js_form_validation",
            'features/js/js_bahan',
            'features/js/js_datatable'
        );
        $data['js_chart'] = array();
        $params = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $selected_outlet,
        );
        $data['totalBahan'] = $this->Masteritemingredients->getTotalIngredients($params);
        $data['rs_kategori'] = $this->Masteritemingredients->getMasterCategory($params);
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();

        $this->load->model('Options');
        $data['Options'] = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $selected_outlet));

        $data['menu'] = "produk";
        $data['title'] = "Nuta Cloud - Items - Bahan";
        $this->load->view('main_part', $data);
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
        $list_variasi_harga = [
            ['VarianName' => 'Reguler', 'SellPrice' => 0, 'IsReguler' => 1, 'Placeholder' => 'Reguler'],
            ['VarianName' => '', 'SellPrice' => 0, 'IsReguler' => 0, 'Placeholder' => 'misal: Cup Ukuran Kecil'],
            ['VarianName' => '', 'SellPrice' => 0, 'IsReguler' => 0, 'Placeholder' => 'misal: Cup Ukuran Besar']
        ];
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
            $list_variasi_harga[0]['SellPrice'] = $harga_jual;
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
                    if ($e['ModifierID'] == $d->ModifierID) {
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
        $data['page_part'] = 'bahan/form_bahan';
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
            'features/js/js_form_bahan',
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

    public function ajaxIngredients()
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
            'IsProduct' => 'false',
            'draw' => $draw,
            'start' => $start,
            'length' => $length,
            'order' => $order,
            'search' => $search,
        );
        $result = $this->Masteritemingredients->getDatatablesIngredients($params, $this->visibilityMenu);

        echo json_encode($result);
    }

    public function ajax_detail_by_ingredients()
    {
        $Outlet = $this->input->get_post('OutletID');
        $IngredientsID = $this->input->get_post('IngredientsID');
        $IngredientsDeviceNo = $this->input->get_post('IngredientsDeviceNo');

        $where = array(
            getPerusahaanNo(), $Outlet, $IngredientsID, $IngredientsDeviceNo
        );
        $ItemsIngredient = $this->Masteritemingredients->getItemsIngredient($where);
        echo json_encode($ItemsIngredient);
    }

    public function ajax_search_category()
    {
        $Outlet = $this->input->get_post('Outlet');
        $search = $this->input->get_post('search');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'CategoryName' => $search
        );
        $result = $this->Masteritemingredients->getSearchCategory($where);
        echo json_encode(array('results' => $result));
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
            'IsProduct' => 'false'
        );

        if ($id) {
            $where['ItemID <> '] = $id;
        }

        $result = $this->Masteritemingredients->isExistField($where);

        echo json_encode(array('valid' => !$result));
    }

    public function ajax_insert_bahan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $ItemName = $this->input->get_post('ItemName');
        // $CategoryID = $this->input->get_post('CategoryID');
        $CategoryName = $this->input->get_post('CategoryName');
        $Unit = $this->input->get_post('Unit');

        $PurchasePrice = $this->input->get_post('PurchasePrice');
        $PurchasePrice = str_replace('Rp', '', $PurchasePrice);
        $PurchasePrice = str_replace(' ', '', $PurchasePrice);
        $PurchasePrice = str_replace('.', '', $PurchasePrice);
        $PurchasePrice = str_replace(',', '.', $PurchasePrice);

        $this->load->model('Options');
        $Options = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $Outlet));
        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
        );

        if ($CategoryName != '' || !empty($CategoryName)) {
            // Check category
            $Category = $this->checkCategory(array_merge($where, array('CategoryName' => $CategoryName)));
        }


        $this->load->model('Masteritem');
        $ItemID = $this->Masteritem->getMaxItemID($Outlet, getPerusahaanNo());
        $RowNumber = $this->Masteritemingredients->getRowNumber(array_merge($where, array('IsProduct' => 'false')));

        $params = array(
            'ItemID' => $ItemID,
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'DeviceNo' => 0,
            'ItemName' => $ItemName,
            'Barcode' => '',
            'Stock' => 'true',
            'BeginningStock' => 0,
            'BeginningCogs' => 0,
            'Unit' => $Unit,
            'ImageLink' => '',
            'TaxPercent' => 0,
            'SellPriceIncludeTax' => 'false',
            'IsProduct' => 'false',
            'IsProductHasIngredients' => 'false',
            'SellPrice' => $PurchasePrice,
            'PurchasePrice' => $PurchasePrice,
            'RowNumber' => $RowNumber,
            'CreatedVersionCode' => $Options->CreatedVersionCode,
            'EditedVersionCode' => $Options->EditedVersionCode,
            'Varian' => $Options->Varian,
        );

        if ($CategoryName != '' || !empty($CategoryName)) {
            $params['CategoryID'] = $Category['CategoryID'];
            $params['CategoryDeviceNo'] = $Category['DeviceNo'];
        }

        $insert = $this->Masteritemingredients->insertBahan($params);

        if ($insert == true) {
            $response = array(
                'status' => 200,
                'message' => 'Bahan Berhasil Disimpan'
            );
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Bahan Gagal Disimpan'
            );
        }

        echo json_encode($response);
        exit();
    }

    public function ajax_update_bahan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $ItemID = $this->input->get_post('ItemID');
        $DeviceNo = $this->input->get_post('DeviceNo');
        $ItemName = $this->input->get_post('ItemName');
        $CategoryName = $this->input->get_post('CategoryName');
        $Unit = $this->input->get_post('Unit');
        $PurchasePrice = $this->input->get_post('PurchasePrice');
        $PurchasePrice = str_replace('Rp', '', $PurchasePrice);
        $PurchasePrice = str_replace(' ', '', $PurchasePrice);
        $PurchasePrice = str_replace('.', '', $PurchasePrice);
        $PurchasePrice = str_replace(',', '.', $PurchasePrice);

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'ItemID' => $ItemID,
            'DeviceNo' => $DeviceNo
        );

        $Bahan = $this->Masteritemingredients->getDetailBahan($where);
        $RowVersion = (intval($Bahan['RowVersion']) + 1);

        // Check category
        if ($CategoryName != '' || !empty($CategoryName)) {
            $Category = $this->checkCategory(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $Outlet, 'CategoryName' => $CategoryName));
        }


        $params = array(
            'ItemName' => $ItemName,
            'Unit' => $Unit,
            'SellPrice' => $PurchasePrice,
            'PurchasePrice' => $PurchasePrice,
            'RowVersion' => $RowVersion
        );

        if ($CategoryName != '' || !empty($CategoryName)) {
            $params['CategoryID'] = $Category['CategoryID'];
            $params['CategoryDeviceNo'] = $Category['DeviceNo'];
        } else {
            $params['CategoryID'] = 0;
            $params['CategoryDeviceNo'] = 1;
        }

        $update = $this->Masteritemingredients->updateBahan($params, $where);

        if ($update == true) {
            $response = array(
                'status' => 200,
                'message' => 'Bahan Berhasil Disimpan'
            );
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Bahan Gagal Disimpan'
            );
        }

        echo json_encode($response);
        exit();
    }

    public function ajax_delete_bahan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $ItemID = $this->input->get_post('ItemID');
        $DeviceNo = $this->input->get_post('DeviceNo');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'ItemID' => $ItemID,
            'DeviceNo' => $DeviceNo
        );

        $delete = $this->Masteritemingredients->deleteBahan($where);

        if ($delete) {
            $response = array(
                'status' => 200,
                'message' => 'Bahan Berhasil Dihapus'
            );
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Bahan Gagal Dihapus'
            );
        }

        echo json_encode($response);
    }

    public function ajax_get_detail_bahan()
    {
        $Outlet = $this->input->get_post('Outlet');
        $ItemID = $this->input->get_post('ItemID');
        $DeviceNo = $this->input->get_post('DeviceNo');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'ItemID' => $ItemID,
            'DeviceNo' => $DeviceNo
        );

        $result = $this->Masteritemingredients->getDetailBahan($where);
        $result['PurchasePrice'] = str_replace('.', ',', $result['PurchasePrice']);

        $this->load->model('Kategori');

        $where = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'DeviceID' => $Outlet,
            'CategoryID' => $result['CategoryID'],
            'DeviceNo' => $result['CategoryDeviceNo']
        );
        $Category = $this->Kategori->getDetailCategoryDynamic($where);
        $result['CategoryName'] = $Category['CategoryName'];

        echo json_encode($result);
    }

    private function checkCategory($where)
    {
        $this->load->model('Kategori');

        $Category = $this->Kategori->getDetailCategoryDynamic($where);
        if ($Category) {
            return $Category;
        }

        $insert = $this->Kategori->insertKategori($where['DeviceID'], $where['CategoryName'], 'Dapur', getPerusahaanNo());
        if ($insert['status'] == 200) {
            $response = array(
                "status" => 200,
                "message" => $insert['message'],
                "CategoryID" => $insert['CategoryID'],
                "DeviceNo" => $insert['CloudDevNo']
            );
            return $response;
        } else {
            return $insert;
        }
    }
}
