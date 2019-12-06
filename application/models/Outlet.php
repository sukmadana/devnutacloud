<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outlet extends CI_Model
{
    var $_tableName = "outlet";
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

    public function createNewOutlet($data)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(
            array(
                'PerusahaanID' => $data['id_perusahaan'],
                'NamaOutlet' => $data['nama_outlet']
            )
        );
        $query = $this->_dbMaster->get($this->_tableName);
        $count = $query->num_rows();
        $outletid = -1;
        if ($count > 0) {
            $outlet = $query->result();
            return 'Nama Outlet "' . $outlet[0]->NamaOutlet . '" sudah terdaftar, ganti dengan nama yang lain.';

        } else {
            $this->_dbMaster->insert(
                $this->_tableName,
                    array(
                        'NamaOutlet' => $data['nama_outlet'],
                        'AlamatOutlet' => $data['alamat_outlet'],
                        'PerusahaanID' => $data['id_perusahaan'],
                        'DataMasterBisaDiambil' => $data['allow_download_data'],
                        'PerusahaanNo' => $data['nomor_perusahaan'],
                        'Propinsi' => $data['provinsi_outlet'],
                        'Kota' => $data['kota_outlet'],
                        /*'NoTelp' => $data['notelp_outlet'],*/
                        'PemilikOutlet' => $data['pemilik_outlet']
                    )
                );
            $outletid = $this->_dbMaster->insert_id();
        }
        return $outletid;
    }

    public function isOutletNameHasBeenRegisteredInCompany($idperusahaan, $namaoutlet)
    {
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'NamaOutlet' => $namaoutlet, 'SudahKonfirmasi' => 1));
        $count = $query->num_rows();
        return $count > 0;
    }

    public function updateSudahKonfirmasi($idperusahaan, $idoutlet)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('PerusahaanID' => $idperusahaan, 'OutletID' => $idoutlet));
        $this->_dbMaster->update($this->_tableName, array('SudahKonfirmasi' => 1));
    }

    public function getAll($idperusahaan)
    {
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan));
        $result = $query->result();
        $outlets = array();
        foreach ($result as $r) {
            array_push($outlets, array('outletid' => $r->OutletID, 'outletname' => $r->NamaOutlet, 'outletaddress' => $r->AlamatOutlet));
        }
        return $outlets;
    }

    public function getByUser($idperusahaan, $username)
    {
        $sqlString = "SELECT o.* FROM outlet o
inner join userperusahaancabang a where a.OutletID=o.OutletID and a.PerusahaanID=o.PerusahaanID
and a.Username = " . $this->db->escape($username) . " AND o.OutletID = " . $this->db->escape($idperusahaan);
        $query = $this->db->query($sqlString);
        $result = $query->result();
        $outlets = array();
        foreach ($result as $r) {
            array_push($outlets, array('outletid' => $r->OutletID, 'outletname' => $r->NamaOutlet, 'outletaddress' => $r->AlamatOutlet));
        }
        return $outlets;
    }

    public function getById($idperusahaan, $idoutlet)
    {
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'OutletID' => $idoutlet));
        $result = $query->result();
        return $result[0];
    }

    public function getByName($PerusahaanNo, $idoutlet)
    {
        $query = $this->db->get_where($this->_tableName, array('PerusahaanNo' => $PerusahaanNo, 'OutletID' => $idoutlet));
        $result = $query->result();
        return $result[0];
    }

    public function updateOutlet($idOutlet, $namaOutlet, $alamatOutlet, $idperusahaan, $allowdownload)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('PerusahaanID' => $idperusahaan, 'OutletID' => $idOutlet));
        $query = $this->_dbMaster->get($this->_tableName);
        $count = $query->num_rows();

        if ($count > 0) {
            $oldoutlet = $query->result();
            if ($oldoutlet[0]->NamaOutlet != $namaOutlet) {
                $this->_dbMaster->where(array('PerusahaanID' => $idperusahaan, 'NamaOutlet' => $namaOutlet));
                $queryCekNama = $this->_dbMaster->get($this->_tableName);
                $countCekNama = $queryCekNama->num_rows();
                if ($countCekNama > 0) {
                    $resultCekNama = $queryCekNama->result();
                    return 'Nama Outlet sudah terdaftar dengan alamat ' . $resultCekNama[0]->AlamatOutlet;
                } else {
                    $this->_dbMaster->where(array('PerusahaanID' => $idperusahaan, 'OutletID' => $idOutlet));
                    $this->_dbMaster->update($this->_tableName, array('NamaOutlet' => $namaOutlet, 'AlamatOutlet' => $alamatOutlet,
                        'DataMasterBisaDiambil' => $allowdownload));
                }

            } else {
                $this->_dbMaster->where(array('PerusahaanID' => $idperusahaan, 'OutletID' => $idOutlet));
                $this->_dbMaster->update($this->_tableName, array('NamaOutlet' => $namaOutlet, 'AlamatOutlet' => $alamatOutlet, 'DataMasterBisaDiambil' => $allowdownload));
            }
        } else {
            return "Outlet tidak ditemukan";
        }
        return 1;
    }

    public function update_outlet_data($data)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(
            array(
                'PerusahaanID' => $data['id_perusahaan'],
                'OutletID' => $data['id_outlet']
            )
        );
        $query = $this->_dbMaster->get($this->_tableName);
        $count = $query->num_rows();

        if ($count > 0) {
            $oldoutlet = $query->result();
            if ($oldoutlet[0]->NamaOutlet != $data['nama_outlet']) {
                $this->_dbMaster->where(
                    array(
                        'PerusahaanID' => $data['id_perusahaan'],
                        'NamaOutlet' => $data['nama_outlet']
                    )
                );
                $queryCekNama = $this->_dbMaster->get($this->_tableName);
                $countCekNama = $queryCekNama->num_rows();
                if ($countCekNama > 0) {
                    $resultCekNama = $queryCekNama->result();
                    return 'Nama Outlet sudah terdaftar dengan alamat ' . $resultCekNama[0]->AlamatOutlet;
                } else {
                    $this->_dbMaster->where(
                        array(
                            'PerusahaanID' => $data['id_perusahaan'],
                            'OutletID' => $data['id_outlet']
                        )
                    );
                    $this->_dbMaster->update(
                        $this->_tableName,
                        array(
                            'NamaOutlet' => $data['nama_outlet'],
                            'AlamatOutlet' => $data['alamat_outlet'],
                            'PerusahaanID' => $data['id_perusahaan'],
                            'DataMasterBisaDiambil' => $data['allow_download_data'],
                            'PerusahaanNo' => $data['nomor_perusahaan'],
                            'Propinsi' => $data['provinsi_outlet'],
                            'Kota' => $data['kota_outlet'],
                            /*'NoTelp' => $data['notelp_outlet'],*/
                            'PemilikOutlet' => $data['pemilik_outlet']
                        )
                    );
                    $this->_dbMaster->where(
                        array(
                            'PerusahaanID' => $data['id_perusahaan'],
                            'OutletID' => $data['id_outlet']
                        )
                    );
                    $this->_dbMaster->update(
                        'options',
                        array(
                            'MobilePhone' => $data['notelp_outlet'],
                        )
                    );
                    
                }

            } else {
                $this->_dbMaster->where(
                    array(
                        'PerusahaanID' => $data['id_perusahaan'],
                        'OutletID' => $data['id_outlet']
                    )
                );
                $this->_dbMaster->update(
                    $this->_tableName,
                    array(
                        'NamaOutlet' => $data['nama_outlet'],
                        'AlamatOutlet' => $data['alamat_outlet'],
                        'PerusahaanID' => $data['id_perusahaan'],
                        'DataMasterBisaDiambil' => $data['allow_download_data'],
                        'PerusahaanNo' => $data['nomor_perusahaan'],
                        'Propinsi' => $data['provinsi_outlet'],
                        'Kota' => $data['kota_outlet'],
                        /*'NoTelp' => $data['notelp_outlet'],*/
                        'PemilikOutlet' => $data['pemilik_outlet']
                    )
                );

                $this->_dbMaster->where(
                    array(
                        'PerusahaanID' => $data['id_perusahaan'],
                        'OutletID' => $data['id_outlet']
                    )
                );
                $this->_dbMaster->update(
                    'options',
                    array(
                        'MobilePhone' => $data['notelp_outlet'],
                    )
                );
            }
        } else {
            return "Outlet tidak ditemukan";
        }
        return 1;
    }

    public function setNonAktif($idperusahaan, $idoutlet)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('PerusahaanID' => $idperusahaan, 'OutletID' => $idoutlet));
        $this->_dbMaster->update($this->_tableName, array('DeviceIDAktif' => ''));
    }

    public function delete($idoutlet, $idperusahaan)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('PerusahaanID' => $idperusahaan, 'OutletID' => $idoutlet));
        $this->_dbMaster->delete($this->_tableName);
    }

    public function setKodeAktivasi($kode, $idoutlet)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('OutletID' => $idoutlet));
        $this->_dbMaster->update($this->_tableName, array('KodeAktivasi' => $kode));
    }

    public function setStatusAktivasi($status, $idoutlet)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('OutletID' => $idoutlet));
        $this->_dbMaster->update($this->_tableName, array('StatusAktivasi' => $status));
    }

    public function setTotalHargaAktivasi($total, $idoutlet)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('OutletID' => $idoutlet));
        $this->_dbMaster->update($this->_tableName, array('TotalHargaAktivasi' => $total));
    }

    public function setSnapTokenAktivasi($token, $idoutlet)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('OutletID' => $idoutlet));
        $this->_dbMaster->update($this->_tableName, array('SnapTokenAktivasi' => $token));
    }

    public function hasNewAktivasi($perusahaanid, $outletid)
    {
        $sql="SELECT * FROM outlet WHERE (KodeAktivasi IS NOT NULL AND KodeAktivasi <>'') 
AND PerusahaanID=" . $this->db->escape($perusahaanid) . ' AND OutletID=' . $outletid;
        $query = $this->db->query($sql);
        $num = $query->num_rows();
        $row = $query->row();
        $has_aktivasi = $num == 1;
        if ($has_aktivasi) {
            return ['has' => $has_aktivasi, 'kode' => $row->KodeAktivasi];
        } else {
            return ['has' => $has_aktivasi, 'kode' => null];
        }

    }

    public function getAktivasi($perusahaanid, $kodeaktivasi)
    {
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $perusahaanid, 'KodeAktivasi' => $kodeaktivasi));
        $row = $query->row();
        return ['kode' => $row->KodeAktivasi, 'status' => $row->StatusAktivasi, 'total' => $row->TotalHargaAktivasi, 'token' => $row->SnapTokenAktivasi, 'outletid' => $row->OutletID];
    }

    public function getPerusahaanNoByOutlet($outletid)
    {
        $query = $this->db->get_where($this->_tableName, array('OutletID' => $outletid));
        $row = $query->row();
        return $row->PerusahaanNo;
    }

    public function getOutletByIdOnly($idoutlet)
    {
        $this->db->select('outlet.*, options.MobilePhone, userperusahaan.Username' );
        $this->db->from($this->_tableName);
        $this->db->join('options', 'outlet.OutletID = options.OutletID');
        $this->db->join('userperusahaan', 'userperusahaan.iduserperusahaan = outlet.PemilikOutlet', 'left');
        $this->db->where('outlet.OutletID', $idoutlet);
        $query = $this->db->get();
        return $query->row();
    }

    public function getNutaUser($OutletID = null){
        $this->initDbMaster();
        if ($OutletID === null) {
            return false;
        } else {
            $PerusahaanID = getLoggedInUserID();
            $this->_dbMaster->where('OutletID', $OutletID);
            $this->_dbMaster->from('userperusahaancabang');
            $userperusahaancabang = $this->_dbMaster->get()->result_array();

            $this->_dbMaster->where('PerusahaanID', $PerusahaanID);
            $this->_dbMaster->where('IsOwner', '1');
            $this->_dbMaster->from('userperusahaan');
            $userperusahaan = $this->_dbMaster->get()->result_array(); 

            $result = array_merge($userperusahaancabang, $userperusahaan);
            return $result;
        }
    }

    public function countNutaUser($OutletID = null){
        $this->initDbMaster();
        if ($OutletID === null) {
            return 0;
        } else {
            $PerusahaanID = getLoggedInUserID();
            $this->_dbMaster->where('OutletID', $OutletID);
            $this->_dbMaster->from('userperusahaancabang');
            $userperusahaancabang = $this->_dbMaster->count_all_results(); 

            
            $this->_dbMaster->where('PerusahaanID', $PerusahaanID);
            $this->_dbMaster->where('IsOwner', '1');
            $this->_dbMaster->from('userperusahaan');
            $userperusahaan = $this->_dbMaster->count_all_results(); 

            return $userperusahaan + $userperusahaancabang;
        }
    }

    public function getOutletOptions($OutletID = null){
        $this->initDbMaster();
        if ($OutletID === null) {
            return false;
        } else {
            $this->_dbMaster->where('OutletID', $OutletID);
            $this->_dbMaster->from('options');
            $query = $this->_dbMaster->get();
            return $query->row_array();
        }
    }

    public function update_download_module($OutletID = null, $DataMasterBisaDiambil){
        $this->db->set('DataMasterBisaDiambil', $DataMasterBisaDiambil);
        $this->db->where('OutletID', $OutletID);
        $this->db->update($this->_tableName);

    }

    public function get_outlet_info($devid){
        $query_outlet = $this->db->query("
            SELECT op.TglExpired AS exp, ot.NamaOutlet AS nama, ot.AlamatOutlet  AS alamat, op.IsTrial AS istrial, op.FiturMejaAktifSampai
            FROM outlet ot
            INNER JOIN options op ON op.DeviceID = ot.OutletID
            WHERE ot.perusahaanid =" . $this->db->escape(getLoggedInUserID()) . " AND op.DeviceID=" . $this->db->escape($devid)
        );
        return $query_outlet->row();
    }

    public function get_outlet_expired_date_by_perusahaanid($perusahaanid){
        $query_outlet = $this->db->query("
            SELECT op.TglExpired AS exp, ot.NamaOutlet AS nama, ot.AlamatOutlet  AS alamat
            FROM outlet ot 
            INNER JOIN options op ON op.DeviceID = ot.OutletID 
            WHERE ot.perusahaanid = " . $perusahaanid
        );
        return $query_outlet->result_array();
    }

    public function get_outlet_user_permission($PerusahaanID, $Username){
        $this->initDbMaster();
        if ($Username === null) {
            return false;
        } else {
            $this->_dbMaster->select('OutletView, OutletNew, OutletEdit, OutletDelete');
            $this->_dbMaster->from('userperusahaanaksescloud');
            $this->_dbMaster->where('PerusahaanID', $PerusahaanID);
            $this->_dbMaster->where('Username', $Username);
            $query = $this->_dbMaster->get();
            return $query->row_array();
        }

    }
}