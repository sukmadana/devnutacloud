<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Options extends CI_Model
{

    var $Table = "options";

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    protected function initDbMaster()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    function IsDeviceIDexist($devID)
    {
        $query = $this->db->get_where($this->Table, array('DeviceID' => $devID));
        $result = $query->result();
        $retval = array();
        if (count($result) > 0) {
            $retval = array('exist' => TRUE, 'name' => $result[0]->CompanyName);
        } else {
            $retval = array('exist' => FALSE, 'name' => '');
        }
        return $retval;
    }

    public function get_options($where)
    {
        $query = $this->db->get_where($this->Table, $where);
        return $query->row();
    }

    public function get_by_devid($devid)
    {
        $query = $this->db->get_where($this->Table, array('DeviceID' => $devid));
        $option = $query->row();
        return $option;
    }

    public function createorupdate_by_devid($idOutlet, $PurchaseModule, $StockModule, $MenuRacikan, $PriceVariation, $StockModifier, $SendReceiptToCustomerViaEmail, $DiningTable, $SupportBarcode, $namaoutlet, $alamat, $email, $no_telp = "")
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idOutlet, 'OptionID' => 1));
        $query = $this->_dbMaster->get($this->Table);
        $count = $query->num_rows();
        $perusahaanNo = getPerusahaanNo();
        if ($count > 0) {
            $update_param = array(
                'PurchaseModule' => $PurchaseModule,
                'StockModule' => $StockModule,
                'MenuRacikan' => $MenuRacikan,
                'PriceVariation' => $PriceVariation,
                'StockModifier' => $StockModifier,
                'SendReceiptToCustomerViaEmail' => $SendReceiptToCustomerViaEmail,
                'DiningTable' => $DiningTable,
                'SupportBarcode' => $SupportBarcode,
                'MobilePhone' => $no_telp,
            );
            if ($no_telp == "") {
                unset($update_param['MobilePhone']);
            }
            $this->_dbMaster->where(array('DeviceID' => $idOutlet, 'OptionID' => 1));
            $this->_dbMaster->update($this->Table, $update_param);

            $last_update_data =  array(
                "table"     => "modul",
                "column"    =>  array(
                    'PurchaseModule' => $PurchaseModule,
                    'StockModule' => $StockModule,
                    'MenuRacikan' => $MenuRacikan,
                    'PriceVariation' => $PriceVariation,
                    'StockModifier' => $StockModifier,
                    'SendReceiptToCustomerViaEmail' => $SendReceiptToCustomerViaEmail,
                    'DiningTable' => $DiningTable,
                    'SupportBarcode' => $SupportBarcode,
                )
            );
            $this->load->model('Firebasemodel');
            $this->Firebasemodel->push_firebase(
                $idOutlet,
                $last_update_data,
                1,
                1,
                $perusahaanNo,
                0
            );
        } else {
            $this->_dbMaster->insert($this->Table, array(
                //            `OptionID`,`Language`,`StockModule`,`FinanceModule`,`PurchaseModule`,`EDCModule`,`StockMinus`,`MoneyMinus`,
                'OptionID' => 1,
                'Language' => 1,
                'StockModule' => $StockModule,
                'FinanceModule' => 'true',
                'PurchaseModule' => $PurchaseModule,
                'EDCModule' => 'true',
                'StockMinus' => '1',
                'MoneyMinus' => '1',
                //`Tax`,`SaleLayoutMode`,`ShowSearchTextField`,`SupportBarcode`,`MultiAccount`,`CompanyName`,`CompanyAddress`,
                'Tax' => 'false',
                'SaleLayoutMode' => '1',
                'ShowSearchTextField' => 'false',
                'SupportBarcode' => 'false',
                'MultiAccount' => 'false',
                'CompanyName' => $namaoutlet,
                'CompanyAddress' => $alamat,
                //`CompanyLogoLink`,`CompanyPhone`,`PrintOnce`,`PrinterMacAddress`,`UseBluetoothPrinter`,`TglExpired`,`TglInstall`,
                'CompanyLogoLink' => '',
                'CompanyPhone' => '',
                'PrintOnce' => 'true',
                'PrinterMacAddress' => '',
                'UseBluetoothPrinter' => '',

                //`KodeBooking`,`KodeKupon`,`IsTrial`,`JudulPromo`,`DeskripsiPromo`,`Website`,`ImgPromoLink`,`GridSize`,`MenuRacikan`,
                'KodeBooking' => '',
                'KodeKupon' => '',
                'IsTrial' => 'true',
                'JudulPromo' => '',
                'DeskripsiPromo' => '',
                'Website' => '',
                'ImgPromoLink' => '',
                'GridSize' => 4,
                'MenuRacikan' => $MenuRacikan,
                //`TMPrinter`,`OnlineImagePath`,`CompanyEmail`,`MobilePhone`,`Varian`,`DeviceID`,`PerusahaanNo`,`PerusahaanID`,
                'TMPrinter' => '3',
                'OnlineImagePath' => '',
                'CompanyEmail' => $email,
                'MobilePhone' => $no_telp,
                'Varian' => 'Nuta',
                'DeviceID' => $idOutlet,
                'PerusahaanNo' => $perusahaanNo,
                'PerusahaanID' => getLoggedInUserID(),
                //`OutletID`,`CreatedVersionCode`,`EditedVersionCode`,`RowVersion`,`UsernameRegistrasi`,`CetakPesanan`,`FootNoteSatu`,
                'OutletID' => $idOutlet,
                'CreatedVersionCode' => 98,
                'EditedVersionCode' => 0,
                'RowVersion' => 0,
                'UsernameRegistrasi' => getLoggedInUsername(),
                'CetakPesanan' => 0,
                'FootNoteSatu' => 'Terima kasih atas kunjungan Anda',
                //`FootNoteDua`,`FootNoteTiga`,`FootNoteEmpat`,`PreviousDatabaseVersion`,`PriceIncludeTax`,`TaxPercent`,
                'FootNoteDua' => '',
                'FootNoteTiga' => '',
                'FootNoteEmpat' => '',
                'PreviousDatabaseVersion' => 0,
                'PriceIncludeTax' => 0,
                'TaxPercent' => 10,
                //            `PrintToKitchen`,`KitchenPrinterMacAddress`,`SendReceiptToCustomerViaEmail`,`PriceVariation`,
                'PrintToKitchen' => 0,
                'KitchenPrinterMacAddress' => '',
                'SendReceiptToCustomerViaEmail' => $SendReceiptToCustomerViaEmail,
                'PriceVariation' => $PriceVariation,
                //`DiningTable`,`NumColumnDiningTable`,`LoggedinUsername`,`FiturMejaAktifSampai`,`PrintToBar`,`StockModifier`,

                'DiningTable' => $DiningTable,
                'SupportBarcode' => $SupportBarcode,
                'NumColumnDiningTable' => 16,
                'LoggedinUsername' => '',
                'PrintToBar' => 0,
                'StockModifier' => $StockModifier,

                //`RoundingInTransaction`,`RoundingTo`,`PrintSaleSummaryInCloseOutlet`,`BarPrinterMacAddress`,`TglJamUpdate`
                'RoundingInTransaction' => 0,
                'RoundingTo' => 500,
                'PrintSaleSummaryInCloseOutlet' => 0,
                'BarPrinterMacAddress' => '',
            ));
        }
        return 1;
    }

    public function update_by_devid($idOutlet, $arraymodul)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idOutlet, 'OptionID' => 1));
        $query = $this->_dbMaster->get($this->Table);
        $count = $query->num_rows();

        if ($count > 0) {
            //            $oldoutlet = $query->result();
            $this->_dbMaster->where(array('DeviceID' => $idOutlet, 'OptionID' => 1));
            $this->_dbMaster->update($this->Table, $arraymodul);
        } else {
            return "Outlet tidak ditemukan di tabel options";
        }
        return 1;
    }
}
