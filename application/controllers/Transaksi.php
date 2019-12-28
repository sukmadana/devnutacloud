
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends MY_Controller
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
//}
    }

    public function item()
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
        } else if(count($availableOutlets)==1) {
            $this->nutaquery->SetOutlet($this->default_outlet_id);
        }
        $query_master_item = $this->nutaquery->get_query_daftar_master_item(getPerusahaanNo());
        $query = $this->db->query($query_master_item);
        $result = $query->result();
        $fields = $query->field_data();
        $outletids = array();
        foreach ($availableOutlets as $k => $v) {
            array_push($outletids, $k);
        }
        $data['outletids'] = $outletids;

        $data['outlets'] = $availableOutlets;
        $data['selected_outlet'] = $this->nutaquery->getOutlet();
        $data['page_part'] = 'transaksi/transaksi_master_item';
        $data['js_part'] = array(
            "features/js/js_socket",
            "features/js/js_form",
            'features/js/js_grid_item',
            'features/js/js_datatable',
            'features/js/js_transaksi_master_item',
            'features/js/js_dialog_hapus_item');
        $data['js_chart'] = array();
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $data['menu'] = "produk";
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
            $pilihans = $this->Mastermodifier->getPilihan($ekstra['ModifierID'], $this->nutaquery->getOutlet());
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
            $iditem = $item->ItemID;
            $item_name = $item->ItemName;
            $kategori = $item->CategoryID;
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
        $data['page_part'] = 'transaksi/transaksi_form_master_item';
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
            'features/js/js_form_master_item',
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

    public function tesmodifier() {
        $this->load->model('Mastermodifier');
        $this->Mastermodifier->tesmodifier('Topping',2678);
    }
}