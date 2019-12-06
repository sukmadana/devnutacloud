<?php

/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 08/06/2017
 * Time: 11:51
 */
class Uangkeluarmodel extends MY_Model
{
    var $_tableName = "cloud_cashbankout";
    protected $table = "cloud_cashbankout";
    protected $primary_key = ['DeviceID', 'TransactionID'];
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

    public function get_transaction_id()
    {
        return "SELECT CONCAT(COALESCE(MAX(TransactionID), 0) + 1) AS result  FROM " . $this->_tableName;
    }

    public function get_query_uang_keluar()
    {
        return "SELECT TransactionID,TransactionNumber,TransactionTime,TransactionDate,AccountID,PaidTo,Note,Amount FROM " . $this->_tableName . " ORDER BY TransactionID ASC";
    }

    public function get_query_uang_keluar2($idOutlet)
    {
        return "SELECT TransactionID,TransactionNumber,TransactionTime,TransactionDate,AccountID,PaidTo,Note,Amount FROM " . $this->_tableName . " WHERE DeviceID=$idOutlet ORDER BY TransactionID ASC";
    }

    public function get_generate_nomoruangkeluar($date, $deviceid)
    {
        $format = date_format(date_create($date), 'ymd');

        return "SELECT CONCAT('UKC/', '{$format}/' , COALESCE(MAX(CAST(REPLACE(TransactionNumber,'UKC/{$format}/','') AS UNSIGNED)), 0) + 1) AS result 
                FROM " . $this->_tableName . " 
                WHERE TransactionDate = '{$date}' AND DeviceID = {$deviceid}";
    }

    public function get_no_transaksi()
    {
        $kd = 'UKC/';
        $tglSekarang = date("Y-m-d");
        $tgl = substr(date("Y"), 2, 2) . '' . date("m") . '' . date("d") . '/';
        $query = $this->db->query("SELECT MAX(TransactionNumber) as max_id FROM " . $this->_tableName . " WHERE TransactionDate='$tglSekarang'");
        $row = $query->row_array();
        $max_id = $row['max_id'];
        $max_id1 = (int)substr($max_id, 10, 1);
        $kode = $max_id1 + 1;
        $no_kode = $kd . '' . $tgl . '' . sprintf("%01s", $kode);
        return $no_kode;
    }

    public function updateUangKeluar($iscloud, $where_attr = [], $attributes)
    {
        return $this->db->update($iscloud === 1 ? $this->table : "cashbankout", array_merge($where_attr, $attributes), $where_attr);
    }

    public function deleteUangKeluar($iscloud, $where_attr = [])
    {
        return $this->db->delete($iscloud === 1 ? $this->table : "cashbankout", $where_attr);
    }

}