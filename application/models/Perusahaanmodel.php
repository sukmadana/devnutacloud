<?php

/*
 * This file created by Em Husnan
 * Copyright 2015
 */

class Perusahaanmodel extends CI_MODEL
{

    protected $_tableName = 'perusahaan';
    protected $_dbMaster;

    function __construct()
    {
        parent::__construct();
    }

    protected function initDbMaster()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    function Create($data)
    {
        $this->initDbMaster();
        return $this->Update($data);
    }

    function Update($data)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where($this->_tableName, array('PerusahaanID' => $data['PerusahaanID']));
        $count = $query->num_rows();
        if ($count == 0) {
            $this->_dbMaster->insert($this->_tableName, $data);
            return array('status' => 'OK');
        } else {
            $this->_dbMaster->where(array('PerusahaanID' => $data['PerusahaanID']));
            $this->_dbMaster->update($this->_tableName, $data);
            return array('status' => 'OK');
        }
        return array('status' => 'BAD',);
    }

    public function GetIdPerusahaanBelumConfirm($daftarDenganDeviceID)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('registerwithdeviceid' => $daftarDenganDeviceID, 'confirm' => 0));
        $result = $query->result();
        if (count($result) == 1) {
            return $result[0]->email;
        } else {
            return NULL;
        }
    }

    public function UpdateKonfirm($deviceid, $idperusahaan, $time)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('registerwithdeviceid' => $deviceid, 'time' => $time, 'PerusahaanID' => $idperusahaan));
        $this->_dbMaster->update($this->_tableName, array('confirm' => 1,));
    }

    public function GetNamaPerusahaan($idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan));
        $result = $query->result();
        if (count($result) == 1) {
            return $result[0]->namaperusahaan;
        } else {
            return NULL;
        }
    }

    public function GetEmailPerusahaan($idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan));
        $result = $query->result();
        if (count($result) == 1) {
            return $result[0]->email;
        } else {
            return NULL;
        }
    }

    public function GetRegisterDeviceID($idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan));
        $result = $query->result();
        if (count($result) == 1) {
            return $result[0]->registerwithdeviceid;
        } else {
            return NULL;
        }
    }
    //Menu perusahaan muncul ketika belum terdaftar
    // dan sudah
    public function IsMenuPerusahaanVisible($registerwithdeviceid)
    {
        $this->load->database();
        $munculkanmenu = false;
        $query = $this->db->query("SELECT PerusahaanID FROM options WHERE deviceid = " . $this->db->escape($registerwithdeviceid));
        $count = $query->num_rows();
        if ($count == 1) {
            $result = $query->result();
            if (isNotEmpty($result[0]->PerusahaanID)) {
                $munculkanmenu = false;
            } else {
                $munculkanmenu = true;
            }
        }
        return $munculkanmenu;
    }

    // public function getDaftarDevice($idperusahaan) {
    //     $this->load->database();
    //     $query = $this->db->get_where("outlet", array('PerusahaanID' => $idperusahaan));
    //     $result = $query->result();
    //     return $result;
    // }

    public function getDaftarDevice($idperusahaan, $username)
    {
        $perusahaanNo = getPerusahaanNo();
        $query = $this->db->query("
            SELECT * FROM (
            SELECT
                (select count(DeviceID) FROM usertablet where DeviceID = ot.OutletID and PerusahaanNo = ot.PerusahaanNo)
                as user_tablet,

                (select count(OutletID) FROM userperusahaancabang where OutletID = ot.OutletID)
                as user_nuta,

                ot.OutletID, ot.NamaOutlet, ot.AlamatOutlet, ot.Kota, ot.Propinsi, ot.PemilikOutlet, op.IsTrial, op.TglExpired, op.TglInstall, up.Username, op.MobilePhone  FROM outlet ot
            JOIN options op ON op.PerusahaanNo = ot.PerusahaanNo AND op.OutletID = ot.OutletID
            LEFT JOIN userperusahaancabang upc ON upc.Username = '$username'
            LEFT JOIN userperusahaan up ON up.iduserperusahaan = ot.PemilikOutlet
            WHERE ot.PerusahaanID = '$idperusahaan' ORDER BY op.TglExpired)

            final
            GROUP BY OutletID
            ");
        $result = $query->result();
        return $result;
    }

    public function getDaftarDeviceNonOwner($idperusahaan, $username)
    {
        $perusahaanNo = getPerusahaanNo();
        $query = $this->db->query("
            SELECT *
            FROM (
                SELECT (
                select count(DeviceID) FROM usertablet where DeviceID = ot.OutletID and PerusahaanNo = $perusahaanNo )
                as user_tablet,
                (select count(OutletID) FROM userperusahaancabang where OutletID = ot.OutletID) as user_nuta,
                ot.OutletID, ot.NamaOutlet, ot.AlamatOutlet, ot.Kota, ot.Propinsi, ot.PemilikOutlet, op.IsTrial, op.TglExpired,op.TglInstall, up.Username, op.MobilePhone
                FROM userperusahaancabang upc
                LEFT JOIN  outlet ot
                    ON upc.OutletID = ot.OutletID
                LEFT JOIN options op
                    ON op.PerusahaanNo = ot.PerusahaanNo AND op.OutletID = ot.OutletID
                LEFT JOIN userperusahaan up
                    ON up.iduserperusahaan = ot.PemilikOutlet
                WHERE ot.PerusahaanID = '$idperusahaan'
                AND upc.Username = '$username'
                ORDER BY op.TglExpired
            ) final
            GROUP BY OutletID
        ");
        return $query->result();;
    }

    public function generatePerusahaanID($namaperusahaan, $email, $pemilik, $deviceid)
    {
        $this->initDbMaster();
        $sql = "call nutacloud.generateidperusahaan('" . $namaperusahaan . "', '" . $email . "', '" . $pemilik . "', '" . $deviceid . "');";
        $query = $this->_dbMaster->query($sql);
        $result = $query->result();
        $perusahaanID = $result[0]->perusahaanID;
        return $perusahaanID;
    }

    public function isEmailPerusahaanExist($email)
    {
        $query = $this->db->get_where($this->_tableName, array('email' => $email));
        $result = $query->num_rows();

        return $result > 0;
    }

    public function isPerusahaanExist($idperusahaan)
    {
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan));
        $result = $query->num_rows();
        return $result > 0;
    }

    public function getModulPerusahaan($idperusahaan)
    {
        $sql = "SELECT
CASE WHEN (SUM(CASE WHEN StockModule='true' THEN 1 ELSE 0 END)) >0 THEN 1 ELSE 0 END IsUseStockModule,
CASE WHEN (SUM(CASE WHEN StockModule='true' AND MenuRacikan='true' THEN 1 ELSE 0 END)) >0 THEN 1 ELSE 0 END IsUseIngredientsModule,
CASE WHEN (SUM(CASE WHEN PurchaseModule='true' THEN 1 ELSE 0 END)) >0 THEN 1 ELSE 0 END IsUsePurchaseModule,
CASE WHEN (SUM(CASE WHEN Tax='true' THEN 1 ELSE 0 END)) >0 THEN 1 ELSE 0 END IsUseTaxModule,
CASE WHEN (SUM(PriceVariation))>0 THEN 1 ELSE 0 END IsUseVarianAndPrice,
CASE WHEN (SUM(DiningTable))>0 THEN 1 ELSE 0 END IsDiningTable,
CASE WHEN (MAX(Greatest(CreatedVersionCode,EditedVersionCode)))>=98 THEN 1 ELSE 0 END IsUsePushNotification
FROM options WHERE PerusahaanID= " . $this->db->escape($idperusahaan);
        $query = $this->db->query($sql);
        $result = $query->result();
        return array(
            'IsUseStockModule' => $result[0]->IsUseStockModule, 'IsUsePurchaseModule' => $result[0]->IsUsePurchaseModule, 'IsUseTaxModule' => $result[0]->IsUseTaxModule,
            'IsUseVarianAndPrice' => $result[0]->IsUseVarianAndPrice, 'IsDiningTable' => $result[0]->IsDiningTable,
            'IsUseIngredientsModule' => $result[0]->IsUseIngredientsModule
        );
    }

    public function get_perusahaanno_by_devid($devid)
    {
        $queryStr = "SELECT PerusahaanNo FROM perusahaan p WHERE p.PerusahaanID= " . $this->db->escape($devid);
        $queryNo = $this->db->query($queryStr);
        return $queryNo->result();
    }
}
