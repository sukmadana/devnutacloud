<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mastertax extends CI_Model
{
    var $_tableName = "mastertax";

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


    public function getAllTaxByOutlet($idoutlet)
    {
        $this->db->select('*');
        $this->db->from($this->_tableName);
        $this->db->where('DeviceID', $idoutlet);
        $this->db->where('PerusahaanNO', getPerusahaanNo());
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function deleteMasterTax($where)
    {

        $query = $this->db->get_where('mastertaxdelete', $where);
        $tax = $query->row_array();

        if ($query->num_rows() > 0) {
            $this->db->delete('mastertaxdelete', $where);
        }

        $query = $this->db->get_where($this->_tableName, $where);
        $tax = $query->row_array();

        if ($query->num_rows() == 0) {
            return false;
        }

        if ($this->db->insert('mastertaxdelete', $tax)) {
            if ($this->db->delete($this->_tableName, $where)) {
                // Push to firebase
                $last_update_data = array(
                    "table" => "delete" . $this->_tableName,
                    "column" => array(
                        "TaxID" => $tax['TaxID'],
                        "DeviceNo" => $tax['DeviceNo']
                    )
                );
                $this->Firebasemodel->push_firebase(
                    $tax['DeviceID'],
                    $last_update_data,
                    $tax['TaxID'],
                    $tax['DeviceNo'],
                    $tax['PerusahaanNo'],
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

    public function getNewTaxID($where)
    {
        $query = $this->db->order_by('TaxID', 'desc')->get_where($this->_tableName, $where);
        $lastTax = $query->row();
        return (intval($lastTax->TaxID) + 1);
    }

    public function insertMasterTax($params)
    {

        $insert = $this->db->insert($this->_tableName, $params);

        if ($insert) {
            // Push to firebase
            $where = array(
                'PerusahaanNo' => $params['PerusahaanNo'],
                'DeviceID' => $params['DeviceID'],
                'TaxID' => $params['TaxID'],
            );
            $query = $this->db->get_where($this->_tableName, $where);
            $dataInsert = $query->row_array();
            $last_insert_data = array(
                "table" => $this->_tableName,
                "column" => $dataInsert
            );
            $this->Firebasemodel->push_firebase(
                $dataInsert['DeviceID'],
                $last_update_data,
                $dataInsert['TaxID'],
                $dataInsert['DeviceNo'],
                $dataInsert['PerusahaanNo'],
                0
            );

            return true;
        } else {
            return false;
        }
    }

    public function getDetailMasterTax($where)
    {
        $sql = "SELECT a.*, REPLACE(a.TaxPercent, '.', ',') AS 'TaxPercent', IF(TaxPercent REGEXP '[[.percent-sign.]]', 'yes','no' ) AS 'percent'
            FROM ".$this->_tableName." a
            WHERE PerusahaanNo = ? AND DeviceID = ? AND TaxID = ? ";
        $query = $this->db->query($sql, $where);
        return $query->row_array();
    }

    public function updateMasterTax($params, $where)
    {

        $update = $this->db->update($this->_tableName, $params, $where);

        if ($update) {
            // Push to firebase
            $query = $this->db->get_where($this->_tableName, $where);
            $dataUpdate = $query->row_array();
            $last_update_data = array(
                "table" => $this->_tableName,
                "column" => $dataUpdate
            );
            $this->Firebasemodel->push_firebase(
                $dataUpdate['DeviceID'],
                $last_update_data,
                $dataUpdate['TaxID'],
                $dataUpdate['DeviceNo'],
                $dataUpdate['PerusahaanNo'],
                0
            );

            return true;
        } else {
            return false;
        }
    }
    
}

