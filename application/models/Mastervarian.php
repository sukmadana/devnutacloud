<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 07/12/2016
 * Time: 19:27
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Mastervarian extends CI_Model
{
    var $_tableName = "mastervariant";
    protected $_dbMaster;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Firebasemodel');
    }

    protected function initDbMaster()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function getVariasiHarga($itemid, $deviceid)
    {
        $retval = array();
        if($itemid == "new") {
            return $retval;
        }
        $realitemid = explode(".", $itemid)[0];
        $devno = explode(".", $itemid)[1];
        $sql = "SELECT VarianName, ItemID, ItemDeviceNo,SellPrice,IsReguler 
FROM mastervariant WHERE  ItemID =" . $realitemid .
            " AND ItemDeviceNo = " . $devno .
            " AND DeviceID = " . $deviceid;
        $query = $this->db->query($sql);
        $result = $query->result();
        if (count($result) > 0) {

            foreach ($result as $row) {
                array_push($retval,
                    array('VarianName' => $row->VarianName, 'SellPrice' => $row->SellPrice, 'IsReguler' => $row->IsReguler, 'Placeholder' => ''));
            }
        }
        return $retval;

    }

    public function getVariasiHargaArray($itemid, $deviceid)
    {
        $realitemid = explode(".", $itemid)[0];
        $devno = explode(".", $itemid)[1];
        $sql = "SELECT VarianName, ItemID,SellPrice,IsReguler FROM mastervariant 
WHERE ItemID =" . $realitemid . "
AND ItemDeviceNo = " . $devno . "
AND DeviceID = " . $deviceid;
        $query = $this->db->query($sql);
        $result = $query->result_array();
        $retval = $result;
        return $retval;

    }

    public function createVariasiharga($idoutlet, $itemid, $namaVariasi, $harga, $isReguler, $perusahaanNo)
    {
        $realitemid = explode(".", $itemid)[0];
        $devno = explode(".", $itemid)[1];
        $id = $this->generateNewID($idoutlet);
        $this->initDbMaster();

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($idoutlet);
        $cloudDevno = 0;
        if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
            $cloudDevno = 1;
        }

        $attrib_mastervariant = array(
            'VarianID' => $id,
            'DeviceNo' => $cloudDevno,
            'DeviceID' => $idoutlet,
            'VarianName' => $namaVariasi,
            'SellPrice' => $harga,
            'ItemID' => $realitemid,
            'ItemDeviceNo' => $devno,
            'IsReguler' => $isReguler,
            'HasBeenDownloaded' => 0,
            'Varian' => 'Nuta',
            'PerusahaanNo' => $perusahaanNo
        );

        $result_insert_mastervariant = $this->_dbMaster->insert($this->_tableName, $attrib_mastervariant);

        if ($result_insert_mastervariant) {
            //push to firebase
            $insert_query = $this->_dbMaster->get_where($this->_tableName, array(
                'VarianID' => $id,
                'DeviceNo' => $cloudDevno,
                'DeviceID' => $idoutlet,
                'PerusahaanNo' => $perusahaanNo
            ));
            $last_insert_data = array(
                "table" => $this->_tableName,
                "column" => $insert_query->row_array()
            );
            $this->load->model('Options');
            $options = $this->Options->get_by_devid($idoutlet);
            if($options->CreatedVersionCode<200 && $options->EditedVersionCode<200) {
                $this->Firebasemodel->push_firebase($idoutlet, $last_insert_data,
                    $id, $cloudDevno, $perusahaanNo, 0);
            }
        }

    }

    public function updateVariasiHarga($idoutlet, $itemid, $namaVariasiLama, $namaVariasiBaru, $sellPrice, $isReguler,$perusahaanNo)
    {
        $realitemid = explode(".", $itemid)[0];
        $devno = explode(".", $itemid)[1];
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'VarianName' => $namaVariasiLama,
            'ItemID' => $realitemid, 'ItemDeviceNo' => $devno));
        $query = $this->_dbMaster->get($this->_tableName);
        $count = $query->num_rows();
        if ($count > 0) {
            $result = $query->row();
            $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'VarianID' => $result->VarianID, 'DeviceNo' => $result->DeviceNo));
            $this->_dbMaster->update($this->_tableName, array('VarianName' => $namaVariasiBaru, 'SellPrice' => $sellPrice, 'IsReguler' => $isReguler, 'HasBeenDownloaded' => 0));

            //push to firebase
            $query_dataupdated = $this->_dbMaster->get_where($this->_tableName, array(
                'VarianID' => $result->VarianID, 'DeviceNo' => $result->DeviceNo,
                'DeviceID' => $idoutlet,
                'PerusahaanNo' => $perusahaanNo
            ));
            $last_update_data = array(
                "table" => $this->_tableName,
                "column" => $query_dataupdated->row_array()
            );
            $this->load->model('Options');
            $options = $this->Options->get_by_devid($idoutlet);
            if($options->CreatedVersionCode<200 && $options->EditedVersionCode<200) {
                $this->Firebasemodel->push_firebase($idoutlet, $last_update_data,
                    $result->VarianID, $result->DeviceNo, $perusahaanNo, 0);
            }
        }
        return $count > 0;

    }

    public function hapusVariasiHarga($idoutlet, $itemid, $namavariasiharga, $perusahaanNo)
    {
        $realitemid = explode(".", $itemid)[0];
        $devno = explode(".", $itemid)[1];
        $this->initDbMaster();
        $query_deleting_varian = $this->_dbMaster->get_where('mastervariant', array('DeviceID' => $idoutlet,
            'VarianName' => $namavariasiharga, 'ItemID' => $realitemid, 'ItemDeviceNo' => $devno));
        $deleting_varians = $query_deleting_varian->result_array();
        for ($i = 0; $i < count($deleting_varians); $i++) {

            $query_is_exist_in_delete_table = $this->_dbMaster->get_where('mastervariantdelete',
                array('DeviceID' => $idoutlet, 'VarianID' => $deleting_varians[$i]['VarianID'], 'DeviceNo' => $deleting_varians[$i]['DeviceNo']));
            $exist_in_deletevariant_table = $query_is_exist_in_delete_table->num_rows() > 0;
            if ($exist_in_deletevariant_table) {
                $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'VarianID' => $deleting_varians[$i]['VarianID']));
                $this->_dbMaster->delete('mastervariantdelete');
            }

            $deleting_varians[$i]['HasBeenDownloaded'] = 0;

            $deleted_variant = array(
                "table" => 'deletemastervariant',
                "column" => array(
                    "VarianID" => $deleting_varians[$i]['VarianID'],
                    'DeviceNo' => $deleting_varians[$i]['DeviceNo']
                )
            );
            $this->load->model('Options');
            $options = $this->Options->get_by_devid($idoutlet);
            if($options->CreatedVersionCode<200 && $options->EditedVersionCode<200) {
                $this->Firebasemodel->push_firebase($idoutlet, $deleted_variant,
                    $deleting_varians[$i]['VarianID'], $deleting_varians[$i]['DeviceNo'], $perusahaanNo, 0);
            }
        }
        $this->_dbMaster->insert_batch('mastervariantdelete', $deleting_varians);
        $this->_dbMaster->where(array('VarianName' => $namavariasiharga, 'ItemID' => $realitemid,
            'DeviceID' => $idoutlet, 'ItemDeviceNo' => $devno));
        $this->_dbMaster->delete($this->_tableName);

    }

    public function generateNewID($deviceid)
    {
        $sql = "
        SELECT
          COALESCE (MAX(VarianID),0) +1 as ID
        FROM
        (SELECT
            VarianID
        FROM
            mastervariant
        WHERE
            DeviceID = " . $this->db->escape($deviceid) . " UNION ALL SELECT
            ItemID
        FROM
            mastervariantdelete
        WHERE
            DeviceID = " . $this->db->escape($deviceid) . ") a
        ";
        $query = $this->db->query($sql);
        $row = $query->row();
        return $row->ID;
    }

}