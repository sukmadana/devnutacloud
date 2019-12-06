<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Masteropsimakan extends CI_Model
{
    var $_tableName = "masteropsimakan";
    protected $_dbMaster;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Firebasemodel');

        $this->initDbMaster();
    }

    protected function initDbMaster()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function getTotalOpsiMakan($params)
    {
        $query = $this->_dbMaster->get_where($this->_tableName, $params);
        return $query->num_rows();
    }

    public function getOpsiMakan($params)
    {
        $sql = "SELECT *,
            SUBSTRING_INDEX(MarkupRounding,'#',1) AS 'MarkupMarkupRoundingValue',
            CASE
            WHEN SUBSTRING_INDEX(MarkupRounding,'#',-1) = '0' THEN CONCAT('Rp ', SUBSTRING_INDEX(MarkupRounding,'#',1),' Terdekat')
            WHEN SUBSTRING_INDEX(MarkupRounding,'#',-1) = '1' THEN CONCAT('Rp ', SUBSTRING_INDEX(MarkupRounding,'#',1),' Kebawah')
            WHEN SUBSTRING_INDEX(MarkupRounding,'#',-1) = '2' THEN CONCAT('Rp ', SUBSTRING_INDEX(MarkupRounding,'#',1),' Keatas')
            END AS 'MarkupRoundingRemark'
            FROM masteropsimakan
            WHERE PerusahaanNo = ? AND DeviceID = ?
            ORDER BY NamaOpsiMakan ";
        $query = $this->_dbMaster->query($sql, array($params['PerusahaanNo'], $params['DeviceID']));
        return $query->result_array();
    }

    public function isExistField($where)
    {
        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        return $query->num_rows() > 0 ? true : false;
    }

    public function getNewOpsiMakanID($where)
    {
        $query = $this->_dbMaster->order_by('OpsiMakanID', 'DESC')->get_where($this->_tableName, $where);
        $result = $query->row();
        if ($query->num_rows() > 0) {
            return (intval($result->OpsiMakanID) + 1);
        }
        return 1;
    }

    public function getDetailOpsiMakan($where)
    {
        $sql = "SELECT *, SUBSTRING_INDEX(MarkupRounding,'#',1) AS 'MarkupRoundingValue', SUBSTRING_INDEX(MarkupRounding,'#',-1) AS 'MarkupRoundingType'
            FROM masteropsimakan
            WHERE PerusahaanNo = ? AND DeviceID = ? AND OpsiMakanID = ? AND DeviceNo = ? ";
        $query = $this->_dbMaster->query($sql, array($where['PerusahaanNo'], $where['DeviceID'], $where['OpsiMakanID'], $where['DeviceNo']));
        return $query->row_array();
    }

    public function getCashBankAccountPerusahaan($params)
    {
        $query = $this->_dbMaster->order_by('BankName', 'ASC')->get_where('mastercashbankaccount', array_merge($params, array('AccountType' => 2)));
        return $query->result_array();
    }

    public function insertOpsiMakan($params)
    {
        $insert = $this->_dbMaster->insert($this->_tableName, $params);

        if ($insert) {
            // Push to firebase
            $where = array(
                'PerusahaanNo' => $params['PerusahaanNo'],
                'DeviceID' => $params['DeviceID'],
                'OpsiMakanID' => $params['OpsiMakanID'],
            );
            $query = $this->_dbMaster->get_where($this->_tableName, $where);
            $dataInsert = $query->row_array();
            $last_insert_data = array(
                "table" => $this->_tableName,
                "column" => $dataInsert
            );

            // $this->load->model('Options');
            // $options = $this->Options->get_by_devid($params['DeviceID']);
            // if ($options->CreatedVersionCode < 200 && $options->EditedVersionCode < 200) {
            //     $this->Firebasemodel->push_firebase(
            //         $dataInsert['DeviceID'],
            //         $last_insert_data,
            //         $dataInsert['OpsiMakanID'],
            //         $dataInsert['DeviceNo'],
            //         $dataInsert['PerusahaanNo'],
            //         0
            //     );
            // }

            $this->Firebasemodel->push_firebase(
                $dataInsert['DeviceID'],
                $last_insert_data,
                $dataInsert['OpsiMakanID'],
                $dataInsert['DeviceNo'],
                $dataInsert['PerusahaanNo'],
                0
            );

            return true;
        } else {
            return false;
        }
    }

    public function updateOpsiMakan($params, $where)
    {
        $update = $this->_dbMaster->update($this->_tableName, $params, $where);

        if ($update) {
            // Push to firebase
            $query = $this->_dbMaster->get_where($this->_tableName, $where);
            $dataUpdate = $query->row_array();
            $last_update_data = array(
                "table" => $this->_tableName,
                "column" => $dataUpdate
            );

            // $this->load->model('Options');
            // $options = $this->Options->get_by_devid($params['DeviceID']);
            // if ($options->CreatedVersionCode < 200 && $options->EditedVersionCode < 200) {
            //     $this->Firebasemodel->push_firebase(
            //         $dataInsert['DeviceID'],
            //         $last_insert_data,
            //         $dataInsert['OpsiMakanID'],
            //         $dataInsert['DeviceNo'],
            //         $dataInsert['PerusahaanNo'],
            //         0
            //     );
            // }

            $this->Firebasemodel->push_firebase(
                $dataUpdate['DeviceID'],
                $last_update_data,
                $dataUpdate['OpsiMakanID'],
                $dataUpdate['DeviceNo'],
                $dataUpdate['PerusahaanNo'],
                0
            );

            return true;
        } else {
            return false;
        }
    }

    public function deleteOpsiMakan($where)
    {
        $query = $this->_dbMaster->get_where($this->_tableName . "delete", $where);


        if ($query->num_rows() > 0) {
            $this->_dbMaster->delete($this->_tableName . "delete", $where);
        }

        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        $OpsiMakan = $query->row_array();

        if ($query->num_rows() == 0) {
            return false;
        }

        if ($this->_dbMaster->insert($this->_tableName . "delete", $OpsiMakan)) {
            if ($this->_dbMaster->delete($this->_tableName, $where)) {
                // Push to firebase
                $delete_data = array(
                    "table" => "delete" . $this->_tableName,
                    "column" => array(
                        "OpsiMakanID" => $OpsiMakan['OpsiMakanID'],
                        "DeviceNo" => $OpsiMakan['DeviceNo']
                    )
                );
                $this->Firebasemodel->push_firebase(
                    $OpsiMakan['DeviceID'],
                    $delete_data,
                    $OpsiMakan['OpsiMakanID'],
                    $OpsiMakan['DeviceNo'],
                    $OpsiMakan['PerusahaanNo'],
                    0
                );

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
