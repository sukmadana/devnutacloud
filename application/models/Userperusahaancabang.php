<?php

/*
 * This file created by Em Husnan 
 * Copyright 2015
 */

class Userperusahaancabang extends CI_MODEL
{

    protected $_tableName = 'userperusahaancabang';
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
        $this->_dbMaster->insert($this->_tableName, $data);
        return TRUE;
    }

    public function Delete($idperusahaan, $username, $deviceid)
    {
        $this->initDbMaster();
        $this->_dbMaster->delete($this->_tableName, array('PerusahaanID' => $idperusahaan, 'Username' => $username, "OutletID" => $deviceid));
        return TRUE;
    }

    public function getListCabang($username, $idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'Username' => $username));

        return $query->result();

    }

    public function isUserAllowAksesCabang($cabang, $username, $idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'Username' => $username,
            'OutletID' => $cabang));
        if ($query !== FALSE) {
            return $query->num_rows() == 1;
        } else {
            return FALSE;
        }


    }

    public function isUserHasAksesCabang($username, $idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'Username' => $username));
        return $query->num_rows() > 0;
    }
}
