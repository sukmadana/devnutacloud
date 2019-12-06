<?php

/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 21/11/16
 * Time: 13:13
 */
class Satuan extends CI_Model
{
    var $_tableName = "mastersatuan";
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


    public function createSatuan($idoutlet, $namasatuan, $perusahaanNo)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'Satuan' => $namasatuan));
        $query = $this->_dbMaster->get('mastersatuan');
        $count = $query->num_rows();
        if ($count > 0) {
            return 'Nama Satuan sudah terdaftar';
        } else {
            $sqlid = "
        SELECT
          COALESCE (MAX(SatuanID),0) +1 as id
        FROM
        (SELECT
            SatuanID
        FROM
            mastersatuan
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . " UNION ALL SELECT
            SatuanID
        FROM
           mastersatuandelete
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . ") a";
            $queryid = $this->db->query($sqlid);
            $resultid = $queryid->result();
            $satuanid = $resultid[0]->id;
            $this->_dbMaster->insert('mastersatuan', array('SatuanID' => $satuanid, 'DeviceID' => $idoutlet, 'Satuan' => $namasatuan, 'Varian' => 'Nuta',
                'HasBeenDownloaded' => 0, 'PerusahaanNo' => $perusahaanNo));
            return $satuanid;
        }
    }

    public function editSatuan($idOutlet, $namaSatuanLama, $namaSatuanBaru, $perusahaanNo)
    {
        $this->initDbMaster();
        $isSatuanUnique = $this->isSatuanNotExist($idOutlet, $namaSatuanBaru);
        $idsatuan = $this->getSatuanIDByName($idOutlet, $namaSatuanLama, $perusahaanNo);
        if ($isSatuanUnique) {
            $this->_dbMaster->where(array('DeviceID' => $idOutlet,
                'Satuan' => $namaSatuanLama, 'SatuanID' => $idsatuan));
            $this->_dbMaster->update('mastersatuan', array('Satuan' => $namaSatuanBaru, 'HasBeenDownloaded' => 0));

            return '';
        } else {
            return 'Nama Satuan sudah terdaftar';
        }
    }

    public function deleteSatuan($idsatuan, $arrayOfidOutlet)
    {
        $this->initDbMaster();
        $log = '';
        foreach ($arrayOfidOutlet as $index => $idoutlet) {

            $this->_dbMaster->where(array('DeviceID' => $idoutlet));
            $query = $this->_dbMaster->get('mastersatuan');
            $num = $query->num_rows();
            //satuan tidak bisa dihapus jika tersisa 1
            if ($num > 1) {
                // 0. Update Item
                $this->_dbMaster->query('UPDATE masteritem SET HasBeenDownloaded=0 , SatuanID = 0,Unit=\'\' WHERE DeviceID = ' . $this->_dbMaster->escape($idoutlet) .
                    ' AND Unit = ' . $this->_dbMaster->escape($idsatuan));
                //1. Insert ke category delete
                $query = $this->_dbMaster->get_where('mastersatuan', array('DeviceID' => $idoutlet,
                    'Satuan' => $idsatuan));
                $datas = $query->result_array();
                $datas[0]['HasBeenDownloaded'] = 0;
                $this->_dbMaster->insert('mastersatuandelete', $datas[0]);

                $this->_dbMaster->where(array('DeviceID' => $idoutlet,
                    'Satuan' => $idsatuan));
                $this->_dbMaster->delete('mastersatuan');
            } else {
                //daftar outlet yang gagal dihapus
                $log .= $idoutlet . ";";
            }
        }
        return $log;
    }

    public function getSatuanIDByName($idoutlet, $namasatuan, $perusahaanNo)
    {
        $this->initDbMaster();
        if (isNotEmpty($namasatuan)) {
            $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'Satuan' => $namasatuan, 'PerusahaanNo' => $perusahaanNo));
            $query = $this->_dbMaster->get('mastersatuan');
            $count = $query->num_rows();
            if ($count > 0) {
                $result = $query->result();
                return $result[0]->SatuanID;
            } else {

                $sqlid = "
        SELECT
          COALESCE (MAX(SatuanID),0) +1 as id
        FROM
        (SELECT
            SatuanID
        FROM
            mastersatuan
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . " UNION ALL SELECT
            SatuanID
        FROM
           mastersatuandelete
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . ") a";
                $queryid = $this->db->query($sqlid);
                $resultid = $queryid->result();
                $satuanid = $resultid[0]->id;
                $this->_dbMaster->insert('mastersatuan', array('SatuanID' => $satuanid, 'DeviceID' => $idoutlet, 'Satuan' => $namasatuan, 'Varian' => 'Nuta',
                    'HasBeenDownloaded' => 0, 'PerusahaanNo' => $perusahaanNo
                ));
                return $satuanid;
            }
        } else {
            return 0;
        }
    }

    public function isSatuanInMultiOutlet($namasatuan, $perusahaanID)
    {
        $query = $this->db->query("select OutletID from outlet where outletid in (select DeviceID from mastersatuan where Satuan="
            . $this->db->escape($namasatuan) .
            ") AND PerusahaanID=" . $this->db->escape($perusahaanID));
        $result = $query->result();
        $retval = array();
        foreach ($result as $r) {
            array_push($retval, $r->OutletID);
        }
        return $retval;
    }

    public function isSatuanNotExist($idoutlet, $namasatuan)
    {
        $query = $this->db->get_where('mastersatuan', array('DeviceID' => $idoutlet, 'Satuan' => $namasatuan));
        $count = $query->num_rows();
        return $count == 0;
    }

    public function getDaftarSatuan($idoutlet)
    {
        $query = $this->db->get_where('mastersatuan', array('DeviceID' => $idoutlet));
        $result = $query->result();
        $retval = array();
        foreach ($result as $r) {
            $retval[$r->SatuanID] = $r->Satuan;
        }
        return $retval;
    }

    public function getMaxSatuanID($idoutlet)
    {
        $sql = "
        SELECT
          COALESCE (MAX(SatuanID),0) +1 as id
        FROM
        (SELECT
            SatuanID
        FROM
            mastersatuan
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . " UNION ALL SELECT
            SatuanID
        FROM
            mastersatuandelete
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . ") a
        ";

        $queryid = $this->db->query($sql);
        $resultid = $queryid->result();
        $satuanid = $resultid[0]->id;
        return $satuanid;
    }
}