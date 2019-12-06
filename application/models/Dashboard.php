<?php

/*
 * This file created by Em Husnan 
 * Copyright 2015
 */

class Dashboard extends CI_Model
{

    var $dateFormat = 'Y-m-d';
    var $NoPerusahaan = 0;
    var $tabelsale = 'sale';
    var $tabelsaleitemdetail = 'saleitemdetail';
    var $tabelsaleitemdetailingredients = 'saleitemdetailingredients';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function setNoPerusahaan($nomor)
    {
        $this->NoPerusahaan = $nomor;
    }

    public function setTableSale($tbl_sale)
    {
        $this->tabelsale = $tbl_sale;
    }

    public function setTableSaleDetail($tbl_sale_detail)
    {
        $this->tabelsaleitemdetail = $tbl_sale_detail;
    }

    public function setTableSaleDetailIngredients($tbl_sale_detail_ing)
    {
        $this->tabelsaleitemdetailingredients = $tbl_sale_detail_ing;
    }

    function getRpPenjualan($perusahaanID, $outlet, $deviceID, $cabangs, $tglmulai, $tglsampai)
    {
        $dateNow = date($this->dateFormat);
        $Condition = "";
        $Condition2 = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE Pending = 'false' AND s.deviceid=" . $this->db->escape($deviceID) . " AND s.SaleDate >= " . $this->db->escape($tglmulai) . " AND s.SaleDate <= " . $this->db->escape($tglsampai) . " ";
            $Condition2 = " WHERE s.deviceid=" . $this->db->escape($deviceID) . " AND s.CreatedDate >= " . $this->db->escape($tglmulai) . " AND s.CreatedDate <= " . $this->db->escape($tglsampai) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND SaleDate >= " . $this->db->escape($tglmulai) . " AND SaleDate <=" . $this->db->escape($tglsampai) . " ";
                    $Condition2 = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND CashDownPayment+BankDownPayment<>0 AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND CreatedDate >= " . $this->db->escape($tglmulai) . " AND CreatedDate <=" . $this->db->escape($tglsampai) . " ";
                } else {
                    $Condition = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND SaleDate >= " . $this->db->escape($tglmulai) . " AND SaleDate <= " . $this->db->escape($tglsampai) . " ";
                    $Condition2 = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND CashDownPayment+BankDownPayment<>0 AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND CreatedDate >= " . $this->db->escape($tglmulai) . " AND CreatedDate <= " . $this->db->escape($tglsampai) . " ";
                }
            } else {
                $Condition = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.deviceid=" . $this->db->escape($outlet) . " AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND SaleDate >= " . $this->db->escape($tglmulai) . " AND SaleDate <= " . $this->db->escape($tglsampai) . " ";
                $Condition2 = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND CashDownPayment+BankDownPayment<>0 AND s.deviceid=" . $this->db->escape($outlet) . " AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND CreatedDate >= " . $this->db->escape($tglmulai) . " AND CreatedDate <= " . $this->db->escape($tglsampai) . " ";
            }
        }

        $queryStr = "SELECT coalesce(SUM(Total+Rounding-(CashDownPayment+BankDownPayment)),0) Total FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo " . $Condition;
        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }

        $queryStr = "SELECT coalesce(SUM(CashDownPayment+BankDownPayment),0) Total FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo " . $Condition2;
        $query = $this->db->query($queryStr);
        $result = $query->result();
        if (count($result) >= 0) {
            $total = $total + $result[0]->Total;
        }
        return $total;
    }

    public function getRpPenjualanKemarin($perusahaanID, $outlet, $deviceID, $cabangs)
    {
        $dateYesterday = date($this->dateFormat, time() - 60 * 60 * 24);
        $Condition = "";
        $Condition2 = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE Pending = 'false' AND s.deviceid=" . $this->db->escape($deviceID) . " AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
            $Condition2 = " WHERE CashDownPayment+BankDownPayment<>0 AND s.deviceid=" . $this->db->escape($deviceID) . " AND CreatedDate=" . $this->db->escape($dateYesterday) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
                    $Condition2 = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND CashDownPayment+BankDownPayment<>0 AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND CreatedDate=" . $this->db->escape($dateYesterday) . " ";
                } else {
                    $Condition = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
                    $Condition2 = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND CashDownPayment+BankDownPayment<>0 AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND CreatedDate=" . $this->db->escape($dateYesterday) . " ";
                }
            } else {
                $Condition = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.deviceid=" . $this->db->escape($outlet) . " AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
                $Condition2 = " WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND CashDownPayment+BankDownPayment<>0 AND s.deviceid=" . $this->db->escape($outlet) . " AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " AND CreatedDate=" . $this->db->escape($dateYesterday) . " ";
            }
        }
        $queryStr = "SELECT coalesce(SUM(Total+Rounding),0) Total FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo " . $Condition;
        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }
        return $total;
    }

    function getHppHariIni($perusahaanID, $outlet, $deviceID, $cabangs, $tglmulai, $tglsampai)
    {
        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE s.Pending = 'false' AND s.deviceid=" . $this->db->escape($deviceID) . " AND s.SaleDate>=" . $this->db->escape($tglmulai) . " AND s.SaleDate<=" . $this->db->escape($tglsampai) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Pending = 'false' AND s.DeviceID IN (" . $strCabangs . ") AND s.SaleDate>=" . $this->db->escape($tglmulai) . " AND s.SaleDate<=" . $this->db->escape($tglsampai) . " ";
                } else {
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Pending = 'false' AND s.SaleDate>=" . $this->db->escape($tglmulai) . " AND s.SaleDate<=" . $this->db->escape($tglsampai) . " ";
                }
            } else {
                $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Pending = 'false' AND s.deviceid=" . $this->db->escape($outlet) . " AND s.SaleDate>=" . $this->db->escape($tglmulai) . " AND s.SaleDate<=" . $this->db->escape($tglsampai) . " ";
            }
        }

        $queryStr = "SELECT coalesce(SUM(COGS),0) Total FROM " . $this->tabelsale . " s INNER JOIN
                " . $this->tabelsaleitemdetail . " sd ON s.PerusahaanNo=sd.PerusahaanNo
                AND s.DeviceID=sd.DeviceID 
                AND s.TransactionID=sd.TransactionID
                AND s.DeviceNo=sd.TransactionDeviceNo 
                " . $Condition;
        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }
        /*
                $sqlPurchaseIdependentTotalOutlet = "
                    SELECT SUM(sd.SubTotal) Total
                    FROM purchase s INNER JOIN purchaseitemdetail sd
                    ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo
                    AND s.DeviceID = sd.DeviceID
                    LEFT JOIN masteritem mi ON sd.PerusahaanNo=mi.PerusahaanNo AND sd.ItemID=mi.ItemID AND mi.DeviceID=sd.DeviceID
                    LEFT JOIN masteritemdetailingredients md
                    ON sd.PerusahaanNo=md.PerusahaanNo AND md.IngredientsID=sd.ItemID
                    AND md.DeviceID = sd.DeviceID
                    LEFT JOIN mastermodifierdetail md2
                    ON sd.PerusahaanNo=md2.PerusahaanNo AND md2.IngredientsID=sd.ItemID
                    AND md2.DeviceID = sd.DeviceID
                    INNER JOIN options o
                    ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                    " . str_replace("SaleDate","PurchaseDate",$Condition) . " AND
                  md.IngredientsID IS NULL AND md2.IngredientsID IS NULL AND (mi.IsProduct='false' OR (mi.IsProduct IS NULL AND sd.IsProduct=0))
                    ";

                $query = $this->db->query($sqlPurchaseIdependentTotalOutlet);
                $result = $query->result();
                if (count($result) >= 0) {
                    $total = $total + $result[0]->Total;
                }
        */
        return $total;
    }

    function getHppKemarin($perusahaanID, $outlet, $deviceID, $cabangs)
    {
        $dateYesterday = date($this->dateFormat, time() - 60 * 60 * 24);
        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE s.Pending = 'false' AND s.deviceid=" . $this->db->escape($deviceID) . " AND s.SaleDate=" . $this->db->escape($dateYesterday) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Pending = 'false' AND s.DeviceID IN (" . $strCabangs . ") AND s.SaleDate=" . $this->db->escape($dateYesterday) . " ";
                } else {
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Pending = 'false' AND s.SaleDate=" . $this->db->escape($dateYesterday) . " ";
                }
            } else {
                $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Pending = 'false' AND s.deviceid=" . $this->db->escape($outlet) . " AND s.SaleDate=" . $this->db->escape($dateYesterday) . " ";
            }
        }

        $queryStr = "SELECT coalesce(SUM(COGS),0) Total  FROM " . $this->tabelsale . " s INNER JOIN
                " . $this->tabelsaleitemdetail . " sd 
                ON s.PerusahaanNo=sd.PerusahaanNo
                AND s.DeviceID=sd.DeviceID
                AND s.TransactionID=sd.TransactionID 
                AND s.DeviceNo=sd.TransactionDeviceNo
                " . $Condition;
        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }
        /*
               $sqlPurchaseIdependentTotalOutlet = "
                    SELECT SUM(sd.SubTotal) Total
                    FROM purchase s INNER JOIN purchaseitemdetail sd
                    ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo
                    AND s.DeviceID = sd.DeviceID
                    LEFT JOIN masteritem mi ON sd.PerusahaanNo=mi.PerusahaanNo AND sd.ItemID=mi.ItemID AND mi.DeviceID=sd.DeviceID
                    LEFT JOIN masteritemdetailingredients md
                    ON sd.PerusahaanNo=md.PerusahaanNo AND md.IngredientsID=sd.ItemID
                    AND md.DeviceID = sd.DeviceID
                    LEFT JOIN mastermodifierdetail md2
                    ON sd.PerusahaanNo=md2.PerusahaanNo AND md2.IngredientsID=sd.ItemID
                    AND md2.DeviceID = sd.DeviceID
                    INNER JOIN options o
                    ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                    " . str_replace("SaleDate","PurchaseDate",$Condition) . " AND
                  md.IngredientsID IS NULL AND md2.IngredientsID IS NULL AND (mi.IsProduct='false' OR (mi.IsProduct IS NULL AND sd.IsProduct=0))
                    ";

                $query = $this->db->query($sqlPurchaseIdependentTotalOutlet);
                $result = $query->result();
                if (count($result) >= 0) {
                    $total = $total + $result[0]->Total;
                }
        */
        return $total;
    }

    public function getJumlahTransaksi($perusahaanID, $outlet, $deviceID, $cabangs, $datestart, $dateend)
    {
        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE Pending = 'false' AND s.deviceid=" . $this->db->escape($deviceID) . " AND SaleDate >=" . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.DeviceID IN (" . $strCabangs . ") AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " ";
                } else {
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " ";
                }
            } else {
                $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.deviceid=" . $this->db->escape($outlet) . " AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " ";
            }
        }
        $queryStr = "SELECT coalesce(COUNT(TransactionID),0) Total  FROM " . $this->tabelsale . " s " . $Condition;

        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }
        return $total;
    }

    public function getJumlahTransaksiKemarin($perusahaanID, $outlet, $deviceID, $cabangs)
    {
        $dateYesterday = date($this->dateFormat, time() - 60 * 60 * 24);
        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE Pending = 'false' AND s.deviceid=" . $this->db->escape($deviceID) . " AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.DeviceID IN (" . $strCabangs . ") AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
                } else {
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
                }
            } else {
                $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.deviceid=" . $this->db->escape($outlet) . " AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
            }
        }
        $queryStr = "SELECT coalesce(COUNT(TransactionID),0) Total FROM " . $this->tabelsale . " s " . $Condition;
        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }
        return $total;
    }

    public function getRataTransaksi($perusahaanID, $outlet, $deviceID, $cabangs, $datestart, $dateend)
    {
        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE Pending = 'false' AND s.deviceid=" . $this->db->escape($deviceID) . " AND SaleDate >=" . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.DeviceID IN (" . $strCabangs . ") AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " ";
                } else {
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " ";
                }
            } else {
                $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.deviceid=" . $this->db->escape($outlet) . " AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " ";
            }
        }
        $queryStr = "SELECT coalesce(AVG(Total),0) Total  FROM " . $this->tabelsale . " s " . $Condition;

        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }
        return $total;
    }

    public function getRataTransaksiKemarin($perusahaanID, $outlet, $deviceID, $cabangs)
    {
        $dateYesterday = date($this->dateFormat, time() - 60 * 60 * 24);
        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE Pending = 'false' AND s.deviceid=" . $this->db->escape($deviceID) . " AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.DeviceID IN (" . $strCabangs . ") AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
                } else {
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
                }
            } else {
                $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending = 'false' AND s.deviceid=" . $this->db->escape($outlet) . " AND SaleDate=" . $this->db->escape($dateYesterday) . " ";
            }
        }
        $queryStr = "SELECT coalesce(AVG(Total),0) Total FROM " . $this->tabelsale . " s " . $Condition;
        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }
        return $total;
    }

    public function getBiaya($perusahaanID, $outlet, $deviceID, $cabangs, $datestart, $dateend)
    {
        $dateNow = date($this->dateFormat);
        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE s.deviceid=" . $this->db->escape($deviceID) . " AND TransactionDate >= " . $this->db->escape($datestart) . " AND TransactionDate <= " . $this->db->escape($dateend) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND TransactionDate >= " . $this->db->escape($datestart) . " AND TransactionDate <= " . $this->db->escape($dateend) . " ";
                } else {
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND TransactionDate >= " . $this->db->escape($datestart) . " AND TransactioNDate  <= " . $this->db->escape($dateend) . " ";
                }
            } else {
                $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.deviceid=" . $this->db->escape($outlet) . " AND TransactionDate >= " . $this->db->escape($datestart) . " AND TransactionDate <= " . $this->db->escape($dateend) . " ";
            }
        }
        $queryStr = "SELECT coalesce(SUM(Amount),0) Total  FROM cashbankout s " . $Condition . " AND s.SpendingType = 1";
        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }

        $queryStr = "SELECT coalesce(SUM(Amount),0) Total  FROM cloud_cashbankout s " . $Condition . " AND s.SpendingType = 1";
        $query = $this->db->query($queryStr);
        $result = $query->result();
        if (count($result) >= 0) {
            $total += $result[0]->Total;
        }
        return $total;
    }

    public function getBiayaKemarin($perusahaanID, $outlet, $deviceID, $cabangs)
    {
        $dateYesterday = date($this->dateFormat, time() - 60 * 60 * 24);
        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " WHERE s.deviceid=" . $this->db->escape($deviceID) . " AND TransactionDate=" . $this->db->escape($dateYesterday) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND TransactionDate=" . $this->db->escape($dateYesterday) . " ";
                } else {
                    $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND TransactionDate=" . $this->db->escape($dateYesterday) . " ";
                }
            } else {
                $Condition = " WHERE s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.deviceid=" . $this->db->escape($outlet) . " AND TransactionDate=" . $this->db->escape($dateYesterday) . " ";
            }
        }
        $queryStr = "SELECT coalesce(SUM(Amount),0) Total  FROM cashbankout s " . $Condition . " AND s.SpendingType = 1";
        $query = $this->db->query($queryStr);
        $result = $query->result();
        $total = 0;
        if (count($result) >= 0) {
            $total = $result[0]->Total;
        }

        $queryStr = "SELECT coalesce(SUM(Amount),0) Total  FROM cloud_cashbankout s " . $Condition . " AND s.SpendingType = 1";
        $query = $this->db->query($queryStr);
        $result = $query->result();
        if (count($result) >= 0) {
            $total += $result[0]->Total;
        }
        return $total;
    }

    function getPenjualanBulanIni($perusahaanID, $outlet, $deviceID, $cabangs, $datestart, $dateend)
    {
        $sqlQuery = "";
        //$m = date('m');
        //$Y = date('Y');$Condition = "";

        if ($perusahaanID == $deviceID) {
            $ConditionMore = " AND s.deviceid=" . $this->db->escape($deviceID) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $ConditionMore = " AND s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") ";
                } else {
                    $ConditionMore = " AND s.PerusahaanNo=" . $this->NoPerusahaan . " ";
                }
            } else {
                $ConditionMore = " AND s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.deviceid=" . $this->db->escape($outlet) . " ";
            }
        }

        $days = $this->getAllTanggalBetweenTwoDates($datestart, $dateend);
        $bulans = array();
        $haris = array();
        foreach ($days as $day) {
            if (in_array($day['bulan'], $bulans) == FALSE) {
                array_push($bulans, $day['bulan']);
            }
        }
        for ($a = 1; $a < 32; $a++) {
//            if ($a < 10) {
//                array_push($haris, '0' . $a);
//            } else {
            array_push($haris, (string)$a);
//            }
        }
        $isHariIni = ($datestart === $dateend) && ($datestart === date('Y-m-d'));
        if ($isHariIni) {
            $exDateStart = explode('-', $datestart);
            $tahun = $exDateStart[0];
            $bulan = $exDateStart[1];


//            $WHERE = " WHERE saledate LIKE '" . $tahun . "-" . $bulan . "%'";
        }

        foreach ($days as $day) {
//            if ($isHariIni == FALSE) {
            $WHERE = " WHERE saledate='" . $day['tahun'] . "-" . $day['bulan'] . "-" . $day['tanggal'] . "' ";
//            }
            $sqlQuery .= " SELECT '" . $day['tahun'] . "-" . $day['bulan'] . "-" . $day['tanggal'] . "' as Tanggal , coalesce(SUM(Total),0) as Total from " . $this->tabelsale . " s
             " . $WHERE . "AND Pending = 'false' " . $ConditionMore . "\n";
            $sqlQuery .= " UNION ALL\n";
        }

        $sqlQuery = substr($sqlQuery, 0, strlen($sqlQuery) - strlen("UNION ALL\n"));

        $whereDate = "
WHERE selected_date between '" . $days[0]['tahun'] . "-" . $days[0]['bulan'] . "-" . $days[0]['tanggal'] . "' AND '" .
            $days[count($days) - 1]['tahun'] . "-" . $days[count($days) - 1]['bulan'] . "-31' ";
        $whereSaleDate = "
WHERE SaleDate between '" . $days[0]['tahun'] . "-" . $days[0]['bulan'] . "-" . $days[0]['tanggal'] . "' AND '" .
            $days[count($days) - 1]['tahun'] . "-" . $days[count($days) - 1]['bulan'] . "-31' ";
        $whereCreatedDate = "
WHERE CreatedDate between '" . $days[0]['tahun'] . "-" . $days[0]['bulan'] . "-" . $days[0]['tanggal'] . "' AND '" .
            $days[count($days) - 1]['tahun'] . "-" . $days[count($days) - 1]['bulan'] . "-31' ";
        $sqlQuery = "
select v.selected_date as Tanggal, 
COALESCE(SUM(TotalForSum),0) Total from 
(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
LEFT JOIN 
(SELECT s.*,STR_TO_DATE(s.SaleDate, '%Y-%m-%d') SaleDateStr, 
s.Total+s.Rounding - (s.CashDownPayment+s.BankDownPayment) TotalForSum FROM sale s
 " . $whereSaleDate . " AND Pending='false' " . $ConditionMore . "
 UNION ALL
 SELECT s.*,STR_TO_DATE(s.CreatedDate, '%Y-%m-%d') SaleDateStr,
 s.CashDownPayment+s.BankDownPayment TotalForSum FROM sale s
 " . $whereCreatedDate . " AND CashDownPayment+BankDownPayment <> 0 " . $ConditionMore . "
)s ON s.SaleDateStr=v.selected_date
-- sale s ON ((STR_TO_DATE(s.SaleDate, '%Y-%m-%d')=v.selected_date AND Pending = 'false')
-- OR (STR_TO_DATE(s.CreatedDate, '%Y-%m-%d')=v.selected_date AND CashDownPayment+BankDownPayment <> 0)
-- )
" . // $ConditionMore . 
//"
//LEFT JOIN sale s2 ON STR_TO_DATE(s.CreatedDate, '%Y-%m-%d')=v.selected_date AND s2.CashDownPayment+s2.BankDownPayment<>0 " . $ConditionMore .
            $whereDate . "
GROUP BY selected_date
";
//log_message('error',$sqlQuery);

        $query = $this->db->query($sqlQuery);
        $result = $query->result();
        return $this->getDataChartPenjualanBulanIni($result, $haris, $bulans);
    }
//Fungsi ini harus return array
// agar bisa di json_encode
    function getDataChartPenjualanBulanIni($resultPenjualanBulanIni, $haris, $bulans)
    {
        //----
        $strData = '[';
        $dataPerBulan = array();
        foreach ($bulans as $series) {
            $data = '[';
            $dataPer = array();
            foreach ($haris as $x) {
                $row = $this->findData($x, $series, $resultPenjualanBulanIni);
                $data .= '[' . $row['tgl'] . ',' . $row['total'] . '],';
                array_push($dataPer, $row);
            }
            $dataPerBulan[$series] = $dataPer;
            $data = substr($data, 0, strlen(($data)) - 1);
            $data .= ']';
            $strSeries = $this->createSeries($series, $data);
            $strData .= $strSeries;

        }

        $strData = substr($strData, 0, strlen($strData) - 1);
        $strData .= "]";
        $strTicks = "[";
        foreach ($haris as $hari) {

            if ($hari < 10) {
                $strTicks .= '"0' . $hari . '"';
            } else {
                $strTicks .= '"' . $hari . '"';
            }
            if ($hari != 31) {
                $strTicks .= ',';
            }
        }
        $strTicks .= "]";

        return array('data' => $strData, 'ticks' => $strTicks);
        //-----
    }

    protected function findData($ptgl, $pbln, $resultPenjualanBulanIni)
    {
        $retval = array('tgl' => $ptgl, 'bln' => $pbln, 'total' => 0);
        foreach ($resultPenjualanBulanIni as $row) {
            $rowTanggal = explode('-', $row->Tanggal);

            $tgl = $rowTanggal[2];
            $bln = $rowTanggal[1];

            if (intval($tgl) == intval($ptgl) && intval($bln) == intval($pbln)) {

                $retval = array('tgl' => intval($tgl), 'bln' => intval($bln), 'total' => $row->Total);

                break;
            }
        }

        return $retval;
    }


    var
        $monthIndonesia = array(
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    );
    var
        $colors = array('01' => "#83ce16",
        '02' => "#FF80AB",
        '03' => "#8C9EFF",
        '04' => "#8C9EFF",
        '05' => "#FDD835",
        '06' => "#4FC3F7",
        '07' => "#d84315",
        '08' => "#ff8a80",
        '09' => "#ffb300",
        '10' => "#90a4ae",
        '11' => "#64b5f6",
        '12' => "#b388ff"
    );

    protected function createSeries($bulan, $data)
    {
        return '{
            "data": ' . $data . ',
            "label": "' . $this->monthIndonesia[$bulan] . '",
            "color": "' . $this->colors[$bulan] . '",

            "lines": {
                "show": true,
                "fill": 0,
                "lineWidth": 2
            },
            "splines": {
                "show": false,
                "tension": 0.5,
                "lineWidth": 2,
                "fill": 0
            },
            "points": {
                "show": true,
                "lineWidth": 2,
                "radius": 4,
                "symbol": "circle",
                "fill": true,
                "fillColor": "#ffffff"

            }

        },';
    }

//    protected function getTglBetweenTwoDates($dateYmdStart, $dateYmdEnd)
//    {
//        $datestart = strtotime($dateYmdStart);
//        $dateend = strtotime($dateYmdEnd);
//        $datediff = $dateend - $datestart;
//        $toDateDiff = ($datediff / (60 * 60 * 24));
//        $retval = array();
//
//        $toSplitNoDiff = explode('#', date('d#m#Y', $datestart));
//        array_push($retval, array("tanggal" => $toSplitNoDiff[0], 'bulan' => $toSplitNoDiff[1], 'tahun' => $toSplitNoDiff[2]));
//
//        for ($from = 0; $from < $toDateDiff; $from++) {
//            $timeNext = strtotime(date('Y-m-d', $datestart) . "+1 day");
//            $datestart = $timeNext;
//            $toSplit = explode('#', date('d#m#Y', $timeNext));
//            array_push($retval, array("tanggal" => $toSplit[0], 'bulan' => $toSplit[1], 'tahun' => $toSplit[2]));
//        }
//        return $retval;
//    }

    protected function getAllTanggalBetweenTwoDates($dateYmdStart, $dateYmdEnd)
    {

        $datestart = strtotime($dateYmdStart);
        $dateend = strtotime($dateYmdEnd);
        $differentTime = $dateend - $datestart;
        $toMonthDif = round($differentTime / 60 / 60 / 24 / 30);
        $splitDateStart = explode('#', date('d#m#Y', $datestart));
        $splitDateEnd = explode('#', date('d#m#Y', $dateend));

        $startBulan = intval($splitDateStart[1]);
        $startTahun = $splitDateStart[2];
        #$endBulan = intval($splitDateEnd[1]);
        #$endTahun = $splitDateEnd[2];
        #$toMonthDif = $endBulan - $startBulan;
        $retval = array();

        for ($x = 0; $x <= $toMonthDif; $x++) {
            $bln = $startBulan;
            if ($bln > 12) {
                $bln = 1;
                $startTahun++;
            }
            if ($bln < 10) {
                $bln = '0' . $bln;
            }
            for ($from = 1; $from <= 31; $from++) {
                $tgl = $from;
                if ($from < 10) {
                    $tgl = '0' . $from;
                }
                array_push($retval, array("tanggal" => $tgl, 'bulan' => $bln, 'tahun' => $startTahun));
            }
            $startBulan++;
        }
        return $retval;
    }

    function getPenjualanTerlaris($perusahaanID, $outlet, $deviceID, $cabangs, $datestart, $dateend)
    {
        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $exDateStart = explode('-', $datestart);
            $tahun = $exDateStart[0];
            $bulan = $exDateStart[1];
            $WHERE = " WHERE 1=1";
            $Condition = " s.DeviceID = " . $this->db->escape($deviceID) . "";
        } else {
            $WHERE = " WHERE s.SaleDate >= " . $this->db->escape($datestart) . " AND s.SaleDate <= " . $this->db->escape($dateend) . "";
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo=" . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID = " . $this->db->escape($outlet) . " AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " ";
            }
        }
//        $isHariIni = ($datestart === $dateend) && ($datestart === date('Y-m-d'));
//        if ($isHariIni) {
//            $exDateStart = explode('-', $datestart);
//            $tahun = $exDateStart[0];
//            $bulan = $exDateStart[1];
//            $WHERE = " WHERE s.SaleDate LIKE '" . $tahun . "-" . $bulan . "%'";
//        } else {

//        }

        $sqlquery = "
    SELECT * FROM
    (
        SELECT Z.ItemName,SUM(Z.Quantity) Quantity,SUM(Z.Total) Total FROM
        (
                SELECT s.DeviceID,sd.ItemName, SUM(sd.Quantity) Quantity,SUM(sd.SubTotal) Total
                FROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd
                ON s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceID=sd.DeviceID
                AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo AND s.Pending = 'false'
                INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                " . $WHERE . "
                AND " . $Condition . "
                GROUP BY s.DeviceID,sd.ItemName
        ) Z
                GROUP BY Z.ItemName
        ) A
        ORDER BY A.Quantity desc limit 5
        ";
        //log_message('error', $sqlquery);

        $query = $this->db->query($sqlquery);
        $result = $query->result();
        return $this->getDataChartPenjualanTerlaris($result);
    }

    function getDataChartPenjualanTerlaris($resultPenjualanTerlaris)
    {
        $strData = '[';
        foreach ($resultPenjualanTerlaris as $index => $p) {
            $strData .= '{"label":"' . html_escape($p->ItemName) . '",';
            $strData .= '"data":' . $p->Quantity . "},";
            if ($index == count($resultPenjualanTerlaris) - 1) {
                $strData = substr($strData, 0, strlen(($strData)) - 1);
            }
        }
        $strData .= ']';
        return $strData;
    }


    function getChartRekapPembayaran($perusahaanID, $outlet, $deviceID, $cabangs, $datestart, $dateend)
    {

        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " AND s.DeviceID = " . $this->db->escape($deviceID) . "";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " AND o.PerusahaanNo=" . $this->NoPerusahaan . " AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . "";
                } else {
                    $Condition = " AND o.PerusahaanNo=" . $this->NoPerusahaan . " AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . "";
                }
            } else {
                $Condition = " AND o.PerusahaanNo=" . $this->NoPerusahaan . " AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " AND s.DeviceID = " . $this->db->escape($outlet) . " AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " ";
            }
        }

        $sql = "
SELECT 'Tunai' AS Uraian, COALESCE((SELECT SUM(CashPaymentAmount-s.change)),0) Total FROM " . $this->tabelsale . " s
                INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE (PaymentMode=1 OR PaymentMode=3)  " . $Condition . "
UNION ALL
SELECT 'Non-Tunai' AS Uraian, COALESCE((SELECT SUM(BankPaymentAmount)),0) Total FROM " . $this->tabelsale . " s
                INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE (PaymentMode=2 OR PaymentMode=3)  " . $Condition . "
";
        // echo $sql;

        //log_message('error', $sql);

        $query = $this->db->query($sql);

        $result = $query->result();
        return $this->getDataChartRekapPembayaran($result);
    }

    function getDataChartRekapPembayaran($resultRekapPembayaran)
    {
        $strData = "[";
        foreach ($resultRekapPembayaran as $index => $p) {

            $strData .= '{
            "data": ' . $p->Total . ' ,
            "label": "' . html_escape($p->Uraian) . '"
            },';
            if ($index == count($resultRekapPembayaran) - 1) {
                $strData = substr($strData, 0, strlen(($strData)) - 1);
            }
        }
        $strData .= "]";
        return array('data' => $strData);
    }

    function getChartOutletTerlaris($perusahaanID, $outlet, $deviceID, $cabangs, $datestart, $dateend)
    {

        $Condition = "";
        if ($perusahaanID == $deviceID) {
            $Condition = " o.DeviceID = " . $this->db->escape($deviceID) . "";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanID = " . $this->db->escape($perusahaanID) . "";
                }
            } else {
                $Condition = " o.DeviceID = " . $this->db->escape($outlet) . " AND o.PerusahaanID = " . $this->db->escape($perusahaanID) . " ";
            }
        }


        $sql = "
SELECT Outlet, Total FROM
(
        SELECT CONCAT(REPLACE(o.CompanyName, '\n', '<br/>'), ' ',REPLACE(o.CompanyAddress, '\n', '<br/>')) AS Outlet, SUM(sd.Quantity) Total FROM " . $this->tabelsale . " s
        INNER JOIN " . $this->tabelsaleitemdetail . " sd 
        ON s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceID=sd.DeviceID AND s.TransactionID=sd.TransactionID
        AND s.DeviceNo=sd.TransactionDeviceNo
        INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
        WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND SaleDate >= " . $this->db->escape($datestart) . " AND SaleDate <= " . $this->db->escape($dateend) . " AND " . $Condition . "
        GROUP BY o.CompanyName, o.CompanyAddress
) X
ORDER BY X.Total DESC LIMIT 5
";
        //log_message('error', $sql);
        $query = $this->db->query($sql);
        $result = $query->result();
        return $this->getDataChartOutletTerlaris($result);
    }

    function getDataChartOutletTerlaris($resultOutetTerlaris)
    {
        $strData = "[";
        foreach ($resultOutetTerlaris as $index => $p) {

            $strData .= '{
            "data":' . $p->Total . ' ,
            "label": "' . html_escape($p->Outlet) . '"
            },';
            if ($index == count($resultOutetTerlaris) - 1) {
                $strData = substr($strData, 0, strlen(($strData)) - 1);
            }
        }
        $strData .= "]";
        return array('data' => $strData);
    }

    function getPengunjungBulanIni($perusahaanID, $outlet, $deviceID, $cabangs, $datestart, $dateend)
    {
        $sqlQuery = "";
        //$m = date('m');
        //$Y = date('Y');$Condition = "";

        if ($perusahaanID == $deviceID) {
            $ConditionMore = " AND s.deviceid=" . $this->db->escape($deviceID) . " ";
        } else {
            if ($outlet == "Semua") {
                $strCabangs = '';
                foreach ($cabangs as $cabang) {
                    $strCabangs = $strCabangs . ",'" . $cabang->OutletID . "'";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $ConditionMore = " AND s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") ";
                } else {
                    $ConditionMore = " AND s.PerusahaanNo=" . $this->NoPerusahaan . " ";
                }
            } else {
                $ConditionMore = " AND s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.deviceid=" . $this->db->escape($outlet) . " ";
            }
        }

        $days = $this->getAllTanggalBetweenTwoDates($datestart, $dateend);
        $bulans = array();
        $haris = array();
        foreach ($days as $day) {
            if (in_array($day['bulan'], $bulans) == FALSE) {
                array_push($bulans, $day['bulan']);
            }
        }
        for ($a = 1; $a < 32; $a++) {
//            if ($a < 10) {
//                array_push($haris, '0' . $a);
//            } else {
            array_push($haris, (string)$a);
//            }
        }
        $isHariIni = ($datestart === $dateend) && ($datestart === date('Y-m-d'));
        if ($isHariIni) {
            $exDateStart = explode('-', $datestart);
            $tahun = $exDateStart[0];
            $bulan = $exDateStart[1];


//            $WHERE = " WHERE saledate LIKE '" . $tahun . "-" . $bulan . "%'";
        }

        foreach ($days as $day) {
//            if ($isHariIni == FALSE) {
            $WHERE = " WHERE saledate='" . $day['tahun'] . "-" . $day['bulan'] . "-" . $day['tanggal'] . "' ";
//            }
            $sqlQuery .= " SELECT '" . $day['tahun'] . "-" . $day['bulan'] . "-" . $day['tanggal'] . "' as Tanggal , coalesce(SUM(Pax),0) as Total from " . $this->tabelsale . " s
                INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
             " . $WHERE . "AND Pending = 'false' " . $ConditionMore . "\n";
            $sqlQuery .= " UNION ALL\n";
        }

        $sqlQuery = substr($sqlQuery, 0, strlen($sqlQuery) - strlen("UNION ALL\n"));

        $whereDate = "
WHERE selected_date between '" . $days[0]['tahun'] . "-" . $days[0]['bulan'] . "-" . $days[0]['tanggal'] . "' AND '" .
            $days[count($days) - 1]['tahun'] . "-" . $days[count($days) - 1]['bulan'] . "-31' ";
        $whereSaleDate = "
WHERE SaleDate between '" . $days[0]['tahun'] . "-" . $days[0]['bulan'] . "-" . $days[0]['tanggal'] . "' AND '" .
            $days[count($days) - 1]['tahun'] . "-" . $days[count($days) - 1]['bulan'] . "-31' ";

        $sqlQuery = "
select v.selected_date as Tanggal, COALESCE(SUM(PAX),0) Total from 
(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
LEFT JOIN 
(SELECT s.*,STR_TO_DATE(s.SaleDate, '%Y-%m-%d') SaleDateStr, 
s.Total+s.Rounding - (s.CashDownPayment+s.BankDownPayment) TotalForSum FROM sale s
 " . $whereSaleDate . " AND Pending='false' " . $ConditionMore . "
)s ON s.SaleDateStr=v.selected_date
-- sale s ON STR_TO_DATE(s.SaleDate, '%Y-%m-%d')=v.selected_date AND Pending = 'false' " . //$ConditionMore .
            $whereDate . "
GROUP BY selected_date
";

        $query = $this->db->query($sqlQuery);
        $result = $query->result();
        return $this->getDataChartPenjualanBulanIni($result, $haris, $bulans);
    }

}
