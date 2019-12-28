<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uang_keluar extends MY_Controller
{
    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();

        $this->load->model('Uangkeluarmodel');
    }


    public function simpanData()
    {

        $tanggal_sekarang = date("Y-m-d");
        $jam = date('H:i');
        $no_uang_keluar = $this->Uangkeluarmodel->get_no_transaksi();
        list($jumlah, $keterangan, $ambil_dari, $dibayar_ke, $jenis, $outlet) = $this->post_variables();
        $is_edit = $this->isEdit();
        if ($is_edit['boolean']) {
            $idTransaksi = $is_edit['id'];
            $this->Uangkeluarmodel->update($dibayar_ke, $jumlah, $keterangan, $jenis, $ambil_dari, $idTransaksi);
            redirect('Uang_keluar/data?outlet=' . $outlet . '&notify=1&src=Save');
        } else {

            $this->Uangkeluarmodel->insert($no_uang_keluar, $tanggal_sekarang, $jam, $ambil_dari, $dibayar_ke, $jenis, $keterangan, $jumlah, $outlet, getPerusahaanNo());
            redirect('Uang_keluar/data?outlet=' . $outlet . '&notify=1&src=Save');

        }
    }


    public function hapusData()
    {
        $transaksino = $this->input->get('no');
        $deviceid = $this->input->get('outlet');
        $this->Uangkeluarmodel->delete($transaksino, $deviceid);
        redirect('Uang_keluar/data?notify=1&src=Delete&outlet=' . $deviceid);
    }

    public function editData()
    {
        $nomer_uang_keluar = $this->input->get('no');
        $available_outlets = $this->GetOutletTanpaSemua();

        $this->setDefaultOutletId($available_outlets);
        $get_outlet = $this->input->get('outlet');
        if (isNotEmpty($get_outlet)) {
            $this->default_outlet_id = $get_outlet;
        }

        $data['outlets'] = $available_outlets;
        $data['selected_outlet'] = $this->default_outlet_id;

        $data['page_part'] = 'uang_keluar/form_uang_keluar_edit';

        $data['js_part'] = array();
        $data['js_chart'] = array();
        $data['datagrid'] = array('fields' => array());
        $data['visibilityMenu'] = $this->visibilityMenu;

        $data['uangKeluar'] = $this->db->get_where('cashbankout', ['TransactionNumber' => $nomer_uang_keluar, 'DeviceID' => $get_outlet]);

        $this->load->view('main_part', $data);


    }

    public function tambahData()
    {
        $available_outlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($available_outlets);
        $get_outlet = $this->input->get('outlet');
        if (isNotEmpty($get_outlet)) {
            $this->default_outlet_id = $get_outlet;
        }

        $data['outlets'] = $available_outlets;
        $data['selected_outlet'] = $this->default_outlet_id;

        $data['page_part'] = 'uang_keluar/form_uang_keluar';

        $data['js_part'] = array();
        $data['js_chart'] = array();
        $data['datagrid'] = array('fields' => array());
        $data['visibilityMenu'] = $this->visibilityMenu;


        $query_string = "
SELECT 
AccountID,
    CASE AccountType
        WHEN 1 THEN AccountName
        WHEN 2 THEN CONCAT(BankName , ' ', AccountNumber, ' ', AccountName)
    END AS AccountName
FROM
    nutacloud.mastercashbankaccount
WHERE
    DeviceID = " . $this->db->escape($this->default_outlet_id);
        $query_mastercashbank = $this->db->query($query_string);
        $mastercashbank = $query_mastercashbank->result();
        $data['mastercashbank'] = $mastercashbank;


        $this->load->view('main_part', $data);
    }

    public function data()
    {
        $this->load->library('CurrencyFormatter');

        $availableOutlets = $this->GetOutletTanpaSemua();
        if (count($availableOutlets) > 1) {
            $this->default_outlet_id = -999;
        } else {
            $this->setDefaultOutletId($availableOutlets);
        }
        $get_outlet = $this->input->get('outlet');
        if (isNotEmpty($get_outlet)) {
            $this->default_outlet_id = $get_outlet;

        }

        $query_master_item = $this->Uangkeluarmodel->get_query_uang_keluar2($this->default_outlet_id);

        $query = $this->db->query($query_master_item);
        $result = $query->result();
        $fields = $query->field_data();
        $notify = $this->input->get('notify');
        $pushToTablet = isset($notify);


        $outletids = array();
        foreach ($availableOutlets as $k => $v) {
            array_push($outletids, $k);
        }
        $js = array(
            'features/js/js_socket',
            'features/js/js_form',
            'features/js/js_grid_item',
            'features/js/js_datatable',
            'features/js/js_tambah_uang_keluar',
            'features/js/js_dialog_hapus_item',
        );
        if ($pushToTablet) {
            $data['src'] = $this->input->get('src');
            array_push($js, 'features/js/js_uang_keluar');
        }

        $data['outletids'] = $outletids;
        $data['outlets'] = $availableOutlets;
        $data['notify'] = $pushToTablet;
        $data['selected_outlet'] = $this->default_outlet_id;
        $data['page_part'] = 'uang_keluar/uang_keluar_tabel';
        $data['js_part'] = $js;
        $data['js_chart'] = array();
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part', $data);
    }

    /**
     * @return array
     */
    private function isEdit()
    {
        $id = $this->input->get('edit');
        return ['boolean' => isset($id), 'id' => $id];
    }

    /**
     * @return array
     */
    private function post_variables()
    {
        $jumlah = $this->input->post('jumlah');
        $keterangan = $this->input->post('keterangan');
        $ambil_dari = $this->input->post('ambilDari');
        $dibayar_ke = $this->input->post('dibayarke');
        $jenis = $this->input->post('optionsJenis');
        $outlet = $this->input->post('idOutlet');
        return array($jumlah, $keterangan, $ambil_dari, $dibayar_ke, $jenis, $outlet);
    }


}