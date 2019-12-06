<?php

class Usertablet extends CI_Model
{

    var $_tableName = "usertablet";
    protected $_dbMaster;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    protected function initDbMaster()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function Create($data)
    {
        $this->initDbMaster();
        return $this->_dbMaster->insert($this->_tableName, $data);
    }

    public function getListUser($perusahaanno, $outletid)
    {
        $this->initDbMaster();
        $query = $this->db->order_by('Username')->get_where($this->_tableName, array('PerusahaanNo' => $perusahaanno, 'DeviceID' => $outletid));
        return $query->result();
    }

    public function countListUser($perusahaanno, $outletid)
    {
        $this->db->get_where($this->_tableName, array('PerusahaanNo' => $perusahaanno, 'DeviceID' => $outletid));
        return $this->db->count_all_results();
    }

    public function getUser($username, $perusahaanno, $outletid)
    {
        $this->initDbMaster();
        $query = $this->db->get_where($this->_tableName, array('Username' => $username, 'PerusahaanNo' => $perusahaanno, 'DeviceID' => $outletid));
        return $query->row();
    }

    public function isUsernameExist($username, $perusahaanno)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanNo' => $perusahaanno, 'username' => $username));
        $count = $query->num_rows();
        return $count >= 1;
    }

    public function isEmailExist($email, $perusahaanno)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanNo' => $perusahaanno, 'email' => $email));
        $count = $query->num_rows();
        return $count >= 1;
    }

    public function checkOldPassword($oldpassword, $username, $perusahaanno, $outletid)
    {
        $this->initDbMaster();
        $query = $this->db->get_where($this->_tableName, array('Password' => $oldpassword, 'Username' => $username, 'PerusahaanNo' => $perusahaanno, 'DeviceID' => $outletid));
        $count = $query->num_rows();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getLastID()
    {
        $this->initDbMaster();

        $this->db->select('UserID');
        $this->db->from($this->_tableName);
        $this->db->order_by('UserID', 'desc');
        $query = $this->db->get();
        return $query->row()->UserID;
    }

    public function updateHakAkses($kolom, $value, $username, $perusahaanno, $outletid, $push = true)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('Username' => $username, 'PerusahaanNo' => $perusahaanno, 'DeviceID' => $outletid));
        $this->_dbMaster->update($this->_tableName, array($kolom => $value));
        $this->updateRowVersion($username, $perusahaanno, $outletid);

        // Push to firebase
        if ($push) {
            $usertablet = $this->_dbMaster->get_where($this->_tableName, array(
                "Username" => $username,
                "PerusahaanNo" => $perusahaanno,
                "DeviceID" => $outletid,
            ));
            $data = $usertablet->row();
            $this->pushFirebaseCreateOrUpdate($data->UserID, $data->PerusahaanNo, $data->DeviceID);
        }
    }

    public function updateRowVersion($username, $perusahaanno, $outletid)
    {
        $this->_dbMaster->set('RowVersion', 'RowVersion + 1', false);
        $this->_dbMaster->where('Username', $username);
        $this->_dbMaster->where('PerusahaanNo', $perusahaanno);
        $this->_dbMaster->where('DeviceID', $outletid);
        $this->_dbMaster->update($this->_tableName);
    }

    public function delete($username, $perusahaanno, $outletid)
    {
        $this->initDbMaster();
        $query_deleting_item = $this->_dbMaster->get_where('usertablet', array('Username' => $username, 'PerusahaanNo' => $perusahaanno, 'DeviceID' => $outletid));
        $deleting_items = $query_deleting_item->result_array();

        $deleted_data = array(
            "table" => 'usertabletdelete',
            "column" => array(
                "Username" => $deleting_items[0]['Username'],
                "PerusahaanNo" => $deleting_items[0]['PerusahaanNo'],
                "DeviceID" => $deleting_items[0]['DeviceID'],
            ),
        );

        /* Cek usertabletdelete */
        $query_is_exist_in_delete_table = $this->_dbMaster->get_where('usertabletdelete', array('UserID' => $deleting_items[0]['UserID'], 'PerusahaanNo' => $perusahaanno, 'DeviceID' => $outletid));
        $exist_in_delete_table = $query_is_exist_in_delete_table->num_rows() > 0;

        if ($exist_in_delete_table) {
            $this->_dbMaster->where(array('UserID' => $deleting_items[0]['UserID'], 'PerusahaanNo' => $perusahaanno, 'DeviceID' => $outletid));
            $this->_dbMaster->delete('usertabletdelete');
        }

        $this->_dbMaster->insert('usertabletdelete', $deleting_items[0]);

        $deleted = $this->_dbMaster->delete('usertablet', array('Username' => $username, 'PerusahaanNo' => $perusahaanno, 'DeviceID' => $outletid));
        if ($deleted) {
            $this->pushFirebaseDelete('deleteusertablet', $deleting_items[0]);
            return true;
        } else {
            return false;
        }
    }

    // Push to firebase
    public function pushFirebaseCreateOrUpdate($UserID, $PerusahaanNo, $DeviceID)
    {

        //if ($result_insert_masterpromo) {
        //push to firebase
        $insert_query = $this->_dbMaster->get_where($this->_tableName, array(
            "UserID" => $UserID,
            "PerusahaanNo" => $PerusahaanNo,
            "DeviceID" => $DeviceID,
        ));
        $last_insert_data = array(
            "table" => $this->_tableName,
            "column" => $insert_query->row_array(),
        );
        $this->load->model('Firebasemodel');
        $this->Firebasemodel->push_firebase($DeviceID, $last_insert_data, $UserID, 0, getPerusahaanNo(), 0);
        //}
    }

    public function pushFirebaseDelete($table, $deleted_data)
    {

        //if ($result_insert_masterpromo) {
        //push to firebase
        $last_insert_data = array(
            "table" => $table,
            "column" => $deleted_data,
        );
        $this->load->model('Firebasemodel');
        $this->Firebasemodel->push_firebase($deleted_data['DeviceID'], $last_insert_data, $deleted_data['UserID'], 0, getPerusahaanNo(), 0);
        //}
    }

    public function pushFirebaseCreateOrUpdateWithProgress($perusahaanNo, $outletID, $UserID, $token, $proses, $jumlahSemuaProses)
    {

        //if ($result_insert_masterpromo) {
        //push to firebase
        $insert_query = $this->_dbMaster->get_where($this->_tableName, array(
            "PerusahaanNo" => $perusahaanNo,
            "DeviceID" => $outletID,
            "PromoID" => $UserID,
        ));
        $last_insert_data = array(
            "table" => $this->_tableName,
            "column" => $insert_query->row_array(),
        );
        $this->load->model('Outlet');
        $dataoutlet = $this->Outlet->getOutletByIdOnly($outletID);
        $namaoutlet = $dataoutlet->NamaOutlet . " - " . $dataoutlet->AlamatOutlet;
        $this->load->model('Firebasemodel');
        //$this->Firebasemodel->push_firebase($outletID, $last_insert_data, $promoID, 0, getPerusahaanNo(), 0);
        $proses = $this->Firebasemodel->push_firebase_withprogres($outletID, $namaoutlet, $last_insert_data, $promoID, 0, getPerusahaanNo(), 0, $token, $proses, $jumlahSemuaProses);
        return $proses;
        //}
    }

    public function getAllHakAkses($access)
    {
        // Array Parent
        $rs_hak_akses = array(
            array(
                'level' => '1', 'label' => 'Penjualan',
                'detail' => array(
                    'AllowKasir' => array('akses' => 'Kasir', 'allow' => (!$access ? 0 : $access->AllowKasir)),
                    'AllowEditNamaStand' => array('akses' => 'Ubah Nama Resto', 'allow' => (!$access ? 0 : $access->AllowEditNamaStand)),
                    'AllowTambahMenu' => array('akses' => 'Tambah Item', 'allow' => (!$access ? 0 : $access->AllowTambahMenu)),
                    'AllowEditMenu' => array('akses' => 'Edit Item', 'allow' => (!$access ? 0 : $access->AllowEditMenu)),
                    'AllowHapusMenu' => array('akses' => 'Hapus Item', 'allow' => (!$access ? 0 : $access->AllowHapusMenu)),
                    'AllowEditPenjualan' => array('akses' => 'Edit Penjualan', 'allow' => (!$access ? 0 : $access->AllowEditPenjualan)),
                    'AllowHapusPenjualan' => array('akses' => 'Hapus Penjualan', 'allow' => (!$access ? 0 : $access->AllowHapusPenjualan)),
                    'AllowHapusOrder' => array('akses' => 'Hapus Order', 'allow' => (!$access ? 0 : $access->AllowHapusOrder)),
                )
            ),
            array(
                'level' => '1', 'label' => 'Data Rekening',
                'detail' => array(
                    'AllowTambahDataRekening' => array('akses' => 'Tambah Data Rekening', 'allow' => (!$access ? 0 : $access->AllowTambahDataRekening)),
                    'AllowEditDataRekening' => array('akses' => 'Edit Data Rekening', 'allow' => (!$access ? 0 : $access->AllowEditDataRekening)),
                    'AllowHapusDataRekening' => array('akses' => 'Hapus Data Rekening', 'allow' => (!$access ? 0 : $access->AllowHapusDataRekening)),
                )
            ),
            array(
                'level' => '1', 'label' => 'Uang Masuk',
                'detail' => array(
                    'AllowTambahUangMasuk' => array('akses' => 'Tambah Uang Masuk', 'allow' => (!$access ? 0 : $access->AllowTambahUangMasuk)),
                    'AllowEditUangMasuk' => array('akses' => 'Edit Uang Masuk', 'allow' => (!$access ? 0 : $access->AllowEditUangMasuk)),
                    'AllowHapusUangMasuk' => array('akses' => 'Hapus Uang Masuk', 'allow' => (!$access ? 0 : $access->AllowHapusUangMasuk)),
                )
            ),
            array(
                'level' => '1', 'label' => 'Uang Keluar',
                'detail' => array(
                    'AllowTambahUangKeluar' => array('akses' => 'Tambah Uang Keluar', 'allow' => (!$access ? 0 : $access->AllowTambahUangKeluar)),
                    'AllowEditUangKeluar' => array('akses' => 'Edit Uang Keluar', 'allow' => (!$access ? 0 : $access->AllowEditUangKeluar)),
                    'AllowHapusUangKeluar' => array('akses' => 'Hapus Uang Keluar', 'allow' => (!$access ? 0 : $access->AllowHapusUangKeluar)),
                )
            ),
            array(
                'level' => '1', 'label' => 'Laporan',
                'detail' => array(
                    'AllowLaporanPenjualan' => array('akses' => 'Penjualan', 'allow' => (!$access ? 0 : $access->AllowLaporanPenjualan)),
                    'AllowLaporanRekapPenjualan' => array('akses' => 'Penjualan per Kategori', 'allow' => (!$access ? 0 : $access->AllowLaporanRekapPenjualan)),
                    'AllowLaporanRekapPembayaran' => array('akses' => 'Rekap Pembayaran', 'allow' => (!$access ? 0 : $access->AllowLaporanRekapPembayaran)),
                    'AllowLaporanSaldoKasRekening' => array('akses' => 'Saldo Kas / Rekening', 'allow' => (!$access ? 0 : $access->AllowLaporanSaldoKasRekening)),
                    'AllowLaporanTipePenjualan' => array('akses' => 'Tipe Penjualan', 'allow' => (!$access ? 0 : $access->AllowLaporanTipePenjualan)),
                    'AllowLaporanStok' => array('akses' => 'Stok', 'allow' => (!$access ? 0 : $access->AllowLaporanStok)),
                    'AllowLaporanAwan' => array('akses' => 'Lihat Laporan di awan', 'allow' => (!$access ? 0 : $access->AllowLaporanAwan)),
                    'AllowLaporanRekapMutasiStok' => array('akses' => 'Rekap Mutasi Stok', 'allow' => (!$access ? 0 : $access->AllowLaporanRekapMutasiStok)),
                )
            ),
            array(
                'level' => '1', 'label' => 'Pengaturan',
                'detail' => array(
                    'AllowPrinter' => array('akses' => 'Printer', 'allow' => (!$access ? 0 : $access->AllowPrinter)),
                    'AllowModul' => array('akses' => 'Modul', 'allow' => (!$access ? 0 : $access->AllowModul)),
                    'AllowPajak' => array('akses' => 'Pajak', 'allow' => (!$access ? 0 : $access->AllowPajak)),
                    'AllowDiskon' => array('akses' => 'Diskon', 'allow' => (!$access ? 0 : $access->AllowDiskon)),
                    'AllowMeja' => array('akses' => 'Meja', 'allow' => (!$access ? 0 : $access->AllowMeja)),
                    'AllowTipePembayaran' => array('akses' => 'Tipe Pembayaran', 'allow' => (!$access ? 0 : $access->AllowTipePembayaran)),
                    'AllowTipePenjualan' => array('akses' => 'Tipe Penjualan', 'allow' => (!$access ? 0 : $access->AllowTipePenjualan)),
                    'AllowCopyDariOutletLain' => array('akses' => 'Copy dari Outlet Lain', 'allow' => (!$access ? 0 : $access->AllowCopyDariOutletLain)),
                )
            ),
            array(
                'level' => '1', 'label' => 'Pembelian',
                'detail' => array(
                    'AllowPembelian' => array('akses' => 'Pembelian', 'allow' => (!$access ? 0 : $access->AllowPembelian)),
                    'AllowTambahItemPembelian' => array('akses' => 'Tambah Item Pembelian', 'allow' => (!$access ? 0 : $access->AllowTambahItemPembelian)),
                    'AllowEditItemPembelian' => array('akses' => 'Edit Item Pembelian', 'allow' => (!$access ? 0 : $access->AllowEditItemPembelian)),
                    'AllowHapusItemPembelian' => array('akses' => 'Hapus Item Pembelian', 'allow' => (!$access ? 0 : $access->AllowHapusItemPembelian)),
                    'AllowTambahSupplier' => array('akses' => 'Tambah Suplier', 'allow' => (!$access ? 0 : $access->AllowTambahSupplier)),
                    'AllowEditSupplier' => array('akses' => 'Edit Nama Suplier', 'allow' => (!$access ? 0 : $access->AllowEditSupplier)),
                    'AllowHapusSupplier' => array('akses' => 'Hapus Suplier', 'allow' => (!$access ? 0 : $access->AllowHapusSupplier)),
                    'AllowEditPembelian' => array('akses' => 'Edit Pembelian', 'allow' => (!$access ? 0 : $access->AllowEditPembelian)),
                    'AllowHapusPembelian' => array('akses' => 'Hapus Pembelian', 'allow' => (!$access ? 0 : $access->AllowHapusPembelian)),
                )
            ),
            array(
                'level' => '1', 'label' => 'Stok',
                'detail' => array(
                    'AllowKoreksiStok' => array('akses' => 'Koreksi Stok', 'allow' => (!$access ? 0 : $access->AllowKoreksiStok)),
                    'AllowTambahItemStok' => array('akses' => 'Tambah Item Stok', 'allow' => (!$access ? 0 : $access->AllowTambahItemStok)),
                    'AllowEditItemStok' => array('akses' => 'Edit Item Stok', 'allow' => (!$access ? 0 : $access->AllowEditItemStok)),
                    'AllowHapusItemStok' => array('akses' => 'Hapus Item Stok', 'allow' => (!$access ? 0 : $access->AllowHapusItemStok)),
                )
            ),
        );

        return $rs_hak_akses;
    }
}
