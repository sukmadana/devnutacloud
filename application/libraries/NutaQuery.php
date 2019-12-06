<?php

class NutaQuery {

    var $CI;
    var $dateStart = '1';
    var $dateEnd = '2';
    var $dateFormat = 'Y-m-d';
    var $clientDeviceID = '';
    var $clientPerusahaanID = '';
    var $Varian = 'Nuta Resto';
    var $NoPerusahaan = 0;
    var $Outlet = 'Semua';
    var $Cabangs = array();
    var $tabelsale = 'sale';
    var $tabelsaleitemdetail = 'saleitemdetail';
    var $tabelsaleitemdetailingredients = 'saleitemdetailingredients';
    var $tabelsaledelete = 'saledelete';
    var $tabelsaleitemdetaildelete = 'saleitemdetaildelete';
    var $Customer = '';

    function __construct() {
        $this->dateStart = date($this->dateFormat);
        $this->dateEnd = date($this->dateFormat);
        $this->CI = & get_instance();
    }

//    function dump()
//    {
//        echo var_dump(array(
//            'dateStart' => $this->dateStart,
//            'dateEnd' => $this->dateEnd,
//            'clientDeviceID' => $this->clientDeviceID,
//            'clientPerusahaanID' => $this->clientPerusahaanID,
//            'Varian' => $this->Varian,
//            'Outlet' => $this->Outlet,
//            'Cabangs' => $this->Cabangs
//        ));
//        exit;
//    }

    function setCabangs($cabangs) {
        $this->Cabangs = $cabangs;
    }

    function setDate($start, $end) {
        $this->dateStart = $start;
        $this->dateEnd = $end;
    }

    function setVarian($v) {
        $this->Varian = $v;
    }

    function getVarian() {
        return $this->Varian;
    }

    function setOutlet($o) {
        $this->Outlet = strval($o);
    }

    function getOutlet() {
        return $this->Outlet;
    }

    function getDateStart() {
        return $this->dateStart;
    }

    function getDateEnd() {
        return $this->dateEnd;
    }

    function setDeviceID($devID) {
        $this->clientDeviceID = strval($devID);
    }

    function setPerusahaaanID($idperusahaan) {
        if (isNotEmpty($idperusahaan)) {
            $this->clientPerusahaanID = $idperusahaan;
        } else {
            $this->clientPerusahaanID = $this->clientDeviceID;
        }
    }

    public function setNomorPerusahaan($nomor) {
        $this->NoPerusahaan = $nomor;
    }

    function setCustomer($o) {
        $this->Customer = strval($o);
    }

    function getCustomer() {
        return $this->Customer;
    }

    public function setTableSale($tbl_sale) {
        $this->tabelsale = $tbl_sale;
    }

    public function setTableSaleDetail($tbl_sale_detail) {
        $this->tabelsaleitemdetail = $tbl_sale_detail;
    }

    public function setTableSaleDetailIngredients($tbl_sale_detail_ing) {
        $this->tabelsaleitemdetailingredients = $tbl_sale_detail_ing;
    }

    public function get_query_penjualan($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule) {
        $this->CheckDeviceID();
        //$whereOutlet = " WHERE h.Outlet = " . $this->CI->db->escape($this->Outlet);
        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua")
            $whereOutlet = "";


        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $queryString = "\tSELECT 'Semua' AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,\n" .
                        "\tCOALESCE(mi.ItemName,sd.ItemName) ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,
                        CONCAT(EditedDate,', ',EditedTime) WaktuDiubah, 1 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=d.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                        " o.DeviceID IN (" . $strCabangs . ")  \n" .
                        "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " AND o.PerusahaanID=" . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tUNION ALL SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,COALESCE(s.CustomerName,'-') CustomerName,\n\t'" .
                        "Diskon Final" .
                        "' ItemName, 1 AS Qty,\n" .
                        "\t0,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END,
                        s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) ,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,2 UrutanTampil\n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID\n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID IN (" . $strCabangs . ") \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.SubTotal <> s.Total AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%' AND o.PerusahaanID=" . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tAND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID\n";
                $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,'Pajak' ItemName, 1 AS Qty,\n " .
                        "\ts.TaxValue+s.TaxValueExclude, '' Discount, s.TaxValue+s.TaxValueExclude SubTotal,CreatedBy DibuatOleh,
            CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat,
            EditedBy DiubahOleh,
            CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah, 3 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Tax=1 AND s.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
                return "SELECT h.Outlet, h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
                        . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah  FROM (" . $queryString . ") h
                        ORDER BY h.SaleDateTime,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
            } else {
                $queryString = "\tSELECT 'Semua' AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,\n" .
                        "\tCOALESCE(mi.ItemName,sd.ItemName) ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,1 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo " .
                        "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                        "\tUNION ALL SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,COALESCE(s.CustomerName,'-') CustomerName,\n\t'" .
                        "Diskon Final" .
                        "' ItemName, 1 AS Qty,\n" .
                        "\t0,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.Total ELSE s.FinalDiscount END,
                        s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-SUM(sd.SubTotal) ,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,2 UrutanTampil\n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.SubTotal <> s.Total AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%' AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tAND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID\n";
                $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,'Pajak' ItemName, 1 AS Qty,\n " .
                        "\ts.TaxValue, '' Discount, s.TaxValue SubTotal,CreatedBy DibuatOleh,
            CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat,
            EditedBy DiubahOleh,
            CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah,3 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Tax=1 AND s.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
                return "SELECT h.Outlet, h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
                        . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah  FROM (" . $queryString . ") h" . $whereOutlet . "
                        ORDER BY h.UrutanTampil,h.SaleDateTime,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
            }
        } else {
            $extrakolom1 = "";
            $extrakolom2 = "";
            $joinExtraKolom = "";
            $groupByExtraKolom = "";
            $extrakolom3 = "";
            $extrakolom4 = "";
            $extrakolom2A = "";
            if ($ShowExtraKolom) {
                $extrakolom1 = ",CategoryName AS Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2 = ",'' Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
                $extrakolom3 = ",Kategori";
                $extrakolom4 = ",DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah";
                $joinExtraKolom = "
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
";

                if ($isUseVarianModule) {
                    $extrakolom1 = str_replace("Kategori", "Kategori, sd.VarianName, COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1);
                    $extrakolom2 = str_replace("Kategori", "Kategori, '' VarianName, '' PilihanExtra", $extrakolom2);
                    $extrakolom2A = str_replace("Kategori", "Kategori, VarianName, PilihanExtra", $extrakolom2A);
                    $extrakolom3 = str_replace("Kategori", "Kategori, VarianName AS Varian, PilihanExtra", $extrakolom3);
                    $joinExtraKolom = $joinExtraKolom . "
                    LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo AND
                    sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sd.DeviceNo=sdm.DetailDeviceNo
                    ";
                    $groupByExtraKolom = "
GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.TransactionDeviceNo,sd.DetailID,sd.DeviceNo
";
                }
            }
            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }
            $queryString = "\tSELECT s.TransactionID,s.DeviceNo,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n" .
                    "\tCASE WHEN sd.Note = '' THEN sd.ItemName ELSE 
                CONCAT(sd.ItemName,'<br><i>',sd.Note,'</i>') END ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,1 UrutanTampil" . $extrakolom1 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                    "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                    " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo \n" . $joinExtraKolom .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "  AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                    $groupByExtraKolom .
//                "\tUNION ALL
//SELECT Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
// (
//     SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,',', s.SaleTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t'" .
//                "Diskon Final" .
//                "' ItemName, 1 AS Qty,\n" .
//                "\t0 UnitPrice,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END Discount,
//                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
//                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
//                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
//                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
//                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
//                AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85
//     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID HAVING SUM(sd.SubTotal) <> s.Total\n
//  ) x
//" .
//                "\tUNION ALL
//SELECT Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
// (
//     SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,',', s.SaleTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t" .
//                "CONCAT('Diskon Final : ', sd.DiscountName) ItemName, 1 AS Qty,\n" .
//                "\tsd.DiscountValue UnitPrice,sd.Discount Discount,
//                0 SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
//                "\tFROM " . $this->tabelsale . " s INNER JOIN salediscountdetail sd\n" .
//                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
//                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
//                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . "
//     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
//  ) x
//";
                    "\tUNION ALL
SELECT TransactionID,DeviceNo,Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
 (
     SELECT s.TransactionID,s.DeviceNo,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t" .
                    "CASE WHEN sd2.TransactionID IS NULL THEN 'Diskon Final'" .
                    " ELSE CONCAT('Diskon Final : ', sd2.DiscountName) END ItemName, 1 AS Qty,\n" .
                    "\tCASE WHEN sd2.TransactionID IS NULL THEN 0 ELSE sd2.DiscountValue END UnitPrice,
                CASE WHEN sd2.TransactionID IS NULL THEN
                    CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END
                ELSE sd2.Discount END Discount,
                CASE WHEN sd2.TransactionID IS NULL THEN
                    s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal)
                ELSE 0 END SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                    "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                    "\tLEFT JOIN salediscountdetail sd2
                ON s.TransactionID=sd2.TransactionID AND s.PerusahaanNo=sd2.PerusahaanNo AND s.DeviceNo=sd2.TransactionDeviceNo AND s.Pending = 'false' AND s.DeviceID=sd2.DeviceID
                INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID, s.DeviceNo HAVING SUM(sd.SubTotal) <> s.Total\n
  ) x
";
            $queryString .= "UNION ALL
                    SELECT s.TransactionID,s.DeviceNo,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pajak' ItemName, 1 AS Qty,\n " .
                    "\ts.TaxValue, '' Discount, s.TaxValue SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND ((s.Tax=1 AND s.PriceIncludeTax=0) AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85) AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
            $queryPajak = "
            UNION ALL
    SELECT TransactionID,DeviceNo, Outlet,DeviceID,SaleNumber,SaleDateTime,
    CustomerName, Pax, substring_index(DetailExcludeTaxValue, '$', 1) ItemName, 
    1 AS Qty, CAST(substring_index(DetailExcludeTaxValue, '$', -1) AS Decimal(19,4)) UnitPrice,
    Discount, CAST(substring_index(DetailExcludeTaxValue, '$', -1) AS Decimal(19,4)) SubTotal, 
    UrutanTampil" . $extrakolom2A . "
    FROM
    (
        SELECT s.TransactionID,s.DeviceNo,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,
        CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,TaxValueExclude,DetailExcludeTaxValues,nomor,
        substring_index(substring_index(DetailExcludeTaxValues, '#', nomor+1), '#', -1) AS DetailExcludeTaxValue,
        CASE WHEN s.DiningTable<>'' THEN 
        CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
        ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) 
        END CustomerName, Pax,'' Discount, 3 UrutanTampil ". $extrakolom2 . "
        FROM sale s
        INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo 
        AND o.DeviceID = " . $this->Outlet . "
        INNER JOIN tmp_number10 
        ON char_length(DetailExcludeTaxValues) - char_length(replace(DetailExcludeTaxValues, '#', '')) >= nomor
        WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending='false' 
        AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " 
        AND s.SaleDate<=" . $this->CI->db->escape($this->dateEnd) . " 
    ) X
                ";
            $queryString .= $queryPajak;
            //log_message('error',$queryPajak);

            $queryString .= "
                    UNION ALL
                    SELECT s.TransactionID,s.DeviceNo,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pembulatan' ItemName, 1 AS Qty,\n " .
                    "\ts.Rounding, '' Discount, s.Rounding SubTotal,5 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Rounding<>0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

            $queryString .= "
                    UNION ALL
                    SELECT s.TransactionID,s.DeviceNo,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber,CONCAT(s.CreatedDate, ', ', s.CreatedTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Uang Muka' ItemName, 1 AS Qty,\n " .
                    "\ts.CashDownPayment+s.BankDownPayment, '' Discount, s.CashDownPayment+s.BankDownPayment SubTotal,1 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND s.CreatedDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd);

            $queryString .= "
                    UNION ALL
                    SELECT s.TransactionID,s.DeviceNo,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Dipotong Uang Muka' ItemName, 1 AS Qty,\n " .
                    "\t-(s.CashDownPayment+s.BankDownPayment), '' Discount, -(s.CashDownPayment+s.BankDownPayment) SubTotal,4 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }
//            echo "SELECT h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
//                . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal" . $extrakolom4 . " FROM (" . $queryString . ") h
//                  ORDER BY h.UrutanTampil,h.SaleDateTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'

            return "SELECT h.TransactionID,h.DeviceNo,h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
                    . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal" . $extrakolom4 . " FROM (" . $queryString . ") h
                    ORDER BY h.UrutanTampil,h.SaleDateTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
        }
    }

    function get_query_penjualan_void($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule) {
        $this->CheckDeviceID();

        $outlet = $this->CI->db->escape($this->Outlet);

        $extrakolom1 = "";
        $extrakolom2 = "";
        $joinExtraKolom = "";
        $groupByExtraKolom = "";
        $extrakolom3 = "";
        $extrakolom4 = "";
        $extrakolom2A = "";

        if ($ShowExtraKolom) {
            $extrakolom1 = ",CategoryName AS Kategori,'-' DibuatOleh,s.TglJamDelete WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2 = ",'' Kategori,'' DibuatOleh,s.TglJamDelete WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
            $extrakolom3 = ",Kategori";
            $extrakolom4 = ",DibuatOleh DivoidOleh,WaktuDibuat TglVoid";
            $joinExtraKolom = "
                LEFT JOIN mastercategory mc ON 
                    mc.CategoryID = mi.CategoryID 
                    AND mi.DeviceID = mc.DeviceID 
                    AND mi.PerusahaanNo = mc.PerusahaanNo 
                    AND mc.DeviceNo = mi.CategoryDeviceNo
            ";

            if ($isUseVarianModule) {
                $extrakolom1 = str_replace("Kategori", "Kategori, sd.VarianName, COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1) . " ";
                $extrakolom2 = str_replace("Kategori", "Kategori, '' VarianName, '' PilihanExtra", $extrakolom2) . " ";
                $extrakolom2A = str_replace("Kategori", "Kategori, VarianName, PilihanExtra", $extrakolom2A) . " ";
                $extrakolom3 = str_replace("Kategori", "Kategori, VarianName AS Varian, PilihanExtra", $extrakolom3) . " ";
                $joinExtraKolom = $joinExtraKolom . "LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo AND sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sd.DeviceNo=sdm.DetailDeviceNo ";
                $groupByExtraKolom = "GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.TransactionDeviceNo,sd.DetailID,sd.DeviceNo ";
            }
        }

        $pax = "";
        if ($IsDiningTable) {
            $pax = ", h.Pax";
        }

        $queryString = "
            SELECT 
                CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet, 
                o.DeviceID, 
                s.SaleNumber, 
                CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime, 
                CASE 
                    WHEN s.DiningTable<>'' THEN CONCAT( 
                        CASE 
                            WHEN s.CustomerName='' THEN '-' 
                            ELSE s.CustomerName END, '/', s.DiningTable 
                    ) 
                    ELSE ( 
                        CASE 
                            WHEN s.CustomerName='' THEN '-' 
                            ELSE s.CustomerName END
                    ) 
                END CustomerName, 
                Pax, 
                CASE WHEN sd.Note = '' THEN sd.ItemName ELSE 
                CONCAT(sd.ItemName,'<br><i>',sd.Note,'</i>') END ItemName, 
                (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, 
                sd.UnitPrice, 
                sd.Discount, 
                sd.SubTotal - sd.SubTotalCanceled SubTotal, 
                1 UrutanTampil" . $extrakolom1 . "
            FROM " . $this->tabelsaledelete . " s 
            INNER JOIN " . $this->tabelsaleitemdetaildelete . " sd ON
                s.TransactionID=sd.TransactionID 
                AND s.PerusahaanNo=sd.PerusahaanNo
                AND s.DeviceID = sd.DeviceID 
                AND s.DeviceNo = sd.TransactionDeviceNo 
            INNER JOIN options o ON 
                s.DeviceID = o.DeviceID 
                AND s.PerusahaanNo = o.PerusahaanNo 
                AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
            LEFT JOIN masteritem mi ON 
                sd.ItemID = mi.ItemID 
                AND sd.PerusahaanNo = mi.PerusahaanNo 
                AND mi.DeviceID = sd.DeviceID 
                AND mi.DeviceNo = sd.ItemDeviceNo " .
                $joinExtraKolom .
                "WHERE
                o.PerusahaanNo = " . $this->CI->db->escape($this->NoPerusahaan) . " 
                AND (sd.Quantity - sd.QtyCanceled + sd.QtyCanceler) <>0 
                AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) .
                $groupByExtraKolom . "
            UNION ALL
            SELECT
                Outlet, 
                DeviceID, 
                SaleNumber, 
                SaleDate, 
                CustomerName, 
                Pax, 
                ItemName, 
                Qty, 
                UnitPrice, 
                Discount, 
                SubTotal, 
                UrutanTampil " . $extrakolom2A . " 
            FROM (
                SELECT 
                    CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet, 
                    o.DeviceID, 
                    s.SaleNumber, 
                    CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate, 
                    CASE 
                        WHEN s.DiningTable<>'' THEN CONCAT( 
                            CASE 
                                WHEN s.CustomerName = '' THEN '-' 
                                ELSE s.CustomerName 
                            END, 
                            '/', 
                            s.DiningTable 
                        ) ELSE (
                            CASE 
                                WHEN s.CustomerName = '' THEN '-' 
                                ELSE s.CustomerName 
                            END
                        ) END CustomerName, 
                    Pax, 
                    CASE 
                        WHEN sd2.TransactionID IS NULL THEN 'Diskon Final' 
                        ELSE CONCAT('Diskon Final : ', sd2.DiscountName) 
                    END ItemName, 
                    1 AS Qty, 
                    CASE 
                        WHEN sd2.TransactionID IS NULL THEN 0 
                        ELSE sd2.DiscountValue 
                    END UnitPrice, 
                    CASE 
                        WHEN sd2.TransactionID IS NULL THEN
                            CASE 
                                WHEN s.FinalDiscount = 'null' THEN sd.SubTotal - (
                                        CASE 
                                            WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue 
                                            ELSE 0 
                                        END 
                                    ) - s.TaxValueExclude - s.Total 
                                ELSE s.FinalDiscount 
                            END 
                        ELSE sd2.Discount 
                    END Discount, 
                    CASE 
                        WHEN sd2.TransactionID IS NULL THEN s.Total - (
                            CASE 
                                WHEN s.Tax = 1 AND s.PriceIncludeTax = 0 THEN s.TaxValue 
                                ELSE 0 
                            END
                        ) - s.TaxValueExclude - SUM(sd.SubTotal) 
                        ELSE 0 
                    END SubTotal, 
                    2 UrutanTampil, 
                    s.Total " . $extrakolom2 . " 
                FROM " . $this->tabelsaledelete . " s 
                INNER JOIN " . $this->tabelsaleitemdetaildelete . " sd ON 
                    s.TransactionID = sd.TransactionID 
                    AND s.PerusahaanNo = sd.PerusahaanNo 
                    AND s.DeviceNo = sd.TransactionDeviceNo
                    AND s.DeviceID = sd.DeviceID 
                LEFT JOIN salediscountdetaildelete sd2 ON 
                    s.TransactionID = sd2.TransactionID 
                    AND s.PerusahaanNo = sd2.PerusahaanNo 
                    AND s.DeviceNo = sd2.TransactionDeviceNo
                    AND s.DeviceID = sd2.DeviceID
                INNER JOIN options o ON 
                    s.DeviceID = o.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                WHERE 
                    o.PerusahaanNo = " . $this->CI->db->escape($this->NoPerusahaan) . " 
                    AND s.FinalDiscount <> '' 
                    AND s.FinalDiscount NOT LIKE '0%' 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                GROUP BY 
                    s.DeviceID, s.TransactionID, s.DeviceNo 
                HAVING 
                    SUM(sd.SubTotal) <> s.Total
            ) x 
            UNION ALL
            SELECT 
                CONCAT(o.CompanyName, ' ', o.CompanyAddress) AS Outlet, 
                o.DeviceID, 
                s.SaleNumber, 
                CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime, 
                CASE 
                    WHEN s.DiningTable<>'' THEN CONCAT(
                        CASE 
                            WHEN s.CustomerName='' THEN '-' 
                            ELSE s.CustomerName 
                        END
                        , '/'
                        , s.DiningTable
                    ) ELSE (
                        CASE 
                            WHEN s.CustomerName='' THEN '-' 
                            ELSE s.CustomerName 
                        END
                    ) 
                END CustomerName, 
                Pax, 
                'Pajak' ItemName, 
                1 AS Qty, 
                s.TaxValue, 
                '' Discount, 
                s.TaxValue SubTotal, 
                3 UrutanTampil" . $extrakolom2 . " 
            FROM " . $this->tabelsaledelete . " s 
            INNER JOIN options o ON 
                s.DeviceID = o.DeviceID 
                AND s.PerusahaanNo = o.PerusahaanNo 
                AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . "
            WHERE 
                o.PerusahaanNo = " . $this->CI->db->escape($this->NoPerusahaan) . " 
                AND (
                    (s.Tax=1 AND s.PriceIncludeTax=0) 
                    AND s.CreatedVersionCode < 85 
                    AND s.EditedVersionCode < 85
                )
                AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
            UNION ALL
            SELECT 
                CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet, 
                o.DeviceID, 
                s.SaleNumber, 
                CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime, 
                CASE 
                    WHEN s.DiningTable<>'' THEN CONCAT(
                        CASE 
                            WHEN s.CustomerName = '' THEN '-' 
                            ELSE s.CustomerName 
                        END, 
                        '/', 
                        s.DiningTable
                    ) ELSE (
                        CASE 
                            WHEN s.CustomerName='' THEN '-' 
                            ELSE s.CustomerName 
                        END
                    ) 
                END CustomerName, 
                Pax, 
                CONCAT(sd.TaxName, ' ', replace(CAST(sd.TaxPercent AS CHAR),'.0',''), '%') ItemName, 
                1 AS Qty, 
                CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)), '' Discount, 
                CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) SubTotal,
                3 UrutanTampil" . $extrakolom2 . " 
            FROM " . $this->tabelsaledelete . " s 
            INNER JOIN saleitemdetailtaxdelete sd ON 
                s.DeviceID = sd.DeviceID 
                AND s.PerusahaanNo = sd.PerusahaanNo 
                AND s.TransactionID = sd.TransactionID 
                AND s.DeviceNo = sd.TransactionDeviceNo
            INNER JOIN options o ON 
                s.DeviceID = o.DeviceID 
                AND s.PerusahaanNo = o.PerusahaanNo 
                AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
            WHERE 
                o.PerusahaanNo = " . $this->CI->db->escape($this->NoPerusahaan) . " 
                AND sd.PriceIncludeTax = 0 
                AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
            GROUP BY 
                s.TransactionID, sd.TaxName, s.DeviceNo
            UNION ALL
            SELECT 
                CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet, 
                o.DeviceID, 
                s.SaleNumber, 
                CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,
                CASE 
                    WHEN s.DiningTable<>'' THEN CONCAT(
                        CASE 
                            WHEN s.CustomerName = '' THEN '-' 
                            ELSE s.CustomerName 
                        END, 
                        '/', 
                        s.DiningTable
                    ) 
                    ELSE (
                        CASE 
                            WHEN s.CustomerName='' THEN '-' 
                            ELSE s.CustomerName 
                        END
                    ) 
                END CustomerName, 
                Pax, 
                'Pembulatan' ItemName, 
                1 AS Qty, 
                s.Rounding, 
                '' Discount, 
                s.Rounding SubTotal, 
                5 UrutanTampil " . $extrakolom2 . "
            FROM " . $this->tabelsaledelete . " s 
            INNER JOIN options o ON 
                s.DeviceID = o.DeviceID 
                AND s.PerusahaanNo = o.PerusahaanNo 
                AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
            WHERE 
                o.PerusahaanNo = " . $this->CI->db->escape($this->NoPerusahaan) . " 
                AND s.Rounding <> 0 
                AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
            UNION ALL 
            SELECT 
                CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet, 
                o.DeviceID, 
                s.SaleOrderNumber, 
                CONCAT(s.CreatedDate, ', ', s.CreatedTime) SaleDateTime, 
                CASE 
                    WHEN s.DiningTable<>'' THEN CONCAT(
                        CASE 
                            WHEN s.CustomerName='' THEN '-' 
                            ELSE s.CustomerName 
                        END, 
                        '/', 
                        s.DiningTable
                    ) 
                    ELSE (
                        CASE 
                            WHEN s.CustomerName = '' THEN '-' 
                            ELSE s.CustomerName 
                        END
                    ) 
                END CustomerName, 
                Pax, 
                'Uang Muka' ItemName, 
                1 AS Qty, 
                s.CashDownPayment + s.BankDownPayment, 
                '' Discount, 
                s.CashDownPayment + s.BankDownPayment SubTotal, 
                1 UrutanTampil" . $extrakolom2 . " 
            FROM " . $this->tabelsaledelete . " s 
            INNER JOIN options o ON 
                s.DeviceID = o.DeviceID 
                AND s.PerusahaanNo = o.PerusahaanNo 
                AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
            WHERE 
                o.PerusahaanNo = " . $this->CI->db->escape($this->NoPerusahaan) . " 
                AND s.CashDownPayment + s.BankDownPayment <> 0 
                AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . " 
                AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . " 
            UNION ALL
            SELECT 
                CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet, 
                o.DeviceID, 
                s.SaleNumber, 
                CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,
                CASE 
                    WHEN s.DiningTable <> '' THEN CONCAT(
                        CASE 
                            WHEN s.CustomerName='' THEN '-' 
                            ELSE s.CustomerName 
                        END, 
                        '/', 
                        s.DiningTable 
                    ) 
                    ELSE ( 
                        CASE 
                            WHEN s.CustomerName='' THEN '-' 
                            ELSE s.CustomerName 
                        END
                    ) 
                END CustomerName, 
                Pax, 
                'Dipotong Uang Muka' ItemName, 
                1 AS Qty, 
                -(s.CashDownPayment + s.BankDownPayment), 
                '' Discount, 
                -(s.CashDownPayment + s.BankDownPayment) SubTotal, 
                4 UrutanTampil " . $extrakolom2 . " 
            FROM " . $this->tabelsaledelete . " s 
            INNER JOIN options o ON 
                s.DeviceID = o.DeviceID 
                AND s.PerusahaanNo = o.PerusahaanNo 
                AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
            WHERE 
                o.PerusahaanNo = " . $this->CI->db->escape($this->NoPerusahaan) . " 
                AND s.CashDownPayment + s.BankDownPayment <> 0 
                AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

        $pax = "";
        if ($IsDiningTable) {
            $pax = "h.Pax, ";
        }


        return "
            SELECT 
                h.SaleNumber as Nomor, 
                h.SaleDateTime as Tanggal, 
                h.CustomerName as Pelanggan, " .
                $pax . " 
                h.ItemName as Item" .
                $extrakolom3 . ", 
                h.Quantity as Qty, 
                h.UnitPrice as HargaSatuan, 
                h.Discount as Diskon, 
                h.SubTotal" .
                $extrakolom4 . " 
            FROM (" . $queryString . ") h
            ORDER BY 
                h.UrutanTampil, h.SaleDateTime, h.SaleNumber, h.Discount ASC, h.ItemName";
    }

    function get_query_rekap_pembayaran() {
        $this->CheckDeviceID();

        $strCabangs = '';
        foreach ($this->Cabangs as $cabang) {
            $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
        }

        if (strlen($strCabangs) > 0) {
            $strCabangs = substr($strCabangs, 1);
            $sqlPembayaranPerOutlet = "
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , CONCAT(o.CompanyName,' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , CONCAT('Penjualan Tunai', ' ', m.AccountName) JenisPembayaran
                    , CONCAT('<a href=\"" . base_url() . "laporan/PenjualanPerKategoriByCashBank?date_start=" .
                    $this->dateStart . "&date_end=" . $this->dateEnd . "&outlet=" . $this->Outlet
                    . "&accounts=',m.AccountID,'.',m.DeviceNo,'\" target=\"_blank\">Lihat per Kategori</a>') Link
                    , CAST(1 AS Signed) UrutanBayar 
                    , COALESCE(SUM(CashPaymentAmount - s.Change),0) Total 
                FROM " . $this->tabelsale . " s 
                RIGHT JOIN options o ON 
                    o.DeviceID = s.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND (PaymentMode = 1 OR PaymentMode=3) 
                    AND Pending = 'false' 
                    AND SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                JOIN mastercashbankaccount m ON 
                    s.CashAccountID = m.AccountID 
                    AND s.PerusahaanNo = m.PerusahaanNo 
                    AND s.CashAccountDeviceNo = m.DeviceNo 
                    AND o.DeviceID = m.DeviceID 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND o.DeviceID IN (" . $strCabangs . ") 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                GROUP BY 
                    o.CompanyName, o.CompanyAddress 
                UNION ALL 
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , CONCAT(o.CompanyName,' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , CONCAT('Penjualan Tunai', ' ', m.AccountName) JenisPembayaran 
                    , CONCAT('<a href=\"" . base_url() . "laporan/PenjualanPerKategoriByCashBank?date_start=" .
                    $this->dateStart . "&date_end=" . $this->dateEnd . "&outlet=" . $this->Outlet
                    . "&accounts=',m.AccountID,'.',m.DeviceNo,'\" target=\"_blank\">Lihat per Kategori</a>') Link
                    , CAST(1 AS Signed) UrutanBayar 
                    , COALESCE(SUM(CashDownPayment),0) Total 
                FROM " . $this->tabelsale . " s 
                RIGHT JOIN options o ON 
                    o.DeviceID = s.DeviceID 
                    AND s.PerusahaanNo=o.PerusahaanNo 
                    AND s.CashDownPayment <> 0 
                    AND CreatedDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                JOIN mastercashbankaccount m ON 
                    s.CashDownPaymentAccountID = m.AccountID 
                    AND s.PerusahaanNo = m.PerusahaanNo 
                    AND s.CashDownPaymentAccountDeviceNo = m.DeviceNo 
                    AND o.DeviceID = m.DeviceID 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND o.DeviceID IN (" . $strCabangs . ") 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                GROUP BY 
                    o.CompanyName, o.CompanyAddress 
                UNION ALL 
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , CONCAT(o.CompanyName,' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , CONCAT('Penjualan Non Tunai', ' ', m.BankName, ' ', m.AccountNumber, ' ', m.AccountName) JenisPembayaran 
                    , CONCAT('<a href=\"" . base_url() . "laporan/PenjualanPerKategoriByCashBank?date_start=" .
                    $this->dateStart . "&date_end=" . $this->dateEnd . "&outlet=" . $this->Outlet
                    . "&accounts=',m.AccountID,'.',m.DeviceNo,'\" target=\"_blank\">Lihat per Kategori</a>') Link
                    , CAST(2 AS Signed) UrutanBayar 
                    , COALESCE(SUM(BankPaymentAmount),0) Total 
                FROM " . $this->tabelsale . " s 
                RIGHT JOIN options o ON 
                    o.DeviceID = s.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo
                    AND (PaymentMode = 2 OR PaymentMode = 3) 
                    AND Pending = 'false'
                    AND SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
                    AND SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                JOIN mastercashbankaccount m ON 
                    s.BankAccountID = m.AccountID 
                    AND s.PerusahaanNo = m.PerusahaanNo 
                    AND s.BankAccountDeviceNo = m.DeviceNo 
                    AND o.DeviceID = m.DeviceID 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND o.DeviceID IN (" . $strCabangs . ") 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                GROUP BY m.DeviceID,m.AccountID,m.DeviceNo 
                UNION ALL 
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , CONCAT(o.CompanyName,' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , CONCAT('Penjualan Non Tunai', ' ', m.AccountName) JenisPembayaran 
                    , CONCAT('<a href=\"" . base_url() . "laporan/PenjualanPerKategoriByCashBank?date_start=" .
                    $this->dateStart . "&date_end=" . $this->dateEnd . "&outlet=" . $this->Outlet
                    . "&accounts=',m.AccountID,'.',m.DeviceNo,'\" target=\"_blank\">Lihat per Kategori</a>') Link
                    , CAST(2 AS Signed) UrutanBayar 
                    , COALESCE(SUM(BankDownPayment),0) Total 
                FROM " . $this->tabelsale . " s 
                RIGHT JOIN options o ON 
                    o.DeviceID = s.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND s.BankDownPayment <> 0 
                    AND CreatedDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                JOIN mastercashbankaccount m ON 
                    s.BankDownPaymentAccountID = m.AccountID 
                    AND s.PerusahaanNo = m.PerusahaanNo 
                    AND s.BankDownPaymentAccountDeviceNo = m.DeviceNo 
                    AND o.DeviceID = m.DeviceID 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND o.DeviceID IN (" . $strCabangs . ") 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                GROUP BY m.DeviceID,m.AccountID,m.DeviceNo 
            ";

            $sqlPembayaranTotalPerOutlet = "
                UNION ALL 
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , X.Outlet 
                    , X.DeviceID 
                    , 'Total Pembayaran' 
                    , ''
                    , CAST(3 AS Signed) UrutanBayar 
                    , SUM(Total) Total 
                FROM (" . $sqlPembayaranPerOutlet . ") X 
                GROUP BY X.Outlet 
            ";

            $sqlPembayaranAllOutlet = "
                SELECT 
                    CAST(2 AS Signed) UrutanOutlet 
                    , 'Semua' Outlet 
                    , 'Semua' DeviceID 
                    , JenisPembayaran 
                    , Link
                    , UrutanBayar 
                    , SUM(Total) Total 
                FROM (" . $sqlPembayaranPerOutlet . ") X 
                GROUP BY X.JenisPembayaran, X.UrutanBayar 
            ";
            $sqlPembayaranTotalAllOutlet = "
                UNION ALL 
                SELECT 
                    CAST(2 AS Signed) UrutanOutlet 
                    , 'Semua' Outlet 
                    , 'Semua' DeviceID 
                    , 'Total Pembayaran'
                    , '' Link
                    , CAST(3 AS Signed) UrutanBayar 
                    , SUM(Total) Total 
                FROM (" . $sqlPembayaranAllOutlet . ") X 
            ";

            if ($this->Outlet == "Semua") {
                // log_message("error", "query rekap pembayaran A
                //     SELECT 
                //         h.JenisPembayaran 
                //         , h.Total 
                //     FROM (" . $sqlPembayaranAllOutlet . $sqlPembayaranTotalAllOutlet . " ) h 
                //     ORDER BY UrutanBayar ASC
                // ");
                return "
                    SELECT 
                        h.JenisPembayaran , Link
                        , h.Total 
                    FROM (" . $sqlPembayaranAllOutlet . $sqlPembayaranTotalAllOutlet . " ) h 
                    ORDER BY UrutanBayar ASC
                ";
            } else {
                // log_message("error", "query rekap pembayaran B
                //     SELECT 
                //         h.JenisPembayaran
                //         , h.Total 
                //     FROM (" . $sqlPembayaranPerOutlet . $sqlPembayaranTotalPerOutlet . " ) h 
                //     WHERE 
                //         h.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                //     ORDER BY UrutanOutlet, Outlet, UrutanBayar ASC ");
                // log_message('error',"AAA
                //     SELECT 
                //         h.JenisPembayaran, Link, h.Total 
                //     FROM (" . $sqlPembayaranPerOutlet . $sqlPembayaranTotalPerOutlet . " ) h 
                //     WHERE 
                //         h.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                //     ORDER BY UrutanOutlet, Outlet, UrutanBayar ASC 
                // ");
                return "
                    SELECT 
                        h.JenisPembayaran, Link, h.Total 
                    FROM (" . $sqlPembayaranPerOutlet . $sqlPembayaranTotalPerOutlet . " ) h 
                    WHERE 
                        h.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                    ORDER BY UrutanOutlet, Outlet, UrutanBayar ASC 
                ";
            }
        } else {
            $sqlPembayaranPerOutlet = "
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , CONCAT(o.CompanyName,' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , CONCAT('Penjualan Tunai', ' ', m.AccountName) JenisPembayaran 
                    , CONCAT('<a href=\"" . base_url() . "laporan/PenjualanPerKategoriByCashBank?date_start=" .
                    $this->dateStart . "&date_end=" . $this->dateEnd . "&outlet=" . $this->Outlet
                    . "&accounts=',m.AccountID,'.',m.DeviceNo,'\" target=\"_blank\">Lihat per Kategori</a>') Link
                    , CAST(1 AS Signed) UrutanBayar 
                    , COALESCE(SUM(CashPaymentAmount - s.Change),0) Total 
                FROM " . $this->tabelsale . " s 
                RIGHT JOIN options o ON 
                    o.DeviceID = s.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND (PaymentMode = 1 OR PaymentMode = 3) 
                    AND Pending = 'false' 
                    AND SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                JOIN mastercashbankaccount m ON 
                    s.CashAccountID = m.AccountID 
                    AND s.PerusahaanNo = m.PerusahaanNo 
                    AND s.CashAccountDeviceNo = m.DeviceNo 
                    AND o.DeviceID = m.DeviceID 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                GROUP BY o.DeviceID, o.CompanyName, o.CompanyAddress,m.AccountID,m.DeviceNo 
                UNION ALL 
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet 
                    , o.DeviceID 
                    , CONCAT('Penjualan Tunai', ' ', m.AccountName) JenisPembayaran 
                    , CONCAT('<a href=\"" . base_url() . "laporan/PenjualanPerKategoriByCashBank?date_start=" .
                    $this->dateStart . "&date_end=" . $this->dateEnd . "&outlet=" . $this->Outlet
                    . "&accounts=',m.AccountID,'.',m.DeviceNo,'\" target=\"_blank\">Lihat per Kategori</a>') Link
                    , CAST(1 AS Signed) UrutanBayar 
                    , COALESCE(SUM(CashDownPayment),0) Total 
                FROM " . $this->tabelsale . " s 
                RIGHT JOIN options o ON 
                    o.DeviceID = s.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND (s.CashDownPayment <> 0) 
                    AND CreatedDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                JOIN mastercashbankaccount m ON 
                    s.CashDownPaymentAccountID = m.AccountID 
                    AND s.PerusahaanNo = m.PerusahaanNo 
                    AND s.CashDownPaymentAccountDeviceNo = m.DeviceNo 
                    AND o.DeviceID = m.DeviceID 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                GROUP BY o.DeviceID, o.CompanyName, o.CompanyAddress,m.AccountID,m.DeviceNo 
                UNION ALL 
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , CONCAT(o.CompanyName,' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , CONCAT('Penjualan Non Tunai', ' ', m.BankName, ' ', m.AccountNumber, ' ', m.AccountName) JenisPembayaran 
                    , CONCAT('<a href=\"" . base_url() . "laporan/PenjualanPerKategoriByCashBank?date_start=" .
                    $this->dateStart . "&date_end=" . $this->dateEnd . "&outlet=" . $this->Outlet
                    . "&accounts=',m.AccountID,'.',m.DeviceNo,'\" target=\"_blank\">Lihat per Kategori</a>') Link
                    , CAST(2 AS Signed) UrutanBayar 
                    , COALESCE(SUM(BankPaymentAmount),0) Total 
                FROM " . $this->tabelsale . " s 
                RIGHT JOIN options o ON 
                    o.DeviceID = s.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND (PaymentMode = 2 OR PaymentMode = 3) 
                    AND Pending = 'false' 
                    AND SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                JOIN mastercashbankaccount m ON 
                    s.BankAccountID = m.AccountID 
                    AND s.PerusahaanNo = m.PerusahaanNo 
                    AND s.BankAccountDeviceNo = m.DeviceNo 
                    AND o.DeviceID = m.DeviceID 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                GROUP BY o.DeviceID, o.CompanyName, o.CompanyAddress,m.AccountID,m.DeviceNo 
                UNION ALL 
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , CONCAT(o.CompanyName,' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , CONCAT('Penjualan Non Tunai', ' ', m.BankName, ' ', m.AccountNumber, ' ', m.AccountName) JenisPembayaran 
                    , CONCAT('<a href=\"" . base_url() . "laporan/PenjualanPerKategoriByCashBank?date_start=" .
                    $this->dateStart . "&date_end=" . $this->dateEnd . "&outlet=" . $this->Outlet
                    . "&accounts=',m.AccountID,'.',m.DeviceNo,'\" target=\"_blank\">Lihat per Kategori</a>') Link
                    , CAST(2 AS Signed) UrutanBayar 
                    , COALESCE(SUM(BankDownPayment),0) Total 
                FROM " . $this->tabelsale . " s 
                RIGHT JOIN options o ON 
                    o.DeviceID = s.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND (s.BankDownPayment <> 0) 
                    AND CreatedDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                JOIN mastercashbankaccount m ON 
                    s.BankDownPaymentAccountID = m.AccountID 
                    AND s.PerusahaanNo = m.PerusahaanNo 
                    AND s.BankDownPaymentAccountDeviceNo = m.DeviceNo 
                    AND o.DeviceID = m.DeviceID 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                GROUP BY o.DeviceID, o.CompanyName, o.CompanyAddress,m.AccountID,m.DeviceNo 
            ";

            $sqlPembayaranTotalPerOutlet = " 
                UNION ALL 
                SELECT 
                    CAST(1 AS Signed) UrutanOutlet 
                    , X.Outlet 
                    , X.DeviceID 
                    , 'Total Pembayaran' 
                    , ''
                    , CAST(3 AS Signed) UrutanBayar 
                    , SUM(Total) Total 
                FROM (" . $sqlPembayaranPerOutlet . ") X 
                GROUP BY X.DeviceID, X.Outlet 
            ";

            $sqlPembayaranAllOutlet = "
                SELECT 
                    CAST(2 AS Signed) UrutanOutlet 
                    , 'Semua' Outlet 
                    , 'Semua' DeviceID 
                    , JenisPembayaran 
                    , Link
                    , UrutanBayar 
                    , SUM(Total) Total 
                FROM (" . $sqlPembayaranPerOutlet . ") X 
                GROUP BY X.JenisPembayaran, X.UrutanBayar 
            ";
            $sqlPembayaranTotalAllOutlet = " 
                UNION ALL 
                SELECT 
                    CAST(2 AS Signed) UrutanOutlet 
                    , 'Semua' Outlet 
                    , 'Semua' DeviceID 
                    , 'Total Pembayaran' 
                    , ''
                    , CAST(3 AS Signed) UrutanBayar 
                    , SUM(Total) Total 
                FROM (" . $sqlPembayaranAllOutlet . ") X 
            ";

            if ($this->Outlet == "Semua") {
                // log_message("error","query rekap pembayaran C
                //     SELECT 
                //         h.JenisPembayaran 
                //         , SUM(h.Total) Total 
                //     FROM (" . $sqlPembayaranAllOutlet . $sqlPembayaranTotalAllOutlet . " ) h 
                //     GROUP BY JenisPembayaran 
                //     ORDER BY UrutanBayar ASC");
                return " 
                    SELECT 
                        h.JenisPembayaran 
                        , SUM(h.Total) Total 
                    FROM (" . $sqlPembayaranAllOutlet . $sqlPembayaranTotalAllOutlet . " ) h 
                    GROUP BY JenisPembayaran 
                    ORDER BY UrutanBayar ASC";
            } else {
                // log_message("error","query rekap pembayaran D
                //     SELECT 
                //         h.JenisPembayaran 
                //         , SUM(h.Total) Total 
                //     FROM (" . $sqlPembayaranPerOutlet . $sqlPembayaranTotalPerOutlet . " ) h 
                //     WHERE 
                //         h.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                //     GROUP BY JenisPembayaran 
                //     ORDER BY UrutanOutlet, Outlet, UrutanBayar ASC ");
                // log_message('error',"BBB
                //     SELECT 
                //         h.JenisPembayaran , h.Link
                //         , SUM(h.Total) Total 
                //     FROM (" . $sqlPembayaranPerOutlet . $sqlPembayaranTotalPerOutlet . " ) h 
                //     WHERE 
                //         h.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                //     GROUP BY JenisPembayaran 
                //     ORDER BY UrutanOutlet, Outlet, UrutanBayar ASC
                // ");
                return " 
                    SELECT 
                        h.JenisPembayaran , h.Link
                        , SUM(h.Total) Total 
                    FROM (" . $sqlPembayaranPerOutlet . $sqlPembayaranTotalPerOutlet . " ) h 
                    WHERE 
                        h.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                    GROUP BY JenisPembayaran 
                    ORDER BY UrutanOutlet, Outlet, UrutanBayar ASC 
                ";
            }
        }
    }

    function get_query_saldo_uang($minDate = false) {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
            }
        }
        $sqlPenjualanTunai = "SELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        m.AccountName AS KasRekening, CAST(1 AS Signed) UrutanKasRek, SUM(CashPaymentAmount-s.Change) Total \n" .
                "\tFROM sale s INNER JOIN options o
            ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN
            mastercashbankaccount m
            ON s.CashAccountID=m.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND s.CashAccountDeviceNo=m.DeviceNo
            AND o.DeviceID=m.DeviceID" .
                "\tWHERE (s.PaymentMode=1 OR s.PaymentMode=3)";
        if ($minDate != false) {
            $sqlPenjualanTunai .= " and SaleDate > '" . $minDate . "'";
        }
        $sqlPenjualanTunai .= " AND SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND " . $Condition . " \n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";
        $sqlPenjualanTunaiDP = "SELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        m.AccountName AS KasRekening, CAST(1 AS Signed) UrutanKasRek, SUM(CashDownPayment) Total \n" .
                "\tFROM sale s INNER JOIN options o
            ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN
            mastercashbankaccount m
            ON s.CashDownPaymentAccountID=m.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND s.CashDownPaymentAccountDeviceNo=m.DeviceNo
            AND o.DeviceID=m.DeviceID" .
                "\tWHERE (s.CashDownPayment<>0)";
        if ($minDate != false) {
            $sqlPenjualanTunaiDP .= " and CreatedDate > '" . $minDate . "'";
        }
        $sqlPenjualanTunaiDP .= " AND CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND " . $Condition . " \n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";

        $sqlPenjualanNonTunai = "\tSELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName) AS KasRekening, CAST(2 AS Signed) UrutanKasRek, SUM(BankPaymentAmount) Total \n" .
                "\tFROM options o INNER JOIN " . $this->tabelsale . " s 
            ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN mastercashbankaccount m
            ON s.BankAccountID=m.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.BankAccountDeviceNo
            AND o.DeviceID=m.DeviceID" .
                "\tWHERE (s.PaymentMode=2 OR s.PaymentMode=3)";
        if ($minDate != false) {
            $sqlPenjualanNonTunai .= " and SaleDate > '" . $minDate . "'";
        }
        $sqlPenjualanNonTunai .= " AND SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND " . $Condition . "\n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";

        $sqlPenjualanNonTunaiDP = "\tSELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName) AS KasRekening, CAST(2 AS Signed) UrutanKasRek, SUM(BankDownPayment) Total \n" .
                "\tFROM options o INNER JOIN " . $this->tabelsale . " s 
            ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN mastercashbankaccount m
            ON s.BankDownPaymentAccountID=m.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.BankDownPaymentAccountDeviceNo
            AND o.DeviceID=m.DeviceID" .
                "\tWHERE (s.BankDownPayment<>0)";
        if ($minDate != false) {
            $sqlPenjualanNonTunaiDP .= " and CreatedDate > '" . $minDate . "'";
        }
        $sqlPenjualanNonTunaiDP .= " AND CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND " . $Condition . "\n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";

        $sqlUangMasuk = "\tSELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        CASE WHEN m.AccountType=1 THEN m.AccountName ELSE CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName) END AS KasRekening, 
        COALESCE(AccountType,2) UrutanKasRek, SUM(Amount) Total \n" .
                "\tFROM cashbankin s INNER JOIN options o ON
            o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN mastercashbankaccount m ON
            s.AccountID=m.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
            AND o.DeviceID=m.DeviceID" .
                "\tWHERE IgnoreInCloud=0";
        if ($minDate != false) {
            $sqlUangMasuk .= " and TransactionDate > '" . $minDate . "'";
        }
        $sqlUangMasuk .= " AND TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND " . $Condition . " \n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";
        $sqlUangMasukCloud = "\tSELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        CASE WHEN m.AccountType=1 THEN m.AccountName ELSE CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName) END AS KasRekening, 
        COALESCE(AccountType,2) UrutanKasRek, SUM(Amount) Total \n" .
                "\tFROM cloud_cashbankin s INNER JOIN options o ON
            o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN mastercashbankaccount m ON
            s.AccountID=m.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
            AND o.DeviceID=m.DeviceID" .
                "\tWHERE IgnoreInCloud=0";
        if ($minDate != false) {
            $sqlUangMasukCloud .= " and TransactionDate > '" . $minDate . "'";
        }
        $sqlUangMasukCloud .= " AND TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND " . $Condition . " \n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";

        $sqlUangKeluar = "\tSELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        CASE WHEN m.AccountType=1 THEN m.AccountName ELSE CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName) END AS KasRekening, 
        COALESCE(AccountType,2) UrutanKasRek, SUM(-Amount) Total \n" .
                "\tFROM cashbankout s INNER JOIN options o
            ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN mastercashbankaccount m ON
            s.AccountID=m.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
            AND o.DeviceID=m.DeviceID" .
                "\tWHERE TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND " . $Condition;
        if ($minDate != false) {
            $sqlUangKeluar .= " and TransactionDate > '" . $minDate . "'";
        }
        $sqlUangKeluar .= " \n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";

        $sqlUangKeluarCloud = "\tSELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        CASE WHEN m.AccountType=1 THEN m.AccountName ELSE CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName) END AS KasRekening, 
        COALESCE(AccountType,2) UrutanKasRek, SUM(-Amount) Total \n" .
                "\tFROM cloud_cashbankout s INNER JOIN options o
            ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN mastercashbankaccount m ON
            s.AccountID=m.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
            AND o.DeviceID=m.DeviceID" .
                "\tWHERE TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND " . $Condition;
        if ($minDate != false) {
            $sqlUangKeluarCloud .= " and TransactionDate > '" . $minDate . "'";
        }
        $sqlUangKeluarCloud .= " \n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";

        $sqlPembelianTunai = "\tSELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        m.AccountName AS KasRekening, CAST(1 AS Signed) UrutanKasRek, -SUM(CashPaymentAmount-s.Change) Total \n" .
                "\tFROM purchase s INNER JOIN options o
            ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN mastercashbankaccount m ON
            s.CashAccountID=m.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.CashAccountDeviceNo
            AND o.DeviceID=m.DeviceID" .
                "\tWHERE (s.PaymentMode=1 OR s.PaymentMode=3)
            AND PurchaseDate <= " . $this->CI->db->escape($this->dateEnd);
        if ($minDate != false) {
            $sqlPembelianTunai .= " and PurchaseDate > '" . $minDate . "'";
        }
        $sqlPembelianTunai .= " AND " . $Condition . " \n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";

        $sqlPembelianNonTunai = "\tSELECT m.AccountID,m.DeviceNo, CAST(1 AS Signed) UrutanOutlet, CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet, 
        CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName) AS KasRekening, CAST(2 AS Signed) UrutanKasRek, -SUM(BankPaymentAmount) Total \n" .
                "\tFROM purchase s INNER JOIN options o
            ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo" .
                "\tINNER JOIN mastercashbankaccount m ON s.BankAccountID=m.AccountID AND m.DeviceNo=s.BankAccountDeviceNo
            AND o.DeviceID=m.DeviceID AND s.PerusahaanNo=m.PerusahaanNo" .
                "\tWHERE (s.PaymentMode=2 OR s.PaymentMode=3)
            AND PurchaseDate <= " . $this->CI->db->escape($this->dateEnd);
        if ($minDate != false) {
            $sqlPembelianNonTunai .= " and PurchaseDate > '" . $minDate . "'";
        }
        $sqlPembelianNonTunai .= " AND " . $Condition . " \n" .
                "\tGROUP BY o.CompanyName,o.CompanyAddress,m.AccountID, m.DeviceNo";

        $sqlPerOutlet = "SELECT UrutanOutlet,Outlet,AccountID,DeviceNo,KasRekening,UrutanKasRek, SUM(Total) Total FROM (\n" .
                $sqlPenjualanTunai . "\nUNION ALL\n" . $sqlPenjualanTunaiDP .
                "\nUNION ALL\n" . $sqlPenjualanNonTunai . "\nUNION ALL\n" . $sqlPenjualanNonTunaiDP .
                "\nUNION ALL\n" . $sqlUangMasuk . "\nUNION ALL\n" . $sqlUangMasukCloud . "\nUNION ALL\n" . $sqlUangKeluar . "\nUNION ALL\n" . $sqlUangKeluarCloud . "\n " .
                "UNION ALL \n " . $sqlPembelianTunai . " \n UNION ALL " . $sqlPembelianNonTunai . ") X" .
                " GROUP BY UrutanOutlet,Outlet,AccountID,DeviceNo,UrutanKasRek";

//        echo $sqlPerOutlet;

        $sqlAllOutlet = "SELECT CAST(2 AS Signed) UrutanOutlet,'Semua' AS Outlet,AccountID,DeviceNo,KasRekening,UrutanKasRek, SUM(Total) Total FROM (\n" .
                $sqlPenjualanTunai . "\nUNION ALL\n" . $sqlPenjualanTunaiDP .
                "\nUNION ALL\n" . $sqlPenjualanNonTunai . "\nUNION ALL\n" . $sqlPenjualanNonTunaiDP .
                "\nUNION ALL\n" . $sqlUangMasuk . "\nUNION ALL\n" . $sqlUangMasukCloud . "\nUNION ALL\n" . $sqlUangKeluar . "\nUNION ALL\n" . $sqlUangMasukCloud . "\n" .
                "UNION ALL \n " . $sqlPembelianTunai . " \n UNION ALL " . $sqlPembelianNonTunai . ") X" .
                " GROUP BY AccountID,DeviceNo,UrutanKasRek";
        $query = $sqlPerOutlet . "\nUNION ALL\n" . $sqlAllOutlet;

        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            return "SELECT AccountID,DeviceNo,h.Total Saldo FROM (" . $sqlPerOutlet . ")h";
        } else {
            if ($this->Outlet == "Semua") {
                return "SELECT h.Outlet,AccountID,DeviceNo,h.Total Saldo FROM (" . $query . ")h ";
                /*
                  $strCabangs='';
                  foreach ($this->Cabangs as $cabang) {
                  $strCabangs=$strCabangs . ",'" . $cabang->OutletID . "'";
                  }
                  if(strlen($strCabangs)>0) {
                  $strCabangs=substr($strCabangs,1);

                  $query = $sqlPerOutlet . "\nUNION ALL\n" . $sqlAllOutlet;
                  return "SELECT h.Outlet,h.KasRekening,h.Total Saldo FROM (" . $query . ")h ";
                  }
                  else {
                  $query = $sqlPerOutlet . "\nUNION ALL\n" . $sqlAllOutlet;
                  return "SELECT h.Outlet,h.KasRekening,h.Total Saldo FROM (" . $query . ")h where h.Outlet=" . $this->CI->db->escape($this->Outlet);
                  }
                 */
            } else {
                //log_message('error',"SELECT AccountID,DeviceNo,h.Total Saldo FROM (" . $sqlPerOutlet . ")h");
                //var_dump("SELECT h.KasRekening,h.Total Saldo FROM (" . $sqlPerOutlet . ")h");
                return "SELECT AccountID,DeviceNo,h.Total Saldo FROM (" . $sqlPerOutlet . ")h";
            }
        }
    }

    function get_query_rekap_penjualan($isUseTaxModule, $rekapper) {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " s.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " s.PerusahaanNo=" . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }


        $queryStringItem = "SELECT Z.UrutanTampil, Z.ItemName,
            CASE WHEN UrutanTampil>1 THEN NULL ELSE 
            SUM(Z.Quantity) END Quantity ,SUM(Z.Total) Total FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, sd.ItemName AS ItemName, sd.Quantity, sd.SubTotal AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID
                WHERE 
                s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 2 AS UrutanTampil, 'Diskon Final' AS ItemName, 1 AS Quantity, 
                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo INNER JOIN 
                " . $this->tabelsaleitemdetail . " sd
                    ON s.DeviceID=sd.DeviceID AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
                WHERE 
                s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                GROUP BY s.DeviceID,s.TransactionID,s.DeviceNo,s.Total,s.Tax,s.PriceIncludeTax,s.TaxValue
                UNION ALL
                SELECT s.DeviceID, 3 AS UrutanTampil, 'Pajak' AS ItemName, 1 AS Quantity, s.TaxValue AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE s.Tax=1 AND s.PriceIncludeTax=0 AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85
                AND s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 3 AS UrutanTampil, sd.TaxName AS ItemName, 1 AS Quantity, CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) AS Total
                FROM " . $this->tabelsale . " s
                INNER JOIN saleitemdetailtax sd ON sd.DeviceID = s.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE sd.PriceIncludeTax=0
                AND s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
                GROUP BY s.DeviceID, s.TransactionID, s.DeviceNo, sd.TaxName
                UNION ALL
                SELECT s.DeviceID, 6 AS UrutanTampil, 'Pembulatan' AS ItemName, 1 AS Quantity, s.Rounding AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE s.Rounding<>0
                AND s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 4 AS UrutanTampil, 'Uang Muka' AS ItemName, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 5 AS UrutanTampil, 'Dipotong Uang Muka' AS ItemName, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
                ) Z GROUP BY Z.UrutanTampil, Z.ItemName HAVING SUM(Z.Quantity) <> 0 ";

        $queryStringCustomer = "SELECT CustomerName, SUM(Total) Total
        FROM
        (   SELECT
            x.Outlet,
            x.CustomerName,
            SUM(x.Total) Total
            FROM
            (   SELECT
                CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
                o.DeviceID,COALESCE(mi.CustomerName,s.CustomerName) as CustomerName,
                s.Total+s.Rounding-(s.CashDownPayment+s.BankDownPayment) Total
                FROM " . $this->tabelsale . " s
                LEFT JOIN
                mastercustomer mi
                ON s.CustomerID=mi.CustomerID AND s.PerusahaanNo=mi.PerusahaanNo AND s.CustomerDeviceNo=mi.DeviceNo
                AND mi.DeviceID=s.DeviceID
                INNER JOIN options o
                ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE
                s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
                UNION ALL
                SELECT
                CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
                o.DeviceID,COALESCE(mi.CustomerName,s.CustomerName) as CustomerName,
                (s.CashDownPayment+s.BankDownPayment) Total
                FROM " . $this->tabelsale . " s
                LEFT JOIN
                mastercustomer mi
                ON s.CustomerID=mi.CustomerID AND s.PerusahaanNo=mi.PerusahaanNo AND s.CustomerDeviceNo=mi.DeviceNo
                AND mi.DeviceID=s.DeviceID
                INNER JOIN options o
                ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE
                s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.CashDownPayment+s.BankDownPayment<>0
                AND " . $Condition . "
            ) x
            GROUP BY x.Outlet,
            x.CustomerName
        ) Z GROUP BY CustomerName";

        $queryStringCustomerAndItem = "
            SELECT Z.UrutanTampil, Z.ItemName,Z.CustomerName,SUM(Z.Quantity) Quantity,SUM(Z.Total) Total FROM
            (
                SELECT s.DeviceID, 1 AS UrutanTampil, s.CustomerName AS CustomerName, 
                sd.ItemName AS ItemName, sd.Quantity, sd.SubTotal AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd 
                ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                WHERE s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 2 AS UrutanTampil, s.CustomerName AS CustomerName, 
                'Diskon Final' AS ItemName, 0 AS Quantity, 
                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd
                ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                WHERE s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                GROUP BY s.DeviceID,s.CustomerName,s.Total,s.Tax,s.PriceIncludeTax,s.TaxValue
                UNION ALL
                SELECT s.DeviceID, 3 AS UrutanTampil, s.CustomerName AS CustomerName, 
                'Pajak' AS ItemName, 1 AS Quantity, s.TaxValue+s.TaxValueExclude AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE s.Tax=1 AND s.PriceIncludeTax=0
                AND s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 6 AS UrutanTampil, s.CustomerName AS CustomerName, 
                'Pembulatan' AS ItemName, 1 AS Quantity, s.Rounding AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE s.Rounding<>0
                AND s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 4 AS UrutanTampil, s.CustomerName AS CustomerName, 
                'Uang Muka' AS ItemName, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 5 AS UrutanTampil, s.CustomerName AS CustomerName, 
                'Dipotong Uang Muka' AS ItemName, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
            ) Z GROUP BY Z.UrutanTampil, Z.ItemName,Z.CustomerName HAVING SUM(Z.Quantity) <> 0 ";

        $queryStringKategori = "SELECT Z.UrutanTampil, Z.CategoryName,
            CASE WHEN UrutanTampil=2 OR UrutanTampil=3 THEN 0 ELSE 
            SUM(Z.Quantity) END Quantity ,SUM(Z.Total) Total FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, COALESCE(mc.CategoryName,'Lainnya') AS CategoryName, sd.Quantity, sd.SubTotal AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                LEFT JOIN masteritem mi ON mi.ItemID=sd.ItemID AND mi.DeviceID=s.DeviceID AND mi.PerusahaanNo=sd.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
                WHERE 
                s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 1 AS UrutanTampil, 'Uang Muka' AS CategoryName, 1 Quantity, s.CashDownPayment+s.BankDownPayment AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 1 AS UrutanTampil, 'Dipotong Uang Muka' AS CategoryName, 1 Quantity, -(s.CashDownPayment+s.BankDownPayment) AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.Pending = 'false'
                AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) Z GROUP BY Z.UrutanTampil, Z.CategoryName HAVING SUM(Z.Quantity) <> 0 ";

        $queryStringKategoriDanItem = "SELECT Z.UrutanTampil, Z.CategoryName, Z.ItemName,
            CASE WHEN UrutanTampil=2 OR UrutanTampil=3 THEN 0 ELSE
            SUM(Z.Quantity) END Quantity ,SUM(Z.Total) Total FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, COALESCE(mc.CategoryName,'Lainnya') AS CategoryName,
                sd.ItemName AS ItemName, sd.Quantity, sd.SubTotal AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceID=sd.DeviceID AND s.TransactionID=sd.TransactionID AND s.Pending = 'false' AND s.DeviceNo=sd.TransactionDeviceNo
                LEFT JOIN masteritem mi ON mi.ItemID=sd.ItemID AND mi.DeviceID=s.DeviceID AND mi.PerusahaanNo=sd.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
                WHERE
                s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 4 AS UrutanTampil,
                'Uang Muka' AS CategoryName, 'Uang Muka' AS ItemName, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN mastercustomer mc ON mc.CustomerID=s.CustomerID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceID AND mc.DeviceNo=s.CustomerDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 5 AS UrutanTampil,
                'Dipotong Uang Muka' AS CategoryName, 'Dipotong Uang Muka' AS ItemName, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN mastercustomer mc ON mc.CustomerID=s.CustomerID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceID AND mc.DeviceNo=s.CustomerDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
            ) Z GROUP BY Z.UrutanTampil, Z.CategoryName, Z.ItemName HAVING SUM(Z.Quantity) <> 0 ";

        $queryStringUser = "SELECT User, SUM(Total) Total
        FROM
        (   SELECT
            x.Outlet, x.User,
            SUM(x.Total) Total
            FROM
            (   SELECT
                CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
                o.DeviceID,CASE WHEN EditedBy='' THEN CreatedBy ELSE EditedBy END User,
                s.Total+s.Rounding Total
                FROM " . $this->tabelsale . " s
                INNER JOIN options o
                ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE
                s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false'
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 1 AS UrutanTampil, CreatedBy AS User, s.CashDownPayment+s.BankDownPayment AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 1 AS UrutanTampil, EditedBy AS User, -(s.CashDownPayment+s.BankDownPayment) AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND s.Pending = 'false'
                AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) x
            GROUP BY x.Outlet,
            x.User
        ) Z GROUP BY User";

        $queryString = $queryStringItem;
        if ($rekapper === "item") {
            $queryString = "SELECT ItemName AS Item, Quantity Qty,Total FROM (" . $queryStringItem . ") ff
            ORDER BY ff.UrutanTampil, ff.ItemName";
        } else if ($rekapper === "pelanggan") {
            $queryString = "SELECT CustomerName Pelanggan, Total FROM (" . $queryStringCustomer . ") ff
            ORDER BY ff.CustomerName";
        } else if ($rekapper === "pelangganitem") {
            $queryString = "SELECT CustomerName Pelanggan, ItemName AS Item, Quantity Qty, Total FROM (" . $queryStringCustomerAndItem . ") ff
            WHERE ff.Total<>0 ORDER BY ff.CustomerName, ff.UrutanTampil, ff.ItemName";
        } else if ($rekapper === "kategori") {
            $queryString = "SELECT CategoryName Kategori, Total FROM (" . $queryStringKategori . ") ff
            ORDER BY ff.CategoryName";
        } else if ($rekapper === "kategoriitem") {
            $queryString = "SELECT CategoryName Kategori, ItemName Item, Quantity, Total FROM (" . $queryStringKategoriDanItem . ") ff
            ORDER BY UrutanTampil,ff.CategoryName, ff.ItemName";
        } else if ($rekapper === "user") {
            $queryString = "SELECT User, Total FROM (" . $queryStringUser . ") ff
            ORDER BY ff.User";
        }

        //log_message('error',$queryString);

        return $queryString;
    }

    function get_query_rekap_penjualan_varian() {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " s.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->DeviceID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }

        $queryStringItemAndVarian = "SELECT Z.UrutanTampil, Z.ItemName,Z.Varian,SUM(Z.Quantity) Quantity,SUM(Z.TotalPerItem) TotalPerItem FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, 
                sd.VarianName AS Varian,
                sd.ItemName AS ItemName,  
                sd.Quantity, sd.SubTotal AS TotalPerItem
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                WHERE s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND sd.VarianID<>0 AND " . $Condition . "
            ) Z GROUP BY Z.UrutanTampil,Z.Varian, Z.ItemName HAVING SUM(Z.Quantity) <> 0 ";

        $queryString = "SELECT Varian, ItemName AS Item, Quantity Qty, TotalPerItem FROM (" . $queryStringItemAndVarian . ") ff
            WHERE ff.TotalPerItem<>0 ORDER BY ff.UrutanTampil, ff.Varian, ff.ItemName";
        return $queryString;
    }

    function get_query_rekap_penjualan_modifier() {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " s.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->DeviceID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }

        $queryStringItemAndModifier = "SELECT Z.UrutanTampil, Z.ItemName,Z.KelompokPilihan,Z.Pilihan,SUM(Z.Quantity) Quantity,SUM(Z.Total) Total FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, 
                sd.ItemName AS ItemName, 
                sdx.ModifierName AS KelompokPilihan, 
                sdx.ChoiceName AS Pilihan, 
                sd.Quantity * (CASE WHEN sdx.QtyChoice<>0 THEN sdx.QtyChoice ELSE 1 END) Quantity, sdx.ChoicePrice AS Harga,
                sd.Quantity * (CASE WHEN sdx.QtyChoice<>0 THEN sdx.QtyChoice ELSE 1 END) * sdx.ChoicePrice AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                INNER JOIN saleitemdetailmodifier sdx ON sdx.DetailID=sd.DetailID AND sd.PerusahaanNo=sdx.PerusahaanNo AND sdx.DeviceID=s.DeviceID AND sd.DeviceNo=sdx.DetailDeviceNo
                WHERE s.saleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.saleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) Z GROUP BY Z.UrutanTampil, Z.ItemName,Z.KelompokPilihan,Z.Pilihan HAVING SUM(Z.Quantity) <> 0 ";

        $queryString = "SELECT ItemName AS Item, KelompokPilihan, Pilihan, Quantity Qty, Total FROM (" . $queryStringItemAndModifier . ") ff
            ORDER BY ff.UrutanTampil, ff.ItemName, ff.KelompokPilihan, Pilihan";
        return $queryString;
    }

    function get_query_laba($isUseTaxModule) {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }

        $sqlSalesTotalOutlet = "
            SELECT  CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
            'Total Penjualan' as Uraian , SUM(
            CASE WHEN (s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND s.Pending = 'false') THEN
            s.Total+s.Rounding-(s.CashDownPayment+s.BankDownPayment) ELSE 0 END
            +
            CASE WHEN (s.CashDownPayment+s.BankDownPayment<>0 AND 
            s.CreatedDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . ")
                THEN s.CashDownPayment+s.BankDownPayment ELSE 0 END
            ) Total, 1 UrutanUraian
            FROM " . $this->tabelsale . " s
            INNER JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            WHERE ((s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND s.Pending = 'false') OR 
            (s.CashDownPayment+s.BankDownPayment<>0 AND 
            s.CreatedDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . ")) AND " . $Condition . "
            GROUP BY Outlet ";
        $sqlTotalCOGSOutlet = "
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,SUM(sd.COGS+sd.COGSModifier) Total
            FROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd
            ON s.TransactionID=sd.TransactionID AND s.Pending = 'false' AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
            AND s.DeviceID = sd.DeviceID
            INNER JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            WHERE s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND " . $Condition . "
            GROUP BY Outlet";
        $sqlPurchaseIdependentTotalOutlet = "
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,SUM(sd.SubTotal) Total
            FROM purchase s INNER JOIN purchaseitemdetail sd
            ON s.TransactionID=sd.TransactionID
            AND s.DeviceID = sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
            LEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND mi.DeviceID=sd.DeviceID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
            LEFT JOIN masteritemdetailingredients md
            ON md.IngredientsID=sd.ItemID AND sd.PerusahaanNo=md.PerusahaanNo AND md.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md.DeviceID = sd.DeviceID
            LEFT JOIN mastermodifierdetail md2
            ON md2.IngredientsID=sd.ItemID AND sd.PerusahaanNo=md2.PerusahaanNo AND md2.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md2.DeviceID = sd.DeviceID
            INNER JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            WHERE md.IngredientsID IS NULL AND md2.IngredientsID IS NULL AND (mi.IsProduct='false' OR (mi.IsProduct IS NULL AND sd.IsProduct=0))
            AND " . $Condition . "
            AND s.PurchaseDate>=" . $this->CI->db->escape($this->dateStart) . "
            AND s.PurchaseDate <= " . $this->CI->db->escape($this->dateEnd) . "
            GROUP BY Outlet";
        $sqlHPPOutlet = "SELECT Outlet,'Total HPP' , SUM(Total), 2 UrutanUraian FROM (" . $sqlTotalCOGSOutlet . " UNION ALL " . $sqlPurchaseIdependentTotalOutlet . ") t GROUP BY Outlet";
        $sqlMinusHPPOutlet = "SELECT Outlet,'Total HPP' , -SUM(Total), 2 UrutanUraian FROM (" . $sqlTotalCOGSOutlet . " UNION ALL " . $sqlPurchaseIdependentTotalOutlet . ") t GROUP BY Outlet";
        $sqlExpenseTotalOutlet = "
        SELECT outlet,'Total Pengeluaran' AS uraian,SUM(Total) Total, 3 UrutanUraian
        FROM
        (
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,COALESCE(s.Amount,0) Total
            FROM cashbankout s
            RIGHT JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            AND s.SpendingType = 1
            AND s.TransactionDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
            WHERE " . $Condition . "
            UNION ALL
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,COALESCE(s.Amount,0) Total
            FROM cloud_cashbankout s
            RIGHT JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            AND s.SpendingType = 1
            AND s.TransactionDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
            WHERE " . $Condition . "
        ) X
            GROUP BY Outlet";
        $sqlMinusExpenseTotalOutlet = "
        SELECT outlet,'Total Pengeluaran' AS uraian,SUM(Total) Total, 3 UrutanUraian
        FROM
        (
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,-COALESCE(s.Amount,0) Total, 4 UrutanUraian
            FROM cashbankout s
            RIGHT JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            AND s.TransactionDate>=" . $this->CI->db->escape($this->dateStart) . "
            AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND s.SpendingType = 1
            WHERE
            " . $Condition . "
            UNION ALL
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,-COALESCE(s.Amount,0) Total, 4 UrutanUraian
            FROM cloud_cashbankout s
            RIGHT JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            AND s.TransactionDate>=" . $this->CI->db->escape($this->dateStart) . "
            AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
            AND s.SpendingType = 1
            WHERE
            " . $Condition . "
        ) X
            GROUP BY Outlet";

        $sqlPendapatanLainnya = "
        SELECT outlet,'Total Pendapatan Lain' AS uraian,SUM(Total) Total, 3 UrutanUraian
        FROM
        (
            SELECT  CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
            COALESCE(s.Amount,0) Total
                FROM cashbankin s
                RIGHT JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            AND s.IncomeType = 1
            AND s.TransactionDate>=" . $this->CI->db->escape($this->dateStart) . "
                 AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
                WHERE " . $Condition . "
            UNION ALL
            SELECT  CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
            COALESCE(s.Amount,0) Total
                FROM cloud_cashbankin s
                RIGHT JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            AND s.IncomeType = 1
            AND s.TransactionDate>=" . $this->CI->db->escape($this->dateStart) . "
                 AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
                WHERE " . $Condition . "
        ) X GROUP BY Outlet";

        $sqlPajak = " SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
        'Total Pajak' as Uraian,SUM(s.TaxValue+s.TaxValueExclude+s.TaxValueInclude) Total, 6 UrutanUraian " .
                "\tFROM " . $this->tabelsale . " s \n" .
                "INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo \n" .
                "\tWHERE (s.Tax=1 OR s.CreatedVersionCode>=85 OR s.EditedVersionCode>=85) AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . "  AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) .
                "AND " . $Condition . " GROUP BY Outlet\n";
        $sqlPajakMinus = " SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
        'Total Pajak' as Uraian,SUM(-s.TaxValue-s.TaxValueExclude-s.TaxValueInclude) Total, 6 UrutanUraian " .
                "\tFROM " . $this->tabelsale . " s \n" .
                "INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo \n" .
                "\tWHERE  (s.Tax=1 OR s.CreatedVersionCode>=85 OR s.EditedVersionCode>=85) AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . "  AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) .
                "AND " . $Condition . " GROUP BY Outlet \n";
        $sqlLabaOutlet = "SELECT Outlet Outlet,'Laba' Uraian, SUM(Total) Total,5 UrutanUraian FROM(" . $sqlSalesTotalOutlet . " UNION ALL " . $sqlMinusHPPOutlet . " UNION ALL " . $sqlMinusExpenseTotalOutlet . " UNION ALL " . $sqlPendapatanLainnya . " ) r GROUP BY Outlet";

        $sqlLabaDanPajak = "SELECT Uraian,SUM(Total) Total, UrutanUraian  FROM (" . $sqlSalesTotalOutlet . " UNION ALL " . $sqlHPPOutlet . " UNION ALL " . $sqlExpenseTotalOutlet . " UNION ALL " . $sqlLabaOutlet . " UNION ALL " . $sqlPendapatanLainnya . " UNION ALL " . $sqlPajak . "  ) y group by Uraian";
        $sqlLabaFinal = "SELECT 'Laba Final' Uraian,SUM(Total) Total, 7 UrutanUraian  FROM ( " . $sqlLabaOutlet . " UNION ALL " . $sqlPajakMinus . ") y  ";
        return "SELECT Uraian,Total FROM (" . $sqlLabaDanPajak . " UNION ALL " . $sqlLabaFinal . ") final ORDER BY UrutanUraian";
    }

    /* sama dengan query laba, namun di ulang sebanyak shift yang ada 
     * author : Fach */

    function get_query_laba_per_shift($isUseTaxModule, $openID, $openDevNo) {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }

        $sqlSalesTotalOutlet = "
            SELECT  CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
            'Total Penjualan' as Uraian , SUM(
            CASE WHEN (s.openID = " . $openID . " AND s.OpenDeviceNo=" . $openDevNo . "
            AND s.Pending = 'false') THEN
            s.Total+s.Rounding-(s.CashDownPayment+s.BankDownPayment) ELSE 0 END
            +
            CASE WHEN (s.CashDownPayment+s.BankDownPayment<>0 AND 
            s.DownPaymentOpenID=" . $openID . " AND s.DownPaymentOpenDeviceNo = " . $openDevNo . ")
                THEN s.CashDownPayment+s.BankDownPayment ELSE 0 END
            ) Total, 1 UrutanUraian
            FROM " . $this->tabelsale . " s
            INNER JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            WHERE ((s.openID = " . $openID . " AND s.OpenDeviceNo=" . $openDevNo . "
            AND s.Pending = 'false') OR 
            (s.CashDownPayment+s.BankDownPayment<>0 AND 
            s.DownPaymentOpenID=" . $openID . " AND s.DownPaymentOpenDeviceNo = " . $openDevNo . ")) "
                . " AND " . $Condition . "
            GROUP BY Outlet ";
        $sqlTotalCOGSOutlet = "
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,SUM(sd.COGS+sd.COGSModifier) Total
            FROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd
            ON s.TransactionID=sd.TransactionID AND s.Pending = 'false' AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
            AND s.DeviceID = sd.DeviceID
            INNER JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            WHERE s.openID = " . $openID . " AND s.OpenDeviceNo=" . $openDevNo . " AND " . $Condition . "
            GROUP BY Outlet";
        $sqlPurchaseIdependentTotalOutlet = "
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,SUM(sd.SubTotal) Total
            FROM purchase s INNER JOIN purchaseitemdetail sd
            ON s.TransactionID=sd.TransactionID
            AND s.DeviceID = sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
            LEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND mi.DeviceID=sd.DeviceID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
            LEFT JOIN masteritemdetailingredients md
            ON md.IngredientsID=sd.ItemID AND sd.PerusahaanNo=md.PerusahaanNo AND md.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md.DeviceID = sd.DeviceID
            LEFT JOIN mastermodifierdetail md2
            ON md2.IngredientsID=sd.ItemID AND sd.PerusahaanNo=md2.PerusahaanNo AND md2.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md2.DeviceID = sd.DeviceID
            INNER JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            WHERE md.IngredientsID IS NULL AND md2.IngredientsID IS NULL AND (mi.IsProduct='false' OR (mi.IsProduct IS NULL AND sd.IsProduct=0))
            AND " . $Condition . "
            AND s.PurchaseDate>=" . $this->CI->db->escape($this->dateStart) . "
            AND s.PurchaseDate <= " . $this->CI->db->escape($this->dateEnd) . "
            GROUP BY Outlet";
        $sqlHPPOutlet = "SELECT Outlet,'Total HPP' , SUM(Total), 2 UrutanUraian FROM (" . $sqlTotalCOGSOutlet . " UNION ALL " . $sqlPurchaseIdependentTotalOutlet . ") t GROUP BY Outlet";
        $sqlMinusHPPOutlet = "SELECT Outlet,'Total HPP' , -SUM(Total), 2 UrutanUraian FROM (" . $sqlTotalCOGSOutlet . " UNION ALL " . $sqlPurchaseIdependentTotalOutlet . ") t GROUP BY Outlet";
        $sqlExpenseTotalOutlet = "
        SELECT outlet,'Total Pengeluaran' AS uraian,SUM(Total) Total, 3 UrutanUraian
        FROM
        (
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,COALESCE(s.Amount,0) Total
            FROM cashbankout s
            RIGHT JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            AND s.SpendingType = 1
            AND s.OpenID=" . $openID . " AND s.OpenDeviceNo = " . $openDevNo . "
            WHERE " . $Condition . "
        ) X
            GROUP BY Outlet";
        $sqlMinusExpenseTotalOutlet = "
        SELECT outlet,'Total Pengeluaran' AS uraian,SUM(Total) Total, 3 UrutanUraian
        FROM
        (
            SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,-COALESCE(s.Amount,0) Total, 4 UrutanUraian
            FROM cashbankout s
            RIGHT JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            AND s.OpenDeviceNo>=" . $openID . " AND s.OpenDeviceNo <= " . $openDevNo . "
            AND s.SpendingType = 1
            WHERE
            " . $Condition . "
        ) X
            GROUP BY Outlet";

        $sqlPendapatanLainnya = "
        SELECT outlet,'Total Pendapatan Lain' AS uraian,SUM(Total) Total, 3 UrutanUraian
        FROM
        (
            SELECT  CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
            COALESCE(s.Amount,0) Total
                FROM cashbankin s
                RIGHT JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            AND s.IncomeType = 1
            AND s.OpenDeviceNo>=" . $openID . " AND s.OpenDeviceNo <= " . $openDevNo . "
                WHERE " . $Condition . "
        ) X GROUP BY Outlet";

        $sqlPajak = " SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
        'Total Pajak' as Uraian,SUM(s.TaxValue+s.TaxValueExclude+s.TaxValueInclude) Total, 6 UrutanUraian " .
                "\tFROM " . $this->tabelsale . " s \n" .
                "INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo \n" .
                "\tWHERE s.openID = " . $openID . " AND s.OpenDeviceNo=" . $openDevNo . " AND (s.Tax=1 OR s.CreatedVersionCode>=85 OR s.EditedVersionCode>=85) AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . "  AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) .
                "AND " . $Condition . " GROUP BY Outlet\n";
        $sqlPajakMinus = " SELECT CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
        'Total Pajak' as Uraian,SUM(-s.TaxValue-s.TaxValueExclude-s.TaxValueInclude) Total, 6 UrutanUraian " .
                "\tFROM " . $this->tabelsale . " s \n" .
                "INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo \n" .
                "\tWHERE s.openID = " . $openID . " AND s.OpenDeviceNo=" . $openDevNo . " AND (s.Tax=1 OR s.CreatedVersionCode>=85 OR s.EditedVersionCode>=85) AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . "  AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) .
                "AND s.openID = " . $openID . " AND s.OpenDeviceNo=" . $openDevNo . " AND " . $Condition . " GROUP BY Outlet \n";
        $sqlLabaOutlet = "SELECT Outlet Outlet,'Laba' Uraian, SUM(Total) Total,5 UrutanUraian FROM(" . $sqlSalesTotalOutlet . " UNION ALL " . $sqlMinusHPPOutlet . " UNION ALL " . $sqlMinusExpenseTotalOutlet . " UNION ALL " . $sqlPendapatanLainnya . " ) r GROUP BY Outlet";

        $sqlLabaDanPajak = "SELECT Uraian,SUM(Total) Total, UrutanUraian  FROM (" . $sqlSalesTotalOutlet . " UNION ALL " . $sqlHPPOutlet . " UNION ALL " . $sqlExpenseTotalOutlet . " UNION ALL " . $sqlLabaOutlet . " UNION ALL " . $sqlPendapatanLainnya . " UNION ALL " . $sqlPajak . "  ) y group by Uraian";
        $sqlLabaFinal = "SELECT 'Laba Final' Uraian,SUM(Total) Total, 7 UrutanUraian  FROM ( " . $sqlLabaOutlet . " UNION ALL " . $sqlPajakMinus . ") y  ";
        $sql = "SELECT Uraian,Total FROM (" . $sqlLabaDanPajak . " UNION ALL " . $sqlLabaFinal . ") final ORDER BY UrutanUraian";
        return $sql;
    }

    protected function CheckDeviceID() {
        if ($this->clientDeviceID === '') {
            return 'Error Device ID is not set';
        }
    }

    public function get_query_rekap_pembelian($rekapper) {
        $this->CheckDeviceID();
        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                    $Condition2 = " DeviceID IN (" . $strCabangs . ") ";
                } else {
                    $Condition = " o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                    $Condition2 = " 1=1 ";
                }
            } else {
                $Condition = " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
            }
        }


        $queryStringItem = "SELECT Z.UrutanTampil, Z.ItemName,
            CASE WHEN UrutanTampil=2 OR UrutanTampil=3 THEN 0 ELSE 
            SUM(Z.Quantity) END Quantity ,SUM(Z.Total) Total FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, sd.ItemName AS ItemName, sd.Quantity, sd.SubTotal AS Total
                FROM purchase s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN purchaseitemdetail sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo 
                AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                WHERE 
                s.PurchaseDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.PurchaseDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 2 AS UrutanTampil, 'Diskon Final' AS ItemName, 1 AS Quantity, 
                s.Total-SUM(sd.SubTotal) Total
                FROM purchase s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo INNER JOIN 
                purchaseitemdetail sd
                    ON s.DeviceID=sd.DeviceID AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID 
                    AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
                WHERE 
                s.PurchaseDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.PurchaseDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.FinalDiscount <> ''
                AND s.FinalDiscount <> '%'
                AND s.FinalDiscount NOT LIKE '0%'
                AND " . $Condition . "
                GROUP BY s.DeviceID,s.TransactionID,s.DeviceNo,s.Total
            ) Z GROUP BY Z.UrutanTampil, Z.ItemName HAVING SUM(Z.Quantity) <> 0 ";

        $queryStringSupplier = "SELECT SupplierName, SUM(Total) Total
        FROM
        (   SELECT
            x.Outlet,
            x.SupplierName,
            SUM(x.Total) Total
            FROM
            (   SELECT
                CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
                o.DeviceID,COALESCE(mi.SupplierName,s.SupplierName) as SupplierName,
                s.Total Total
                FROM purchase s
                LEFT JOIN
                mastersupplier mi
                ON s.SupplierID=mi.SupplierID AND s.PerusahaanNo=mi.PerusahaanNo
                AND mi.DeviceID=s.DeviceID AND mi.DeviceNo=s.SupplierDeviceNo
                INNER JOIN options o
                ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE
                s.PurchaseDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.PurchaseDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) x
            GROUP BY x.Outlet,
            x.SupplierName
        ) Z GROUP BY SupplierName";

        $queryStringSupplierAndItem = "SELECT Z.UrutanTampil, Z.ItemName,Z.SupplierName,SUM(Z.Quantity) Quantity,SUM(Z.Total) Total FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, COALESCE(mc.SupplierName,s.SupplierName) AS SupplierName, 
                sd.ItemName AS ItemName, sd.Quantity, sd.SubTotal AS Total
                FROM purchase s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN purchaseitemdetail sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' 
                AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                LEFT JOIN mastersupplier mc ON mc.SupplierID=s.SupplierID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceID AND mc.DeviceNo=s.SupplierDeviceNo
                WHERE s.PurchaseDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.PurchaseDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 2 AS UrutanTampil, COALESCE(mc.SupplierName,s.SupplierName) AS SupplierName, 
                'Diskon Final' AS ItemName, 0 AS Quantity, 
                s.Total-SUM(sd.SubTotal) Total
                FROM purchase s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN purchaseitemdetail sd
                ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                LEFT JOIN mastersupplier mc ON mc.SupplierID=s.SupplierID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceID AND mc.DeviceNo=s.SupplierDeviceNo
                WHERE s.PurchaseDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.PurchaseDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                GROUP BY s.DeviceID,mc.SupplierName,s.SupplierName,s.Total
        ) Z GROUP BY Z.UrutanTampil, Z.ItemName,Z.SupplierName HAVING SUM(Z.Quantity) <> 0 ";

        $queryString = $queryStringItem;
        if ($rekapper === "item") {
            $queryString = "SELECT ItemName AS Item, Quantity Qty,Total FROM (" . $queryStringItem . ") ff
            ORDER BY ff.UrutanTampil, ff.ItemName";
        } else if ($rekapper === "supplier") {
            $queryString = "SELECT SupplierName Supplier, Total FROM (" . $queryStringSupplier . ") ff
            ORDER BY ff.SupplierName";
        } else if ($rekapper === "supplieritem") {
            $queryString = "SELECT SupplierName Supplier, ItemName AS Item, Quantity Qty, Total FROM (" . $queryStringSupplierAndItem . ") ff
            ORDER BY ff.SupplierName, ff.UrutanTampil, ff.ItemName";
        }

        return $queryString;
    }

    public function get_query_pembelian() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                    $Condition2 = " DeviceID IN (" . $strCabangs . ")";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                    $Condition2 = " DeviceID IN (" . $strCabangs . ")";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
            }
        }

        $queryString = "
            SELECT
            CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
            s.PurchaseNumber,
            CONCAT(s.PurchaseDate ,', ',s.PurchaseTime) as 'SaleDateTime',
            COALESCE(ms.SupplierName, s.SupplierName) SupplierName,
            sd.ItemName ItemName,
            sd.Quantity,sd.UnitPrice,CASE WHEN sd.Discount='%' THEN '' ELSE sd.Discount END Discount,sd.SubTotal,
            CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah
            FROM purchase s
            INNER JOIN options o
                ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            INNER JOIN purchaseitemdetail sd ON s.TransactionID = sd.TransactionID
                AND s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
            LEFT JOIN mastersupplier ms ON ms.SupplierID = s.SupplierID
                AND ms.DeviceID = s.DeviceID AND s.PerusahaanNo=s.PerusahaanNo AND ms.DeviceNo=s.SupplierDeviceNo
            WHERE
                s.PurchaseDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.PurchaseDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            UNION ALL
            SELECT
            CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
            s.PurchaseNumber,
            CONCAT(s.PurchaseDate ,', ',s.PurchaseTime) as 'SaleDateTime',
            COALESCE(ms.SupplierName, s.SupplierName) SupplierName,
            'FinalDiscount' ItemName,
            1 AS Qty, SUM(sd.SubTotal) SubTotal, s.FinalDiscount, s.Total,
            s.CreatedBy DibuatOleh,CONCAT(s.CreatedDate,', ',s.CreatedTime) WaktuDibuat, s.EditedBy DiubahOleh,CONCAT(s.EditedDate,', ',s.EditedTime) WaktuDiubah
            FROM purchase s INNER JOIN purchaseitemdetail sd
            ON s.TransactionID = sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo
            AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo
            INNER JOIN options o
            ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            LEFT JOIN mastersupplier ms ON ms.SupplierID = s.SupplierID 
            AND ms.DeviceID=o.DeviceID AND s.PerusahaanNo=ms.PerusahaanNo AND ms.DeviceNo=s.SupplierDeviceNo
            WHERE
                s.FinalDiscount <> ''
                AND s.FinalDiscount <> '%'
                AND s.FinalDiscount NOT LIKE '0%'
                AND s.PurchaseDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.PurchaseDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
        GROUP BY s.PurchaseNumber
                    ";
        log_message('error', "SELECT h.PurchaseNumber as 'Nomor',h.SaleDateTime as 'Tanggal',h.SupplierName as 'Pelanggan',
            h.ItemName as Item,h.Quantity as Qty,"
                . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah FROM (" . $queryString . ") h ORDER BY h.SaleDateTime,h.Discount ASC");

        $isPerusahaan = $this->clientPerusahaanID != $this->clientDeviceID;
        $isSemua = $this->Outlet == "Semua";
        if ($isPerusahaan && $isSemua) {
            return "SELECT h.Outlet,h.PurchaseNumber as 'Nomor',h.SaleDateTime as 'Tanggal',h.SupplierName as 'Pelanggan',
            h.ItemName as Item,h.Quantity as Qty,"
                    . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah FROM (" . $queryString . ") h ORDER BY h.Outlet,h.SaleDateTime,h.Discount ASC";
        } else {
            return "SELECT h.PurchaseNumber as 'Nomor',h.SaleDateTime as 'Tanggal',h.SupplierName as 'Pelanggan',
            h.ItemName as Item,h.Quantity as Qty,"
                    . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah FROM (" . $queryString . ") h ORDER BY h.SaleDateTime,h.Discount ASC";
        }
    }

    private function getQPurchase($Condition) {
        $qpurchase = "SELECT
                        -- CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
                        ItemID, ItemDeviceNo, p.DeviceID, 'Pembelian' TransactionName,
                        CONCAT( PurchaseNumber, '(', DetailNumber, ')' ) TransactionNumber,
                        DetailID TransactionDetailID, PurchaseDate TransactionDate, PurchaseTime TransactionTime, 
                        CONCAT( 'Supplier: ', COALESCE(ms.SupplierName, '-'), '. ', d.Note ) Keterangan,
                        d.Quantity, d.UnitPrice
                    FROM
                        purchase p
                        INNER JOIN purchaseitemdetail d ON p.TransactionID = d.TransactionID
                          AND p.DeviceID = d.DeviceID AND p.PerusahaanNo=d.PerusahaanNo AND p.DeviceNo=d.TransactionDeviceNo
                        LEFT JOIN mastersupplier ms ON ms.SupplierID = p.SupplierID
                          AND p.DeviceID = ms.DeviceID AND p.PerusahaanNo=ms.PerusahaanNo AND ms.DeviceNo=p.SupplierDeviceNo
                        -- INNER JOIN options o
                        -- ON o.DeviceID=p.DeviceID AND p.PerusahaanNo=o.PerusahaanNo
                        WHERE
                        d.Quantity <> 0 AND " . $Condition . "
        ";
        return $qpurchase;
    }

    private function getQSale($Condition) {
        $qsale = "SELECT -- CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
                  ItemID, ItemDeviceNo, p.DeviceID, 'Penjualan' TransactionName,
                  CONCAT(SaleNumber, '(', DetailNumber, ')') TransactionNumber,
                  DetailID TransactionDetailID, SaleDate TransactionDate,
                  SaleTime TransactionTime,
                  CONCAT('Customer: ', COALESCE(p.CustomerName, '-'), '. ', d.Note) Keterangan,
                  - d.Quantity, d.UnitPrice
                  FROM " . $this->tabelsale . " p
                  INNER JOIN " . $this->tabelsaleitemdetail . " d ON p.TransactionID = d.TransactionID
                        AND p.DeviceID= d.DeviceID AND p.PerusahaanNo=d.PerusahaanNo AND p.DeviceNo=d.TransactionDeviceNo
                  -- INNER JOIN options o
                  --       ON o.DeviceID=p.DeviceID AND p.PerusahaanNo=o.PerusahaanNo
                  WHERE (CashDownPayment+BankDownPayment=0 OR Pending='false') 
                  AND d.Quantity <> 0 AND " . $Condition . " 
                    ";
        return $qsale;
    }

    private function getQStockOpname($Condition) {
        $qstock_opname = "SELECT -- CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
                            ItemID, ItemDeviceNo, p.DeviceID, 'Stock Opname' TransactionName,
                            CONCAT(StockOpnameNumber, '(', DetailNumber, ')') TransactionNumber,
                            DetailID TransactionDetailID, StockOpnameDate TransactionDate, StockOpnameTime TransactionTime,
                            CONCAT('Note: ', d.Note) Keterangan, d.RealStock - d.StockByApp, 0 AS UnitPrice
                        FROM
                            stockopname p
                            INNER JOIN stockopnamedetail d ON p.TransactionID = d.TransactionID
                            AND p.DeviceID= d.DeviceID AND p.PerusahaanNo=d.PerusahaanNo AND p.DeviceNo=d.TransactionDeviceNo
                            -- INNER JOIN options o
                            -- ON o.DeviceID=p.DeviceID AND p.PerusahaanNo=o.PerusahaanNo
                        WHERE p.IgnoreInCloud=0 AND d.RealStock - d.StockByApp <> 0 AND " . $Condition . " ";
        return $qstock_opname;
    }

    private function getQSaleIngredients($Condition) {
        $qsaleingredients = " SELECT -- CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
                                IngredientsID, IngredientsDeviceNo, p.DeviceID,
                                'Penjualan' TransactionName,
                                CONCAT(SaleNumber, '(', DetailNumber, ')') TransactionNumber,
                                DetailIngredientsID TransactionDetailID, SaleDate TransactionDate, SaleTime TransactionTime,
                                CONCAT('Customer: ', COALESCE(p.CustomerName, '-'), '. ') Keterangan,
                                - (d.QtyUsed-d.QtyUsedCancel), 0 AS UnitPrice
                                FROM
                                " . $this->tabelsale . " p
                                INNER JOIN " . $this->tabelsaleitemdetailingredients . " d ON p.TransactionID = d.TransactionID
                                  AND p.DeviceID= d.DeviceID AND p.PerusahaanNo=d.PerusahaanNo AND p.DeviceNo=d.TransactionDeviceNo
                                -- INNER JOIN options o
                                --   ON o.DeviceID=p.DeviceID AND p.PerusahaanNo=o.PerusahaanNo
                                WHERE(CashDownPayment+BankDownPayment=0 OR Pending='false') 
                                AND d.QtyUsed <> 0 AND " . $Condition . "
        ";
        return $qsaleingredients;
    }

    private function getQSaleModifier($Condition) {
        $qsaleingredients = " SELECT -- CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
                                IngredientsID, IngredientsDeviceNo, p.DeviceID,
                                'Penjualan' TransactionName,
                                CONCAT(SaleNumber, '(', DetailNumber, ')') TransactionNumber,
                                SaleDetailModifierID TransactionDetailID, SaleDate TransactionDate, SaleTime TransactionTime,
                                CONCAT('Customer: ', COALESCE(p.CustomerName, '-'), '. ') Keterangan,
                                - (d.QtyUsed-d.QtyUsedCancel+d.QtyUsedCanceler), 0 AS UnitPrice FROM
                                " . $this->tabelsale . " p
                                INNER JOIN saleitemdetailmodifier d ON p.TransactionID = d.TransactionID
                                  AND p.DeviceID= d.DeviceID AND p.PerusahaanNo=d.PerusahaanNo AND p.DeviceNo=d.TransactionDeviceNo
                                -- INNER JOIN options o
                                --   ON o.DeviceID=p.DeviceID AND p.PerusahaanNo=o.PerusahaanNo
                                WHERE (CashDownPayment+BankDownPayment=0 OR Pending='false') 
                                AND d.IngredientsID<>0 AND (d.QtyUsed-d.QtyUsedCancel+d.QtyUsedCanceler)<>0 AND " . $Condition . "
        ";
        return $qsaleingredients;
    }

    private function getQModifierIngredients($Condition) {
        $qsaleingredients = " SELECT -- CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
                                IngredientsID, IngredientsDeviceNo, p.DeviceID,
                                'Penjualan Bahan Ekstra' TransactionName,
                                CONCAT(SaleNumber, '(', DetailNumber, ')') TransactionNumber,
                                DetailIngredientsID TransactionDetailID, SaleDate TransactionDate, SaleTime TransactionTime,
                                CONCAT('Customer: ', COALESCE(p.CustomerName, '-'), '. ') Keterangan,
                                - (d.QtyUsed-d.QtyCancel), 0 AS UnitPrice FROM
                                " . $this->tabelsale . " p
                                INNER JOIN salemodifierdetailingredients d ON p.TransactionID = d.TransactionID
                                  AND p.DeviceID=d.DeviceID AND p.PerusahaanNo=d.PerusahaanNo AND p.DeviceNo=d.TransactionDeviceNo
                                -- INNER JOIN options o
                                --   ON o.DeviceID=p.DeviceID AND p.PerusahaanNo=o.PerusahaanNo
                                WHERE (CashDownPayment+BankDownPayment=0 OR Pending='false') 
                                AND d.IngredientsID<>0 AND (d.QtyUsed-d.QtyCancel)<>0 AND " . $Condition . "
        ";
        return $qsaleingredients;
    }

    private function getQTransferStockIn($Condition) {
        $qpurchase = "SELECT
                        -- CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
                        d.TransferToItemID,d.TransferToItemDeviceNo, p.TransferToDeviceID, 'Transfer Stok (Masuk)' TransactionName,
                        CONCAT( TransferNumber, '(', DetailNumber, ')' ) TransactionNumber,
                        DetailID TransactionDetailID, TransferDate TransactionDate, TransferTime TransactionTime, 
                        d.Note Keterangan,
                        d.Quantity, 0 AS UnitPrice
                    FROM
                        transferstock p
                        INNER JOIN transferstockdetail d ON p.TransactionID = d.TransactionID
                          AND p.DeviceID = d.DeviceID AND p.PerusahaanNo=d.PerusahaanNo AND p.DeviceNo=d.TransactionDeviceNo
                        -- INNER JOIN options o
                        -- ON o.DeviceID=p.TransferToDeviceID AND p.PerusahaanNo=o.PerusahaanNo
                        WHERE
                        d.Quantity <> 0 AND " . $Condition . "
        ";
        return $qpurchase;
    }

    private function getQTransferStockOut($Condition) {
        $qpurchase = "SELECT
                        -- CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,
                        ItemID, ItemDeviceNo, p.DeviceID, 'Transfer Stok (Keluar)' TransactionName,
                        CONCAT( TransferNumber, '(', DetailNumber, ')' ) TransactionNumber,
                        DetailID TransactionDetailID, TransferDate TransactionDate, TransferTime TransactionTime, 
                        d.Note Keterangan,
                        -d.Quantity, 0 AS UnitPrice
                    FROM
                        transferstock p
                        INNER JOIN transferstockdetail d ON p.TransactionID = d.TransactionID
                          AND p.DeviceID = d.DeviceID AND p.PerusahaanNo=d.PerusahaanNo AND p.DeviceNo=d.TransactionDeviceNo
                        -- INNER JOIN options o
                        -- ON o.DeviceID=p.DeviceID AND p.PerusahaanNo=o.PerusahaanNo
                        WHERE
                        d.Quantity <> 0 AND " . $Condition . "
        ";
        return $qpurchase;
    }

    public function get_query_stok() {
        $this->CheckDeviceID();

        //$whereOutlet = " CONCAT(o.CompanyName, ' ',o.CompanyAddress) = " . $this->CI->db->escape($this->Outlet);
        $whereOutlet = " o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID);
        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $ConditionM = "";
        $JoinItem = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
            $ConditionM = " m.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " ";
                    $ConditionM = " m.PerusahaanNo = " . $this->NoPerusahaan . " AND m.DeviceID IN (" . $strCabangs . ") ";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                    $ConditionM = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $whereOutlet = " o.DeviceID = " . $this->CI->db->escape($this->Outlet);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $ConditionM = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND m.DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
            }
        }

        $isPerusahaan = $this->clientPerusahaanID != $this->clientDeviceID;
        $isSemua = $this->Outlet == "Semua";
        if ($isPerusahaan && $isSemua) {
            $sql = "SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,m.ItemName as 'Item',COALESCE(SUM(Quantity),0) as 'Qty',m.Unit as 'Satuan' FROM
                masteritem m INNER JOIN options o ON m.DeviceID=o.DeviceID AND m.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN
                (" . $this->getQPurchase($Condition) .
                    " AND p.PurchaseDate <=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQSale($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQStockOpname($Condition) .
                    " AND p.StockOpnameDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQSaleIngredients($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQSaleModifier($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQModifierIngredients($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQTransferStockIn($Condition) .
                    " AND p.TransferDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQTransferStockOut($Condition) .
                    " AND p.TransferDate<=" . $this->CI->db->escape($this->dateStart) . " \n" .
                    "
                ) v ON v.ItemID=m.ItemID
                AND m.DeviceID = v.DeviceID AND m.DeviceNo = v.ItemDeviceNo
                WHERE m.Stock='true' AND " . $ConditionM . " AND " . $whereOutlet . "
                GROUP BY m.ItemID,m.DeviceNo,m.ItemName,m.Unit,o.CompanyName,o.CompanyAddress";
        } else {
            $sql = "SELECT m.ItemName as 'Item',COALESCE(SUM(Quantity),0) as 'Qty',m.Unit as 'Satuan' FROM
                masteritem m INNER JOIN options o ON m.DeviceID=o.DeviceID AND m.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN
                (" . $this->getQPurchase($Condition) .
                    " AND p.PurchaseDate <=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQSale($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQStockOpname($Condition) .
                    " AND p.StockOpnameDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQSaleIngredients($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQSaleModifier($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQModifierIngredients($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQTransferStockIn($Condition) .
                    " AND p.TransferDate<=" . $this->CI->db->escape($this->dateStart) . " UNION ALL\n" .
                    $this->getQTransferStockOut($Condition) .
                    " AND p.TransferDate<=" . $this->CI->db->escape($this->dateStart) . " \n" .
                    "
                ) v ON v.ItemID=m.ItemID
                AND m.DeviceID = v.DeviceID AND m.DeviceNo=v.ItemDeviceNo
                WHERE m.Stock='true' AND " . $ConditionM . " AND " . $whereOutlet . "
                GROUP BY m.ItemID,m.DeviceNo,m.ItemName,m.Unit";
        }

        return $sql;
    }

    public function get_query_stok2($minDate = false) {
        $this->CheckDeviceID();

        //$whereOutlet = " CONCAT(o.CompanyName, ' ',o.CompanyAddress) = " . $this->CI->db->escape($this->Outlet);
        $whereOutlet = " o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID);
        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $ConditionM = "";
        $JoinItem = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
            $ConditionM = " m.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " p.PerusahaanNo = " . $this->NoPerusahaan . " AND p.DeviceID IN (" . $strCabangs . ") ";
                    $ConditionM = " m.PerusahaanNo = " . $this->NoPerusahaan . " AND m.DeviceID IN (" . $strCabangs . ") ";
                } else {
                    $Condition = " p.PerusahaanNo = " . $this->NoPerusahaan;
                    $ConditionM = " m.PerusahaanNo = " . $this->NoPerusahaan;
                }
            } else {
                $whereOutlet = " o.DeviceID = " . $this->CI->db->escape($this->Outlet);
                $Condition = " p.PerusahaanNo = " . $this->NoPerusahaan . " AND p.DeviceID = " . $this->CI->db->escape($this->Outlet);
                $ConditionM = " m.PerusahaanNo = " . $this->NoPerusahaan . " AND m.DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
            }
        }

        $isPerusahaan = $this->clientPerusahaanID != $this->clientDeviceID;
        $isSemua = $this->Outlet == "Semua";
        if ($isPerusahaan && $isSemua) {
            $sql = "SELECT m.ItemId,m.DeviceNo, CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,m.ItemName as 'Item',COALESCE(SUM(Quantity),0) as 'Qty',m.Unit as 'Satuan' FROM
                masteritem m INNER JOIN options o ON m.DeviceID=o.DeviceID AND m.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN
                (" . $this->getQPurchase($Condition) .
                    " AND p.PurchaseDate <=" . $this->CI->db->escape($this->dateStart);
            if ($minDate != false) {
                $sql .= " AND p.PurchaseDate > '" . $minDate . "'";
            }

            $sql .= " UNION ALL\n" .
                    $this->getQSale($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart);
            if ($minDate != false) {
                $sql .= " AND p.SaleDate > '" . $minDate . "'";
            }

            $sql .= " UNION ALL\n" .
                    $this->getQStockOpname($Condition) .
                    " AND p.StockOpnameDate<=" . $this->CI->db->escape($this->dateStart);
            if ($minDate != false) {
                $sql .= " AND p.StockOpnameDate > '" . $minDate . "'";
            }

            $sql .= " UNION ALL\n" .
                    $this->getQSaleIngredients($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart);
            if ($minDate != false) {
                $sql .= " AND p.SaleDate > '" . $minDate . "'";
            }

            $sql .= " UNION ALL\n" .
                    $this->getQSaleModifier($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart);
            if ($minDate != false) {
                $sql .= " AND p.SaleDate > '" . $minDate . "'";
            }

            $sql .= " UNION ALL\n" .
                    $this->getQModifierIngredients($Condition) .
                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart);
            if ($minDate != false) {
                $sql .= " AND p.SaleDate > '" . $minDate . "'";
            }

            $sql .= " UNION ALL\n" .
                    $this->getQTransferStockIn($Condition) .
                    " AND p.TransferDate<=" . $this->CI->db->escape($this->dateStart);
            if ($minDate != false) {
                $sql .= " AND p.TransferDate > '" . $minDate . "'";
            }

            $sql .= " UNION ALL\n" .
                    $this->getQTransferStockOut($Condition) .
                    " AND p.TransferDate<=" . $this->CI->db->escape($this->dateStart);
            if ($minDate != false) {
                $sql .= " AND p.TransferDate > '" . $minDate . "'";
            }


            $sql .= " \n" .
                    "
                ) v ON v.ItemID=m.ItemID
                AND m.DeviceID = v.DeviceID AND m.DeviceNo = v.ItemDeviceNo
                WHERE m.Stock='true' AND " . $ConditionM . " -- AND " . $whereOutlet . "
                GROUP BY m.ItemID,m.DeviceNo";
        } else {
            $sql = "SELECT m.ItemId,m.DeviceNo, CONCAT('<a href=\"" . base_url() . "laporan/kartustok?date_start=" .
                    $this->dateStart . "&date_end=" . $this->dateStart . "&outlet=" . $this->Outlet . "&item=',m.ItemID,'.',m.DeviceNo,'\" target=\"_blank\">',m.ItemName,'</a>') as 'Item',COALESCE(Quantity,0) as 'Qty',m.Unit as 'Satuan' FROM
                masteritem m -- INNER JOIN options o ON m.DeviceID=o.DeviceID AND m.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN
                (
                SELECT ItemID,ItemDeviceNo,SUM(Quantity) Quantity
                FROM
	                (" . $this->getQPurchase($Condition) .
	                    " AND p.PurchaseDate <=" . $this->CI->db->escape($this->dateStart);
	            if ($minDate != false) {
	                $sql .= " AND p.PurchaseDate > '" . $minDate . "'";
	            }

	            $sql .= " UNION ALL\n" .
	                    $this->getQSale($Condition) .
	                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart);
	            if ($minDate != false) {
	                $sql .= " AND p.SaleDate > '" . $minDate . "'";
	            }

	            $sql .= " UNION ALL\n" .
	                    $this->getQStockOpname($Condition) .
	                    " AND p.StockOpnameDate<=" . $this->CI->db->escape($this->dateStart);
	            if ($minDate != false) {
	                $sql .= " AND p.StockOpnameDate > '" . $minDate . "'";
	            }

	            $sql .= " UNION ALL\n" .
	                    $this->getQSaleIngredients($Condition) .
	                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart);
	            if ($minDate != false) {
	                $sql .= " AND p.SaleDate > '" . $minDate . "'";
	            }

	            $sql .= " UNION ALL\n" .
	                    $this->getQSaleModifier($Condition) .
	                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart);
	            if ($minDate != false) {
	                $sql .= " AND p.SaleDate > '" . $minDate . "'";
	            }

	            $sql .= " UNION ALL\n" .
	                    $this->getQModifierIngredients($Condition) .
	                    " AND p.SaleDate<=" . $this->CI->db->escape($this->dateStart);
	            if ($minDate != false) {
	                $sql .= " AND p.SaleDate > '" . $minDate . "'";
	            }

	            $sql .= " UNION ALL\n" .
	                    $this->getQTransferStockIn($Condition) .
	                    " AND p.TransferDate<=" . $this->CI->db->escape($this->dateStart);
	            if ($minDate != false) {
	                $sql .= " AND p.TransferDate > '" . $minDate . "'";
	            }

	            $sql .= " UNION ALL\n" .
	                    $this->getQTransferStockOut($Condition) .
	                    " AND p.TransferDate<=" . $this->CI->db->escape($this->dateStart);
	            if ($minDate != false) {
	                $sql .= " AND p.TransferDate > '" . $minDate . "'";
	            }

	            if ($minDate != false) {
	                $sql .= " \nUNION ALL\n"
	                        . "SELECT -- '' AS Outlet, 
	                        ItemID, ItemDeviceNo, DeviceID, 'Stok Awal' TransactionName,
	                  '-' TransactionNumber,
	                  '-' TransactionDetailID, InsertedDate TransactionDate,
	                  '23:59:59' TransactionTime,
	                  '-' Keterangan,
	                  Qty, 0 AS UnitPrice
	                  FROM checkpoint WHERE PerusahaanNo=" . $this->NoPerusahaan .
	                        " AND DeviceID=" . $this->Outlet . " AND InsertedDate='" . $minDate . "'";
	            }
	            $sql .= " \n" .
	                    "
	            ) x
            	GROUP BY ItemID,ItemDeviceNo
            ) v ON v.ItemID=m.ItemID
            AND m.DeviceNo=v.ItemDeviceNo
            WHERE m.Stock='true' AND " . $ConditionM . " -- AND " . $whereOutlet . "
            ORDER BY m.ItemName";
        }

        return $sql;
    }

    public function get_query_kartu_stok($ItemID) {
        $realitemid = explode(".", $ItemID)[0];
        $devno = explode(".", $ItemID)[1];
        $isPerusahaan = $this->clientPerusahaanID != $this->clientDeviceID;
        if (!$isPerusahaan) {
            return "call nutacloud.kartustok1(" . $realitemid . "," . $devno . "," . $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . "," .
                    $this->CI->db->escape($this->clientDeviceID) . ") ";
        } else {
            //echo "call nutacloud.kartustok1(" . $ItemID . "," . $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . "," .
            //$this->CI->db->escape($this->Outlet) . ") ";

            return "call nutacloud.kartustok1(" . $realitemid . "," . $devno . "," . $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . "," .
                    $this->CI->db->escape($this->Outlet) . ") ";

            //return "call nutacloud.kartustok2(" . $ItemID . ",'" . $this->dateStart . "','" . $this->dateEnd . "','" .
            //$this->Outlet . "', '" . $this->clientPerusahaanID . "') ";
        }
    }

    public function get_query_kartu_stok2($ItemID, $minDate) {
        $realitemid = explode(".", $ItemID)[0];
        $devno = explode(".", $ItemID)[1];
        $isPerusahaan = $this->clientPerusahaanID != $this->clientDeviceID;
        if (!$isPerusahaan) {
            return "call nutacloud.kartuStokV2(" . $realitemid . "," . $devno . "," . $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . "," .
                    $this->CI->db->escape($this->clientDeviceID) . ", '" . $minDate . "') ";
        } else {
            return "call nutacloud.kartuStokV2(" . $realitemid . "," . $devno . "," . $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . "," .
                    $this->CI->db->escape($this->Outlet) . ", '" . $minDate . "') ";
        }
    }

    public function get_query_mutasi_kas($CashbankaccountID, $minDate) {
        $realitemid = explode(".", $CashbankaccountID)[0];
        $devno = explode(".", $CashbankaccountID)[1];
        $isPerusahaan = $this->clientPerusahaanID != $this->clientDeviceID;
        if (!$isPerusahaan) {
            return "call nutacloud.mutasikas(" . $realitemid . "," . $devno . "," . $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . "," .
                    $this->CI->db->escape($this->clientDeviceID) . ",'" . $minDate . "') ";
        } else {
            return "call nutacloud.mutasikas(" . $realitemid . "," . $devno . "," . $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . "," .
                    $this->CI->db->escape($this->Outlet) . ",'" . $minDate . "') ";
            //return "call nutacloud.kartustok2(" . $ItemID . ",'" . $this->dateStart . "','" . $this->dateEnd . "','" .
            //$this->Outlet . "', '" . $this->clientPerusahaanID . "') ";
        }
    }

    public function get_query_rekap_mutasi_stok() {
        $isPerusahaan = $this->clientPerusahaanID != $this->clientDeviceID;
        if (!$isPerusahaan) {
            return "call nutacloud.rekapmutasistok1 (" . $this->CI->db->escape($this->clientDeviceID) . "," .
                    $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . ") ";
        } else {
            return "call nutacloud.rekapmutasistok1 (" . $this->CI->db->escape($this->Outlet) . "," .
                    $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . ") ";
            //return "call nutacloud.rekapmutasistok2 ('" . $this->Outlet . "','" .
            //$this->clientPerusahaanID . "','" . $this->dateStart . "','" . $this->dateEnd . "') ";
        }
    }

    public function get_query_rekap_mutasi_stok2($minDate) {
        $isPerusahaan = $this->clientPerusahaanID != $this->clientDeviceID;
        if (!$isPerusahaan) {
            return "call nutacloud.rekapmutasistok1V2 (" . $this->CI->db->escape($this->clientDeviceID) . "," .
                    $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . ", '" . $minDate . "') ";
        } else {
            return "call nutacloud.rekapmutasistok1V2 (" . $this->CI->db->escape($this->Outlet) . "," .
                    $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->dateEnd) . " , '" . $minDate . "') ";
        }
    }

    public function get_query_delete_data() {
        $sql = "

DELETE FROM cashbankin WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM cloud_cashbankin WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM cashbankindelete WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM cashbankout WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM cloud_cashbankout WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM cashbankoutdelete WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM checkpoint WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND InsertedDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM checkpoint_uang WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND InsertedDate <=" . $this->CI->db->escape($this->dateEnd) . ";

DELETE FROM purchase WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND PurchaseDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM purchasedelete WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND PurchaseDate <=" . $this->CI->db->escape($this->dateEnd) . ";

DELETE d.* FROM purchaseitemdetail d LEFT JOIN purchase p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM purchaseitemdetaildelete d LEFT JOIN purchasedelete p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;

DELETE FROM " . $this->tabelsale . " WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND SaleDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM saledelete WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND SaleDate <=" . $this->CI->db->escape($this->dateEnd) . ";

DELETE d.* FROM " . $this->tabelsaleitemdetail . " d LEFT JOIN " . $this->tabelsale . " p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM saleitemdetaildelete d LEFT JOIN saledelete p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM " . $this->tabelsaleitemdetailingredients . " d LEFT JOIN " . $this->tabelsale . " p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM saleitemdetailingredientsdelete d LEFT JOIN saledelete p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM saleitemdetailmodifier d LEFT JOIN " . $this->tabelsale . " p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM saleitemdetailmodifierdelete d LEFT JOIN saledelete p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM saleitemdetailtax d LEFT JOIN " . $this->tabelsale . " p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM saleitemdetailtaxdelete d LEFT JOIN saledelete p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM salediningtabledetail d LEFT JOIN " . $this->tabelsale . " p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM salediningtabledetaildelete d LEFT JOIN saledelete p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM salediscountdetail d LEFT JOIN " . $this->tabelsale . " p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM salediscountdetaildelete d LEFT JOIN saledelete p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;

DELETE FROM stockopname WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND StockOpnameDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE FROM stockopnamedelete WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND StockOpnameDate <=" . $this->CI->db->escape($this->dateEnd) . ";
DELETE d.* FROM stockopnamedetail d LEFT JOIN stockopname p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE d.* FROM stockopnamedetaildelete d LEFT JOIN stockopnamedelete p ON p.PerusahaanNo=d.PerusahaanNo AND p.TransactionID=d.TransactionID AND p.DeviceID=d.DeviceID WHERE d.PerusahaanNo= " . $this->NoPerusahaan . " AND d.DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND p.TransactionID IS NULL;
DELETE FROM customerfeedback WHERE OutletID=" . $this->CI->db->escape($this->getOutlet()) . " AND TglFeedback <=" . $this->CI->db->escape($this->dateEnd . ' 23:59:59') . ";
    ";
        //echo $sql;
        return $sql;
    }

    public function get_query_konfirmasi_delete_data() {

        return "
SELECT 'Penjualan' tablename,COUNT(*) jumlah FROM " . $this->tabelsale . " WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND SaleDate <=" . $this->CI->db->escape($this->dateEnd) . "
UNION ALL
SELECT 'Pembelian' tablename,COUNT(*) jumlah FROM purchase WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND PurchaseDate <=" . $this->CI->db->escape($this->dateEnd) . "
UNION ALL
SELECT 'Koreksi Stok' tablename,COUNT(*) jumlah FROM stockopname WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND StockOpnameDate <=" . $this->CI->db->escape($this->dateEnd) . "
UNION ALL
SELECT 'Uang Masuk' tablename,SUM(CNT) jumlah FROM 
(
    SELECT COUNT(*) cnt FROM cashbankin WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . "
    UNION ALL
    SELECT COUNT(*) cnt FROM cloud_cashbankin WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . "
) X
UNION ALL
SELECT 'Uang Keluar' tablename,SUM(cnt) jumlah FROM 
(
    SELECT COUNT(*) cnt FROM cashbankout WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . "
    UNION ALL
    SELECT COUNT(*) cnt FROM cloud_cashbankout WHERE PerusahaanNo= " . $this->NoPerusahaan . " AND DeviceID=" . $this->CI->db->escape($this->getOutlet()) . " AND TransactionDate <=" . $this->CI->db->escape($this->dateEnd) . "
) X
UNION ALL
SELECT 'Feedback Pelanggan' tablename,COUNT(*) jumlah FROM customerfeedback WHERE OutletID=" . $this->CI->db->escape($this->getOutlet()) . " AND TglFeedback <=" . $this->CI->db->escape($this->dateEnd . ' 23:59:59') . ";

";
    }

    private function getQFirstStock() {

        $query = "SELECT ItemID,'First Stock' TransactionName,\n
ItemID TransactionNumber,\n
ItemID TransactionDetailID, '1900-01-01' TransactionDate,\n
'00:00' TransactionTime,\n
'' Keterangan
, BeginningStock, BeginningCOGS AS UnitPrice \n

FROM masteritem WHERE Stock = 'true'";

        return $query;
    }

    public function get_query_kasir() {
        $query = "SELECT ItemID,'First Stock' TransactionName,\n
ItemID TransactionNumber,\n
ItemID TransactionDetailID, '1900-01-01' TransactionDate,\n
'00:00' TransactionTime,\n
'' Keterangan
, BeginningStock, BeginningCOGS AS UnitPrice \n

FROM masteritem WHERE Stock = 'true'";

        return $query;
    }

    public function get_query_feedback() {
        $query = "
SELECT
    a.TglFeedback,
    a.Email,
    a.Description,
    s.CreatedBy,
    s.CreatedBy Kasir,
    CONCAT(
   case when a.IsWaktuChecked = 1 then ' Waktu Menunggu,' else '' end,
   case when a.IsKualitasChecked= 1 then ' Kualitas,' else '' end,
   case when a.IsCustomerServiceChecked = 1 then ' Customer Service,' else '' end,
   case when a.IsLainnyaChecked = 1 then ' Lainnya,' else '' end
   ) as 'Subject',
    a.Response
FROM
    nutacloud.customerfeedback a
        INNER JOIN
    " . $this->tabelsale . " s ON a.SaleTransactionID = s.TransactionID
        AND a.OutletID = s.DeviceID
WHERE s.PerusahaanNo = " . $this->NoPerusahaan . " AND a.TglFeedback >= " . $this->CI->db->escape($this->dateStart) . "
                AND a.TglFeedback <= " . $this->CI->db->escape($this->dateEnd . " 23:59:59") . "
        ";
        if ($this->Outlet != "Semua") {
            $query .= "AND s.DeviceID = " . $this->CI->db->escape($this->Outlet);
        }
        $query .= " ORDER BY a.TglFeedback DESC";
//echo $query;
        return $query;
    }

    public function get_query_rekap_feedback() {
        $query = "
SELECT
    'Waktu Menunggu' AS 'Subject',
    'Good' AS 'Response',
COUNT(*) Total
FROM
    nutacloud.customerfeedback a WHERE a.IsWaktuChecked =1
    AND a.Response = 'good' AND  a.TglFeedback >= " . $this->CI->db->escape($this->dateStart) . "
                AND a.TglFeedback <= " . $this->CI->db->escape($this->dateEnd . " 23:59:59");
        if ($this->Outlet != "Semua") {
            $query .= "AND OutletID = " . $this->CI->db->escape($this->Outlet);
        }
        $query .= "
UNION ALL
  SELECT
    'Waktu Menunggu' AS 'Subject',
    'Bad' AS 'Response',
COUNT(*) Total
FROM
    nutacloud.customerfeedback a WHERE a.IsWaktuChecked =1
    AND a.Response = 'bad'
  AND a.TglFeedback >= " . $this->CI->db->escape($this->dateStart) . "
                AND a.TglFeedback <= " . $this->CI->db->escape($this->dateEnd . " 23:59:59") . "
    ";
        if ($this->Outlet != "Semua") {
            $query .= "AND OutletID = " . $this->CI->db->escape($this->Outlet);
        }
        $query .= "
UNION ALL
SELECT
    'Kualitas' AS 'Subject',
    'Good' AS 'Response',
COUNT(*) Total
FROM
    nutacloud.customerfeedback a WHERE a.IsKualitasChecked = 1
    AND a.Response = 'good' AND a.TglFeedback >= " . $this->CI->db->escape($this->dateStart) . "
                AND a.TglFeedback <= " . $this->CI->db->escape($this->dateEnd . " 23:59:59");
        if ($this->Outlet != "Semua") {
            $query .= "AND OutletID = " . $this->CI->db->escape($this->Outlet);
        }
        $query .= "UNION ALL
  SELECT
    'Kualitas' AS 'Subject',
    'Bad' AS 'Response',
COUNT(*) Total
FROM
    nutacloud.customerfeedback a WHERE a.IsKualitasChecked = 1
AND a.Response = 'bad' AND  a.TglFeedback >= " . $this->CI->db->escape($this->dateStart) . "
                AND a.TglFeedback <= " . $this->CI->db->escape($this->dateEnd . " 23:59:59") . "
    ";
        if ($this->Outlet != "Semua") {
            $query .= "AND OutletID = " . $this->CI->db->escape($this->Outlet);
        }
        $query .= "
UNION ALL
SELECT
    'Customer Service' AS 'Subject',
    'Good' AS 'Response',
COUNT(*) Total
FROM
    nutacloud.customerfeedback a WHERE a.IsCustomerServiceChecked=1
    AND a.Response = 'good' AND  a.TglFeedback >= " . $this->CI->db->escape($this->dateStart) . "
                AND a.TglFeedback <= " . $this->CI->db->escape($this->dateEnd . " 23:59:59");
        if ($this->Outlet != "Semua") {
            $query .= "AND OutletID = " . $this->CI->db->escape($this->Outlet);
        }
        $query .= " UNION ALL
  SELECT
    'Customer Service' AS 'Subject',
    'Bad' AS 'Response',
COUNT(*) Total
FROM
    nutacloud.customerfeedback a WHERE a.IsCustomerServiceChecked=1
AND a.Response = 'bad' AND  a.TglFeedback >= " . $this->CI->db->escape($this->dateStart) . "
                AND a.TglFeedback <= " . $this->CI->db->escape($this->dateEnd . " 23:59:59") . "
    ";
        if ($this->Outlet != "Semua") {
            $query .= "AND OutletID = " . $this->CI->db->escape($this->Outlet);
        }
        $query .= "
UNION ALL
SELECT
    'Lainnya' AS 'Subject',
    'Good' AS 'Response',
COUNT(*) Total
FROM
    nutacloud.customerfeedback a WHERE a.IsLainnyaChecked =1
    AND a.Response = 'good' AND a.TglFeedback >= " . $this->CI->db->escape($this->dateStart) . "
                AND a.TglFeedback <= " . $this->CI->db->escape($this->dateEnd . " 23:59:59");
        if ($this->Outlet != "Semua") {
            $query .= "AND OutletID = " . $this->CI->db->escape($this->Outlet);
        }
        $query .= "UNION ALL
  SELECT
    'Lainnya' AS 'Subject',
    'Bad' AS 'Response',
COUNT(*) Total
FROM
    nutacloud.customerfeedback a WHERE a.IsLainnyaChecked =1
AND a.Response = 'bad' AND  a.TglFeedback >= " . $this->CI->db->escape($this->dateStart) . "
                AND a.TglFeedback <= " . $this->CI->db->escape($this->dateEnd . " 23:59:59") . "
    ";
        if ($this->Outlet != "Semua") {
            $query .= "AND OutletID = " . $this->CI->db->escape($this->Outlet);
        }
        return $query;
    }

    public function get_query_charge_edc() {
        $query = "

SELECT SaleNumber as 'Nomor', SaleDate as 'Tanggal',CustomerName as 'Pelanggan',
Persen as 'Charge',ChargeValue as 'ChargeRp' FROM (
SELECT *,ChargeValue*100/Total Persen
FROM(
    SELECT PaymentMode, AutoClearing,s.SaleNumber,s.SaleDate,s.SaleTime,COALESCE(ms.CustomerName,s.CustomerName) CustomerName,
    CASE WHEN s.PaymentMode=2 THEN s.Total+s.Rounding ELSE s.Total-s.CashPaymentAmount END Total,
    s.TransactionID,CASE WHEN s.PaymentMode=2 THEN s.BankPaymentAmount-s.Total-s.Rounding ELSE s.BankPaymentAmount-s.Total-s.Rounding-s.CashPaymentAmount END ChargeValue
    FROM " . $this->tabelsale . " s
    LEFT JOIN mastercustomer ms ON ms.CustomerID=s.CustomerID AND s.DeviceID = ms.DeviceID AND s.PerusahaanNo=ms.PerusahaanNo
    WHERE s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
        if ($this->Outlet != "Semua") {
            $query .= "  AND s.DeviceID= " . $this->CI->db->escape($this->Outlet);
        }

        $query .= "AND (((s.PaymentMode=2 AND s.BankPaymentAmount-s.Total-s.Rounding > 0) 
            OR (s.PaymentMode=3 AND s.BankPaymentAmount-s.Total-s.Rounding-s.CashPaymentAmount > 0))
     AND s.AutoClearing='true'
    )
) X ORDER BY SaleDate, SaleTime, SaleNumber ) Q";

        return $query;
    }

    public function get_query_daftar_master_item($perusahaanNo) {
        return "
    SELECT
        ItemID,
        OnlineImagePath AS Foto,
        ItemName AS Nama,
        COALESCE(c.CategoryName, '') AS Kategori,
        CASE WHEN IsProduct = 'true' THEN SellPrice ELSE 0 END AS HargaJual,
        CASE WHEN IsProduct = 'true' THEN 'Produk' ELSE 'Bahan' END AS ProdukAtauBahan,
        CASE
            WHEN IsProduct <> 'true' THEN '-'
            WHEN IsProducthasIngredients = 'true' THEN 'Ada'
            ELSE 'Tidak Ada'
        END AS AdaBahan
    FROM
        masteritem i
            LEFT JOIN
        mastercategory c ON c.CategoryID = i.CategoryID
        AND c.PerusahaanNo = i.PerusahaanNo
            AND c.DeviceID = i.DeviceID AND c.DeviceNo=i.CategoryDeviceNo

    WHERE
    i.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND i.PerusahaanNo =" . $this->CI->db->escape($perusahaanNo) . " ORDER BY i.ItemName ASC";
    }

    public function get_query_daftar_produk($perusahaanNo) {
        return "
    SELECT
        i.ItemID,i.DeviceNo,
        OnlineImagePath AS Foto,
        ItemName AS Nama,
        COALESCE(c.CategoryName, '') AS Kategori,
        Unit AS Satuan,
        CASE WHEN IsProduct = 'true' THEN SellPrice ELSE 0 END AS HargaJual,
        CASE
            WHEN IsProduct <> 'true' THEN '-'
            WHEN IsProducthasIngredients = 'true' THEN Concat('Ada: ', hpp.Bahan)
            ELSE 'Tidak Ada'
        END AS AdaBahan,
        COALESCE(hpp.PurchasePrice,i.PurchasePrice) AS HargaBeli
    FROM
        masteritem i
            LEFT JOIN
        mastercategory c ON c.CategoryID = i.CategoryID
        AND c.PerusahaanNo = i.PerusahaanNo
            AND c.DeviceID = i.DeviceID AND c.DeviceNo=i.CategoryDeviceNo
    LEFT JOIN
        (
            SELECT b.ItemID,b.ItemDeviceNo, SUM(b.QtyNeed*i2.PurchasePrice) PurchasePrice,
            GROUP_CONCAT(CONCAT(b.QtyNeed,' ',i2.Unit,' ',i2.ItemName)) Bahan
            FROM masteritemdetailingredients b INNER JOIN masteritem i2 
            ON b.PerusahaanNo=i2.PerusahaanNo AND b.DeviceID=i2.DeviceID
            AND b.IngredientsID=i2.ItemID AND b.IngredientsDeviceNo=i2.DeviceNo
            WHERE i2.DeviceID = " . $this->Outlet . " AND i2.PerusahaanNo =" . $perusahaanNo . "
            GROUP BY b.ItemID,b.ItemDeviceNo
        ) hpp
        ON i.ItemID = hpp.ItemID AND i.DeviceNo = hpp.ItemDeviceNo
        AND i.IsProductHasIngredients='true'
    WHERE
    IsProduct = 'true' AND
    i.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND i.PerusahaanNo =" . $this->CI->db->escape($perusahaanNo) . " ORDER BY i.ItemName ASC";
    }

    public function get_modifier($perusahaanNo) {
        $string = "select mastermodifier.ModifierID,
                  mastermodifier.DeviceNo,
                  mastermodifier.DeviceID,
                  mastermodifier.ModifierName `Nama Pilihan Extra`,
                  CASE
                    WHEN mastermodifier.ChooseOneOnly = 0 THEN \"Tidak\"
                    ELSE \"Ya\"
                    END 
                  `Pelanggan Hanya Bisa Pilih Satu Extra`,
                  CASE
                    WHEN mastermodifier.CanAddQuantity = 0 THEN \"Tidak\"
                    ELSE \"Ya\"
                    END 
                  `Pelanggan Bisa Menambah Jumlah Per Pilihan`,
                  'Pilihan' `Pilihan` 
                    from mastermodifier
                    where PerusahaanNo=" . $perusahaanNo . " AND DeviceID = " . $this->CI->db->escape($this->Outlet);
        return $string;
    }

    public function get_query_daftar_bahan($perusahaanNo) {
        return "
    SELECT
        ItemID,i.DeviceNo,
        OnlineImagePath AS Foto,
        ItemName AS Nama,
        COALESCE(c.CategoryName, '') AS Kategori,
        Unit AS Satuan,
        CASE WHEN IsProduct = 'false' THEN PurchasePrice ELSE 0 END AS HargaBeli
    FROM
        masteritem i
            LEFT JOIN
        mastercategory c ON c.CategoryID = i.CategoryID
        AND c.PerusahaanNo = i.PerusahaanNo
            AND c.DeviceID = i.DeviceID AND c.DeviceNo=i.CategoryDeviceNo

    WHERE
    IsProduct = 'false' AND
    i.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND i.PerusahaanNo =" . $this->CI->db->escape($perusahaanNo) . " ORDER BY i.ItemName ASC";
    }

    public function get_query_daftar_kategori($perusahaanNo) {
        return "
    SELECT
        CategoryName AS Kategori,
        IPPrinter AS OpsiCetak
    FROM
        mastercategory i
    WHERE
    i.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND i.PerusahaanNo =" . $this->CI->db->escape($perusahaanNo) . " ORDER BY i.CategoryName ASC";
    }

    public function get_query_bahan_item($itemid) {
        $realitemid = explode(".", $itemid)[0];
        $devno = explode(".", $itemid)[1];
        return "SELECT x.DetailID, x.DeviceNo, m.ItemName, x.IngredientsID, x.IngredientsDeviceNo, 
x.QtyNeed, m.PurchasePrice, m.SatuanID, m.Unit Satuan
FROM
(
    SELECT md.DetailID, md.DeviceNo,md.IngredientsID,md.IngredientsDeviceNo, md.QtyNeed, md.DeviceID, mi.PurchasePrice
    FROM masteritem mi
    INNER JOIN masteritemdetailingredients md ON md.ItemID = mi.ItemID
    AND md.DeviceID = mi.DeviceID AND mi.DeviceNo=md.ItemDeviceNo
    WHERE md.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
    AND md.ItemID = " . $this->CI->db->escape($realitemid) . "
    AND md.ItemDeviceNo = " . $this->CI->db->escape($devno) . "
) x
INNER JOIN masteritem m ON x.IngredientsID = m.ItemID AND x.IngredientsDeviceNo=m.DeviceNo 
AND x.DeviceID = m.DeviceID
WHERE m.DeviceID = " . $this->CI->db->escape($this->Outlet);
    }

    public function get_query_rincian_laba() {
        $this->CheckDeviceID();
        //$whereOutlet = " WHERE h.Outlet = " . $this->CI->db->escape($this->Outlet);
        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);

        $queryString = "\tSELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, \n" .
                "\tCOALESCE(mc.CategoryName,'Lainnya') CategoryName,sd.ItemName ItemName, sd.DetailNumber, sd.Quantity Quantity, sd.UnitPrice ,sd.Discount, (sd.SubTotal) SubTotal, sd.COGS + sd.COGSModifier AS COGS,
CASE WHEN d2.TransactionID IS NULL AND d3.TransactionID IS NULL OR sd.COGS + sd.COGSModifier = 0 THEN '' ELSE CONCAT(sd.DetailID,'.',sd.DeviceNo) END AS `RincianHPP`\n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo
LEFT JOIN " . $this->tabelsaleitemdetailingredients . " d2 ON sd.DetailID=d2.DetailID AND sd.PerusahaanNo=d2.PerusahaanNo AND s.TransactionID=d2.TransactionID
AND sd.DeviceID=d2.DeviceID AND sd.DetailID=d2.DetailID AND d2.DetailNumber=1 AND s.DeviceNo=d2.TransactionDeviceNo AND sd.DeviceNo=d2.DetailDeviceNo
LEFT JOIN saleitemdetailmodifier d3 ON sd.DetailID=d3.DetailID AND sd.PerusahaanNo=d3.PerusahaanNo AND s.TransactionID=d3.TransactionID
AND sd.DeviceID=d3.DeviceID AND sd.DetailID=d3.DetailID AND d3.DetailNumber=1 AND s.DeviceNo=d3.TransactionDeviceNo AND sd.DeviceNo=d3.DetailDeviceNo
LEFT JOIN mastercategory mc ON mi.PerusahaanNo=mc.PerusahaanNo AND mi.DeviceID=mc.DeviceID AND mi.CategoryID=mc.CategoryID AND mi.CategoryDeviceNo=mc.DeviceNo
 \n" .
                "\tWHERE s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) .
                " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " AND o.DeviceID= " . $this->CI->db->escape($this->Outlet) . " \n

UNION ALL
SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.PurchaseNumber SaleNumber, s.PurchaseDate,s.PurchaseTime,'-' CustomerName,
sd.ItemName ItemName, sd.DetailNumber, sd.Quantity, 0 UnitPrice, '' Discount, 0 SubTotal, sd.SubTotal AS COGS,
'' AS RincianHPP
            FROM purchase s INNER JOIN purchaseitemdetail sd
            ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo
            AND s.DeviceID = sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo
            LEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo
            LEFT JOIN masteritemdetailingredients md
            ON md.IngredientsID=sd.ItemID AND sd.PerusahaanNo=md.PerusahaanNo AND md.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md.DeviceID = sd.DeviceID
            LEFT JOIN mastermodifierdetail md2
            ON md2.IngredientsID=sd.ItemID AND md2.PerusahaanNo=sd.PerusahaanNo AND md2.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md2.DeviceID = sd.DeviceID
            INNER JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            WHERE s.PerusahaanNo = " . $this->NoPerusahaan . " AND md.IngredientsID IS NULL AND md2.IngredientsID IS NULL AND (mi.IsProduct='false' OR (mi.IsProduct IS NULL AND sd.IsProduct=0))
AND sd.Quantity<>0 AND s.PurchaseDate>=" . $this->CI->db->escape($this->dateStart) .
                " AND s.PurchaseDate<= " . $this->CI->db->escape($this->dateEnd) .
                " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n

";

        $queryString = "\tSELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
        CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
        ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, \n" .
                "\tCOALESCE(mc.CategoryName,'Lainnya') CategoryName,sd.ItemName ItemName, sd.DetailNumber, sd.Quantity Quantity, 
            sd.UnitPrice ,sd.Discount, sd.SubTotal SubTotal, sd.COGS + sd.COGSModifier AS COGS,
CASE WHEN NOT EXISTS 
(
    SELECT d2.TransactionID FROM saleitemdetailingredients d2 WHERE d2.PerusahaanNo=s.PerusahaanNo 
    AND d2.DeviceID=s.DeviceID AND d2.TransactionID=s.TransactionID AND s.DeviceNo=d2.TransactionDeviceNo
    AND d2.DetailID=sd.DetailID AND sd.DeviceNo=d2.DetailDeviceNo
    UNION ALL
    SELECT d2.TransactionID FROM saleitemdetailmodifier d2 WHERE d2.PerusahaanNo=s.PerusahaanNo 
    AND d2.DeviceID=s.DeviceID AND d2.TransactionID=s.TransactionID AND s.DeviceNo=d2.TransactionDeviceNo
    AND d2.DetailID=sd.DetailID AND sd.DeviceNo=d2.DetailDeviceNo
)
OR sd.COGS + sd.COGSModifier = 0 THEN '' ELSE CONCAT(sd.DetailID,'.',sd.DeviceNo) END AS `RincianHPP`
FROM sale s INNER JOIN saleitemdetail sd
ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo
INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
LEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo
LEFT JOIN mastercategory mc ON mi.PerusahaanNo=mc.PerusahaanNo AND mi.DeviceID=mc.DeviceID AND mi.CategoryID=mc.CategoryID AND mi.CategoryDeviceNo=mc.DeviceNo
WHERE s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) .
                " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " AND o.DeviceID= " . $this->CI->db->escape($this->Outlet) . " \n

UNION ALL
SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.PurchaseNumber SaleNumber, s.PurchaseDate, s.PurchaseTime,'-' CustomerName,
COALESCE(mc.CategoryName,'Lainnya') CategoryName,sd.ItemName ItemName, sd.DetailNumber, sd.Quantity, 0 UnitPrice, '' Discount, 0 SubTotal, sd.SubTotal AS COGS,
'' AS RincianHPP
            FROM purchase s INNER JOIN purchaseitemdetail sd
            ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo
            AND s.DeviceID = sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo
            LEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo
    LEFT JOIN mastercategory mc ON mi.PerusahaanNo=mc.PerusahaanNo AND mi.DeviceID=mc.DeviceID AND mi.CategoryID=mc.CategoryID AND mi.CategoryDeviceNo=mc.DeviceNo
            LEFT JOIN masteritemdetailingredients md
            ON md.IngredientsID=sd.ItemID AND sd.PerusahaanNo=md.PerusahaanNo AND md.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md.DeviceID = sd.DeviceID
            LEFT JOIN mastermodifierdetail md2
            ON md2.IngredientsID=sd.ItemID AND md2.PerusahaanNo=sd.PerusahaanNo AND md.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md2.DeviceID = sd.DeviceID

            INNER JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            WHERE s.PerusahaanNo = " . $this->NoPerusahaan . " AND md.IngredientsID IS NULL AND md2.IngredientsID IS NULL AND (mi.IsProduct='false' OR (mi.IsProduct IS NULL AND sd.IsProduct=0))
AND sd.Quantity<>0 AND s.PurchaseDate>=" . $this->CI->db->escape($this->dateStart) .
                " AND s.PurchaseDate<= " . $this->CI->db->escape($this->dateEnd) .
                " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n

";
// echo "SELECT h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
//        . "h.UnitPrice as HargaSatuan,h.SubTotal, h.COGS Hpp, h.SubTotal - h.COGS AS LabaKotor, RincianHpp FROM (" . $queryString . ") h
//                  ORDER BY h.SaleDateTime, h.SaleNumber, h.ItemName, h.Quantity DESC";
        return "SELECT h.SaleNumber as Nomor,h.SaleDate Tanggal, h.SaleTime as Jam,h.CustomerName as Pelanggan, h.CategoryName Kategori,h.ItemName as Item,h.Quantity as Qty,"
                . "h.UnitPrice as HargaSatuan,h.SubTotal, h.COGS Hpp, h.SubTotal - h.COGS AS LabaKotor, RincianHpp FROM (" . $queryString . ") h
                    ORDER BY h.SaleDate, h.SaleTime, h.SaleNumber, h.ItemName, h.DetailNumber, h.Quantity DESC"; //WHERE h.Outlet='" . $this->Outlet . "'
    }

    public function get_query_rincian_laba_per_shift($openID, $openDevNo) {
        $this->CheckDeviceID();
        //$whereOutlet = " WHERE h.Outlet = " . $this->CI->db->escape($this->Outlet);
        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);

        $queryString = "\tSELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, \n" .
                "\tCOALESCE(mc.CategoryName,'Lainnya') CategoryName,sd.ItemName ItemName, sd.DetailNumber, sd.Quantity Quantity, sd.UnitPrice ,sd.Discount, (sd.SubTotal) SubTotal, sd.COGS + sd.COGSModifier AS COGS,
CASE WHEN d2.TransactionID IS NULL AND d3.TransactionID IS NULL OR sd.COGS + sd.COGSModifier = 0 THEN '' ELSE CONCAT(sd.DetailID,'.',sd.DeviceNo) END AS `RincianHPP`\n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo
LEFT JOIN " . $this->tabelsaleitemdetailingredients . " d2 ON sd.DetailID=d2.DetailID AND sd.PerusahaanNo=d2.PerusahaanNo AND s.TransactionID=d2.TransactionID
AND sd.DeviceID=d2.DeviceID AND sd.DetailID=d2.DetailID AND d2.DetailNumber=1 AND s.DeviceNo=d2.TransactionDeviceNo AND sd.DeviceNo=d2.DetailDeviceNo
LEFT JOIN saleitemdetailmodifier d3 ON sd.DetailID=d3.DetailID AND sd.PerusahaanNo=d3.PerusahaanNo AND s.TransactionID=d3.TransactionID
AND sd.DeviceID=d3.DeviceID AND sd.DetailID=d3.DetailID AND d3.DetailNumber=1 AND s.DeviceNo=d3.TransactionDeviceNo AND sd.DeviceNo=d3.DetailDeviceNo
LEFT JOIN mastercategory mc ON mi.PerusahaanNo=mc.PerusahaanNo AND mi.DeviceID=mc.DeviceID AND mi.CategoryID=mc.CategoryID AND mi.CategoryDeviceNo=mc.DeviceNo
 \n" .
                "\tWHERE s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.openID = " . $openID .
                " AND s.OpenDeviceNo=" . $openDevNo . " AND o.DeviceID= " . $this->CI->db->escape($this->Outlet) . " \n

UNION ALL
SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.PurchaseNumber SaleNumber, s.PurchaseDate,s.PurchaseTime,'-' CustomerName,
sd.ItemName ItemName, sd.DetailNumber, sd.Quantity, 0 UnitPrice, '' Discount, 0 SubTotal, sd.SubTotal AS COGS,
'' AS RincianHPP
            FROM purchase s INNER JOIN purchaseitemdetail sd
            ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo
            AND s.DeviceID = sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo
            LEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo
            LEFT JOIN masteritemdetailingredients md
            ON md.IngredientsID=sd.ItemID AND sd.PerusahaanNo=md.PerusahaanNo AND md.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md.DeviceID = sd.DeviceID
            LEFT JOIN mastermodifierdetail md2
            ON md2.IngredientsID=sd.ItemID AND md2.PerusahaanNo=sd.PerusahaanNo AND md2.IngredientsDeviceNo=sd.ItemDeviceNo
            AND md2.DeviceID = sd.DeviceID
            INNER JOIN options o
            ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            WHERE s.PerusahaanNo = " . $this->NoPerusahaan . " AND md.IngredientsID IS NULL AND md2.IngredientsID IS NULL AND (mi.IsProduct='false' OR (mi.IsProduct IS NULL AND sd.IsProduct=0))
AND sd.Quantity<>0 AND s.PurchaseDate>=" . $this->CI->db->escape($this->dateStart) .
                " AND s.PurchaseDate<= " . $this->CI->db->escape($this->dateEnd) .
                " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n

";

        $queryString = "\tSELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
        CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
        ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, \n" .
                "\tCOALESCE(mc.CategoryName,'Lainnya') CategoryName,sd.ItemName ItemName, sd.DetailNumber, sd.Quantity Quantity, 
            sd.UnitPrice ,sd.Discount, sd.SubTotal SubTotal, sd.COGS + sd.COGSModifier AS COGS,
CASE WHEN NOT EXISTS 
(
    SELECT d2.TransactionID FROM saleitemdetailingredients d2 WHERE d2.PerusahaanNo=s.PerusahaanNo 
    AND d2.DeviceID=s.DeviceID AND d2.TransactionID=s.TransactionID AND s.DeviceNo=d2.TransactionDeviceNo
    AND d2.DetailID=sd.DetailID AND sd.DeviceNo=d2.DetailDeviceNo
    UNION ALL
    SELECT d2.TransactionID FROM saleitemdetailmodifier d2 WHERE d2.PerusahaanNo=s.PerusahaanNo 
    AND d2.DeviceID=s.DeviceID AND d2.TransactionID=s.TransactionID AND s.DeviceNo=d2.TransactionDeviceNo
    AND d2.DetailID=sd.DetailID AND sd.DeviceNo=d2.DetailDeviceNo
)
OR sd.COGS + sd.COGSModifier = 0 THEN '' ELSE CONCAT(sd.DetailID,'.',sd.DeviceNo) END AS `RincianHPP`
FROM sale s INNER JOIN saleitemdetail sd
ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo
INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
LEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo
LEFT JOIN mastercategory mc ON mi.PerusahaanNo=mc.PerusahaanNo AND mi.DeviceID=mc.DeviceID AND mi.CategoryID=mc.CategoryID AND mi.CategoryDeviceNo=mc.DeviceNo
WHERE s.PerusahaanNo = " . $this->NoPerusahaan .
                " AND s.openID = " . $openID . " AND s.OpenDeviceNo = " . $openDevNo . " AND o.DeviceID= " . $this->CI->db->escape($this->Outlet) . " \n
";
// echo "SELECT h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
//        . "h.UnitPrice as HargaSatuan,h.SubTotal, h.COGS Hpp, h.SubTotal - h.COGS AS LabaKotor, RincianHpp FROM (" . $queryString . ") h
//                  ORDER BY h.SaleDateTime, h.SaleNumber, h.ItemName, h.Quantity DESC";
        return "SELECT h.SaleNumber as Nomor,h.SaleDate Tanggal, h.SaleTime as Jam,h.CustomerName as Pelanggan, h.CategoryName Kategori,h.ItemName as Item,h.Quantity as Qty,"
                . "h.UnitPrice as HargaSatuan,h.SubTotal, h.COGS Hpp, h.SubTotal - h.COGS AS LabaKotor, RincianHpp FROM (" . $queryString . ") h
                    ORDER BY h.SaleDate, h.SaleTime, h.SaleNumber, h.ItemName, h.DetailNumber, h.Quantity DESC"; //WHERE h.Outlet='" . $this->Outlet . "'
    }

    public function get_query_rincian_hpp($deviceid, $detailid, $noperusahaan, $devno = 1) {

        return "
SELECT COALESCE(mi.ItemName,'-') ItemName, (QtyUsed-QtyUsedCancel) AS Qty,
COALESCE(mi.Unit,'PCS') Unit, IngredientsPrice AS HargaPokok,
(QtyUsed-QtyUsedCancel)*IngredientsPrice AS Jumlah FROM " . $this->tabelsaleitemdetailingredients . " d
LEFT JOIN masteritem mi ON mi.ItemID=d.IngredientsID AND mi.PerusahaanNo=d.PerusahaanNo
AND mi.DeviceID=d.DeviceID AND mi.DeviceNo=d.IngredientsDeviceNo
WHERE d.PerusahaanNo = " . $noperusahaan . " AND d.DeviceID=" . $this->CI->db->escape($deviceid) .
                " AND d.DetailID=" . $this->CI->db->escape($detailid) . " AND d.DetailDeviceNo=" . $devno . "
UNION ALL
SELECT COALESCE(mi.ItemName,'-') ItemName, (QtyUsed-QtyUsedCancel) AS Qty,
COALESCE(mi.Unit,'PCS') Unit, IngredientsPrice AS HargaPokok, 
(QtyUsed-QtyUsedCancel+QtyUsedCanceler)*IngredientsPrice AS Jumlah FROM saleitemdetailmodifier d 
LEFT JOIN masteritem mi ON mi.ItemID=d.IngredientsID AND mi.PerusahaanNo=d.PerusahaanNo
AND mi.DeviceID=d.DeviceID
WHERE d.PerusahaanNo = " . $noperusahaan . " AND d.DeviceID=" . $this->CI->db->escape($deviceid) .
                " AND d.DetailID=" . $this->CI->db->escape($detailid) . " AND d.DetailDeviceNo=" . $devno;
    }

    function get_query_pengeluaran() {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " s.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->DeviceID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }

        $queryStringItemAndVarian = "SELECT Nomor, Tanggal, Jumlah, AmbilDari, DibayarKe, Keterangan FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, 
                TransactionNumber Nomor, CONCAT(s.TransactionDate ,', ', s.TransactionTime) Tanggal, s.Amount AS Jumlah,
                CASE WHEN s.AccountID=1 THEN COALESCE(AccountName, s.CashBankAccountName) 
                ELSE COALESCE(CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName),s.CashBankAccountName) END AS AmbilDari, 
                PaidTo DibayarKe, Note Keterangan
                FROM cashbankout s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN mastercashbankaccount m ON m.DeviceID=s.DeviceID AND m.AccountID=s.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
                WHERE s.Note<>'Penarikan uang dari laci kasir saat tutup outlet'
                AND s.TransactionDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 1 AS UrutanTampil, 
                TransactionNumber Nomor, CONCAT(s.TransactionDate ,', ', s.TransactionTime) Tanggal, s.Amount AS Jumlah,
                CASE WHEN s.AccountID=1 THEN COALESCE(AccountName, s.CashBankAccountName) 
                ELSE COALESCE(CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName),s.CashBankAccountName) END AS AmbilDari, 
                PaidTo DibayarKe, Note Keterangan
                FROM cloud_cashbankout s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN mastercashbankaccount m ON m.DeviceID=s.DeviceID AND m.AccountID=s.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
                WHERE s.Note<>'Penarikan uang dari laci kasir saat tutup outlet'
                AND s.TransactionDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) Z ";

        $queryString = "SELECT Nomor,Tanggal, Jumlah, AmbilDari, DibayarKe, Keterangan FROM (" . $queryStringItemAndVarian . ") ff
            ORDER BY ff.Tanggal, ff.Jumlah";
        return $queryString;
    }

    function get_query_pengeluaran_groupby() {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " s.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->DeviceID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }

        $queryStringItemAndVarian = "SELECT Nomor, Tanggal, Jumlah, AmbilDari, DibayarKe, Keterangan FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, 
                TransactionNumber Nomor, CONCAT(s.TransactionDate ,', ', s.TransactionTime) Tanggal, s.Amount AS Jumlah,
                CASE WHEN s.AccountID=1 THEN COALESCE(AccountName, s.CashBankAccountName) 
                ELSE COALESCE(CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName),s.CashBankAccountName) END AS AmbilDari, 
                PaidTo DibayarKe, Note Keterangan
                FROM cashbankout s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN mastercashbankaccount m ON m.DeviceID=s.DeviceID AND m.AccountID=s.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
                WHERE s.Note<>'Penarikan uang dari laci kasir saat tutup outlet'
                AND s.TransactionDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 1 AS UrutanTampil, 
                TransactionNumber Nomor, CONCAT(s.TransactionDate ,', ', s.TransactionTime) Tanggal, s.Amount AS Jumlah,
                CASE WHEN s.AccountID=1 THEN COALESCE(AccountName, s.CashBankAccountName) 
                ELSE COALESCE(CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName),s.CashBankAccountName) END AS AmbilDari, 
                PaidTo DibayarKe, Note Keterangan
                FROM cloud_cashbankout s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN mastercashbankaccount m ON m.DeviceID=s.DeviceID AND m.AccountID=s.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
                WHERE s.Note<>'Penarikan uang dari laci kasir saat tutup outlet'
                AND s.TransactionDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) Z ";

        $queryString = "SELECT DibayarKe, sum(Jumlah) as Jumlah FROM (" . $queryStringItemAndVarian . ") ff
            GROUP BY DibayarKe
            ORDER BY ff.Tanggal, ff.Jumlah";
        return $queryString;
    }

    function get_query_pendapatan_selain_penjualan() {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " s.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->DeviceID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }

        $queryStringItemAndVarian = "SELECT Nomor, Tanggal, Jumlah, TerimaDari, MasukKe, Keterangan FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, 
                TransactionNumber Nomor, CONCAT(s.TransactionDate ,', ', s.TransactionTime) Tanggal, s.Amount AS Jumlah,
                CASE WHEN s.AccountID=1 THEN COALESCE(AccountName, s.CashBankAccountName) 
                ELSE COALESCE(CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName),s.CashBankAccountName) END AS MasukKe, 
                ReceivedFrom TerimaDari, Note Keterangan
                FROM cashbankin s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN mastercashbankaccount m ON m.DeviceID=s.DeviceID AND m.AccountID=s.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
                WHERE s.TransactionDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                AND s.IncomeType = 1
                UNION ALL
                SELECT s.DeviceID, 1 AS UrutanTampil, 
                TransactionNumber Nomor, CONCAT(s.TransactionDate ,', ', s.TransactionTime) Tanggal, s.Amount AS Jumlah,
                CASE WHEN s.AccountID=1 THEN COALESCE(AccountName, s.CashBankAccountName) 
                ELSE COALESCE(CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName),s.CashBankAccountName) END AS MasukKe, 
                ReceivedFrom TerimaDari, Note Keterangan
                FROM cloud_cashbankin s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                LEFT JOIN mastercashbankaccount m ON m.DeviceID=s.DeviceID AND m.AccountID=s.AccountID AND s.PerusahaanNo=m.PerusahaanNo AND m.DeviceNo=s.AccountDeviceNo
                WHERE s.TransactionDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.TransactionDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) Z ";

        $queryString = "SELECT Nomor,Tanggal, Jumlah, TerimaDari, MasukKe, Keterangan FROM (" . $queryStringItemAndVarian . ") ff
            ORDER BY ff.Tanggal, ff.Jumlah";
        return $queryString;
    }

    public function get_query_neraca() {
        return "call nutacloud.neraca(" . $this->CI->db->escape($this->dateStart) . "," . $this->CI->db->escape($this->Outlet) . ") ";
    }

    public function get_query_modul_outlet() {
        return "SELECT PurchaseModule,MenuRacikan,StockModifier,PriceVariation from options where deviceid=" . $this->CI->db->escape($this->Outlet);
    }

    public function get_query_rekap_penjualan_per_jam() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
            SELECT Jam, SUM(Cnt) JumlahTransaksi, SUM(Total) Total
FROM
(
    SELECT concat(substr(saletime,1,2),':00 - ',substr(saletime,1,2),':59') Jam, 1 Cnt, 
    s.Total+s.Rounding-(CashDownPayment+BankDownPayment) Total
    FROM sale s INNER JOIN options o
        ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE Pending='false' AND saletime <> ''
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
    UNION ALL 
    SELECT concat(substr(CreatedTime,1,2),':00 - ',substr(CreatedTime,1,2),':59') Jam, 1 Cnt, 
    CashDownPayment+BankDownPayment Total
    FROM sale s INNER JOIN options o
        ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE CashDownPayment+BankDownPayment
    AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
) X GROUP BY Jam";

        return $queryString;
    }

    public function get_query_rekap_penjualan_per_jam_per_item() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
            SELECT Jam,ItemName NamaItem, SUM(Quantity) Quantity, SUM(Total) Total
FROM
(
    SELECT concat(substr(saletime,1,2),':00 - ',substr(saletime,1,2),':59') Jam,sd.ItemName,
    (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity,sd.SubTotal-sd.SubTotalCanceled Total
    FROM sale s 
INNER JOIN saleitemdetail sd ON s.TransactionID = sd.TransactionID
    AND s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
    INNER JOIN options o
        ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE Pending='false' AND saletime <> ''
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
) X GROUP BY Jam,ItemName
ORDER BY Jam, Quantity DESC";

        return $queryString;
    }

    public function get_query_rekap_penjualan_per_kasir() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
            SELECT Kasir, SUM(Cnt) JumlahTransaksi, SUM(Total) Total
FROM
(
    SELECT CASE WHEN EditedBy<>'' THEN EditedBy ELSE CreatedBy END Kasir, 1 Cnt, 
    s.Total+s.Rounding-(s.CashDownPayment+s.BankDownPayment) Total
    FROM sale s INNER JOIN options o
        ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE Pending='false'
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
    UNION ALL
    SELECT CreatedBy Kasir, 1 Cnt, s.CashDownPayment+s.BankDownPayment Total
    FROM sale s INNER JOIN options o
        ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE s.CashDownPayment+s.BankDownPayment<>0
    AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
) X GROUP BY Kasir";

        return $queryString;
    }

    public function get_query_rata2_belanja_per_pelanggan() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
SELECT x.CustomerName Pelanggan, SUM(x.Total) TotalBelanja, SUM(x.Cnt) JumlahTransaksi,
SUM(x.Total) / SUM(x.Cnt) `Rata-Rata`
FROM
(   SELECT COALESCE(mi.CustomerName,CASE WHEN s.CustomerName<>'' THEN s.CustomerName ELSE 'Umum' END) as CustomerName,
    1 Cnt, s.Total+s.Rounding-(s.CashDownPayment+s.BankDownPayment) Total
    FROM sale s
    LEFT JOIN
    mastercustomer mi
    ON s.CustomerID=mi.CustomerID AND s.PerusahaanNo=mi.PerusahaanNo
    AND mi.DeviceID=s.DeviceID AND mi.DeviceNo=s.CustomerDeviceNo
    INNER JOIN options o
    ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE Pending='false' AND
    s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
    UNION ALL
    SELECT COALESCE(mi.CustomerName,CASE WHEN s.CustomerName<>'' THEN s.CustomerName ELSE 'Umum' END) as CustomerName,
    1 Cnt, s.CashDownPayment+s.BankDownPayment Total
    FROM sale s
    LEFT JOIN
    mastercustomer mi
    ON s.CustomerID=mi.CustomerID AND s.PerusahaanNo=mi.PerusahaanNo
    AND mi.DeviceID=s.DeviceID AND mi.DeviceNo=s.CustomerDeviceNo
    INNER JOIN options o
    ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE s.CashDownPayment+s.BankDownPayment<>0 AND
    s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
) x
GROUP BY x.CustomerName";
//s.CashDownPayment+s.BankDownPayment
        return $queryString;
    }

    public function get_query_rekap_penjualan_per_kategori() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
            SELECT 1 AS Urutan, COALESCE(mc.CategoryName,'Lainnya') AS Kategori,
COALESCE(mi.ItemName,sd.ItemName) AS Item, sd.Quantity,
sd.SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
INNER JOIN saleitemdetail sd ON s.TransactionID = sd.TransactionID
    AND s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
LEFT JOIN masteritem mi ON sd.ItemID = mi.ItemID
    AND mi.DeviceID = sd.DeviceID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID
    AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
WHERE Pending='false' AND
    s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
        UNION ALL
            SELECT 2 AS Urutan, 'Uang Muka' AS Kategori,
'Uang Muka' AS Item, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE s.CashDownPayment+s.BankDownPayment<>0
    AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
        UNION ALL
            SELECT 3 AS Urutan, 'Dipotong Uang Muka' AS Kategori,
'Dipotong Uang Muka' AS Item, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE Pending='false' AND s.CashDownPayment+s.BankDownPayment<>0
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition;

        return "SELECT Kategori, Item, SUM(Quantity) Quantity, SUM(SubTotal) TotalPerItem FROM (" . $queryString . ") ff
        GROUP BY ff.Kategori, ff.Item
        ORDER BY Urutan,Kategori,Item";
    }

    public function get_query_rekap_penjualan_per_kategori_semua_item() {
        $this->CheckDeviceID();

        $Condition = "AND s.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND mi.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ")";
                $Condition = " mi.PerusahaanNo = " . $this->NoPerusahaan . " AND mi.DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " mi.PerusahaanNo = " . $this->NoPerusahaan . " AND mi.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        } else {
            $Condition = " s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet);
            $Condition2 = " mi.PerusahaanNo = " . $this->NoPerusahaan . " AND mi.DeviceID = " . $this->CI->db->escape($this->Outlet);
        }

        $queryString = "
SELECT 1 AS Urutan, COALESCE(CategoryName,'Lainnya') Kategori,ItemName Item,COALESCE(Quantity,0) Quantity, COALESCE(SubTotal,0) SubTotal FROM 
masteritem mi
LEFT JOIN mastercategory mc
ON mc.PerusahaanNo=mi.PerusahaanNo
AND mc.DeviceID=mi.DeviceID
AND mc.DeviceNo=mi.CategoryDeviceNo
AND mc.CategoryID=mi.CategoryID
LEFT JOIN
(
    SELECT ItemID,ItemDeviceNo,SUM(Quantity) Quantity,SUM(SubTotal) SubTotal FROM sale s 
    INNER JOIN saleitemdetail d
    ON s.PerusahaanNo=d.PerusahaanNo
    AND s.DeviceID=d.DeviceID
    AND s.DeviceNo=d.TransactionDeviceNo
    AND s.TransactionID=d.TransactionID
    WHERE " . $Condition . " 
        AND SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
        AND SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    GROUP BY ItemID,ItemDeviceNo
) sd
ON
mi.ItemID=sd.ItemID
AND mi.DeviceNo=sd.ItemDeviceNo
WHERE " . $Condition2 . " 
AND mi.IsProduct='true'
        UNION ALL
            SELECT 2 AS Urutan, 'Uang Muka' AS Kategori,
'Uang Muka' AS Item, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE s.CashDownPayment+s.BankDownPayment<>0
    AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
        UNION ALL
            SELECT 3 AS Urutan, 'Dipotong Uang Muka' AS Kategori,
'Dipotong Uang Muka' AS Item, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE Pending='false' AND s.CashDownPayment+s.BankDownPayment<>0
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition;

        return "SELECT Kategori, Item, SUM(Quantity) Quantity, SUM(SubTotal) TotalPerItem FROM (" . $queryString . ") ff
        GROUP BY ff.Kategori, ff.Item
        ORDER BY Urutan,Kategori,Item";
    }

    public function get_query_laba_per_kategori() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
            SELECT 1 AS Urutan, COALESCE(mc.CategoryName,'Lainnya') AS Kategori,
                COALESCE(mi.ItemName,sd.ItemName) AS Item, sd.Quantity,
                sd.SubTotal, sd.COGS + sd.COGSModifier as COGS
                FROM sale s
                INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN saleitemdetail sd ON s.TransactionID = sd.TransactionID
                    AND s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
                LEFT JOIN masteritem mi ON sd.ItemID = mi.ItemID
                    AND mi.DeviceID = sd.DeviceID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID
                    AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
                WHERE Pending='false' 
                    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
                    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                    AND " . $Condition . "
            UNION ALL
            SELECT 2 AS Urutan, 'Uang Muka' AS Kategori,
                'Uang Muka' AS Item, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment SubTotal,0 as COGS
                FROM sale s
                INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                    AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
                    AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
                    AND " . $Condition . "
            UNION ALL
            SELECT 3 AS Urutan, 'Dipotong Uang Muka' AS Kategori,
                'Dipotong Uang Muka' AS Item, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS SubTotal,0 as COGS
                FROM sale s
                INNER JOIN options o ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE Pending='false' AND s.CashDownPayment+s.BankDownPayment<>0
                    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
                    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                    AND " . $Condition;

        return "SELECT Kategori, Item, SUM(Quantity) Quantity, SUM(SubTotal) TotalPerItem, SUM(COGS) TotalHppPerItem, (SUM(SubTotal) - sum(COGS)) TotalLabaKotorPerItem  FROM (" . $queryString . ") ff
        GROUP BY ff.Kategori, ff.Item
        ORDER BY Urutan,Kategori,Item";
    }

    public function get_query_rekap_penjualan_per_opsimakan() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
            SELECT 
                s.NamaOpsiMakan AS TipePenjualan,
                COALESCE(mi.ItemName,sd.ItemName) AS Item, 
                sd.Quantity,
                sd.SubTotal,
                sd.MarkupValue
            FROM sale s
            INNER JOIN options o
                ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
            INNER JOIN saleitemdetail sd ON s.TransactionID = sd.TransactionID
                AND s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
            LEFT JOIN masteritem mi ON sd.ItemID = mi.ItemID
                AND mi.DeviceID = sd.DeviceID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
            WHERE Pending='false' AND
                s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition;

        return "
            SELECT 
                TipePenjualan, 
                Item, 
                SUM(Quantity) Quantity, 
                SUM(SubTotal) TotalPerItem, 
                SUM(MarkupValue) MarkupPerItem 
            FROM (" . $queryString . ") ff
            GROUP BY 
                ff.TipePenjualan, ff.Item
            ORDER BY
                TipePenjualan, Item";
    }

    public function get_query_rekap_penjualan_tunai() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.cashpaymentamount <> 0";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.cashpaymentamount <> 0";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.cashpaymentamount <> 0";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
            SELECT 1 AS Urutan, COALESCE(mc.CategoryName,'Lainnya') AS Kategori,
COALESCE(mi.ItemName,sd.ItemName) AS Item, sd.Quantity,
sd.SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
INNER JOIN saleitemdetail sd ON s.TransactionID = sd.TransactionID
    AND s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
LEFT JOIN masteritem mi ON sd.ItemID = mi.ItemID
    AND mi.DeviceID = sd.DeviceID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID
    AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
WHERE Pending='false' AND
    s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
        UNION ALL
            SELECT 2 AS Urutan, 'Uang Muka' AS Kategori,
'Uang Muka' AS Item, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE s.CashDownPayment+s.BankDownPayment<>0
    AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
        UNION ALL
            SELECT 3 AS Urutan, 'Dipotong Uang Muka' AS Kategori,
'Dipotong Uang Muka' AS Item, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE Pending='false' AND s.CashDownPayment+s.BankDownPayment<>0
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition;

        return "SELECT Kategori, Item, SUM(Quantity) Quantity, SUM(SubTotal) TotalPerItem FROM (" . $queryString . ") ff
        GROUP BY ff.Kategori, ff.Item
        ORDER BY Urutan,Kategori,Item";
    }

    public function get_query_penjualan_per_kategori_by_cashbank($accountid, $accountdevno) {
        $this->CheckDeviceID();

        $conditionAccount = " AND ((CashAccountID=$accountid AND CashAccountDeviceNo=$accountdevno) "
                . "OR (BankAccountID=$accountid AND BankAccountDeviceNo=$accountdevno)) ";
        $conditionAccountDP = " AND ((CashDownPaymentAccountID=$accountid AND CashDownPaymentAccountDeviceNo=$accountdevno) "
                . "OR (BankDownPaymentAccountID=$accountid AND BankDownPaymentAccountDeviceNo=$accountdevno)) ";

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID);
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID);
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID);
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
            SELECT 1 AS Urutan, COALESCE(mc.CategoryName,'Lainnya') AS Kategori,
COALESCE(mi.ItemName,sd.ItemName) AS Item, sd.Quantity,
sd.SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
INNER JOIN saleitemdetail sd ON s.TransactionID = sd.TransactionID
    AND s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
LEFT JOIN masteritem mi ON sd.ItemID = mi.ItemID
    AND mi.DeviceID = sd.DeviceID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID
    AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
WHERE Pending='false' AND
    s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . $conditionAccount . "
        UNION ALL
            SELECT 2 AS Urutan, 'Uang Muka' AS Kategori,
'Uang Muka' AS Item, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE s.CashDownPayment+s.BankDownPayment<>0
    AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . $conditionAccountDP . "
        UNION ALL
            SELECT 3 AS Urutan, 'Dipotong Uang Muka' AS Kategori,
'Dipotong Uang Muka' AS Item, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS SubTotal
FROM sale s
INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
WHERE Pending='false' AND s.CashDownPayment+s.BankDownPayment<>0
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . $conditionAccount;

        return "SELECT Kategori, Item, SUM(Quantity) Quantity, SUM(SubTotal) TotalPerItem FROM (" . $queryString . ") ff
        GROUP BY ff.Kategori, ff.Item
        ORDER BY Urutan,Kategori,Item";
    }

    public function get_query_laporan_diskon() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
    SELECT s.SaleNumber Nomor,CONCAT(s.SaleDate,', ', s.SaleTime) Tanggal, CONCAT('Diskon Final ', s.FinalDiscount) KeteranganDiskon,
    -(s.Total - (CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal)) AS NominalDiskonRp
    FROM sale s INNER JOIN saleitemdetail sd
    ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceID=sd.DeviceID  AND s.DeviceNo=sd.TransactionDeviceNo
    INNER JOIN options o
        ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE Pending='false' AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
    AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
    GROUP BY s.DeviceID, s.TransactionID
    UNION ALL
    SELECT s.SaleNumber Nomor,CONCAT(s.SaleDate,', ', s.SaleTime) Tanggal, CONCAT(sd.ItemName, ' ', 'Diskon ', sd.Discount) KeteranganDiskon,
    -(sd.SubTotal-sd.SubTotalCanceled - ((sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) * sd.UnitPrice)) NominalDiskonRp
    FROM sale s INNER JOIN saleitemdetail sd
    ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceID=sd.DeviceID  AND s.DeviceNo=sd.TransactionDeviceNo
    INNER JOIN options o
        ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE Pending='false' AND sd.Discount<>'' AND sd.Discount NOT LIKE '0%'
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
    UNION ALL
    SELECT s.SaleNumber Nomor,CONCAT(s.SaleDate,', ', s.SaleTime) Tanggal, 
    CONCAT(sd.DiscountName, CASE WHEN sd.Discount like '%|%%' escape '|' THEN CONCAT(' ', sd.Discount) ELSE '' END ) KeteranganDiskon,
    sd.DiscountValue NominalDiskonRp
    FROM sale s INNER JOIN salediscountdetail sd
    ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceID=sd.DeviceID  AND s.DeviceNo=sd.TransactionDeviceNo
    INNER JOIN options o
        ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE Pending='false' AND sd.Discount<>'' AND sd.Discount NOT LIKE '0%'
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
    UNION ALL
    SELECT s.SaleNumber Nomor,CONCAT(s.SaleDate,', ', s.SaleTime) Tanggal, 
    CONCAT('Diskon Final', CASE WHEN s.FinalDiscount like '%|%%' escape '|' THEN CONCAT(' ', s.FinalDiscount) ELSE '' END ) KeteranganDiskon,
    -(s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sid.SubTotal)) NominalDiskonRp
    FROM sale s INNER JOIN saleitemdetail sid
    ON s.TransactionID=sid.TransactionID AND s.PerusahaanNo=sid.PerusahaanNo AND s.DeviceID=sid.DeviceID  AND s.DeviceNo=sid.TransactionDeviceNo
    LEFT JOIN salediscountdetail sd
    ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceID=sd.DeviceID  AND s.DeviceNo=sd.TransactionDeviceNo
    WHERE Pending='false' AND sd.Discount IS NULL
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND s.FinalDiscount<>'' AND s.FinalDiscount<>'0%'
    AND " . $Condition . "
    GROUP BY s.TransactionID, s.DeviceNo
    ";

        return $queryString;
    }

    public function get_query_laporan_pajak_penjualan() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") ";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " s.PerusahaanNo = " . $this->NoPerusahaan . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " s.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

//         $queryString = "
// SELECT NamaPajak, Nomor, Tanggal, Pajak,'By code' as TotalPajak  FROM
// (
//     SELECT CAST('Pajak' as Char(50)) AS NamaPajak, s.SaleNumber Nomor,CONCAT(s.SaleDate,', ', s.SaleTime) Tanggal, 
//     s.TaxValue AS Pajak, s.TransactionID, s.DeviceNo
//     FROM sale s INNER JOIN options o
//         ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
//     WHERE Pending='false' AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85 AND s.Tax = 1
//     AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
//     AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
//     AND " . $Condition . "
//     UNION ALL
//     SELECT TaxName NamaPajak, s.SaleNumber Nomor,CONCAT(s.SaleDate,', ', s.SaleTime) Tanggal, SUM(d.TaxValue) AS Pajak, s.TransactionID, s.DeviceNo
//     FROM sale s INNER JOIN (
//         select s.PerusahaanNo,s.DeviceID,s.TransactionID,s.DeviceNo TransactionDeviceNo,sd.DetailID,sd.DetailDeviceNo,
//         TaxName,
//         sd.TaxValue from sale s
//         inner join saleitemdetailtax sd on
//         s.PerusahaanNo=sd.PerusahaanNo
//         AND s.TransactionID = sd.TransactionID
//         AND s.DeviceID = sd.DeviceID
//         AND s.DeviceNo=sd.TransactionDeviceNo
//         WHERE " . $Condition . " 
//         AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
//         AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
//         AND Pending='false' 
//         GROUP BY sd.TaxName,sd.DetailID,sd.DetailDeviceNo
//     ) d ON s.TransactionID=d.TransactionID 
//     AND s.PerusahaanNo=d.PerusahaanNo AND s.DeviceID=d.DeviceID AND s.DeviceNo=d.TransactionDeviceNo
//     INNER JOIN options o
//         ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
//     WHERE Pending='false' AND (s.CreatedVersionCode>=85 OR s.EditedVersionCode>=85)
//     AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
//     AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
//     AND " . $Condition . "
//     GROUP BY d.TaxName,s.TransactionID, s.DeviceNo
// ) X ORDER BY NamaPajak, Tanggal, DeviceNo, TransactionID
// ";
        $queryString = "
SELECT * FROM
(
    SELECT CAST('Pajak' as Char(50)) AS NamaPajak, s.SaleNumber Nomor,CONCAT(s.SaleDate,', ', s.SaleTime) Tanggal, 
    s.TaxValue AS Pajak,
'By code' as TotalPajak
    FROM sale s INNER JOIN options o
    ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE Pending='false' AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85 AND s.Tax = 1
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
    UNION ALL
    SELECT substring_index(DetailExcludeTaxValue, '$', 1) NamaPajak,
    SaleNumber Nomor,SaleDateTime Tanggal,
    CAST(substring_index(DetailExcludeTaxValue, '$', -1) AS Decimal(19,4)) Pajak,
    'By code' as TotalPajak
    FROM
    (
        SELECT s.TransactionID,s.DeviceNo,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,
        CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,TaxValueExclude,DetailExcludeTaxValues,nomor,
        substring_index(substring_index(DetailExcludeTaxValues, '#', nomor+1), '#', -1) AS DetailExcludeTaxValue,
        CASE WHEN s.DiningTable<>'' THEN 
        CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
        ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) 
        END CustomerName, Pax,'' Discount, 3 UrutanTampil 
        FROM sale s
        INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo 
        AND s.DeviceID = o.DeviceID
        INNER JOIN tmp_number10 
        ON char_length(DetailExcludeTaxValues) - char_length(replace(DetailExcludeTaxValues, '#', '')) >= nomor
        WHERE Pending='false' AND (s.CreatedVersionCode>=85 OR s.EditedVersionCode>=85)
        AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
        AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
        AND " . $Condition . "
    ) X
    UNION ALL
    SELECT substring_index(DetailIncludeTaxValue, '$', 1) NamaPajak,
    SaleNumber Nomor,SaleDateTime Tanggal,
    CAST(substring_index(DetailIncludeTaxValue, '$', -1) AS Decimal(19,4)) Pajak,
    'By code' as TotalPajak
    FROM
    (
        SELECT s.TransactionID,s.DeviceNo,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,
        CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,TaxValueInclude,DetailIncludeTaxValues,nomor,
        substring_index(substring_index(DetailIncludeTaxValues, '#', nomor+1), '#', -1) AS DetailIncludeTaxValue,
        CASE WHEN s.DiningTable<>'' THEN 
        CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
        ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) 
        END CustomerName, Pax,'' Discount, 3 UrutanTampil 
        FROM sale s
        INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo 
        AND s.DeviceID = o.DeviceID
        INNER JOIN tmp_number10 
        ON char_length(DetailIncludeTaxValues) - char_length(replace(DetailIncludeTaxValues, '#', '')) >= nomor
        WHERE Pending='false' AND (s.CreatedVersionCode>=85 OR s.EditedVersionCode>=85)
        AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
        AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
        AND " . $Condition . "
    ) X
) X ORDER BY NamaPajak, Tanggal";
        return $queryString;
    }

    public function get_query_laporan_pembulatan() {
        $this->CheckDeviceID();

        $Condition = "AND o.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        $Condition2 = "AND DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                $Condition2 = " DeviceID IN (" . $strCabangs . ")";
            }
        } else {
            $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            $Condition2 = " DeviceID = " . $this->CI->db->escape($this->Outlet) . "";
        }

        $queryString = "
    SELECT s.SaleNumber Nomor,CONCAT(s.SaleDate,', ', s.SaleTime) Tanggal, COALESCE(mc.CustomerName,s.CustomerName) Pelanggan,
    s.Rounding Pembulatan
    FROM sale s LEFT JOIN mastercustomer mc 
    ON mc.DeviceID=s.DeviceID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.CustomerID=s.CustomerID AND mc.DeviceNo=s.CustomerDeviceNo
    INNER JOIN options o
        ON o.DeviceID=s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
    WHERE Pending='false' AND s.Rounding <> 0
    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . "
    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
    AND " . $Condition . "
    ";

        return $queryString;
    }

    public function uang_masuk($Outlet) {
        return "
SELECT 
    TransactionNumber AS No,
    TransactionID,
    TransactionDate AS Tanggal,
    COALESCE(m.AccountName,c.CashBankAccountName) AS MasukKe,
    ReceivedFrom AS Dari,
    Note AS Keterangan,
    Amount AS Jumlah
FROM
    cashbankin c
        LEFT JOIN
    mastercashbankaccount m ON c.DeviceID = m.DeviceID AND c.AccountDeviceNo=m.DeviceNo
AND c.AccountID = m.AccountID
WHERE 
    c.DeviceID = " . $this->CI->db->escape($Outlet);
    }

    public function get_account_bank() {
        return "
        SELECT * FROM mastercashbankaccount WHERE 
        DeviceID = " . $this->CI->db->escape($this->getOutlet());
    }

    public function get_query_daftar_master_promo($perusahaanNo) {
        return "
        SELECT
            PromoID,
            PromoTitle AS judul,
            CONCAT(PromoFromDate,' - ',PromoToDate) AS 'PeriodeBerlaku',
            CONCAT(PromoFromTime,' - ',PromoToTime) AS 'JamBerlaku',
            CONCAT(
                CASE WHEN ApplyMonday = 1 THEN 'Senin' ELSE '0' END,' ',
                CASE WHEN ApplyTuesday = 1 THEN 'Selasa' ELSE '0' END,' ',
                CASE WHEN ApplyWednesday = 1 THEN 'Rabu' ELSE '0' END,' ',
                CASE WHEN ApplyThursday = 1 THEN 'Kamis' ELSE '0' END,' ',
                CASE WHEN ApplyFriday = 1 THEN 'Jumat' ELSE '0' END,' ',
                CASE WHEN ApplySaturday = 1 THEN 'Sabtu' ELSE '0' END,' ',
                CASE WHEN ApplySunday = 1 THEN 'Minggu' ELSE '0' END) AS 'HariBerlaku'
            FROM masterpromo WHERE DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND PerusahaanNo = '" . $perusahaanNo . "'";
    }

    public function get_query_stok_byoutlet($PerusahaanNo, $outletID, $minDate) {
        $Condition = " p.PerusahaanNo = " . $PerusahaanNo . " AND p.DeviceID = " . $outletID;
        $ConditionIng = " p.PerusahaanNo = " . $PerusahaanNo . " AND p.DeviceID = " . $outletID;
        $ConditionM = " m.PerusahaanNo = " . $PerusahaanNo . " AND m.DeviceID = " . $outletID;
        $ConditionPurchaseDate = "";
        $ConditionSaleDate = "";
        $ConditionStockOpnameDate = "";
        $ConditionTransferDate = "";
        $unioncheckpoint = "";

        if ($minDate != false) {
            $ConditionPurchaseDate = " AND p.PurchaseDate > '" . $minDate . "'";
            $ConditionSaleDate = " AND p.SaleDate > '" . $minDate . "'";
            $ConditionStockOpnameDate = " AND p.StockOpnameDate > '" . $minDate . "'";
            $ConditionTransferDate = " AND p.TransferDate > '" . $minDate . "'";
            $unioncheckpoint = " UNION ALL\n"
                    . "SELECT ItemID, ItemDeviceNo, DeviceID, 'Stok Awal' TransactionName,
                  '-' TransactionNumber,
                  '-' TransactionDetailID, InsertedDate TransactionDate,
                  '23:59:59' TransactionTime,
                  '-' Keterangan,
                  Qty, 0 AS UnitPrice
                  FROM checkpoint WHERE PerusahaanNo=" . $PerusahaanNo .
                    " AND DeviceID=" . $outletID . " AND InsertedDate='" . $minDate . "'";
        }
        $sql = "SELECT v.ItemID, v.ItemDeviceNo, COALESCE(SUM(Quantity),0) as 'Qty' FROM
            (" . $this->getQPurchase($Condition) . $ConditionPurchaseDate .
                " UNION ALL\n" .
                $this->getQSale($Condition) . $ConditionSaleDate .
                " UNION ALL\n" .
                $this->getQStockOpname($Condition) . $ConditionStockOpnameDate .
                " UNION ALL\n" .
                $this->getQSaleIngredients($ConditionIng) . $ConditionSaleDate .
                " UNION ALL\n" .
                $this->getQSaleModifier($ConditionIng) . $ConditionSaleDate .
                " UNION ALL\n" .
                $this->getQModifierIngredients($ConditionIng) . $ConditionSaleDate .
                " UNION ALL\n" .
                $this->getQTransferStockIn($Condition) . $ConditionTransferDate .
                " UNION ALL\n" .
                $this->getQTransferStockOut($Condition) . $ConditionTransferDate .
                $unioncheckpoint .
                " \n" .
                "
            ) v GROUP BY v.ItemID, v.ItemDeviceNo";

//        log_message('error', $sql);
        return $sql;
    }

    public function get_query_stok_single($PerusahaanNo, $outletID, $ItemID) {
        $Condition = " p.PerusahaanNo = " . $PerusahaanNo . " AND p.DeviceID = " . $outletID . " AND d.ItemID = " . $ItemID;
        $ConditionIng = " p.PerusahaanNo = " . $PerusahaanNo . " AND p.DeviceID = " . $outletID . " AND d.IngredientsID = " . $ItemID;
        $ConditionM = " m.PerusahaanNo = " . $PerusahaanNo . " AND m.DeviceID = " . $outletID . " AND m.ItemID = " . $ItemID;

        $sql = "SELECT COALESCE(SUM(Quantity),0) as 'Qty',m.Unit as 'Satuan' FROM
            masteritem m INNER JOIN options o ON m.DeviceID=o.DeviceID AND m.PerusahaanNo=o.PerusahaanNo
            LEFT JOIN
            (" . $this->getQPurchase($Condition) .
                " UNION ALL\n" .
                $this->getQSale($Condition) .
                " UNION ALL\n" .
                $this->getQStockOpname($Condition) .
                " UNION ALL\n" .
                $this->getQSaleIngredients($ConditionIng) .
                " UNION ALL\n" .
                $this->getQSaleModifier($ConditionIng) .
                " UNION ALL\n" .
                $this->getQModifierIngredients($ConditionIng) .
                " UNION ALL\n" .
                $this->getQTransferStockIn($Condition) .
                " UNION ALL\n" .
                $this->getQTransferStockOut($Condition) .
                " \n" .
                "
            ) v ON v.ItemID=m.ItemID
            AND m.DeviceID = v.DeviceID
            WHERE " . $ConditionM;

        return $sql;
    }

    public function get_query_stok_singlenew($PerusahaanNo, $outletID, $ItemID, $ItemDeviceNo) {
        $Condition = " p.PerusahaanNo = " . $PerusahaanNo . " AND p.DeviceID = " . $outletID . " AND d.ItemID = " . $ItemID . " AND d.ItemDeviceNo = " . $ItemDeviceNo;
        $ConditionIng = " p.PerusahaanNo = " . $PerusahaanNo . " AND p.DeviceID = " . $outletID . " AND d.IngredientsID = " . $ItemID . " AND d.IngredientsDeviceNo = " . $ItemDeviceNo;
        $ConditionM = " m.PerusahaanNo = " . $PerusahaanNo . " AND m.DeviceID = " . $outletID . " AND m.ItemID = " . $ItemID . " AND m.ItemDeviceNo = " . $ItemDeviceNo;

        $sql = "SELECT COALESCE(SUM(Quantity),0) as 'Qty',m.Unit as 'Satuan' FROM
            masteritem m INNER JOIN options o ON m.DeviceID=o.DeviceID AND m.PerusahaanNo=o.PerusahaanNo
            LEFT JOIN
            (" . $this->getQPurchase($Condition) .
                " UNION ALL\n" .
                $this->getQSale($Condition) .
                " UNION ALL\n" .
                $this->getQStockOpname($Condition) .
                " UNION ALL\n" .
                $this->getQSaleIngredients($ConditionIng) .
                " UNION ALL\n" .
                $this->getQSaleModifier($ConditionIng) .
                " UNION ALL\n" .
                $this->getQModifierIngredients($ConditionIng) .
                " UNION ALL\n" .
                $this->getQTransferStockIn($Condition) .
                " UNION ALL\n" .
                $this->getQTransferStockOut($Condition) .
                " \n" .
                "
            ) v ON v.ItemID=m.ItemID
            AND m.DeviceID = v.DeviceID AND m.DeviceNo = v.ItemDeviceNo
            WHERE " . $ConditionM;

        return $sql;
    }

    public function get_query_penjualanpershift($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule) {
        $this->CheckDeviceID();
        //$whereOutlet = " WHERE h.Outlet = " . $this->CI->db->escape($this->Outlet);
        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua")
            $whereOutlet = "";

        $extrakolom1 = "";
        $extrakolom2 = "";
        $joinExtraKolom = "";
        $groupByExtraKolom = "";
        $extrakolom3 = "";
        $extrakolom4 = "";
        $extrakolom2A = "";
        if ($ShowExtraKolom) {
            $extrakolom1 = ",CategoryName AS Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2 = ",'' Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
            $extrakolom3 = ",Kategori";
            $extrakolom4 = ",DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah";
            $joinExtraKolom = "
            LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo
            AND mc.DeviceNo = mi.CategoryDeviceNo
";

            if ($isUseVarianModule) {
                $extrakolom1 = str_replace("Kategori", "Kategori, sd.VarianName, COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1);
                $extrakolom2 = str_replace("Kategori", "Kategori, '' VarianName, '' PilihanExtra", $extrakolom2);
                $extrakolom2A = str_replace("Kategori", "Kategori, VarianName, PilihanExtra", $extrakolom2A);
                $extrakolom3 = str_replace("Kategori", "Kategori, VarianName AS Varian, PilihanExtra", $extrakolom3);
                $joinExtraKolom = $joinExtraKolom . "
                LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo
                AND sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sdm.DetailDeviceNo=sd.DeviceNo
                ";
                $groupByExtraKolom = "
GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.DetailID,sd.TransactionDeviceNo,sd.DeviceNo
";
            }
        }
        $pax = "";
        if ($IsDiningTable) {
            $pax = ", h.Pax";
        }
        $queryString = "\tSELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,
            CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n" .
                "\tCASE WHEN sd.Note = '' THEN sd.ItemName ELSE 
                CONCAT(sd.ItemName,'<br><i>',sd.Note,'</i>') END ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice, sd.MarkupValue ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,1 UrutanTampil" . $extrakolom1 . " \n" .
                "\tFROM sale s INNER JOIN saleitemdetail sd \n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo \n" . $joinExtraKolom .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "  AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                $groupByExtraKolom .
                "\tUNION ALL
SELECT OpenID,DeviceNo,OpenDate,OpenTime,CloseDate,CloseTime,Outlet, DeviceID, SaleNumber, SaleDate,SaleTime,CustomerName, Pax, ItemName,Qty, 0 as UnitPrice, MarkupValue, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
(
 SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate,s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t" .
                "CASE WHEN sd2.TransactionID IS NULL THEN 'Diskon Final'" .
                " ELSE CONCAT('Diskon Final : ', sd2.DiscountName) END ItemName, 1 AS Qty,\n" .
                "\tCASE WHEN sd2.TransactionID IS NULL THEN 0 ELSE sd2.DiscountValue END UnitPrice, 0 as MarkupValue,
            CASE WHEN sd2.TransactionID IS NULL THEN
                CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END
            ELSE sd2.Discount END Discount,
            CASE WHEN sd2.TransactionID IS NULL THEN
                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal)
            ELSE 0 END SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tLEFT JOIN salediscountdetail sd2
            ON s.TransactionID=sd2.TransactionID AND s.PerusahaanNo=sd2.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd2.DeviceID AND s.DeviceNo=sd2.TransactionDeviceNo
            INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
 AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID, s.DeviceNo HAVING SUM(sd.SubTotal) <> s.Total\n
) x
";
        $queryString .= "UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pajak' ItemName, 1 AS Qty,\n " .
                "\ts.TaxValue, 0 MarkupValue, '' Discount, s.TaxValue SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND ((s.Tax=1 AND s.PriceIncludeTax=0) AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85) AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $queryString .= "
        UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, 
                Pax,CONCAT(sd.TaxName, ' ', replace(CAST(sd.TaxPercent AS CHAR),'.0',''), '%') ItemName, 1 AS Qty,\n " .
                "\tCEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)), 0 MarkupValue, '' Discount, 
            CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN saleitemdetailtax sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo\n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.PriceIncludeTax=0 AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) .
                "
            GROUP BY s.TransactionID,s.DeviceNo,sd.TaxName
            ";

        $queryString .= "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
                CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pembulatan' ItemName, 1 AS Qty,\n " .
                "\ts.Rounding, 0 MarkupValue, '' Discount, s.Rounding SubTotal,6 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Rounding<>0 AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $queryString .= "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber,s.CreatedDate, s.CreatedTime,
                CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Uang Muka' ItemName, 1 AS Qty,\n " .
                "\ts.CashDownPayment+s.BankDownPayment, 0 MarkupValue, '' Discount, s.CashDownPayment+s.BankDownPayment SubTotal,1 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $queryString .= "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
                CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Dipotong Uang Muka' ItemName, 1 AS Qty,\n " .
                "\t-(s.CashDownPayment+s.BankDownPayment), 0 MarkupValue, '' Discount, -(s.CashDownPayment+s.BankDownPayment) SubTotal,4 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND s.Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $pax = "";
        if ($IsDiningTable) {
            $pax = ", h.Pax";
        }
//            echo "SELECT h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
//                . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal" . $extrakolom4 . " FROM (" . $queryString . ") h
//                  ORDER BY h.UrutanTampil,h.SaleDateTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
//        echo "SELECT OpenID,OpenDate,OpenTime,CloseDate,CloseTime, SaleNumber as Nomor,h.SaleDate as Tanggal, h.SaleTime Jam,
//            h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
//            . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal" . $extrakolom4 . " FROM (" . $queryString . ") h
//                ORDER BY h.UrutanTampil,h.SaleDate, h.SaleTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
        return "SELECT OpenID,DeviceNo,OpenDate,OpenTime,CloseDate,CloseTime, SaleNumber as Nomor,CONCAT(h.SaleDate,', ', h.SaleTime) as Tanggal, h.SaleTime Jam,
            h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
                . "h.UnitPrice as HargaSatuan ,h.MarkupValue as Markup,h.Discount as Diskon,h.SubTotal " . $extrakolom4 . " FROM (" . $queryString . ") h
                ORDER BY h.DeviceNo,h.OpenID,h.UrutanTampil,DeviceNo,h.SaleDate, h.SaleTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
    }

    function get_query_rekapshift() {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " s.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->DeviceID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " o.PerusahaanNo = " . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }

        $queryString = "
                SELECT
                s.DeviceNo, 
    s.`OpenDate`,
    s.`OpenTime`,
    s.`OpenedBy`,
    s.`CloseDate`,
    s.`CloseTime`,
    s.`ClosedBy`,
    s.`StartingCash`,
    s.`TotalSales`,
    s.`TotalCashSales`,
    s.`OtherIncome`,
    s.`Expense`,
    s.`Withdrawal`,
    s.`ExpectedBalance`,
    s.`ActualBalance`,
    s.`Difference`
                FROM opencloseoutlet s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                AND s.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND s.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.CloseDate<>'' AND s.CreatedVersionCode>89 AND " . $Condition;


        return $queryString;
    }

    function get_query_rekap_penjualanpershift($isUseTaxModule, $rekapper) {
        $this->CheckDeviceID();
        $Condition = "";
        if ($this->clientPerusahaanID == $this->clientDeviceID) {
            $Condition = " s.DeviceID = " . $this->CI->db->escape($this->clientDeviceID) . "";
        } else {
            if ($this->Outlet == "Semua") {
                $strCabangs = '';
                foreach ($this->Cabangs as $cabang) {
                    $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
                }
                if (strlen($strCabangs) > 0) {
                    $strCabangs = substr($strCabangs, 1);
                    $Condition = " s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID IN (" . $strCabangs . ") AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                } else {
                    $Condition = " s.PerusahaanNo=" . $this->NoPerusahaan . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
                }
            } else {
                $Condition = " s.PerusahaanNo=" . $this->NoPerusahaan . " AND s.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "";
            }
        }


        $queryStringItem = "SELECT Z.OpenID,Z.DeviceNo,Z.OpenDate,Z.OpenTime,Z.CloseDate,Z.CloseTime,
            Z.UrutanTampil, Z.ItemName,
            CASE WHEN UrutanTampil>1 THEN NULL ELSE 
            SUM(Z.Quantity) END Quantity ,SUM(Z.Total) Total FROM" .
                "(
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,s.DeviceID, 1 AS UrutanTampil, sd.ItemName AS ItemName, sd.Quantity, sd.SubTotal AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo
                AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID
                WHERE 
                f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,s.DeviceID, 2 AS UrutanTampil, 'Diskon Final' AS ItemName, 1 AS Quantity, 
                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo INNER JOIN 
                " . $this->tabelsaleitemdetail . " sd
                    ON s.DeviceID=sd.DeviceID AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                WHERE 
                f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                GROUP BY s.DeviceID,s.TransactionID,s.DeviceNo,s.Total,s.Tax,s.PriceIncludeTax,s.TaxValue
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,s.DeviceID, 3 AS UrutanTampil, 'Pajak' AS ItemName, 1 AS Quantity, s.TaxValue AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                WHERE s.Tax=1 AND s.PriceIncludeTax=0 AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false' AND " . $Condition . "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,s.DeviceID, 3 AS UrutanTampil, sd.TaxName AS ItemName, 1 AS Quantity, CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) AS Total
                FROM " . $this->tabelsale . " s
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                INNER JOIN saleitemdetailtax sd ON sd.DeviceID = s.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                WHERE sd.PriceIncludeTax=0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false' AND " . $Condition . "
                GROUP BY s.DeviceID, s.TransactionID, sd.TaxName
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,s.DeviceID, 5 AS UrutanTampil, 'Pembulatan' AS ItemName, 1 AS Quantity, s.Rounding AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                WHERE s.Rounding<>0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false' AND " . $Condition . "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,s.DeviceID, 1 AS UrutanTampil, 'Uang Muka' AS ItemName, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo AND f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,s.DeviceID, 4 AS UrutanTampil, 'Dipotong Uang Muka' AS ItemName, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending = 'false' AND " . $Condition .
                ") Z GROUP BY Z.OpenID,Z.UrutanTampil, Z.ItemName HAVING SUM(Z.Quantity) <> 0 ";

        $queryStringCustomer = "SELECT CustomerName, SUM(Total) Total
        FROM
        (   SELECT
            x.Outlet,
            x.CustomerName,
            SUM(x.Total) Total
            FROM
            (   SELECT
                CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
                o.DeviceID,COALESCE(mi.CustomerName,s.CustomerName) as CustomerName,
                s.Total+s.Rounding-(s.CashDownPayment+s.BankDownPayment) Total
                FROM " . $this->tabelsale . " s
                LEFT JOIN
                mastercustomer mi
                ON s.CustomerID=mi.CustomerID AND s.PerusahaanNo=mi.PerusahaanNo
                AND mi.DeviceID=s.DeviceID AND s.CustomerDeviceNo = mi.DeviceNo
                INNER JOIN options o
                ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                WHERE
                f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                AND s.Pending='false'
                UNION ALL
                SELECT
                CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
                o.DeviceID,COALESCE(mi.CustomerName,s.CustomerName) as CustomerName,
                s.CashDownPayment+s.BankDownPayment Total
                FROM " . $this->tabelsale . " s
                LEFT JOIN
                mastercustomer mi
                ON s.CustomerID=mi.CustomerID AND s.PerusahaanNo=mi.PerusahaanNo
                AND mi.DeviceID=s.DeviceID AND s.CustomerDeviceNo = mi.DeviceNo
                INNER JOIN options o
                ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo
                WHERE
                f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) x
            GROUP BY x.Outlet,
            x.CustomerName
        ) Z GROUP BY CustomerName";

        $queryStringCustomerAndItem = "
            SELECT Z.UrutanTampil, Z.ItemName,Z.CustomerName,SUM(Z.Quantity) Quantity,SUM(Z.Total) Total FROM
            (
                SELECT s.DeviceID, 1 AS UrutanTampil, COALESCE(mc.CustomerName,s.CustomerName) AS CustomerName, 
                sd.ItemName AS ItemName, sd.Quantity, sd.SubTotal AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo 
                AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                LEFT JOIN mastercustomer mc ON mc.CustomerID=s.CustomerID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceID AND mc.DeviceNo=s.CustomerDeviceNo
                WHERE f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 2 AS UrutanTampil, COALESCE(mc.CustomerName,s.CustomerName) AS CustomerName, 
                'Diskon Final' AS ItemName, 0 AS Quantity, 
                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd
                ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                LEFT JOIN mastercustomer mc ON mc.CustomerID=s.CustomerID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceNo AND s.DeviceNo=s.CustomerDeviceNo
                WHERE f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                GROUP BY s.DeviceID,mc.CustomerName,s.CustomerName,s.Total,s.Tax,s.PriceIncludeTax,s.TaxValue
                UNION ALL
                SELECT s.DeviceID, 3 AS UrutanTampil, COALESCE(mc.CustomerName,s.CustomerName) AS CustomerName, 
                'Pajak' AS ItemName, 0 AS Quantity, s.TaxValue+s.TaxValueExclude AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                LEFT JOIN mastercustomer mc ON mc.CustomerID=s.CustomerID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceID AND mc.DeviceNo=s.DeviceNo
                WHERE s.Tax=1 AND s.PriceIncludeTax=0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending='false' AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 6 AS UrutanTampil, COALESCE(mc.CustomerName,s.CustomerName) AS CustomerName, 
                'Pembulatan' AS ItemName, 0 AS Quantity, s.Rounding AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                LEFT JOIN mastercustomer mc ON mc.CustomerID=s.CustomerID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceID AND mc.DeviceNo=s.CustomerDeviceNo
                WHERE s.Rounding<>0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending='false' AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 4 AS UrutanTampil, COALESCE(mc.CustomerName,s.CustomerName) AS CustomerName, 
                'Uang Muka' AS ItemName, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo
                LEFT JOIN mastercustomer mc ON mc.CustomerID=s.CustomerID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceID AND mc.DeviceNo=s.CustomerDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 5 AS UrutanTampil, COALESCE(mc.CustomerName,s.CustomerName) AS CustomerName, 
                'Dipotong Uang Muka' AS ItemName, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                LEFT JOIN mastercustomer mc ON mc.CustomerID=s.CustomerID AND s.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceID=s.DeviceID AND mc.DeviceNo=s.CustomerDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND s.Pending='false' AND " . $Condition . "
            ) Z GROUP BY Z.UrutanTampil, Z.ItemName,Z.CustomerName HAVING SUM(Z.Quantity) <> 0 ";

        $queryStringKategori = "SELECT Z.UrutanTampil, Z.CategoryName,
            CASE WHEN UrutanTampil=2 OR UrutanTampil=3 THEN 0 ELSE 
            SUM(Z.Quantity) END Quantity ,SUM(Z.Total) Total FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, COALESCE(mc.CategoryName,'Lainnya') AS CategoryName, sd.Quantity, sd.SubTotal AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo 
                AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                LEFT JOIN masteritem mi ON mi.ItemID=sd.ItemID AND mi.DeviceID=s.DeviceID AND mi.PerusahaanNo=sd.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
                WHERE 
                f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 2 AS UrutanTampil, 'Uang Muka' AS CategoryName, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 3 AS UrutanTampil, 'Dipotong Uang Muka' AS CategoryName, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0 AND s.Pending='false'
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) Z GROUP BY Z.UrutanTampil, Z.CategoryName HAVING SUM(Z.Quantity) <> 0 ";

        $queryStringKategoriDanItem = "SELECT Z.UrutanTampil, Z.CategoryName, Z.ItemName,
            CASE WHEN UrutanTampil=2 OR UrutanTampil=3 THEN 0 ELSE
            SUM(Z.Quantity) END Quantity ,SUM(Z.Total) Total FROM" .
                "(
                SELECT s.DeviceID, 1 AS UrutanTampil, COALESCE(mc.CategoryName,'Lainnya') AS CategoryName,
                sd.ItemName AS ItemName, sd.Quantity, sd.SubTotal AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo 
                AND s.Pending = 'false' AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                LEFT JOIN masteritem mi ON mi.ItemID=sd.ItemID AND mi.DeviceID=s.DeviceID AND mi.PerusahaanNo=sd.PerusahaanNo AND mi.DeviceNo=sd.ItemDeviceNo
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
                WHERE
                f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 2 AS UrutanTampil, 'Uang Muka' AS CategoryName,
                'Uang Muka' AS ItemName, 1 AS Quantity, s.CashDownPayment+s.BankDownPayment AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT s.DeviceID, 3 AS UrutanTampil, 'Dipotong Uang Muka' AS CategoryName,
                'Dipotong Uang Muka' AS ItemName, 1 AS Quantity, -(s.CashDownPayment+s.BankDownPayment) AS Total
                FROM " . $this->tabelsale . " s INNER JOIN options o ON o.DeviceID = s.DeviceID AND o.PerusahaanNo=s.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                WHERE s.CashDownPayment+s.BankDownPayment<>0 AND s.Pending='false'
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) Z GROUP BY Z.UrutanTampil, Z.CategoryName, Z.ItemName HAVING SUM(Z.Quantity) <> 0 ";

        $queryStringUser = "SELECT User, SUM(Total) Total
        FROM
        (   SELECT
            x.Outlet, x.User,
            SUM(x.Total) Total
            FROM
            (   SELECT
                CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
                o.DeviceID,CASE WHEN EditedBy='' THEN CreatedBy ELSE EditedBy END User,
                s.Total+s.Rounding-(CashDownPayment+BankDownPayment) Total
                FROM " . $this->tabelsale . " s
                INNER JOIN options o
                ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo
                WHERE s.Pending='false'
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
                UNION ALL
                SELECT
                CONCAT(o.CompanyName,' ',o.CompanyAddress) AS Outlet,
                o.DeviceID,CASE WHEN EditedBy='' THEN CreatedBy ELSE EditedBy END User,
                CashDownPayment+BankDownPayment Total
                FROM " . $this->tabelsale . " s
                INNER JOIN options o
                ON o.DeviceID = s.DeviceID AND s.PerusahaanNo=o.PerusahaanNo
                INNER JOIN opencloseoutlet f ON f.DeviceID = s.DeviceID AND f.PerusahaanNo=s.PerusahaanNo
                AND f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo
                WHERE CashDownPayment+BankDownPayment<>0
                AND f.OpenDate >= " . $this->CI->db->escape($this->dateStart) . "
                AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "
                AND " . $Condition . "
            ) x
            GROUP BY x.Outlet,
            x.User
        ) Z GROUP BY User";

        $queryString = $queryStringItem;
        if ($rekapper === "item") {
            $queryString = "SELECT DeviceNo AS PerangkatKe, OpenDate TanggalBuka,OpenTime JamBuka,
            CloseDate TanggalTutup,CloseTime JamTutup, ItemName AS Item, Quantity Qty,Total FROM (" . $queryStringItem . ") ff
            ORDER BY OpenDate,OpenTime,ff.UrutanTampil, ff.ItemName";
//            echo $queryString;
        } else if ($rekapper === "pelanggan") {
            $queryString = "SELECT CustomerName Pelanggan, Total FROM (" . $queryStringCustomer . ") ff
            ORDER BY ff.CustomerName";
        } else if ($rekapper === "pelangganitem") {
            $queryString = "SELECT CustomerName Pelanggan, ItemName AS Item, Quantity Qty, Total FROM (" . $queryStringCustomerAndItem . ") ff
            WHERE ff.Total<>0 ORDER BY ff.CustomerName, ff.UrutanTampil, ff.ItemName";
        } else if ($rekapper === "kategori") {
            $queryString = "SELECT CategoryName Kategori, Total FROM (" . $queryStringKategori . ") ff
            ORDER BY UrutanTampil,ff.CategoryName";
        } else if ($rekapper === "kategoriitem") {
            $queryString = "SELECT CategoryName Kategori, ItemName Item, Quantity, Total FROM (" . $queryStringKategoriDanItem . ") ff
            ORDER BY UrutanTampil,ff.CategoryName, ff.ItemName";
        } else if ($rekapper === "user") {
            $queryString = "SELECT User, Total FROM (" . $queryStringUser . ") ff
            ORDER BY ff.User";
        }

        return $queryString;
    }

    public function get_query_kategori_pershift($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule, $openID, $openDevNo) {
        $this->CheckDeviceID();

        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua")
            $whereOutlet = "";

        $extrakolom1 = "";
        $extrakolom2 = "";
        $joinExtraKolom = "";
        $groupByExtraKolom = "";
        $extrakolom3 = "";
        $extrakolom4 = "";
        $extrakolom2A = "";
        if ($ShowExtraKolom) {
            $extrakolom1 = ",CategoryName AS Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2 = ",'' Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
            $extrakolom3 = ",Kategori";
            $extrakolom4 = ",DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah";
            $joinExtraKolom = "
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo
                AND mc.DeviceNo = mi.CategoryDeviceNo
            ";

            if ($isUseVarianModule) {
                $extrakolom1 = str_replace("Kategori", "Kategori, sd.VarianName, COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1);
                $extrakolom2 = str_replace("Kategori", "Kategori, '' VarianName, '' PilihanExtra", $extrakolom2);
                $extrakolom2A = str_replace("Kategori", "Kategori, VarianName, PilihanExtra", $extrakolom2A);
                $extrakolom3 = str_replace("Kategori", "Kategori, VarianName AS Varian, PilihanExtra", $extrakolom3);
                $joinExtraKolom = $joinExtraKolom . "
                LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo
                AND sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sdm.DetailDeviceNo=sd.DeviceNo
                ";
                $groupByExtraKolom = "
                    GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.DetailID,sd.TransactionDeviceNo,sd.DeviceNo
                ";
            }
        }
        $pax = "";
        if ($IsDiningTable) {
            $pax = ", h.Pax";
        }
        $queryString = "\tSELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,
            CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, 
            COALESCE(mc.CategoryName,'Lainnya') Kategori,\n" .
                "\tCASE WHEN IFNULL(sd.Discount, '') = '' THEN sd.ItemName ELSE CONCAT(sd.ItemName, '<br/><i>Diskon ', sd.UnitPrice * sd.Quantity - sd.SubTotal, '</i>') END ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice, sd.MarkupValue ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,1 UrutanTampil" . $extrakolom1 . " \n" .
                "\tFROM sale s INNER JOIN saleitemdetail sd \n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo \n" .
                "\tLEFT JOIN mastercategory mc ON mi.CategoryID=mc.CategoryID AND mc.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=mc.DeviceID AND mi.CategoryDeviceNo=mc.DeviceNo \n" .
                $joinExtraKolom .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "  AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                $groupByExtraKolom .
                "\tUNION ALL
            SELECT OpenID,DeviceNo,OpenDate,OpenTime,CloseDate,CloseTime,Outlet, DeviceID, SaleNumber, SaleDate,SaleTime,CustomerName, Pax,Kategori, ItemName,Qty, UnitPrice, MarkupValue, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
            (
            SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate,s.SaleTime,
            CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, 
            Pax, 'Lainnya' Kategori,\n\t" .
                "CASE WHEN sd2.TransactionID IS NULL THEN 'Diskon Final'" .
                " ELSE CONCAT('Diskon Final : ', sd2.DiscountName) END ItemName, 1 AS Qty,\n" .
                "\tCASE WHEN sd2.TransactionID IS NULL THEN 0 ELSE sd2.DiscountValue END UnitPrice, 0 MarkupValue,
            CASE WHEN sd2.TransactionID IS NULL THEN
                CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END
            ELSE sd2.Discount END Discount,
            CASE WHEN sd2.TransactionID IS NULL THEN
                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal)
            ELSE 0 END SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tLEFT JOIN salediscountdetail sd2
            ON s.TransactionID=sd2.TransactionID AND s.PerusahaanNo=sd2.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd2.DeviceID AND s.DeviceNo=sd2.TransactionDeviceNo
            INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
            AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID, s.DeviceNo HAVING SUM(sd.SubTotal) <> s.Total\n
            ) x" .
                "";
        $queryString .= "
            UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, 'Lainnya' Kategori,'Pajak' ItemName, 1 AS Qty,\n " .
                "\ts.TaxValue, 0 MarkupValue, '' Discount, s.TaxValue SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND ((s.Tax=1 AND s.PriceIncludeTax=0) AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85) AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $queryString .= "
            UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, 
                Pax, 'Lainnya' Kategori,CONCAT(sd.TaxName, ' ', replace(CAST(sd.TaxPercent AS CHAR),'.0',''), '%') ItemName, 1 AS Qty,\n " .
                "\tCEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)), '' Discount, 0 MarkupValue, 
            CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN saleitemdetailtax sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo\n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.PriceIncludeTax=0 AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) .
                "
            GROUP BY s.TransactionID,s.DeviceNo,sd.TaxName
            ";

        $queryString .= "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
                CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, 'Lainnya' Kategori,'Pembulatan' ItemName, 1 AS Qty,\n " .
                "\ts.Rounding, '' Discount, 0 MarkupValue, s.Rounding SubTotal,6 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Rounding<>0 AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $queryString .= "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber,s.CreatedDate, s.CreatedTime,
                CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, 'Lainnya' Kategori,'Uang Muka' ItemName, 1 AS Qty,\n " .
                "\ts.CashDownPayment+s.BankDownPayment, '' Discount, 0 MarkupValue, s.CashDownPayment+s.BankDownPayment SubTotal,1 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $queryString .= "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
                CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, 'Lainnya' Kategori,'Dipotong Uang Muka' ItemName, 1 AS Qty,\n " .
                "\t-(s.CashDownPayment+s.BankDownPayment), '' Discount, 0 MarkupValue, -(s.CashDownPayment+s.BankDownPayment) SubTotal,4 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND s.Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $pax = "";
        if ($IsDiningTable) {
            $pax = ", h.Pax";
        }

        return "
            SELECT
                OpenID,
                DeviceID,
                DeviceNo,
                OpenDate,
                OpenTime,
                CloseDate,
                CloseTime, 
                h.Kategori,
                h.Item,
                SUM(Qty) Qty,
                HargaSatuan,
                SUM(Diskon) Diskon,
                SUM(SubTotal) SubTotal
            FROM (
                SELECT 
                    OpenID,
                    DeviceID,
                    DeviceNo,
                    OpenDate,
                    OpenTime,
                    CloseDate,
                    CloseTime, 
                    h.Kategori,
                    h.ItemName as Item" . $extrakolom3 . ",
                    h.Quantity as Qty,
                    h.UnitPrice as HargaSatuan,
                    h.Discount as Diskon,
                    h.SubTotal, 
                    CONCAT(h.OpenDate,h.CloseDate, h.Kategori) as ShiftKategori" . $extrakolom4 . " 
                FROM (" . $queryString . ") h
            ) h 
            WHERE OpenID = " . $openID . " AND DeviceNo = " . $openDevNo . " AND DeviceID = " . $this->CI->db->escape($this->Outlet) . "
            GROUP BY
                OpenID,
                DeviceID,
                DeviceNo,
                OpenDate,
                OpenTime,
                CloseDate,
                CloseTime, 
                h.Kategori,
                h.Item
            ORDER BY
                OpenID,
                DeviceID,
                DeviceNo,
                OpenDate,
                OpenTime,
                CloseDate,
                CloseTime, 
                h.Kategori,
                h.Item
        ";
    }

    public function get_query_opsimakan_pershift($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule, $openID, $openDevNo) {
        $this->CheckDeviceID();

        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua")
            $whereOutlet = "";

        $extrakolom1 = "";
        $extrakolom2 = "";
        $joinExtraKolom = "";
        $groupByExtraKolom = "";
        $extrakolom3 = "";
        $extrakolom4 = "";
        $extrakolom2A = "";
        if ($ShowExtraKolom) {
            $extrakolom1 = ",CategoryName AS Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2 = ",'' Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
            $extrakolom3 = ",Kategori";
            $extrakolom4 = ",DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah";
            $joinExtraKolom = "
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo
                AND mc.DeviceNo = mi.CategoryDeviceNo
            ";

            if ($isUseVarianModule) {
                $extrakolom1 = str_replace("Kategori", "Kategori, sd.VarianName, COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1);
                $extrakolom2 = str_replace("Kategori", "Kategori, '' VarianName, '' PilihanExtra", $extrakolom2);
                $extrakolom2A = str_replace("Kategori", "Kategori, VarianName, PilihanExtra", $extrakolom2A);
                $extrakolom3 = str_replace("Kategori", "Kategori, VarianName AS Varian, PilihanExtra", $extrakolom3);
                $joinExtraKolom = $joinExtraKolom . "
                LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo
                AND sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sdm.DetailDeviceNo=sd.DeviceNo
                ";
                $groupByExtraKolom = "
                    GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.DetailID,sd.TransactionDeviceNo,sd.DeviceNo
                ";
            }
        }
        $pax = "";
        if ($IsDiningTable) {
            $pax = ", h.Pax";
        }
        $queryString = "\tSELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,
            CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, s.NamaOpsiMakan,\n" .
                "\tCASE WHEN IFNULL(sd.Discount, '') = '' THEN sd.ItemName ELSE CONCAT(sd.ItemName, '<br/><i>Diskon ', sd.UnitPrice * sd.Quantity - sd.SubTotal, '</i>') END ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice, sd.MarkupValue ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,1 UrutanTampil" . $extrakolom1 . " \n" .
                "\tFROM sale s INNER JOIN saleitemdetail sd \n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo \n" .
                $joinExtraKolom .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "  AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                $groupByExtraKolom .
                "\tUNION ALL
            SELECT OpenID,DeviceNo,OpenDate,OpenTime,CloseDate,CloseTime,Outlet, DeviceID, SaleNumber, SaleDate,SaleTime,CustomerName, Pax,NamaOpsiMakan, ItemName,Qty, UnitPrice, MarkupValue, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
            (
            SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate,s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, s.NamaOpsiMakan,\n\t" .
                "CASE WHEN sd2.TransactionID IS NULL THEN 'Diskon Final'" .
                " ELSE CONCAT('Diskon Final : ', sd2.DiscountName) END ItemName, 1 AS Qty,\n" .
                "\tCASE WHEN sd2.TransactionID IS NULL THEN 0 ELSE sd2.DiscountValue END UnitPrice, 0 MarkupValue,
            CASE WHEN sd2.TransactionID IS NULL THEN
                CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END
            ELSE sd2.Discount END Discount,
            CASE WHEN sd2.TransactionID IS NULL THEN
                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal)
            ELSE 0 END SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tLEFT JOIN salediscountdetail sd2
            ON s.TransactionID=sd2.TransactionID AND s.PerusahaanNo=sd2.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd2.DeviceID AND s.DeviceNo=sd2.TransactionDeviceNo
            INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
            AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID, s.DeviceNo HAVING SUM(sd.SubTotal) <> s.Total\n
            ) x";
        $queryString .= "
            UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, s.NamaOpsiMakan,'Pajak' ItemName, 1 AS Qty,\n " .
                "\ts.TaxValue, 0 MarkupValue, '' Discount, s.TaxValue SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND ((s.Tax=1 AND s.PriceIncludeTax=0) AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85) AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $queryString .= "
            UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, 
                Pax, s.NamaOpsiMakan,CONCAT(sd.TaxName, ' ', replace(CAST(sd.TaxPercent AS CHAR),'.0',''), '%') ItemName, 1 AS Qty,\n " .
                "\tCEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)), '' Discount, 0 MarkupValue, 
            CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN saleitemdetailtax sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo\n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.PriceIncludeTax=0 AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) .
                "
            GROUP BY s.TransactionID,s.DeviceNo,sd.TaxName
            ";

        $queryString .= "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
                CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, s.NamaOpsiMakan,'Pembulatan' ItemName, 1 AS Qty,\n " .
                "\ts.Rounding, '' Discount, 0 MarkupValue, s.Rounding SubTotal,6 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Rounding<>0 AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $queryString .= "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber,s.CreatedDate, s.CreatedTime,
                CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, s.NamaOpsiMakan,'Uang Muka' ItemName, 1 AS Qty,\n " .
                "\ts.CashDownPayment+s.BankDownPayment, '' Discount, 0 MarkupValue, s.CashDownPayment+s.BankDownPayment SubTotal,1 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $queryString .= "
                UNION ALL
                SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
                CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, s.NamaOpsiMakan,'Dipotong Uang Muka' ItemName, 1 AS Qty,\n " .
                "\t-(s.CashDownPayment+s.BankDownPayment), '' Discount, 0 MarkupValue, -(s.CashDownPayment+s.BankDownPayment) SubTotal,4 UrutanTampil" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND s.Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd);
        $pax = "";
        if ($IsDiningTable) {
            $pax = ", h.Pax";
        }

        return "
            SELECT
                OpenID,
                DeviceID,
                DeviceNo,
                OpenDate,
                OpenTime,
                CloseDate,
                CloseTime, 
                h.NamaOpsiMakan,
                h.Item,
                SUM(Qty) Qty,
                HargaSatuan,
                MarkupValue,
                SUM(Diskon) Diskon,
                SUM(SubTotal) SubTotal
            FROM (
                SELECT 
                    OpenID,
                    DeviceID,
                    DeviceNo,
                    OpenDate,
                    OpenTime,
                    CloseDate,
                    CloseTime, 
                    h.NamaOpsiMakan,
                    h.ItemName as Item" . $extrakolom3 . ",
                    h.Quantity as Qty,
                    h.UnitPrice as HargaSatuan,
                    h.MarkupValue as MarkupValue,
                    h.Discount as Diskon,
                    h.SubTotal, 
                    CONCAT(h.OpenDate,h.CloseDate, h.NamaOpsiMakan) as ShiftOpsiMakan" . $extrakolom4 . " 
                FROM (" . $queryString . ") h
            ) h 
            WHERE OpenID = " . $openID . " AND DeviceNo = " . $openDevNo . " AND DeviceID = " . $this->CI->db->escape($this->Outlet) . "
            GROUP BY
                OpenID,
                DeviceID,
                DeviceNo,
                OpenDate,
                OpenTime,
                CloseDate,
                CloseTime, 
                h.NamaOpsiMakan,
                h.Item
            ORDER BY
                OpenID,
                DeviceID,
                DeviceNo,
                OpenDate,
                OpenTime,
                CloseDate,
                CloseTime, 
                h.NamaOpsiMakan,
                h.Item
        ";
    }

    public function get_query_variant_pershift($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule, $openID, $openDevNo) {
        $this->CheckDeviceID();

        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua")
            $whereOutlet = "";

        $extrakolom1 = "";
        $extrakolom2 = "";
        $joinExtraKolom = "";
        $groupByExtraKolom = "";
        $extrakolom3 = "";
        $extrakolom4 = "";
        $extrakolom2A = "";
        if ($ShowExtraKolom) {
            $extrakolom1 = ",CategoryName AS Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2 = ",'' Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
            $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
            $extrakolom3 = ",Kategori";
            $extrakolom4 = ",DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah";
            $joinExtraKolom = "
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo
                AND mc.DeviceNo = mi.CategoryDeviceNo
            ";

            if ($isUseVarianModule) {
                $extrakolom1 = str_replace("Kategori", "Kategori,   COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1);
                $extrakolom2 = str_replace("Kategori", "Kategori,  '' PilihanExtra", $extrakolom2);
                $extrakolom2A = str_replace("Kategori", "Kategori,   PilihanExtra", $extrakolom2A);
                $extrakolom3 = str_replace("Kategori", "Kategori,  PilihanExtra", $extrakolom3);

                $joinExtraKolom = $joinExtraKolom . "
                LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo
                AND sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sdm.DetailDeviceNo=sd.DeviceNo
                ";
                $groupByExtraKolom = "
                    GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.DetailID,sd.TransactionDeviceNo,sd.DeviceNo
                ";
            }
        }
        $pax = "";
        if ($IsDiningTable) {
            $pax = ", h.Pax";
        }
        $queryString = "\tSELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,
            CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
            CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, sd.VarianName,\n" .
                "\tCASE WHEN IFNULL(sd.Discount, '') = '' THEN sd.ItemName ELSE CONCAT(sd.ItemName, '<br/><i>Diskon ', sd.UnitPrice * sd.Quantity - sd.SubTotal, '</i>') END ItemName, 
            (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,1 UrutanTampil" . $extrakolom1 . " \n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo \n" .
                $joinExtraKolom .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "  AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                $groupByExtraKolom .
                "\t UNION ALL
            SELECT OpenID,DeviceNo,OpenDate,OpenTime,CloseDate,CloseTime,Outlet, DeviceID, SaleNumber, SaleDate,SaleTime,CustomerName, Pax,VarianName, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
            (
            SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate,s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, sd.VarianName,\n\t" .
                "CASE WHEN sd2.TransactionID IS NULL THEN 'Diskon Final'" .
                " ELSE CONCAT('Diskon Final : ', sd2.DiscountName) END ItemName, 1 AS Qty,\n" .
                "\tCASE WHEN sd2.TransactionID IS NULL THEN 0 ELSE sd2.DiscountValue END UnitPrice,
            CASE WHEN sd2.TransactionID IS NULL THEN
                CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END
            ELSE sd2.Discount END Discount,
            CASE WHEN sd2.TransactionID IS NULL THEN
                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal)
            ELSE 0 END SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
                "\tLEFT JOIN salediscountdetail sd2
            ON s.TransactionID=sd2.TransactionID AND s.PerusahaanNo=sd2.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd2.DeviceID AND s.DeviceNo=sd2.TransactionDeviceNo
            INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
            AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID, s.DeviceNo HAVING SUM(sd.SubTotal) <> s.Total\n
            ) x";
        /* _____pajak tidak di tampilkan_____
          $queryString .= "
          UNION ALL
          SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, sd.VarianName,'Pajak' ItemName, 1 AS Qty,\n " .
          "\ts.TaxValue, '' Discount, s.TaxValue SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
          "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
          "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
          "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
          "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
          "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND ((s.Tax=1 AND s.PriceIncludeTax=0) AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85) AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd); */

        /* ____tidak di tampilkan juga_____
          $queryString .= "
          UNION ALL
          SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName,
          Pax, '' as VarianName, CONCAT(sd.TaxName, ' ', replace(CAST(sd.TaxPercent AS CHAR),'.0',''), '%') ItemName, 1 AS Qty,\n " .
          "\tCEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)), '' Discount,
          CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
          "\tFROM " . $this->tabelsale . " s INNER JOIN saleitemdetailtax sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo\n" .
          "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
          "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
          "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.PriceIncludeTax=0 AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) .
          "
          GROUP BY s.TransactionID,s.DeviceNo,sd.TaxName
          "; */

        /*
          _____ tidak butuh pembulatan data_____
          $queryString .= "
          UNION ALL
          SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
          CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable)
          ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, sd.VarianName,'Pembulatan' ItemName, 1 AS Qty,\n " .
          "\ts.Rounding, '' Discount, s.Rounding SubTotal,6 UrutanTampil" . $extrakolom2 . " \n" .
          "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
          "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
          "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
          "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
          "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Rounding<>0 AND Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd); */
        /* _____uang muka tidak di tampikan_____  
          $queryString .= "
          UNION ALL
          SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber,s.CreatedDate, s.CreatedTime,
          CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable)
          ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, sd.VarianName,'Uang Muka' ItemName, 1 AS Qty,\n " .
          "\ts.CashDownPayment+s.BankDownPayment, '' Discount, s.CashDownPayment+s.BankDownPayment SubTotal,1 UrutanTampil" . $extrakolom2 . " \n" .
          "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
          "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
          "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.DownPaymentOpenID AND f.DeviceNo=s.DownPaymentOpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
          "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
          "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd); */
        /* _____DP tidak perlu tampil jg 
          $queryString .=
          UNION ALL
          SELECT f.OpenID,f.DeviceNo,f.OpenDate,f.OpenTime,f.CloseDate,f.CloseTime,CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,s.SaleDate, s.SaleTime,
          CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable)
          ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax, sd.VarianName,'Dipotong Uang Muka' ItemName, 1 AS Qty,\n " .
          "\t-(s.CashDownPayment+s.BankDownPayment), '' Discount, -(s.CashDownPayment+s.BankDownPayment) SubTotal,4 UrutanTampil" . $extrakolom2 . " \n" .
          "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
          "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
          "\tINNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID \n" .
          "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
          "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND s.Pending='false' AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd); */
        $pax = "";
        if ($IsDiningTable) {
            $pax = ", h.Pax";
        }
        $sql = "
            SELECT
                OpenID,
                DeviceID,
                DeviceNo,
                OpenDate,
                OpenTime,
                CloseDate,
                CloseTime, 
                h.VarianName,
                h.Item,
                SUM(Qty) Qty,
                HargaSatuan,
                SUM(Diskon) Diskon,
                SUM(SubTotal) SubTotal
            FROM (
                SELECT 
                    OpenID,
                    DeviceID,
                    DeviceNo,
                    OpenDate,
                    OpenTime,
                    CloseDate,
                    CloseTime, 
                    h.VarianName,
                    h.ItemName as Item" . $extrakolom3 . ",
                    h.Quantity as Qty,
                    h.UnitPrice as HargaSatuan,
                    h.Discount as Diskon,
                    h.SubTotal, 
                    CONCAT(h.OpenDate,h.CloseDate, h.VarianName) as ShiftVarianName" . $extrakolom4 . " 
                FROM (" . $queryString . ") h
            ) h 
            WHERE OpenID = " . $openID . " AND DeviceNo= " . $openDevNo . " AND DeviceID = " . $this->CI->db->escape($this->Outlet) . "
            GROUP BY
                OpenID,
                DeviceID,
                DeviceNo,
                OpenDate,
                OpenTime,
                CloseDate,
                CloseTime, 
                h.VarianName,
                h.Item
            ORDER BY
                OpenID,
                DeviceID,
                DeviceNo,
                OpenDate,
                OpenTime,
                CloseDate,
                CloseTime, 
                h.VarianName,
                h.Item
        ";
        return $sql;
    }

    public function get_query_pilihanekstra_pershift($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule, $openID, $openDevNo) {
        $this->CheckDeviceID();

        $whereOutlet = " and s.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua")
            $whereOutlet = "";

        $querystring = "
                SELECT 
                s.DeviceID, 1 AS UrutanTampil, 
                sd.ItemName AS ItemName, 
                sdx.ModifierName AS KelompokPilihan, 
                sdx.ChoiceName AS Pilihan, 
                sd.Quantity * (CASE WHEN sdx.QtyChoice<>0 THEN sdx.QtyChoice ELSE 1 END) Quantity, sdx.ChoicePrice AS Harga,
                sd.Quantity * (CASE WHEN sdx.QtyChoice<>0 THEN sdx.QtyChoice ELSE 1 END) * sdx.ChoicePrice AS Total
                
                FROM " . $this->tabelsale . " s 
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo 
                INNER JOIN saleitemdetailmodifier sdx ON sdx.DetailID=sd.DetailID AND sd.PerusahaanNo=sdx.PerusahaanNo AND sdx.DeviceID=s.DeviceID AND sd.DeviceNo=sdx.DetailDeviceNo
                INNER JOIN opencloseoutlet f ON f.OpenID=s.OpenID AND f.DeviceNo=s.OpenDeviceNo AND s.PerusahaanNo=f.PerusahaanNo AND s.DeviceID=f.DeviceID
                INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . "
                LEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo 
                WHERE o.PerusahaanNo=" . $this->NoPerusahaan . " 
                    AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 
                    AND f.OpenDate>=" . $this->CI->db->escape($this->dateStart) . " 
                    AND f.OpenDate <= " . $this->CI->db->escape($this->dateEnd) . "  
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "
                    AND f.openID = " . $openID . "
                    AND f.DeviceNo = " . $openDevNo . $whereOutlet;

        $sql = "select UrutanTampil, 
                        ItemName, 
                        KelompokPilihan, 
                        Pilihan, 
                        sum(Quantity) as Quantity,
                        sum(Total) as Total from (" . $querystring . ") x group by UrutanTampil, ItemName, KelompokPilihan, Pilihan ";

        return $sql;
    }

    public function get_list_shift($dateStart, $dateEnd) {
        return "
            SELECT 
                OpenID,
                OpenDate,
                OpenTime,
                CASE WHEN IFNULL(CloseDate, '') = '' THEN '" . date('Y-m-d') . "' ELSE CloseDate END CloseDate,
                CASE WHEN IFNULL(CloseTime, '') = '' THEN '23:59' ELSE CloseTime END CloseTime,
                DeviceNo
            FROM opencloseoutlet oco
            WHERE
                PerusahaanNo = " . $this->NoPerusahaan . "
                AND DeviceID = " . $this->CI->db->escape($this->Outlet) . "
                AND (
                    (
                        OpenDate >= " . $this->CI->db->escape($dateStart) . " AND OpenDate <= " . $this->CI->db->escape($dateEnd) . "
                    )
                )
        ";
    }

    public function get_query_pesananbelumlunas($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule) {
        $this->CheckDeviceID();
        //$whereOutlet = " WHERE h.Outlet = " . $this->CI->db->escape($this->Outlet);
        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua")
            $whereOutlet = "";


        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $queryString = "\tSELECT 'Semua' AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,\n" .
                        "\tCOALESCE(mi.ItemName,sd.ItemName) ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,
                        CONCAT(EditedDate,', ',EditedTime) WaktuDiubah, 1 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=d.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                        " o.DeviceID IN (" . $strCabangs . ")  \n" .
                        "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " AND o.PerusahaanID=" . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tUNION ALL SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,COALESCE(s.CustomerName,'-') CustomerName,\n\t'" .
                        "Diskon Final" .
                        "' ItemName, 1 AS Qty,\n" .
                        "\t0,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END,
                        s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) ,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,2 UrutanTampil\n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID\n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID IN (" . $strCabangs . ") \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.SubTotal <> s.Total AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%' AND o.PerusahaanID=" . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tAND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID\n";
                $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,'Pajak' ItemName, 1 AS Qty,\n " .
                        "\ts.TaxValue+s.TaxValueExclude, '' Discount, s.TaxValue+s.TaxValueExclude SubTotal,CreatedBy DibuatOleh,
            CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat,
            EditedBy DiubahOleh,
            CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah, 3 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Tax=1 AND s.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
                return "SELECT h.Outlet, h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
                        . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah  FROM (" . $queryString . ") h
                        ORDER BY h.SaleDateTime,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
            } else {
                $queryString = "\tSELECT 'Semua' AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,\n" .
                        "\tCOALESCE(mi.ItemName,sd.ItemName) ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,1 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo " .
                        "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                        "\tUNION ALL SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,COALESCE(s.CustomerName,'-') CustomerName,\n\t'" .
                        "Diskon Final" .
                        "' ItemName, 1 AS Qty,\n" .
                        "\t0,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.Total ELSE s.FinalDiscount END,
                        s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-SUM(sd.SubTotal) ,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,2 UrutanTampil\n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.SubTotal <> s.Total AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%' AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tAND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID\n";
                $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,'Pajak' ItemName, 1 AS Qty,\n " .
                        "\ts.TaxValue, '' Discount, s.TaxValue SubTotal,CreatedBy DibuatOleh,
            CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat,
            EditedBy DiubahOleh,
            CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah,3 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Tax=1 AND s.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
                return "SELECT h.Outlet, h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
                        . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah  FROM (" . $queryString . ") h" . $whereOutlet . "
                        ORDER BY h.UrutanTampil,h.SaleDateTime,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
            }
        } else {
            $extrakolom1 = "";
            $extrakolom2 = "";
            $joinExtraKolom = "";
            $groupByExtraKolom = "";
            $extrakolom3 = "";
            $extrakolom4 = "";
            $extrakolom2A = "";
            if ($ShowExtraKolom) {
                $extrakolom1 = ",CategoryName AS Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2 = ",'' Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
                $extrakolom3 = ",Kategori";
                $extrakolom4 = ",DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah";
                $joinExtraKolom = "
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
";

                if ($isUseVarianModule) {
                    $extrakolom1 = str_replace("Kategori", "Kategori, sd.VarianName, COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1);
                    $extrakolom2 = str_replace("Kategori", "Kategori, '' VarianName, '' PilihanExtra", $extrakolom2);
                    $extrakolom2A = str_replace("Kategori", "Kategori, VarianName, PilihanExtra", $extrakolom2A);
                    $extrakolom3 = str_replace("Kategori", "Kategori, VarianName AS Varian, PilihanExtra", $extrakolom3);
                    $joinExtraKolom = $joinExtraKolom . "
                    LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo AND
                    sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sd.DeviceNo=sdm.DetailDeviceNo
                    ";
                    $groupByExtraKolom = "
GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.TransactionDeviceNo,sd.DetailID,sd.DeviceNo
";
                }
            }
            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }
            $queryString = "\tSELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n" .
                    "\tsd.ItemName ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,1 UrutanTampil" . $extrakolom1 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                    "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                    " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo \n" . $joinExtraKolom .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "  AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                    $groupByExtraKolom .
//                "\tUNION ALL
//SELECT Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
// (
//     SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,',', s.SaleTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t'" .
//                "Diskon Final" .
//                "' ItemName, 1 AS Qty,\n" .
//                "\t0 UnitPrice,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END Discount,
//                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
//                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
//                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
//                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
//                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
//                AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85
//     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID HAVING SUM(sd.SubTotal) <> s.Total\n
//  ) x
//" .
//                "\tUNION ALL
//SELECT Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
// (
//     SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,',', s.SaleTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t" .
//                "CONCAT('Diskon Final : ', sd.DiscountName) ItemName, 1 AS Qty,\n" .
//                "\tsd.DiscountValue UnitPrice,sd.Discount Discount,
//                0 SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
//                "\tFROM " . $this->tabelsale . " s INNER JOIN salediscountdetail sd\n" .
//                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
//                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
//                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . "
//     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
//  ) x
//";
                    "\tUNION ALL
SELECT Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
 (
     SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t" .
                    "CASE WHEN sd2.TransactionID IS NULL THEN 'Diskon Final'" .
                    " ELSE CONCAT('Diskon Final : ', sd2.DiscountName) END ItemName, 1 AS Qty,\n" .
                    "\tCASE WHEN sd2.TransactionID IS NULL THEN 0 ELSE sd2.DiscountValue END UnitPrice,
                CASE WHEN sd2.TransactionID IS NULL THEN
                    CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END
                ELSE sd2.Discount END Discount,
                CASE WHEN sd2.TransactionID IS NULL THEN
                    s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal)
                ELSE 0 END SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                    "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                    "\tLEFT JOIN salediscountdetail sd2
                ON s.TransactionID=sd2.TransactionID AND s.PerusahaanNo=sd2.PerusahaanNo AND s.DeviceNo=sd2.TransactionDeviceNo AND s.Pending = 'false' AND s.DeviceID=sd2.DeviceID
                INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID, s.DeviceNo HAVING SUM(sd.SubTotal) <> s.Total\n
  ) x
";
            $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pajak' ItemName, 1 AS Qty,\n " .
                    "\ts.TaxValue, '' Discount, s.TaxValue SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND ((s.Tax=1 AND s.PriceIncludeTax=0) AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85) AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
            $queryString .= "
            UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, 
                    Pax,CONCAT(sd.TaxName, ' ', replace(CAST(sd.TaxPercent AS CHAR),'.0',''), '%') ItemName, 1 AS Qty,\n " .
                    "\tCEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)), '' Discount, 
                CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN saleitemdetailtax sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo\n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) .
                    "
                GROUP BY s.TransactionID,sd.TaxName,s.DeviceNo
                ";

            $queryString .= "
                    UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pembulatan' ItemName, 1 AS Qty,\n " .
                    "\ts.Rounding, '' Discount, s.Rounding SubTotal,5 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Rounding<>0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

            $queryString .= "
                    UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber,CONCAT(s.CreatedDate, ', ', s.CreatedTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Uang Muka' ItemName, 1 AS Qty,\n " .
                    "\ts.CashDownPayment+s.BankDownPayment, '' Discount, s.CashDownPayment+s.BankDownPayment SubTotal,1 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND s.CreatedDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd);

            $queryString .= "
                    UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Dipotong Uang Muka' ItemName, 1 AS Qty,\n " .
                    "\t-(s.CashDownPayment+s.BankDownPayment), '' Discount, -(s.CashDownPayment+s.BankDownPayment) SubTotal,4 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }
//            echo "SELECT h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
//                . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal" . $extrakolom4 . " FROM (" . $queryString . ") h
//                  ORDER BY h.UrutanTampil,h.SaleDateTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'

            return "SELECT h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
                    . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal" . $extrakolom4 . " FROM (" . $queryString . ") h
                    ORDER BY h.UrutanTampil,h.SaleDateTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
        }
    }

    public function get_query_daftar_pilihanekstra($perusahaanNo) {
        return "
    SELECT
        CategoryName AS Pilihan Ekstra
    FROM
        mastercategory i
    WHERE
    i.DeviceID = " . $this->CI->db->escape($this->Outlet) . " AND i.PerusahaanNo =" . $this->CI->db->escape($perusahaanNo) . " ORDER BY i.CategoryName ASC";
    }

    public function get_query_penjualan_belum_lunas($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule) {
        $this->CheckDeviceID();

        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua")
            $whereOutlet = "";


        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $queryString = "\tSELECT 'Semua' AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,\n" .
                        "\tCOALESCE(mi.ItemName,sd.ItemName) ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,
                        CONCAT(EditedDate,', ',EditedTime) WaktuDiubah, 1 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=d.PerusahaanNo AND s.Pending = 'true' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                        " o.DeviceID IN (" . $strCabangs . ")  \n" .
                        "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND o.PerusahaanID=" . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tUNION ALL SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,COALESCE(s.CustomerName,'-') CustomerName,\n\t'" .
                        "Diskon Final" .
                        "' ItemName, 1 AS Qty,\n" .
                        "\t0,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END,
                        s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) ,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,2 UrutanTampil\n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID\n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID IN (" . $strCabangs . ") \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.SubTotal <> s.Total AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%' AND o.PerusahaanID=" . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tAND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID\n";
                $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,'Pajak' ItemName, 1 AS Qty,\n " .
                        "\ts.TaxValue+s.TaxValueExclude, '' Discount, s.TaxValue+s.TaxValueExclude SubTotal,CreatedBy DibuatOleh,
            CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat,
            EditedBy DiubahOleh,
            CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah, 3 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Tax=1 AND s.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
                return "SELECT h.Outlet, h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
                        . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah  FROM (" . $queryString . ") h
                        ORDER BY h.SaleDateTime,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
            } else {
                $queryString = "\tSELECT 'Semua' AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,\n" .
                        "\tCOALESCE(mi.ItemName,sd.ItemName) ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,1 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'true' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo " .
                        "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                        "\tUNION ALL SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,COALESCE(s.CustomerName,'-') CustomerName,\n\t'" .
                        "Diskon Final" .
                        "' ItemName, 1 AS Qty,\n" .
                        "\t0,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.Total ELSE s.FinalDiscount END,
                        s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-SUM(sd.SubTotal) ,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,2 UrutanTampil\n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.SubTotal <> s.Total AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%' AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tAND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID\n";
                $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,'Pajak' ItemName, 1 AS Qty,\n " .
                        "\ts.TaxValue, '' Discount, s.TaxValue SubTotal,CreatedBy DibuatOleh,
            CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat,
            EditedBy DiubahOleh,
            CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah,3 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Tax=1 AND s.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
                return "SELECT h.Outlet, h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
                        . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah  FROM (" . $queryString . ") h" . $whereOutlet . "
                        ORDER BY h.UrutanTampil,h.SaleDateTime,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
            }
        } else {
            $extrakolom1 = "";
            $extrakolom2 = "";
            $joinExtraKolom = "";
            $groupByExtraKolom = "";
            $extrakolom3 = "";
            $extrakolom4 = "";
            $extrakolom2A = "";
            if ($ShowExtraKolom) {
                $extrakolom1 = ",CategoryName AS Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2 = ",'' Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
                $extrakolom3 = ",Kategori";
                $extrakolom4 = ",DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah";
                $joinExtraKolom = "
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
";

                if ($isUseVarianModule) {
                    $extrakolom1 = str_replace("Kategori", "Kategori, sd.VarianName, COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1);
                    $extrakolom2 = str_replace("Kategori", "Kategori, '' VarianName, '' PilihanExtra", $extrakolom2);
                    $extrakolom2A = str_replace("Kategori", "Kategori, VarianName, PilihanExtra", $extrakolom2A);
                    $extrakolom3 = str_replace("Kategori", "Kategori, VarianName AS Varian, PilihanExtra", $extrakolom3);
                    $joinExtraKolom = $joinExtraKolom . "
                    LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo AND
                    sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sd.DeviceNo=sdm.DetailDeviceNo
                    ";
                    $groupByExtraKolom = "
GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.TransactionDeviceNo,sd.DetailID,sd.DeviceNo
";
                }
            }
            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }
            $queryString = "\tSELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber SaleNumber,CONCAT(s.CreatedDate,', ', s.CreatedTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n" .
                    "\tCASE WHEN sd.Note = '' THEN sd.ItemName ELSE 
                CONCAT(sd.ItemName,'<br><i>',sd.Note,'</i>') END ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,1 UrutanTampil" . $extrakolom1 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                    "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'true' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                    " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo \n" . $joinExtraKolom .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0  AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                    $groupByExtraKolom .
                    "\tUNION ALL
SELECT Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
 (
     SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber SaleNumber,CONCAT(s.CreatedDate,',', s.CreatedTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t" .
                    "CASE WHEN sd2.TransactionID IS NULL THEN 'Diskon Final'" .
                    " ELSE CONCAT('Diskon Final : ', sd2.DiscountName) END ItemName, 1 AS Qty,\n" .
                    "\tCASE WHEN sd2.TransactionID IS NULL THEN 0 ELSE sd2.DiscountValue END UnitPrice,
                CASE WHEN sd2.TransactionID IS NULL THEN
                    CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END
                ELSE sd2.Discount END Discount,
                CASE WHEN sd2.TransactionID IS NULL THEN
                    s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal)
                ELSE 0 END SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                    "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo AND s.Pending = 'true' AND s.DeviceID=sd.DeviceID \n" .
                    "\tLEFT JOIN salediscountdetail sd2
                ON s.TransactionID=sd2.TransactionID AND s.PerusahaanNo=sd2.PerusahaanNo AND s.DeviceNo=sd2.TransactionDeviceNo AND s.Pending = 'false' AND s.DeviceID=sd2.DeviceID
                INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " GROUP BY s.DeviceID, s.TransactionID, s.DeviceNo HAVING SUM(sd.SubTotal) <> s.Total\n
  ) x
";
            $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber SaleNumber,CONCAT(s.CreatedDate, ', ', s.CreatedTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pajak' ItemName, 1 AS Qty,\n " .
                    "\ts.TaxValue, '' Discount, s.TaxValue SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND ((s.Tax=1 AND s.PriceIncludeTax=0) AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85) AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
            $queryString .= "
            UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber,CONCAT(s.CreatedDate, ', ', s.CreatedTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, 
                    Pax,CONCAT(sd.TaxName, ' ', replace(CAST(sd.TaxPercent AS CHAR),'.0',''), '%') ItemName, 1 AS Qty,\n " .
                    "\tCEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)), '' Discount, 
                CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN saleitemdetailtax sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo\n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.PriceIncludeTax=0 AND Pending='true'
                GROUP BY s.TransactionID,sd.TaxName,s.DeviceNo
                ";

            $queryString .= "
                    UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber SaleNumber,CONCAT(s.CreatedDate, ', ', s.CreatedTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pembulatan' ItemName, 1 AS Qty,\n " .
                    "\ts.Rounding, '' Discount, s.Rounding SubTotal,5 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Rounding<>0 AND Pending='true'";

            $queryString .= "
                    UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber,CONCAT(s.CreatedDate, ', ', s.CreatedTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Uang Muka' ItemName, 1 AS Qty,\n " .
                    "\t-(s.CashDownPayment+s.BankDownPayment), '' Discount, -(s.CashDownPayment+s.BankDownPayment) SubTotal,1 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND Pending='true' AND s.CashDownPayment+s.BankDownPayment<>0";


            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }

            return "SELECT h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
                    . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal" . $extrakolom4 . " FROM (" . $queryString . ") h
                    ORDER BY h.UrutanTampil,h.SaleDateTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
        }
    }

    public function get_query_penjualan_by_customer($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule) {
        $this->CheckDeviceID();

        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua") {
            $whereOutlet = "";
        }

        if (strlen($this->Customer) > 0) {
            $tmp = explode('.', $this->Customer);
            $customerid = $tmp[0];
            $customerdeviceno = $tmp[1];
        } else {
            $customerid = '0';
            $customerdeviceno = '0';
        }


        if ($this->Outlet == "Semua") {
            $strCabangs = '';

            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }

            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);

                $queryString = "
                    SELECT 
                        'Semua' AS Outlet 
                        , o.DeviceID 
                        , s.CustomerDeviceNo 
                        , s.SaleNumber 
                        , CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime 
                        , COALESCE(s.CustomerID,0) CustomerID 
                        , COALESCE(s.CustomerName,'-') CustomerName 
                        , COALESCE(mi.ItemName,sd.ItemName) ItemName 
                        , (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity 
                        , sd.UnitPrice 
                        , sd.Discount 
                        , sd.SubTotal-sd.SubTotalCanceled SubTotal 
                        , CreatedBy DibuatOleh 
                        , CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat 
                        , EditedBy DiubahOleh 
                        , CONCAT(EditedDate,', ',EditedTime) WaktuDiubah 
                        , 1 UrutanTampil 
                    FROM " . $this->tabelsale . " s 
                    INNER JOIN " . $this->tabelsaleitemdetail . " sd ON 
                        s.TransactionID = sd.TransactionID 
                        AND s.PerusahaanNo = d.PerusahaanNo 
                        AND s.Pending = 'false' 
                        AND s.DeviceID = sd.DeviceID 
                    INNER JOIN options o ON 
                        s.DeviceID = o.DeviceID 
                        AND s.PerusahaanNo = o.PerusahaanNo 
                        AND o.DeviceID IN (" . $strCabangs . ") 
                    LEFT JOIN masteritem mi ON 
                        sd.ItemID = mi.ItemID 
                        AND sd.PerusahaanNo = mi.PerusahaanNo 
                        AND mi.DeviceID = sd.DeviceID 
                    WHERE 
                        o.PerusahaanNo = " . $this->NoPerusahaan . " 
                        AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) <> 0 
                        AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                        AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                        AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                    UNION ALL 
                    SELECT 
                        CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet 
                        , o.DeviceID 
                        , s.CustomerDeviceNo 
                        , s.SaleNumber 
                        , CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate 
                        , COALESCE(s.CustomerID,0) CustomerID 
                        , COALESCE(s.CustomerName,'-') CustomerName 
                        , 'Diskon Final' ItemName 
                        , 1 Quantity 
                        , 0 UnitPrice
                        , CASE WHEN s.FinalDiscount = 'null' THEN sd.SubTotal - (CASE WHEN s.Tax = 1 AND s.PriceIncludeTax = 0 THEN s.TaxValue ELSE 0 END) - s.TaxValueExclude - s.Total ELSE s.FinalDiscount END Discount
                        , s.Total - (CASE WHEN s.Tax = 1 AND s.PriceIncludeTax = 0 THEN s.TaxValue ELSE 0 END) - s.TaxValueExclude - SUM(sd.SubTotal) SubTotal
                        , CreatedBy DibuatOleh 
                        , CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat 
                        , EditedBy DiubahOleh 
                        , CONCAT(EditedDate,', ',EditedTime) WaktuDiubah 
                        , 2 UrutanTampil
                    FROM " . $this->tabelsale . " s 
                    INNER JOIN " . $this->tabelsaleitemdetail . " sd ON 
                        s.TransactionID = sd.TransactionID 
                        AND s.PerusahaanNo = sd.PerusahaanNo 
                        AND s.Pending = 'false' 
                        AND s.DeviceID = sd.DeviceID 
                    INNER JOIN options o ON 
                        s.DeviceID = o.DeviceID 
                        AND s.PerusahaanNo = o.PerusahaanNo 
                        AND o.DeviceID IN (" . $strCabangs . ") 
                    WHERE 
                        o.PerusahaanNo = " . $this->NoPerusahaan . " 
                        AND sd.SubTotal <> s.Total 
                        AND s.FinalDiscount <> '' 
                        AND s.FinalDiscount NOT LIKE '0%' 
                        AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                        AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                        AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                    GROUP BY 
                        s.DeviceID, s.TransactionID 
                ";

                $queryString .= "
                    UNION ALL 
                    SELECT 
                        CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet 
                        , o.DeviceID 
                        , s.CustomerDeviceNo 
                        , s.SaleNumber 
                        , CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime 
                        , COALESCE(s.CustomerID,0) CustomerID 
                        , COALESCE(s.CustomerName,'-') CustomerName 
                        , 'Pajak' ItemName 
                        , 1 Qty 
                        , s.TaxValue + s.TaxValueExclude 
                        , '' Discount 
                        , s.TaxValue + s.TaxValueExclude SubTotal 
                        , CreatedBy DibuatOleh 
                        , CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat 
                        , EditedBy DiubahOleh 
                        , CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah
                        , 3 UrutanTampil 
                    FROM " . $this->tabelsale . " s 
                    INNER JOIN options o ON 
                        s.DeviceID = o.DeviceID 
                        AND s.PerusahaanNo = o.PerusahaanNo 
                        AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                    WHERE 
                        o.PerusahaanNo = " . $this->NoPerusahaan . " 
                        AND s.Tax = 1 
                        AND s.PriceIncludeTax = 0 
                        AND Pending = 'false' 
                        AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                        AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

                return "
                    SELECT 
                        h.Outlet 
                        , h.SaleNumber Nomor 
                        , h.SaleDateTime Tanggal 
                        , h.CustomerID 
                        , h.CustomerName Pelanggan 
                        , h.ItemName Item 
                        , h.Quantity Qty 
                        , h.UnitPrice HargaSatuan 
                        , h.Discount Diskon 
                        , h.SubTotal 
                        , DibuatOleh 
                        , WaktuDibuat TglBuat 
                        , DiubahOleh 
                        , WaktuDiubah TglUbah 
                    FROM (" . $queryString . ") h 
                    WHERE
                        h.CustomerID = " . $this->CI->db->escape($customerid) . " 
                        AND h.CustomerDeviceNo = " . $this->CI->db->escape($customerdeviceno) . " 
                    ORDER BY 
                        h.SaleDateTime, h.Discount ASC, h.ItemName
                ";
            } else {
                $queryString = "
                    SELECT 
                        'Semua' Outlet 
                        , o.DeviceID 
                        , s.CustomerDeviceNo 
                        , s.SaleNumber 
                        , CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime 
                        , COALESCE(s.CustomerID,0) CustomerID 
                        , COALESCE(s.CustomerName,'-') CustomerName 
                        , COALESCE(mi.ItemName,sd.ItemName) ItemName
                        , (sd.Quantity - sd.QtyCanceled + sd.QtyCanceler) Quantity 
                        , sd.UnitPrice 
                        , sd.Discount 
                        , sd.SubTotal - sd.SubTotalCanceled SubTotal 
                        , CreatedBy DibuatOleh 
                        , CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat 
                        , EditedBy DiubahOleh 
                        , CONCAT(EditedDate,', ',EditedTime) WaktuDiubah 
                        , 1 UrutanTampil 
                    FROM " . $this->tabelsale . " s 
                    INNER JOIN " . $this->tabelsaleitemdetail . " sd ON 
                        s.TransactionID = sd.TransactionID 
                        AND s.PerusahaanNo = sd.PerusahaanNo 
                        AND s.Pending = 'false' 
                        AND s.DeviceID = sd.DeviceID 
                    INNER JOIN options o ON 
                        s.DeviceID = o.DeviceID 
                        AND s.PerusahaanNo = o.PerusahaanNo 
                    LEFT JOIN masteritem mi ON 
                        sd.ItemID = mi.ItemID 
                        AND sd.PerusahaanNo = mi.PerusahaanNo 
                        AND mi.DeviceID = sd.DeviceID 
                    WHERE 
                        o.PerusahaanNo = " . $this->NoPerusahaan . " 
                        AND (sd.Quantity - sd.QtyCanceled + sd.QtyCanceler) <> 0 
                        AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                        AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                        AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                    UNION ALL 
                    SELECT 
                        CONCAT(o.CompanyName, ' ',o.CompanyAddress) Outlet 
                        , o.DeviceID 
                        , s.CustomerDeviceNo 
                        , s.SaleNumber 
                        , CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate 
                        , COALESCE(s.CustomerID,0) CustomerID 
                        , COALESCE(s.CustomerName,'-') CustomerName 
                        , 'Diskon Final' ItemName 
                        , 1 Qty 
                        , 0 
                        , CASE WHEN s.FinalDiscount = 'null' THEN sd.SubTotal - (CASE WHEN s.Tax = 1 AND s.PriceIncludeTax = 0 THEN s.TaxValue ELSE 0 END) - s.Total ELSE s.FinalDiscount END 
                        , s.Total - (CASE WHEN s.Tax = 1 AND s.PriceIncludeTax = 0 THEN s.TaxValue ELSE 0 END) - SUM(sd.SubTotal) 
                        , CreatedBy DibuatOleh 
                        , CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat 
                        , EditedBy DiubahOleh 
                        , CONCAT(EditedDate,', ',EditedTime) WaktuDiubah 
                        , 2 UrutanTampil 
                    FROM " . $this->tabelsale . " s 
                    INNER JOIN " . $this->tabelsaleitemdetail . " sd ON 
                        s.TransactionID = sd.TransactionID 
                        AND s.PerusahaanNo = sd.PerusahaanNo 
                        AND s.Pending = 'false' 
                        AND s.DeviceID = sd.DeviceID 
                    INNER JOIN options o ON 
                        s.DeviceID = o.DeviceID 
                        AND s.PerusahaanNo = o.PerusahaanNo 
                    WHERE 
                        o.PerusahaanNo = " . $this->NoPerusahaan . " 
                        AND sd.SubTotal <> s.Total 
                        AND s.FinalDiscount <> '' 
                        AND s.FinalDiscount NOT LIKE '0%' 
                        AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                        AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                        AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                    GROUP BY 
                        s.DeviceID, s.TransactionID 
                ";

                $queryString .= "
                    UNION ALL 
                    SELECT 
                        CONCAT(o.CompanyName, ' ',o.CompanyAddress) Outlet 
                        , o.DeviceID 
                        , s.CustomerDeviceNo 
                        , s.SaleNumber 
                        , CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime 
                        , COALESCE(s.CustomerID,0) CustomerID 
                        , COALESCE(s.CustomerName,'-') CustomerName 
                        , 'Pajak' ItemName 
                        , 1 Qty 
                        , s.TaxValue 
                        , '' Discount 
                        , s.TaxValue SubTotal 
                        , CreatedBy DibuatOleh 
                        , CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat 
                        , EditedBy DiubahOleh 
                        , CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah 
                        , 3 UrutanTampil 
                    FROM " . $this->tabelsale . " s 
                    INNER JOIN options o ON 
                        s.DeviceID = o.DeviceID 
                        AND s.PerusahaanNo = o.PerusahaanNo 
                        AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                    WHERE 
                        o.PerusahaanNo = " . $this->NoPerusahaan . " 
                        AND s.Tax = 1 
                        AND s.PriceIncludeTax = 0 
                        AND Pending = 'false' 
                        AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                        AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

                return "
                    SELECT 
                        h.Outlet
                        , h.SaleNumber Nomor 
                        , h.SaleDateTime  Tanggal 
                        , h.CustomerID 
                        , h.CustomerName Pelanggan 
                        , h.ItemName Item 
                        , h.Quantity Qty
                        , h.UnitPrice HargaSatuan 
                        , h.Discount Diskon 
                        , h.SubTotal 
                        , DibuatOleh 
                        , WaktuDibuat TglBuat 
                        , DiubahOleh 
                        , WaktuDiubah TglUbah 
                    FROM (" . $queryString . ") h " .
                        $whereOutlet . " 
                        h.CustomerID = " . $this->CI->db->escape($customerid) . " 
                        AND h.CustomerDeviceNo = " . $this->CI->db->escape($customerdeviceno) . " 
                    ORDER BY 
                        h.UrutanTampil, h.SaleDateTime, h.Discount ASC, h.ItemName
                ";
            }
        } else {
            $extrakolom1 = "";
            $extrakolom2 = "";
            $joinExtraKolom = "";
            $groupByExtraKolom = "";
            $extrakolom3 = "";
            $extrakolom4 = "";
            $extrakolom2A = "";
            if ($ShowExtraKolom) {
                $extrakolom1 = ",CategoryName AS Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2 = ",'' Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
                $extrakolom3 = ",Kategori";
                $extrakolom4 = ",DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah";
                $joinExtraKolom = "
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
";

                if ($isUseVarianModule) {
                    $extrakolom1 = str_replace("Kategori", "Kategori, sd.VarianName, COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1);
                    $extrakolom2 = str_replace("Kategori", "Kategori, '' VarianName, '' PilihanExtra", $extrakolom2);
                    $extrakolom2A = str_replace("Kategori", "Kategori, VarianName, PilihanExtra", $extrakolom2A);
                    $extrakolom3 = str_replace("Kategori", "Kategori, VarianName AS Varian, PilihanExtra", $extrakolom3);
                    $joinExtraKolom = $joinExtraKolom . "
                    LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo AND
                    sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sd.DeviceNo=sdm.DetailDeviceNo
                    ";
                    $groupByExtraKolom = " GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.TransactionDeviceNo,sd.DetailID,sd.DeviceNo ";
                }
            }
            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }
            $queryString = " 
                SELECT 
                    CONCAT(o.CompanyName, ' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , s.CustomerDeviceNo 
                    , s.SaleNumber 
                    , CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime 
                    , COALESCE(s.CustomerID,0) CustomerID 
                    , CASE WHEN s.DiningTable <> '' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName 
                    , Pax 
                    , sd.ItemName ItemName 
                    , (sd.Quantity - sd.QtyCanceled + sd.QtyCanceler) Quantity 
                    , sd.UnitPrice 
                    , sd.Discount 
                    , sd.SubTotal - sd.SubTotalCanceled SubTotal 
                    , 1 UrutanTampil " . $extrakolom1 . " 
                FROM " . $this->tabelsale . " s 
                INNER JOIN " . $this->tabelsaleitemdetail . " sd ON 
                    s.TransactionID = sd.TransactionID 
                    AND s.PerusahaanNo = sd.PerusahaanNo 
                    AND s.Pending = 'false' 
                    AND s.DeviceID = sd.DeviceID 
                    AND s.DeviceNo = sd.TransactionDeviceNo 
                INNER JOIN options o ON 
                    s.DeviceID = o.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                LEFT JOIN masteritem mi ON 
                    sd.ItemID = mi.ItemID 
                    AND sd.PerusahaanNo = mi.PerusahaanNo 
                    AND mi.DeviceID = sd.DeviceID 
                    AND mi.DeviceNo = sd.ItemDeviceNo " . $joinExtraKolom . " 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND (sd.Quantity - sd.QtyCanceled + sd.QtyCanceler) <> 0 
                    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                    AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) .
                    $groupByExtraKolom . " 
                UNION ALL 
                SELECT 
                    Outlet 
                    , DeviceID 
                    , CustomerDeviceNo 
                    , SaleNumber 
                    , SaleDate 
                    , CustomerID 
                    , CustomerName 
                    , Pax
                    , ItemName 
                    , Qty
                    , UnitPrice
                    , Discount 
                    , SubTotal
                    , UrutanTampil " . $extrakolom2A . " 
                FROM (
                    SELECT 
                        CONCAT(o.CompanyName, ' ',o.CompanyAddress) Outlet 
                        , o.DeviceID 
                        , s.CustomerDeviceNo 
                        , s.SaleNumber 
                        , CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate 
                        , COALESCE(s.CustomerID,0) CustomerID 
                        , CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName 
                        , Pax 
                        , CASE WHEN sd2.TransactionID IS NULL THEN 'Diskon Final' ELSE CONCAT('Diskon Final : ', sd2.DiscountName) END ItemName 
                        , 1 AS Qty 
                        , CASE WHEN sd2.TransactionID IS NULL THEN 0 ELSE sd2.DiscountValue END UnitPrice 
                        , CASE WHEN sd2.TransactionID IS NULL THEN CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal - (CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END) - s.TaxValueExclude - s.Total ELSE s.FinalDiscount END ELSE sd2.Discount END Discount 
                        , CASE WHEN sd2.TransactionID IS NULL THEN s.Total - (CASE WHEN s.Tax = 1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END) - s.TaxValueExclude - SUM(sd.SubTotal) ELSE 0 END SubTotal 
                        , 2 UrutanTampil 
                        , s.Total " . $extrakolom2 . " 
                    FROM " . $this->tabelsale . " s 
                    INNER JOIN " . $this->tabelsaleitemdetail . " sd ON 
                        s.TransactionID = sd.TransactionID 
                        AND s.PerusahaanNo = sd.PerusahaanNo 
                        AND s.DeviceNo = sd.TransactionDeviceNo 
                        AND s.Pending = 'false' 
                        AND s.DeviceID = sd.DeviceID 
                    LEFT JOIN salediscountdetail sd2 ON 
                        s.TransactionID = sd2.TransactionID 
                        AND s.PerusahaanNo = sd2.PerusahaanNo 
                        AND s.DeviceNo = sd2.TransactionDeviceNo 
                        AND s.Pending = 'false' 
                        AND s.DeviceID = sd2.DeviceID
                    INNER JOIN options o ON 
                        s.DeviceID = o.DeviceID 
                        AND s.PerusahaanNo = o.PerusahaanNo 
                        AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                    WHERE 
                        o.PerusahaanNo = " . $this->NoPerusahaan . " 
                        AND s.FinalDiscount <> '' 
                        AND s.FinalDiscount NOT LIKE '0%' 
                        AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " 
                        AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                        AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " 
                    GROUP BY 
                        s.DeviceID, s.TransactionID, s.DeviceNo 
                    HAVING SUM(sd.SubTotal) <> s.Total
                ) x
            ";

            $queryString .= "
                UNION ALL 
                SELECT 
                    CONCAT(o.CompanyName, ' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , s.CustomerDeviceNo 
                    , s.SaleNumber 
                    , CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime 
                    , COALESCE(s.CustomerID,0) CustomerID 
                    , CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName 
                    , Pax 
                    , 'Pajak' ItemName 
                    , 1 AS Qty 
                    , s.TaxValue 
                    , '' Discount 
                    , s.TaxValue SubTotal 
                    , 3 UrutanTampil " . $extrakolom2 . " 
                FROM " . $this->tabelsale . " s 
                INNER JOIN options o ON 
                    s.DeviceID = o.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND (
                        (s.Tax = 1 AND s.PriceIncludeTax = 0) 
                        AND s.CreatedVersionCode < 85 
                        AND s.EditedVersionCode < 85
                    ) 
                    AND Pending = 'false' 
                    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

            $queryString .= "
                UNION ALL 
                SELECT 
                    CONCAT(o.CompanyName, ' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , s.CustomerDeviceNo 
                    , s.SaleNumber 
                    , CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime 
                    , COALESCE(s.CustomerID,0) CustomerID 
                    , CASE WHEN s.DiningTable <> '' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName 
                    , Pax 
                    , CONCAT(sd.TaxName, ' ', replace(CAST(sd.TaxPercent AS CHAR),'.0',''), '%') ItemName 
                    , 1 Qty 
                    , CEIL(SUM(sd.SubTotalAfterDiscount * sd.TaxPercent / 100)) 
                    , '' Discount 
                    , CEIL(SUM(sd.SubTotalAfterDiscount * sd.TaxPercent / 100)) SubTotal 
                    , 3 UrutanTampil " . $extrakolom2 . " 
                FROM " . $this->tabelsale . " s 
                INNER JOIN saleitemdetailtax sd ON 
                    s.DeviceID = sd.DeviceID 
                    AND s.PerusahaanNo = sd.PerusahaanNo 
                    AND s.TransactionID = sd.TransactionID 
                    AND s.DeviceNo = sd.TransactionDeviceNo
                INNER JOIN options o ON 
                    s.DeviceID = o.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND sd.PriceIncludeTax = 0 
                    AND Pending = 'false' 
                    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
                GROUP BY 
                    s.TransactionID, sd.TaxName, s.DeviceNo
            ";

            $queryString .= "
                UNION ALL 
                SELECT 
                    CONCAT(o.CompanyName, ' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , s.CustomerDeviceNo 
                    , s.SaleNumber 
                    , CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime 
                    , COALESCE(s.CustomerID,0) CustomerID 
                    , CASE WHEN s.DiningTable <> '' THEN CONCAT(CASE WHEN s.CustomerName = '' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName = '' THEN '-' ELSE s.CustomerName END) END CustomerName 
                    , Pax 
                    , 'Pembulatan' ItemName 
                    , 1 Qty 
                    , s.Rounding 
                    , '' Discount 
                    , s.Rounding SubTotal 
                    , 5 UrutanTampil " . $extrakolom2 . " 
                FROM " . $this->tabelsale . " s 
                INNER JOIN options o ON 
                    s.DeviceID = o.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND s.Rounding <> 0 
                    AND Pending = 'false' 
                    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

            $queryString .= "
                UNION ALL
                SELECT 
                    CONCAT(o.CompanyName, ' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , s.CustomerDeviceNo 
                    , s.SaleOrderNumber 
                    , CONCAT(s.CreatedDate, ', ', s.CreatedTime) SaleDateTime 
                    , COALESCE(s.CustomerID,0) CustomerID 
                    , CASE WHEN s.DiningTable <> '' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName = '' THEN '-' ELSE s.CustomerName END) END CustomerName 
                    , Pax 
                    , 'Uang Muka' ItemName 
                    , 1 Qty 
                    , s.CashDownPayment + s.BankDownPayment 
                    , '' Discount 
                    , s.CashDownPayment + s.BankDownPayment SubTotal 
                    , 1 UrutanTampil " . $extrakolom2 . " 
                FROM " . $this->tabelsale . " s 
                INNER JOIN options o ON 
                    s.DeviceID = o.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND s.CashDownPayment + s.BankDownPayment <> 0 
                    AND s.CreatedDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd);

            $queryString .= "
                UNION ALL 
                SELECT 
                    CONCAT(o.CompanyName, ' ',o.CompanyAddress) Outlet 
                    , o.DeviceID 
                    , s.CustomerDeviceNo 
                    , s.SaleNumber 
                    , CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime 
                    , COALESCE(s.CustomerID,0) CustomerID 
                    , CASE WHEN s.DiningTable <> '' THEN CONCAT(CASE WHEN s.CustomerName = '' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName 
                    , Pax 
                    , 'Dipotong Uang Muka' ItemName 
                    , 1 Qty 
                    , -(s.CashDownPayment + s.BankDownPayment) 
                    , '' Discount 
                    , -(s.CashDownPayment + s.BankDownPayment) SubTotal 
                    , 4 UrutanTampil " . $extrakolom2 . " 
                FROM " . $this->tabelsale . " s 
                INNER JOIN options o ON 
                    s.DeviceID = o.DeviceID 
                    AND s.PerusahaanNo = o.PerusahaanNo 
                    AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " 
                WHERE 
                    o.PerusahaanNo = " . $this->NoPerusahaan . " 
                    AND s.CashDownPayment + s.BankDownPayment <> 0 
                    AND Pending = 'false' 
                    AND s.SaleDate >= " . $this->CI->db->escape($this->dateStart) . " 
                    AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }

            return "
                SELECT 
                    h.SaleNumber Nomor 
                    , h.SaleDateTime Tanggal 
                    , h.CustomerName Pelanggan " .
                    $pax . " 
                    , h.ItemName Item 
                    " . $extrakolom3 . " 
                    , h.Quantity Qty 
                    , h.UnitPrice HargaSatuan 
                    , h.Discount Diskon 
                    , h.SubTotal " .
                    $extrakolom4 . " 
                FROM (" . $queryString . ") h 
                WHERE 
                    h.CustomerID = " . $this->CI->db->escape($customerid) . " 
                    AND h.CustomerDeviceNo = " . $this->CI->db->escape($customerdeviceno) . " 
                ORDER BY 
                    h.UrutanTampil, h.SaleDateTime, h.SaleNumber, h.Discount ASC, h.ItemName";
        }
    }

    public function get_query_pesanan_batal($isUseTaxModule, $IsDiningTable, $ShowExtraKolom, $isUseVarianModule) {
        $this->CheckDeviceID();
        //$whereOutlet = " WHERE h.Outlet = " . $this->CI->db->escape($this->Outlet);
        $whereOutlet = " WHERE h.DeviceID = " . $this->CI->db->escape($this->Outlet);
        if ($this->Outlet == "Semua")
            $whereOutlet = "";


        if ($this->Outlet == "Semua") {
            $strCabangs = '';
            foreach ($this->Cabangs as $cabang) {
                $strCabangs = $strCabangs . "," . $this->CI->db->escape($cabang->OutletID) . "";
            }
            if (strlen($strCabangs) > 0) {
                $strCabangs = substr($strCabangs, 1);
                $queryString = "\tSELECT 'Semua' AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,\n" .
                        "\tCOALESCE(mi.ItemName,sd.ItemName) ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,
                        CONCAT(EditedDate,', ',EditedTime) WaktuDiubah, 1 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=d.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                        " o.DeviceID IN (" . $strCabangs . ")  \n" .
                        "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " AND o.PerusahaanID=" . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tUNION ALL SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,COALESCE(s.CustomerName,'-') CustomerName,\n\t'" .
                        "Diskon Final" .
                        "' ItemName, 1 AS Qty,\n" .
                        "\t0,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END,
                        s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) ,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,2 UrutanTampil\n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID\n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID IN (" . $strCabangs . ") \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.SubTotal <> s.Total AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%' AND o.PerusahaanID=" . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tAND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID\n";
                $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,'Pajak' ItemName, 1 AS Qty,\n " .
                        "\ts.TaxValue+s.TaxValueExclude, '' Discount, s.TaxValue+s.TaxValueExclude SubTotal,CreatedBy DibuatOleh,
            CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat,
            EditedBy DiubahOleh,
            CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah, 3 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Tax=1 AND s.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
                return "SELECT h.Outlet, h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
                        . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah  FROM (" . $queryString . ") h
                        ORDER BY h.SaleDateTime,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
            } else {
                $queryString = "\tSELECT 'Semua' AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,\n" .
                        "\tCOALESCE(mi.ItemName,sd.ItemName) ItemName, (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler) Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,1 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo " .
                        "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND (sd.Quantity-sd.QtyCanceled+sd.QtyCanceler)<>0 AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                        "\tUNION ALL SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,COALESCE(s.CustomerName,'-') CustomerName,\n\t'" .
                        "Diskon Final" .
                        "' ItemName, 1 AS Qty,\n" .
                        "\t0,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.Total ELSE s.FinalDiscount END,
                        s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-SUM(sd.SubTotal) ,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah,2 UrutanTampil\n" .
                        "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                        "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.SubTotal <> s.Total AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%' AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . "\n" .
                        "\tAND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID\n";
                $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,COALESCE(s.CustomerName,'-') CustomerName,'Pajak' ItemName, 1 AS Qty,\n " .
                        "\ts.TaxValue, '' Discount, s.TaxValue SubTotal,CreatedBy DibuatOleh,
            CONCAT(CreatedDate, ', ', CreatedTime) WaktuDibuat,
            EditedBy DiubahOleh,
            CONCAT(EditedDate, ', ', EditedTime) WaktuDiubah,3 UrutanTampil \n" .
                        "\tFROM " . $this->tabelsale . " s \n" .
                        "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                        "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Tax=1 AND s.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
                return "SELECT h.Outlet, h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan,h.ItemName as Item,h.Quantity as Qty,"
                        . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal,DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah  FROM (" . $queryString . ") h" . $whereOutlet . "
                        ORDER BY h.UrutanTampil,h.SaleDateTime,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
            }
        } else {
            $extrakolom1 = "";
            $extrakolom2 = "";
            $joinExtraKolom = "";
            $groupByExtraKolom = "";
            $extrakolom3 = "";
            $extrakolom4 = "";
            $extrakolom2A = "";
            if ($ShowExtraKolom) {
                $extrakolom1 = ",CategoryName AS Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2 = ",'' Kategori,CreatedBy DibuatOleh,CONCAT(CreatedDate,', ',CreatedTime) WaktuDibuat, EditedBy DiubahOleh,CONCAT(EditedDate,', ',EditedTime) WaktuDiubah";
                $extrakolom2A = ",Kategori,DibuatOleh,WaktuDibuat,DiubahOleh,WaktuDiubah";
                $extrakolom3 = ",Kategori";
                $extrakolom4 = ",DibuatOleh,WaktuDibuat TglBuat,DiubahOleh,WaktuDiubah TglUbah";
                $joinExtraKolom = "
                LEFT JOIN mastercategory mc ON mc.CategoryID=mi.CategoryID AND mi.DeviceID=mc.DeviceID AND mi.PerusahaanNo=mc.PerusahaanNo AND mc.DeviceNo=mi.CategoryDeviceNo
";

                if ($isUseVarianModule) {
                    $extrakolom1 = str_replace("Kategori", "Kategori, sd.VarianName, COALESCE(GROUP_CONCAT(CONCAT(ModifierName,' ',ChoiceName) Separator ', ') , '') PilihanExtra", $extrakolom1);
                    $extrakolom2 = str_replace("Kategori", "Kategori, '' VarianName, '' PilihanExtra", $extrakolom2);
                    $extrakolom2A = str_replace("Kategori", "Kategori, VarianName, PilihanExtra", $extrakolom2A);
                    $extrakolom3 = str_replace("Kategori", "Kategori, VarianName AS Varian, PilihanExtra", $extrakolom3);
                    $joinExtraKolom = $joinExtraKolom . "
                    LEFT JOIN saleitemdetailmodifier sdm ON sdm.TransactionID=sd.TransactionID AND sdm.TransactionDeviceNo=sd.TransactionDeviceNo AND
                    sdm.PerusahaanNo=sd.PerusahaanNo AND sdm.DeviceID=sd.DeviceID AND sdm.DetailID=sd.DetailID AND sd.DeviceNo=sdm.DetailDeviceNo
                    ";
                    $groupByExtraKolom = "
GROUP BY sd.PerusahaanNo,sd.DeviceID,sd.TransactionID,sd.TransactionDeviceNo,sd.DetailID,sd.DeviceNo
";
                }
            }
            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }
            $queryString = "\tSELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n" .
                    "\tCASE WHEN sd.Note = '' THEN sd.ItemName ELSE 
                CONCAT(sd.ItemName,'<br><i>',sd.Note,'</i>') END ItemName, sd.Quantity Quantity, sd.UnitPrice ,sd.Discount, sd.SubTotal-sd.SubTotalCanceled SubTotal,1 UrutanTampil" . $extrakolom1 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd \n" .
                    "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID AND s.DeviceNo=sd.TransactionDeviceNo \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND " .
                    " o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tLEFT JOIN masteritem mi ON sd.ItemID=mi.ItemID AND sd.PerusahaanNo=mi.PerusahaanNo AND mi.DeviceID=sd.DeviceID AND mi.DeviceNo=sd.ItemDeviceNo \n" . $joinExtraKolom .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "  AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " \n" .
                    $groupByExtraKolom .
//                "\tUNION ALL
//SELECT Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
// (
//     SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,',', s.SaleTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t'" .
//                "Diskon Final" .
//                "' ItemName, 1 AS Qty,\n" .
//                "\t0 UnitPrice,CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END Discount,
//                s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal) SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
//                "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
//                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
//                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
//                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
//                AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85
//     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID HAVING SUM(sd.SubTotal) <> s.Total\n
//  ) x
//" .
//                "\tUNION ALL
//SELECT Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
// (
//     SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,',', s.SaleTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t" .
//                "CONCAT('Diskon Final : ', sd.DiscountName) ItemName, 1 AS Qty,\n" .
//                "\tsd.DiscountValue UnitPrice,sd.Discount Discount,
//                0 SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
//                "\tFROM " . $this->tabelsale . " s INNER JOIN salediscountdetail sd\n" .
//                "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
//                "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
//                "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . "
//     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . "
//  ) x
//";
                    "\tUNION ALL
SELECT Outlet, DeviceID, SaleNumber, SaleDate,CustomerName, Pax, ItemName,Qty, UnitPrice, Discount,SubTotal,UrutanTampil " . $extrakolom2A . " FROM
 (
     SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate,', ', s.SaleTime) SaleDate,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,\n\t" .
                    "CASE WHEN sd2.TransactionID IS NULL THEN 'Diskon Final'" .
                    " ELSE CONCAT('Diskon Final : ', sd2.DiscountName) END ItemName, 1 AS Qty,\n" .
                    "\tCASE WHEN sd2.TransactionID IS NULL THEN 0 ELSE sd2.DiscountValue END UnitPrice,
                CASE WHEN sd2.TransactionID IS NULL THEN
                    CASE WHEN s.FinalDiscount='null' THEN sd.SubTotal-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-s.Total ELSE s.FinalDiscount END
                ELSE sd2.Discount END Discount,
                CASE WHEN sd2.TransactionID IS NULL THEN
                    s.Total-(CASE WHEN s.Tax=1 AND s.PriceIncludeTax=0 THEN s.TaxValue ELSE 0 END)-s.TaxValueExclude-SUM(sd.SubTotal)
                ELSE 0 END SubTotal,2 UrutanTampil,s.Total" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN " . $this->tabelsaleitemdetail . " sd\n" .
                    "\tON s.TransactionID=sd.TransactionID AND s.PerusahaanNo=sd.PerusahaanNo AND s.DeviceNo=sd.TransactionDeviceNo AND s.Pending = 'false' AND s.DeviceID=sd.DeviceID \n" .
                    "\tLEFT JOIN salediscountdetail sd2
                ON s.TransactionID=sd2.TransactionID AND s.PerusahaanNo=sd2.PerusahaanNo AND s.DeviceNo=sd2.TransactionDeviceNo AND s.Pending = 'false' AND s.DeviceID=sd2.DeviceID
                INNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.FinalDiscount<>'' AND s.FinalDiscount NOT LIKE '0%'
     AND o.PerusahaanID = " . $this->CI->db->escape($this->clientPerusahaanID) . " AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) . " GROUP BY s.DeviceID, s.TransactionID, s.DeviceNo HAVING SUM(sd.SubTotal) <> s.Total\n
  ) x
";
            $queryString .= "UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pajak' ItemName, 1 AS Qty,\n " .
                    "\ts.TaxValue, '' Discount, s.TaxValue SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND ((s.Tax=1 AND s.PriceIncludeTax=0) AND s.CreatedVersionCode<85 AND s.EditedVersionCode<85) AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);
            $queryString .= "
            UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, 
                    Pax,CONCAT(sd.TaxName, ' ', replace(CAST(sd.TaxPercent AS CHAR),'.0',''), '%') ItemName, 1 AS Qty,\n " .
                    "\tCEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)), '' Discount, 
                CEIL(SUM(sd.SubTotalAfterDiscount*sd.TaxPercent/100)) SubTotal,3 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s INNER JOIN saleitemdetailtax sd ON s.DeviceID=sd.DeviceID AND s.PerusahaanNo=sd.PerusahaanNo AND s.TransactionID=sd.TransactionID AND s.DeviceNo=sd.TransactionDeviceNo\n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND sd.PriceIncludeTax=0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd) .
                    "
                GROUP BY s.TransactionID,sd.TaxName,s.DeviceNo
                ";

            $queryString .= "
                    UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Pembulatan' ItemName, 1 AS Qty,\n " .
                    "\ts.Rounding, '' Discount, s.Rounding SubTotal,5 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.Rounding<>0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

            $queryString .= "
                    UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleOrderNumber,CONCAT(s.CreatedDate, ', ', s.CreatedTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Uang Muka' ItemName, 1 AS Qty,\n " .
                    "\ts.CashDownPayment+s.BankDownPayment, '' Discount, s.CashDownPayment+s.BankDownPayment SubTotal,1 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND s.CreatedDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.CreatedDate <= " . $this->CI->db->escape($this->dateEnd);

            $queryString .= "
                    UNION ALL
                    SELECT CONCAT(o.CompanyName, ' ',o.CompanyAddress) AS Outlet,o.DeviceID,s.SaleNumber,CONCAT(s.SaleDate, ', ', s.SaleTime) SaleDateTime,
                    CASE WHEN s.DiningTable<>'' THEN CONCAT(CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END, '/', s.DiningTable) 
                    ELSE (CASE WHEN s.CustomerName='' THEN '-' ELSE s.CustomerName END) END CustomerName, Pax,'Dipotong Uang Muka' ItemName, 1 AS Qty,\n " .
                    "\t-(s.CashDownPayment+s.BankDownPayment), '' Discount, -(s.CashDownPayment+s.BankDownPayment) SubTotal,4 UrutanTampil" . $extrakolom2 . " \n" .
                    "\tFROM " . $this->tabelsale . " s \n" .
                    "\tINNER JOIN options o ON s.DeviceID=o.DeviceID AND s.PerusahaanNo=o.PerusahaanNo AND o.DeviceID = " . $this->CI->db->escape($this->Outlet) . " \n" .
                    "\tWHERE o.PerusahaanNo=" . $this->NoPerusahaan . " AND s.CashDownPayment+s.BankDownPayment<>0 AND Pending='false' AND s.SaleDate>=" . $this->CI->db->escape($this->dateStart) . " AND s.SaleDate <= " . $this->CI->db->escape($this->dateEnd);

            $pax = "";
            if ($IsDiningTable) {
                $pax = ", h.Pax";
            }
//            echo "SELECT h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
//                . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal" . $extrakolom4 . " FROM (" . $queryString . ") h
//                  ORDER BY h.UrutanTampil,h.SaleDateTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'

            return "SELECT h.SaleNumber as Nomor,h.SaleDateTime as Tanggal,h.CustomerName as Pelanggan" . $pax . ",h.ItemName as Item" . $extrakolom3 . ",h.Quantity as Qty,"
                    . "h.UnitPrice as HargaSatuan,h.Discount as Diskon,h.SubTotal" . $extrakolom4 . " FROM (" . $queryString . ") h WHERE h.Quantity < 0
                    ORDER BY h.UrutanTampil,h.SaleDateTime, h.SaleNumber,h.Discount ASC, h.ItemName"; //WHERE h.Outlet='" . $this->Outlet . "'
        }
    }

}
