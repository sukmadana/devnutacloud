<?php

/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 10/12/2015
 * Time: 14:13
 */
class Useraksescloud extends CI_Model
{
    protected $_tableName = 'userperusahaanaksescloud';
    protected $_dbMaster;

    function __construct()
    {
        parent::__construct();

    }

    protected function initDbMaster()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function Create($data)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where($this->_tableName, $data);
        $count = $query->num_rows();
        if ($count > 0) {
            return FALSE;
        } else {
            $this->_dbMaster->insert($this->_tableName, $data);
            return TRUE;
        }

        return TRUE;
    }

    public function getAkses($username, $idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('Username' => $username, 'PerusahaanID' => $idperusahaan));
        $result = $query->result();
        return $result[0];
    }

    public function updateHakAkses($kolom, $value, $idperusahaan, $username)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('PerusahaanID' => $idperusahaan, 'Username' => $username));
        $this->_dbMaster->update($this->_tableName, array($kolom => $value));
    }
}