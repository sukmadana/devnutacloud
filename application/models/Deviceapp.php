<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deviceapp extends CI_Model
{
    var $_tableName = "device_app";
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

    public function updateStatus($perusahaanNo, $outletID, $deviceNo, $isActive)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array(
            'PerusahaanNo' => $perusahaanNo, 
            'OutletID' => $outletID,
            'DeviceNo' => $deviceNo,
        ));
        $this->_dbMaster->update($this->_tableName, array('IsActive' => $isActive));
    }
}