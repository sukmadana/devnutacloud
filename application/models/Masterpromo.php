<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Masterpromo extends CI_Model {

    var $_tableName = "masterpromo";
    protected $_dbMaster;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->initDbMaster();
    }

    protected function initDbMaster() {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function begintrans() {
        $this->_dbMaster->trans_begin();
    }

    public function committrans() {
        $this->_dbMaster->trans_commit();
    }

    public function rollbacktrans() {
        $this->_dbMaster->trans_rollback();
    }

    public function createNewPromo($perusahaanno, $namapromo, $idoutlet, $jenispromo, $date, $time, $hari, $term, $get, $apply) {
        $this->_dbMaster->where(array('PerusahaanNo' => $perusahaanno, 'DeviceID' => $idoutlet,
            'lower(PromoTitle)' => strtolower($namapromo)));
        $query = $this->_dbMaster->get($this->_tableName);
        $count = $query->num_rows();
        if ($count > 0) {
            $result = $query->result();
            return 'Promo dengan judul ' . $namapromo . ' sudah ada. Silakan pakai judul lain.';
        }
        $termitems = is_array($term[1]) ? implode(',', $term[1]) : $term[1];
        if (empty($termitems) && empty($term[2]) && ($jenispromo == 1 || $jenispromo == 3)) {
            return 'Produk tidak boleh kosong';
        }
        //cek item double di waktu yang beririsan
        $hariberirisan = "".(($hari[0] == 'senin') ? 'OR ApplyMonday=1' : '').
                "".(($hari[1] == 'selasa') ? ' OR ApplyTuesday=1' : '').
                "".(($hari[2] == 'rabu') ? ' OR ApplyWednesday=1' : '').
                "".(($hari[3] == 'kamis') ? ' OR ApplyThursday=1' : '').
                "".(($hari[4] == 'jumat') ? ' OR ApplyFriday=1' : '').
                "".(($hari[5] == 'sabtu') ? ' OR ApplySaturday=1' : '').
                "".(($hari[6] == 'minggu') ? ' OR ApplySunday=1' : '');
        $sqlquery = "SELECT * FROM masterpromo WHERE PerusahaanNo=" . $perusahaanno .
                " AND DeviceID=" . $idoutlet . " AND PromoToDate>='".$date[0] . 
                "' AND PromoFromDate<='".$date[1]."' AND PromoToTime>='".$time[0] . 
                "' AND PromoFromTime<='".$time[1]."' AND (" . substr($hariberirisan, 3) . ")";
        //log_message('error', $sqlquery);
        $query = $this->_dbMaster->query($sqlquery);
        $count = $query->num_rows();
        if ($count > 0) {
            $this->load->model('Masteritem');
            $this->load->model('Kategori');
            $result = $query->result();
            foreach ($result as $row) {
                //log_message('error', 'tes promo beririsan');
                
                $arrayTermItems = explode(',',$term[1]);
                $arrayTermCategories = explode(',',$term[2]);
                //log_message('error', 'count promo kategori ' . count($arrayTermCategories));
                $arrayTermItemsExist = explode(',',$row->TermItems);
                $arrayTermCategoriesExist = explode(',',$row->TermCategories);
                for($i=0;$i<count($arrayTermCategories);$i++) {
                    for($j=0;$j<count($arrayTermCategoriesExist);$j++) {
                        // log_message('error', 'tes a promo beririsan ' . $arrayTermCategoriesExist[$j]);
                    //log_message('error', 'tes a promo beririsan ' . $arrayTermCategories[$i]);
                        if (!empty($arrayTermCategories[$i]) && $arrayTermCategories[$i] == $arrayTermCategoriesExist[$j]) {
                            $kategori = $this->Kategori->getByID($arrayTermCategories[$i],$idoutlet);
                            if($kategori == null) {
                                $namakategori = "Lainnya";
                            } else {
                                $namakategori = $kategori->CategoryName;
                            }
                            return "Kategori " . $namakategori . 
                                    ' sudah ada di promo berjudul ' . $row->PromoTitle . '. Silakan pilih kategori yang lain.';
                        }
                    }
                }
                for($i=0;$i<count($arrayTermItems);$i++) {
                    for($j=0;$j<count($arrayTermItemsExist);$j++) {
                    //log_message('error', 'tes c promo beririsan ' . $arrayTermItems[$i]);
                        if (!empty($arrayTermItems[$i]) && $arrayTermItems[$i] == $arrayTermItemsExist[$j]) {
                            log_message('error', 'gagal simpan promo ' . $row->TermItems . " " . $arrayTermItems[$i] . " " . date("Y-m-d H:i:s.u"));
                            return "Produk " . $this->Masteritem->getByID($arrayTermItems[$i],$idoutlet)->ItemName . 
                                    ' sudah ada di promo berjudul ' . $row->PromoTitle . '.<br>Silakan pilih produk lain.';
                        }
                    }
                }
            }
            //return "Promo dengan judul ' . $namapromo . ' sudah ada. Silakan pakai judul lain.';
        }
        $promoid = $this->getMaxPromoId($idoutlet);
        $attrib_masterpromo = array(
            'PerusahaanNo' => $perusahaanno,
            'DeviceID' => $idoutlet,
            'PromoID' => $promoid,
            'PromoTitle' => $namapromo,
            'PromoFromDate' => $date[0],
            'PromoToDate' => $date[1],
            'PromoFromTime' => $time[0],
            'PromoToTime' => $time[1],
            'ApplyMonday' => ($hari[0] == 'senin') ? '1' : '0',
            'ApplyTuesday' => ($hari[1] == 'selasa') ? '1' : '0',
            'ApplyWednesday' => ($hari[2] == 'rabu') ? '1' : '0',
            'ApplyThursday' => ($hari[3] == 'kamis') ? '1' : '0',
            'ApplyFriday' => ($hari[4] == 'jumat') ? '1' : '0',
            'ApplySaturday' => ($hari[5] == 'sabtu') ? '1' : '0',
            'ApplySunday' => ($hari[6] == 'minggu') ? '1' : '0',
            'PromoType' => $jenispromo,
            'TermQty' => $term[0],
            'TermItems' => is_array($term[1]) ? implode(',', $term[1]) : $term[1],
            'TermCategories' => $term[2],
            'TermTotal' => $term[3],
            'GetDiscountType' => $get[0],
            'GetDiscountValue' => $get[1],
            'GetItemQty' => $get[2],
            'GetItemID' => is_array($get[3]) ? implode(',', $get[3]) : $get[3],
            'ApplyMultiply' => ($apply == 'on') ? '1' : '0',
            'CreatedBy' => getLoggedInUsername(),
            'CreatedDate' => date('Y-m-d'),
            'CreatedTime' => date('H:i'),
            'EditedBy' => "",
            'EditedDate' => "",
            'EditedTime' => ""
        );

        $result_insert_masterpromo = $this->_dbMaster->insert($this->_tableName, $attrib_masterpromo);
        if ($result_insert_masterpromo) {
            return $promoid;
        } else {
            log_message('error', 'Gagal insert master promo : ' . $result_insert_masterpromo);
            return $result_insert_masterpromo;
        }
    }

    public function pushFirebaseCreateOrUpdate($perusahaanNo, $outletID, $promoID) {

        //if ($result_insert_masterpromo) {
        //push to firebase
        $insert_query = $this->_dbMaster->get_where($this->_tableName, array(
            "PerusahaanNo" => $perusahaanNo,
            "DeviceID" => $outletID,
            "PromoID" => $promoID
                ));
        $last_insert_data = array(
            "table" => $this->_tableName,
            "column" => $insert_query->row_array()
        );
        $this->load->model('Firebasemodel');
        $this->Firebasemodel->push_firebase($outletID, $last_insert_data, $promoID, 0, getPerusahaanNo(), 0);
        //}
    }

    public function pushFirebaseCreateOrUpdateWithProgress($perusahaanNo, $outletID, $promoID,$token,$proses,$jumlahSemuaProses) {

        //if ($result_insert_masterpromo) {
        //push to firebase
        $insert_query = $this->_dbMaster->get_where($this->_tableName, array(
            "PerusahaanNo" => $perusahaanNo,
            "DeviceID" => $outletID,
            "PromoID" => $promoID
                ));
        $last_insert_data = array(
            "table" => $this->_tableName,
            "column" => $insert_query->row_array()
        );
        $this->load->model('Outlet');
        $dataoutlet =  $this->Outlet->getOutletByIdOnly($outletID);
        $namaoutlet = $dataoutlet->NamaOutlet . " - " . $dataoutlet->AlamatOutlet;
        $this->load->model('Firebasemodel');
        //$this->Firebasemodel->push_firebase($outletID, $last_insert_data, $promoID, 0, getPerusahaanNo(), 0);
        $proses = $this->Firebasemodel->push_firebase_withprogres($outletID, $namaoutlet, $last_insert_data, $promoID, 0, getPerusahaanNo(), 0,$token,$proses,$jumlahSemuaProses);
        return $proses;
        //}
    }

    public function getByTitle($titlePromo, $idoutlet) {
        $query = $this->db->get_where($this->_tableName, array('PromoTitle' => $titlePromo, 'DeviceID' => $idoutlet));
        $result = $query->result();
        if (count($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    public function updateByName($oldName, $perusahaanno, $namapromo, $idoutlet, $jenispromo, $date, $time, $hari, $term, $get, $apply) {
        $termitems = is_array($term[1]) ? implode(',', $term[1]) : $term[1];
        if (empty($termitems) && empty($term[2]) && ($jenispromo == 1 || $jenispromo == 3)) {
            return 'Produk tidak boleh kosong';
        }
        //cek item double di waktu yang beririsan
        $hariberirisan = "".(($hari[0] == 'senin') ? 'OR ApplyMonday=1' : '').
                "".(($hari[1] == 'selasa') ? ' OR ApplyTuesday=1' : '').
                "".(($hari[2] == 'rabu') ? ' OR ApplyWednesday=1' : '').
                "".(($hari[3] == 'kamis') ? ' OR ApplyThursday=1' : '').
                "".(($hari[4] == 'jumat') ? ' OR ApplyFriday=1' : '').
                "".(($hari[5] == 'sabtu') ? ' OR ApplySaturday=1' : '').
                "".(($hari[6] == 'minggu') ? ' OR ApplySunday=1' : '');
        $sqlquery = "SELECT * FROM masterpromo WHERE PerusahaanNo=" . $perusahaanno .
                " AND DeviceID=" . $idoutlet . " AND PromoTitle<>'" . $oldName . "' AND PromoToDate>='".$date[0] . 
                "' AND PromoFromDate<='".$date[1]."' AND PromoToTime>='".$time[0] . 
                "' AND PromoFromTime<='".$time[1]."' AND (" . substr($hariberirisan, 3) . ")";
        log_message('error', $sqlquery);
        $query = $this->_dbMaster->query($sqlquery);
        $count = $query->num_rows();
        if ($count > 0) {
            $this->load->model('Masteritem');
            $this->load->model('Kategori');
            $result = $query->result();
            foreach ($result as $row) {
                //log_message('error', 'tes promo beririsan');
                
                $arrayTermItems = explode(',',$term[1]);
                $arrayTermCategories = explode(',',$term[2]);
                for($i=0;$i<count($arrayTermCategories);$i++) {
                    //log_message('error', 'tes a promo beririsan ' . $arrayTermCategories[$i]);
                    if (!empty($arrayTermCategories[$i]) && !empty($row->TermCategories) && 
                            strpos($row->TermCategories, $arrayTermCategories[$i]) !== false) {
                        $kategori = $this->Kategori->getByID($arrayTermCategories[$i],$idoutlet);
                        if($kategori == null) {
                            $namakategori = "Lainnya";
                        } else {
                            $namakategori = $kategori->CategoryName;
                        }
                        return "Kategori " . $namakategori . 
                                ' sudah ada di promo berjudul ' . $row->PromoTitle . '.<br>Silakan pilih kategori yang lain.';
                    }
                }
                for($i=0;$i<count($arrayTermItems);$i++) {
                    //log_message('error', 'tes c promo beririsan ' . $arrayTermItems[$i]);
                    if (!empty($arrayTermItems[$i]) && !empty($row->TermItems) && 
                            strpos($row->TermItems, $arrayTermItems[$i]) !== false) {
                        return "Produk " . $this->Masteritem->getByID($arrayTermItems[$i],$idoutlet)->ItemName . 
                                ' sudah ada di promo berjudul ' . $row->PromoTitle . '.<br>Silakan pilih produk lain.';
                    }
                }
            }
            //return "Promo dengan judul ' . $namapromo . ' sudah ada. Silakan pakai judul lain.';
        }
        
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'PromoTitle' => $oldName));
        $this->_dbMaster->update($this->_tableName, array(
            'PerusahaanNo' => $perusahaanno,
            'DeviceID' => $idoutlet,
            'PromoTitle' => $namapromo,
            'PromoFromDate' => $date[0],
            'PromoToDate' => $date[1],
            'PromoFromTime' => $time[0],
            'PromoToTime' => $time[1],
            'ApplyMonday' => ($hari[0] == 'senin') ? '1' : '0',
            'ApplyTuesday' => ($hari[1] == 'selasa') ? '1' : '0',
            'ApplyWednesday' => ($hari[2] == 'rabu') ? '1' : '0',
            'ApplyThursday' => ($hari[3] == 'kamis') ? '1' : '0',
            'ApplyFriday' => ($hari[4] == 'jumat') ? '1' : '0',
            'ApplySaturday' => ($hari[5] == 'sabtu') ? '1' : '0',
            'ApplySunday' => ($hari[6] == 'minggu') ? '1' : '0',
            'PromoType' => $jenispromo,
            'TermQty' => $term[0],
            'TermItems' => is_array($term[1]) ? implode(',', $term[1]) : $term[1],
            'TermCategories' => $term[2],
            'TermTotal' => $term[3],
            'GetDiscountType' => $get[0],
            'GetDiscountValue' => $get[1],
            'GetItemQty' => $get[2],
            'GetItemID' => is_array($get[3]) ? implode(',', $get[3]) : $get[3],
            'ApplyMultiply' => ($apply == 'on') ? '1' : '0',
            'EditedBy' => getLoggedInUsername(),
            'EditedDate' => date('Y-m-d'),
            'EditedTime' => date('H:i')
        ));

        $this->_dbMaster->where(array('PerusahaanNo' => $perusahaanno,
            'DeviceID' => $idoutlet, 'PromoTitle' => $namapromo));
        $query = $this->_dbMaster->get($this->_tableName);
        $result = $query->result();
        if (count($result) > 0) {
//                $last_insert_data =  array(
//                    "table"     => $this->_tableName,
//                    "column"    => $query->row_array()
//                );
//                $this->load->model('Firebasemodel');
//                $this->Firebasemodel->push_firebase($idoutlet,$last_insert_data, $result[0]->PromoID, 0, $perusahaanno, 0);
            return $result[0]->PromoID;
        } else {
            return $this->createNewPromo($perusahaanno, $namapromo, $idoutlet, $jenispromo, $date, $time, $hari, $term, $get, $apply);
        }
    }

    public function deletepromo($namaPromo, $arrayOfidOutlet) {
        foreach ($arrayOfidOutlet as $idoutlet) {

            $query_deleting_item = $this->_dbMaster->get_where('masterpromo', array('DeviceID' => $idoutlet, 'PromoTitle' => $namaPromo));
            $deleting_items = $query_deleting_item->result_array();

            $deleted_data = array(
                "table" => 'deletemasterpromo',
                "column" => array(
                    "PromoID" => $deleting_items[0]['PromoID'],
                    "DeviceNo" => 0
                )
            );
            /* Cek masteritemdelete */
            $query_is_exist_in_delete_table = $this->_dbMaster->get_where('masterpromodelete', array('DeviceID' => $idoutlet, 'PromoTitle' => $namaPromo));
            $exist_in_delete_table = $query_is_exist_in_delete_table->num_rows() > 0;

            if ($exist_in_delete_table) {
                $this->_dbMaster->where(array('PromoTitle' => $namaPromo, 'DeviceID' => $idoutlet));
                $this->_dbMaster->delete('masterpromodelete');
            }


            //$deleting_items[0]['HasBeenDownloaded'] = 0;
            $this->_dbMaster->insert('masterpromodelete', $deleting_items[0]);

            $this->load->model('Firebasemodel');
            $this->Firebasemodel->push_firebase($idoutlet, $deleted_data, $deleting_items[0]['PromoID'], 0, getPerusahaanNo(), 0);

            $sql = "DELETE FROM masterpromo WHERE DeviceID = " . $this->_dbMaster->escape($idoutlet) . " AND PromoTitle = " . $this->_dbMaster->escape($namaPromo) . ";";
            $this->_dbMaster->query($sql);
        }

        return 1;
    }

    public function isInMultiOutlet($promotitle, $perusahaanID) {
        $query = $this->db->query("select OutletID from outlet where outletid in (select DeviceID from masterpromo where PromoTitle="
                . $this->db->escape($promotitle) .
                ") AND PerusahaanID=" . $this->db->escape($perusahaanID));
        $result = $query->result();
        $retval = array();
        foreach ($result as $r) {
            array_push($retval, $r->OutletID);
        }
        return $retval;
    }

    public function getCategory($deviceid, $perusahaanno) {
        $query = $this->db->query("
        select 
            CategoryID,
            CategoryName,
            DeviceNo 
        from mastercategory 
        where 
            DeviceID = " . $this->db->escape($deviceid) . " 
            AND PerusahaanNo = " . $this->db->escape($perusahaanno) . "
        UNION ALL
        SELECT
            0 CategoryID,
            'Lain-lain' CategoryName,
            0 DeviceNo
    ");
        $result = $query->result();
        return $result;
    }

    public function getItem($deviceid, $perusahaanno) {
        $query = $this->db->query("select mi.ItemID,mi.ItemName,mi.DeviceNo,COALESCE(mc.CategoryID,0) CategoryID,
        COALESCE(mc.DeviceNo,0) CategoryDeviceNo from masteritem mi LEFT JOIN mastercategory mc
        ON mc.PerusahaanNo=mi.PerusahaanNo AND mc.DeviceID=mi.DeviceID AND mc.CategoryID=mi.CategoryID AND mc.DeviceNo=mi.CategoryDeviceNo
        where mi.DeviceID = '" . $deviceid . "' AND mi.PerusahaanNo = '" . $perusahaanno . "' AND IsProduct='true'");
        $result = $query->result();
        return $result;
    }

    /**
     * @param $idoutlet
     * @return int
     */
    private function getMaxPromoId($idoutlet) {
        $sql = "
      SELECT COALESCE (MAX(PromoID),0) +1 as id FROM 
      (
        SELECT PromoID FROM masterpromo where DeviceID = " . $idoutlet . "
        UNION ALL 
        SELECT PromoID FROM masterpromodelete where DeviceID = " . $idoutlet . "
      ) X";

        $queryid = $this->db->query($sql);
        $resultid = $queryid->result();
        $itemid = $resultid[0]->id;
        return $itemid;
    }

}
