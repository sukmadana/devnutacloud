<?php
defined('BASEPATH') or exit('No direct script access allowed');

class extra extends MY_Controller
{

    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();
        $this->load->library('NutaQuery');
        $this->load->model('Userperusahaancabang');
        $this->load->model('Perusahaanmodel');
        $this->load->model('Mastermodifier');
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
        $this->load->model('Mastermodifier');
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
        $data['rs_modifier'] = $this->Mastermodifier->getDatatableListModifier($selected_outlet);
        $data['page_part'] = 'extra/list_extra';
        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_form",
            "features/js/js_form_validation",
            'features/js/js_extra',
            // 'features/js/js_grid_item',
            'features/js/js_datatable',
            // 'features/js/js_dialog_hapus_modifier'
        );
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();

        $this->load->model('Options');
        $data['Options'] = $this->Options->get_options(array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $selected_outlet));

        $data['menu'] = "produk";
        $data['title'] = "Nuta Cloud - Items - Pilihan Ekstra";
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
            $modifier = $this->Mastermodifier->getModifierByName($namaitem, $selected_outlet);
            $data['modifieredit'] = $modifier;
        }
        $outletids = array();
        foreach ($availableOutlets as $k => $v) {
            array_push($outletids, $k);
        }
        $data['outletids'] = $outletids;
        $data['outlets_by_item'] = $availableOutlets;
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
        $data['modeform'] = $mode;
        $data['kategories'] = $list_kategori;
        $data['satuans'] = $list_satuan;
        $data['variasi_harga'] = $list_variasi_harga;
        $data['pilihan_ekstra'] = $pilihan_ekstra;
        $data['list_pilihan_ekstra'] = $list_pilihan_ekstra;
        $data['selected_outlet'] = $this->nutaquery->getOutlet();
        $data['page_part'] = 'extra/form_extra';
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
            'features/js/js_form_extra',
            'features/js/js_dialog_simpan_modifier_beberapa_outlet',
            'features/js/js_dialog_hapus_satuan',
            'features/js/js_dialog_variasi_harga',
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
        $this->Mastermodifier->tesmodifier('Topping', 2669);
        //        $this->Mastermodifier->tesmodifier('Topping',2678);
    }

    public function ajaxmodifier()
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
        $result = $this->Mastermodifier->getDatatablesModifier($params, $this->visibilityMenu);

        echo json_encode($result);
    }

    public function ajax_detail_by_modifier()
    {
        $ModifierID = $this->input->get_post('ModifierID');
        $DeviceNo = $this->input->get_post('DeviceNo');
        $OutletID = $this->input->get_post('OutletID');

        $rs_detail = $this->Mastermodifier->getListItemsByModifier($ModifierID, $DeviceNo, $OutletID);
        echo json_encode($rs_detail);
    }

    public function ajax_get_detail_modifier()
    {
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

    public function ajax_create_modifier()
    {
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

    public function ajax_update_modifier()
    {
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

    public function ajax_delete_modifier()
    {
        $Outlet = $this->input->post('outlet');
        $ModifierID = $this->input->post('ModifierID');
        $DeviceNo = $this->input->post('DeviceNo');

        $where = array('ModifierID' => $ModifierID, 'DeviceID' => $Outlet, 'PerusahaanNo' => getPerusahaanNo(), 'DeviceNo' => $DeviceNo);
        $delete = $this->Mastermodifier->deleteModifier($where);
        if ($delete == true) {
            $response = array(
                "status" => 200,
                "message" => "Pilihan Ekstra Berhasil Dihapus"
            );
            echo json_encode($response);
            exit();
        } else {
            $response = array(
                "status" => 400,
                "message" => "Pilihan Ekstra Gagal Dihapus"
            );
            echo json_encode($response);
            exit();
        }
    }

    public function ajax_items_belum_diterapkan()
    {
        $Outlet = $this->input->get_post('outlet');
        $ModifierID = $this->input->get_post('ModifierID');
        $DeviceNo = $this->input->get_post('DeviceNo');

        $result = $this->Mastermodifier->getListProdukBelumTerapkan(array($ModifierID, $DeviceNo, getPerusahaanNo(), $Outlet));
        echo json_encode(array('data' => $result));
    }

    public function ajax_count_items_modifier()
    {
        $Outlet = $this->input->get_post('outlet');
        $ModifierID = $this->input->get_post('ModifierID');
        $DeviceNo = $this->input->get_post('DeviceNo');
        $total = $this->Mastermodifier->countItemsModifier(array($ModifierID, $DeviceNo, getPerusahaanNo(), $Outlet));

        $response = array(
            "status" => 200,
            "total" => intval($total)
        );
        echo json_encode($response);
        exit();
    }

    public function ajax_terapkan_produk()
    {
        $Outlet = $this->input->post('outlet');
        $ModifierID = $this->input->post('ModifierID');
        $DeviceNo = $this->input->post('DeviceNo');
        $ItemsID = $this->input->post('itemsID');
        $ItemsDeviceNo = $this->input->post('itemsDeviceNo');
        $rsItemsModifier = array();
        if (!empty($ItemsID)) {
            foreach ($ItemsID as $i => $ItemID) {
                $rsItemsModifier[$i]['ItemID'] = $ItemID;
                $rsItemsModifier[$i]['ItemDeviceNo'] = $ItemsDeviceNo[$i];
                $rsItemsModifier[$i]['ItemKey'] = $ItemsDeviceNo[$i] . "-" . $ItemID;
            }
        }


        // Terapkan Produk
        $where = array(
            'ModifierID' => $ModifierID,
            'DeviceID' => $Outlet,
            'DeviceNo' => $DeviceNo,
            'PerusahaanNo' => getPerusahaanNo()
        );
        $insert = $this->Mastermodifier->insertItemModifier($where, $rsItemsModifier);

        if ($insert == true) {
            $modifier = $this->Mastermodifier->getDetailModifier($where);

            $response = array(
                "status" => 200,
                "message" => "Pilihan Ekstra " . $modifier->ModifierName . " Berhasil Disimpan"
            );
        } else {
            $response = array(
                "status" => 400,
                "message" => "Pilihan Ekstra Gagal Disimpan"
            );
        }

        echo json_encode($response);
        exit();
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
            $where['ModifierID <> '] = $id;
        }

        $result = $this->Mastermodifier->isExistField($where);

        echo json_encode(array('valid' => !$result));
    }
}
