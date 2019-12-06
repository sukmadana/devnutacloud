<?php

/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 21/11/16
 * Time: 13:09
 */
class Kategori extends CI_Model
{
    var $_tableName = "mastercategory";
    protected $_dbMaster;

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

    public function createKategori($idoutlet, $namakategori, $perusahaanNo)
    {
        $this->initDbMaster();

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($idoutlet);
        $cloudDevno = 0;
        if ($options->CreatedVersionCode < 103 && $options->EditedVersionCode < 103) {
            $cloudDevno = 1;
        }

        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'CategoryName' => $namakategori));
        $query = $this->_dbMaster->get('mastercategory');
        $count = $query->num_rows();
        if ($count > 0) {
            return 'Nama Kategori sudah terdaftar';
        } else {
            $sql_generate_id = "
        SELECT
          COALESCE (MAX(CategoryID),0) +1 as ID
        FROM
        (SELECT
            CategoryID
        FROM
            mastercategory
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . " UNION ALL SELECT
            CategoryID
        FROM
            mastercategorydelete
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . ") a
        ";
            $queryid = $this->db->query($sql_generate_id);
            $resultid = $queryid->row();
            $categoryid = $resultid->ID;

            $tabNumber = $this->getMaxTabNumberInSale($idoutlet, $perusahaanNo);

            $attrib_data = array(
                'CategoryID' => $categoryid,
                'DeviceID' => $idoutlet,
                'DeviceNo' => $cloudDevno,
                'CategoryName' => $namakategori,
                'TabNumberInSale' => $tabNumber,
                'Varian' => 'Nuta',
                'HasBeenDownloaded' => 0,
                'PerusahaanNo' => $perusahaanNo
            );

            $insert_result = $this->_dbMaster->insert('mastercategory', $attrib_data);

            if ($insert_result) {
                $query_datainserted = $this->_dbMaster->get_where('mastercategory', array(
                    'CategoryID' => $categoryid,
                    'DeviceID' => $idoutlet,
                    'DeviceNo' => $cloudDevno,
                    'PerusahaanNo' => $perusahaanNo
                ));
                $last_insert_data = array(
                    "table" => $this->_tableName,
                    "column" => $query_datainserted->row_array()
                );
                $this->Firebasemodel->push_firebase(
                    $idoutlet,
                    $last_insert_data,
                    $categoryid,
                    $cloudDevno,
                    $perusahaanNo,
                    0
                );
            }

            return $categoryid . "." . $cloudDevno;
        }
    }

    public function insertKategori($idoutlet, $namakategori, $IPPrinter, $perusahaanNo)
    {
        $this->initDbMaster();

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($idoutlet);
        $cloudDevno = 0;
        if ($options->CreatedVersionCode < 103 && $options->EditedVersionCode < 103) {
            $cloudDevno = 1;
        }

        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'CategoryName' => $namakategori));
        $query = $this->_dbMaster->get('mastercategory');
        $count = $query->num_rows();
        if ($count > 0) {
            return array(
                "status" => 400,
                "message" => 'Nama Kategori sudah terdaftar'
            );
        } else {
            $sql_generate_id = "
        SELECT
          COALESCE (MAX(CategoryID),0) +1 as ID
        FROM
        (SELECT
            CategoryID
        FROM
            mastercategory
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . " UNION ALL SELECT
            CategoryID
        FROM
            mastercategorydelete
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . ") a
        ";
            $queryid = $this->db->query($sql_generate_id);
            $resultid = $queryid->row();
            $categoryid = $resultid->ID;

            $tabNumber = $this->getMaxTabNumberInSale($idoutlet, $perusahaanNo);

            $attrib_data = array(
                'CategoryID' => $categoryid,
                'DeviceID' => $idoutlet,
                'DeviceNo' => $cloudDevno,
                'CategoryName' => $namakategori,
                'TabNumberInSale' => $tabNumber,
                'Varian' => 'Nuta',
                'HasBeenDownloaded' => 0,
                'PerusahaanNo' => $perusahaanNo,
                'IPPrinter' => $IPPrinter,
                'CreatedVersionCode' => $options->CreatedVersionCode,
                'EditedVersionCode' => $options->EditedVersionCode
            );

            $insert_result = $this->_dbMaster->insert('mastercategory', $attrib_data);

            if ($insert_result) {
                $query_datainserted = $this->_dbMaster->get_where('mastercategory', array(
                    'CategoryID' => $categoryid,
                    'DeviceID' => $idoutlet,
                    'DeviceNo' => $cloudDevno,
                    'PerusahaanNo' => $perusahaanNo
                ));
                $last_insert_data = array(
                    "table" => $this->_tableName,
                    "column" => $query_datainserted->row_array()
                );
                $this->Firebasemodel->push_firebase(
                    $idoutlet,
                    $last_insert_data,
                    $categoryid,
                    $cloudDevno,
                    $perusahaanNo,
                    0
                );
            }
            return array(
                "status" => 200,
                "message" => "Kategori " . $namakategori . " Berhasil Disimpan",
                "CategoryID" => $categoryid,
                "CloudDevNo" => $cloudDevno
            );
        }
    }

    public function isExistCategoryName($where)
    {
        $this->initDbMaster();
        $sql = $this->_dbMaster->get_where($this->_tableName, $where);
        if ($sql->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateKategori($params, $where)
    {
        $update = $this->_dbMaster->update($this->_tableName, $params, $where);
        if ($update == true) {
            $dataUpdated = $this->_dbMaster->get_where($this->_tableName, $where);
            $newCategory = $dataUpdated->row();
            $last_update_data = array(
                "table" => $this->_tableName,
                "column" => $dataUpdated->row_array()
            );
            $this->Firebasemodel->push_firebase(
                $newCategory->DeviceID,
                $last_update_data,
                $newCategory->CategoryID,
                $newCategory->DeviceNo,
                getPerusahaanNo(),
                0
            );

            return true;
        } else {
            return false;
        }
    }

    public function editKategori($idkategori, $devno, $idOutlet, $namaKategoriLama, $namaKategoriBaru)
    {
        $this->initDbMaster();
        $query_datawillbeupdated = $this->_dbMaster->get_where($this->_tableName, array(
            'CategoryName' => $namaKategoriLama,
            'DeviceID' => $idOutlet
        ));
        $rows = $query_datawillbeupdated->row_array();

        if (isset($rows) && count($rows) > 0) {
            $isKategoriUnique = $this->isKategoriNotExist($idOutlet, $namaKategoriBaru);
            if ($isKategoriUnique) {
                $this->_dbMaster->where(array(
                    'DeviceID' => $idOutlet,
                    'CategoryName' => $namaKategoriLama, 'CategoryID' => $idkategori, 'DeviceNo' => $devno
                ));
                $this->_dbMaster->update('mastercategory', array('CategoryName' => $namaKategoriBaru, 'HasBeenDownloaded' => 0));

                $query_dataupdated = $this->_dbMaster->get_where('mastercategory', array(
                    'CategoryID' => $idkategori,
                    'DeviceID' => $idOutlet, 'DeviceNo' => $devno
                    //                'PerusahaanNo' => $perusahaanNo
                ));
                $last_update_data = array(
                    "table" => $this->_tableName,
                    "column" => $query_dataupdated->row_array()
                );
                $this->Firebasemodel->push_firebase(
                    $idOutlet,
                    $last_update_data,
                    $idkategori,
                    $devno,
                    getPerusahaanNo(),
                    0
                );
                return '';
            } else {
                return 'Nama Kategori sudah terdaftar';
            }
        } else {
            $this->createKategori($idOutlet, $namaKategoriBaru, getPerusahaanNo());
            return '';
        }
    }

    public function deleteCategory($where)
    {
        $this->initDbMaster();

        // Update Item
        $this->_dbMaster->update('masteritem', array('HasBeenDownloaded' => 0, 'CategoryID' => 0), array(
            'PerusahaanNo' => $where['PerusahaanNo'],
            'DeviceID' => $where['DeviceID'],
            'CategoryDeviceNo' => $where['DeviceNo'],
            'CategoryID' => $Where['CategoryID']
        ));

        // Copy kategori ke tabel mastercategorydelete
        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        $category = $query->row_array();

        if (!empty($category)) {
            $query = $this->_dbMaster->get_where('mastercategorydelete', array_merge($where, array('DeviceNo' => $category['DeviceNo'])));
            if ($query->num_rows() == 0) {
                // Insert Ke tbl mastercategorydelete
                $this->_dbMaster->insert('mastercategorydelete', $category);
            }

            //  Hapus mastercategory
            $this->_dbMaster->delete($this->_tableName, $where);

            $deleted_data = array(
                "table" => "delete" . $this->_tableName,
                "column" => array(
                    "CategoryID" => $category['CategoryID'],
                    'DeviceNo' => $category['DeviceNo']
                )
            );
            $this->Firebasemodel->push_firebase(
                $category['DeviceID'],
                $deleted_data,
                $category['CategoryID'],
                $category['DeviceNo'],
                $$category['PerusahaanNo'],
                0
            );

            return true;
        }
        return false;
    }

    public function deleteKategori($namaKategori, $arrayOfidOutlet, $perusahaanNo)
    {
        $this->initDbMaster();
        $log = '';
        foreach ($arrayOfidOutlet as $index => $idoutlet) {
            $idkategori = $this->getKategoriIDByName($idoutlet, $namaKategori, $perusahaanNo);
            // 0. Update Item
            $this->_dbMaster->query('UPDATE masteritem SET HasBeenDownloaded=0 , CategoryID = 0 WHERE DeviceID = ' . $this->_dbMaster->escape($idoutlet) .
                ' AND CategoryID = ' . $this->_dbMaster->escape($idkategori));

            $query = $this->_dbMaster->get_where('mastercategory', array(
                'DeviceID' => $idoutlet,
                'CategoryName' => $namaKategori
            ));
            $datas = $query->result_array();


            for ($a = 0; $a < count($datas); $a++) {
                //1. Insert ke category delete
                $datas[$a]['HasBeenDownloaded'] = 0;
                $q2 = $this->_dbMaster->get_where('mastercategorydelete', array(
                    'DeviceID' => $idoutlet, 'CategoryID' => $datas[$a]['CategoryID'],
                    'DeviceNo' => $datas[$a]['DeviceNo']
                ));
                $rows2 = $q2->result_array();
                if (count($rows2) == 0) {
                    $datas[0]['HasBeenDownloaded'] = 0;
                    $this->_dbMaster->insert('mastercategorydelete', $datas[0]);
                }

                //2. Hapus Kategori
                $this->_dbMaster->where(array(
                    'DeviceID' => $idoutlet,
                    'CategoryID' => $datas[$a]['CategoryID'],
                    'DeviceNo' => $datas[$a]['DeviceNo']
                ));
                $this->_dbMaster->delete('mastercategory');

                $deleted_data = array(
                    "table" => "delete" . $this->_tableName,
                    "column" => array(
                        "CategoryID" => $datas[$a]['CategoryID'],
                        'DeviceNo' => $datas[$a]['DeviceNo']
                    )
                );
                $this->Firebasemodel->push_firebase(
                    $idoutlet,
                    $deleted_data,
                    $datas[$a]['CategoryID'],
                    $datas[$a]['DeviceNo'],
                    $perusahaanNo,
                    0
                );
            }

            $log .= $idoutlet . ' berhasil dihapus. ';
        }
        return $log;
    }

    public function getKategoriIDByName($idoutlet, $namakategori, $perusahaanNo)
    {
        $this->initDbMaster();

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($idoutlet);
        $cloudDevno = 0;
        if ($options->CreatedVersionCode < 103 && $options->EditedVersionCode < 103) {
            $cloudDevno = 1;
        }

        $this->_dbMaster->where(array(
            'DeviceID' => $idoutlet,
            'CategoryName' => $namakategori, 'PerusahaanNo' => $perusahaanNo
        ));
        $query = $this->_dbMaster->get('mastercategory');
        $count = $query->num_rows();
        if ($count > 0) {
            $result = $query->result();
            return $result[0]->CategoryID . "." . $result[0]->DeviceNo;
        } else {
            $sql_generate_id = "
        SELECT COALESCE (MAX(CategoryID),0) +1 as ID
        FROM
        (
            SELECT CategoryID
            FROM mastercategory
            WHERE DeviceID = " . $this->db->escape($idoutlet) . "
            UNION ALL
            SELECT CategoryID
            FROM mastercategorydelete
            WHERE DeviceID = " . $this->db->escape($idoutlet) . "
        ) a
        ";

            $queryid = $this->db->query($sql_generate_id);
            $resultid = $queryid->row();
            $categoryid = $resultid->ID;

            $attrib_data = array(
                'CategoryID' => $categoryid,
                'DeviceID' => $idoutlet,
                'DeviceNo' => $cloudDevno,
                'CategoryName' => $namakategori,
                'Varian' => 'Nuta',
                'HasBeenDownloaded' => 0,
                'PerusahaanNo' => $perusahaanNo
            );

            $insert_result = $this->_dbMaster->insert('mastercategory', $attrib_data);

            if ($insert_result) {
                $query_datainserted = $this->_dbMaster->get_where('mastercategory', array(
                    'CategoryID' => $categoryid,
                    'DeviceID' => $idoutlet,
                    'DeviceNo' => $cloudDevno,
                    'PerusahaanNo' => $perusahaanNo
                ));
                $last_insert_data = array(
                    "table" => $this->_tableName,
                    "column" => $query_datainserted->row_array()
                );
                $this->Firebasemodel->push_firebase(
                    $idoutlet,
                    $last_insert_data,
                    $categoryid,
                    $cloudDevno,
                    $perusahaanNo,
                    0
                );
            }

            return $categoryid . "." . $cloudDevno;
        }
    }

    public function getDetailCategory($CategoryID, $DeviceNo, $idoutlet, $perusahaanNo)
    {
        $query = $this->db->get_where($this->_tableName, array('CategoryID' => $CategoryID, 'DeviceNo' => $DeviceNo, 'DeviceID' => $idoutlet, 'PerusahaanNo' => $perusahaanNo));
        return $query->row();
    }

    public function getDetailCategoryDynamic($where)
    {
        $query = $this->db->get_where($this->_tableName, $where);
        return $query->row_array();
    }

    public function getByName($namakategori, $idoutlet)
    {

        $query = $this->db->get_where($this->_tableName, array('CategoryName' => $namakategori, 'DeviceID' => $idoutlet));
        $result = $query->result();
        if (count($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    public function getByID($id, $idoutlet)
    {
        $realkategoriid = explode(".", $id)[0];
        $devno = explode(".", $id)[1];

        $query = $this->db->get_where($this->_tableName, array(
            'CategoryID' => $realkategoriid,
            'DeviceNo' => $devno, 'DeviceID' => $idoutlet
        ));
        $result = $query->result();
        if (count($result) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    public function isKategoriNotExist($idoutlet, $namakategori)
    {
        $query = $this->db->get_where('mastercategory', array('DeviceID' => $idoutlet, 'CategoryName' => $namakategori));
        $count = $query->num_rows();
        return $count == 0;
    }

    public function getDatatableListCategories($idoutlet)
    {
        $sql = "SELECT a.*, totalItems
            FROM mastercategory a
            LEFT JOIN (
                SELECT PerusahaanNO, DeviceID, CategoryDeviceNo, CategoryID, COALESCE(COUNT(*),0) AS 'totalItems' FROM masteritem
                GROUP BY PerusahaanNO, DeviceID, CategoryDeviceNo, CategoryID
            ) b ON a.PerusahaanNO = b.PerusahaanNO AND a.DeviceID = b.DeviceID AND a.CategoryID = b.CategoryID AND a.DeviceNo = b.CategoryDeviceNo
            WHERE a.PerusahaanNO = ? AND a.DeviceID = ?
            ORDER BY TabNumberInSale ";
        $query = $this->db->query($sql, array(getPerusahaanNo(), $idoutlet));
        return $query->result_array();
    }

    public function getListItemsByCategory($CategoryID, $DeviceID, $DeviceNo)
    {
        $query = $this->db->order_by('RowNumber')->get_where('masteritem', array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $DeviceID, 'CategoryID' => $CategoryID, 'CategoryDeviceNo' => $DeviceNo));
        $result = $query->result_array();
        return $result;
    }

    public function getDaftarKategori($idoutlet)
    {
        $query = $this->db->get_where('mastercategory', array('DeviceID' => $idoutlet));
        $result = $query->result();
        $retval = array();
        foreach ($result as $r) {
            $retval[$r->CategoryID . "." . $r->DeviceNo] = $r->CategoryName;
        }
        return $retval;
    }

    public function isKategoriInMultiOutlet($namakategori, $perusahaanID)
    {
        $query = $this->db->query("select OutletID from outlet where outletid in (select DeviceID from mastercategory where CategoryName="
            . $this->db->escape($namakategori) .
            ") AND PerusahaanID=" . $this->db->escape($perusahaanID));
        $result = $query->result();
        $retval = array();
        foreach ($result as $r) {
            array_push($retval, $r->OutletID);
        }
        return $retval;
    }

    public function getMaxCategoriID($idoutlet, $perusahaanNo)
    {
        $sql = "
        SELECT
          COALESCE (MAX(CategoryID),0) +1 as id
        FROM
        (SELECT
            CategoryID
        FROM
            mastercategory
        WHERE
            PerusahaanNo = " . $perusahaanNo . " AND
            DeviceID = " . $this->db->escape($idoutlet) . " UNION ALL SELECT
            CategoryID
        FROM
            mastercategorydelete
        WHERE
            PerusahaanNo = " . $perusahaanNo . " AND
            DeviceID = " . $this->db->escape($idoutlet) . ") a
        ";

        $queryid = $this->db->query($sql);
        $resultid = $queryid->result();
        $categoryid = $resultid[0]->id;
        return $categoryid;
    }

    public function getMaxTabNumberInSale($idoutlet, $perusahaanNo)
    {
        $sql = "
        SELECT
          COALESCE (MAX(TabNumberInSale),0) +1 as TabNumberInSale
        FROM
        (SELECT
            TabNumberInSale
        FROM
            mastercategory
        WHERE
            PerusahaanNo = " . $perusahaanNo . " AND
            DeviceID = " . $this->db->escape($idoutlet) . "
        ) a
        ";

        $queryid = $this->db->query($sql);
        $resultid = $queryid->result();
        $tabNumber = $resultid[0]->TabNumberInSale;
        return $tabNumber;
    }

    public function getItemsOtherCategory($outlet, $categoryid, $deviceno)
    {
        $sql = " SELECT * FROM (
            SELECT itemID, itemName, b.CategoryID, b.CategoryName, a.DeviceNo FROM masteritem a
                        LEFT JOIN mastercategory b ON a.CategoryID = b.CategoryID AND a.PerusahaanNO = b.PerusahaanNO AND a.DeviceID = b.DeviceID AND a.CategoryDeviceNo = b.DeviceNo
                        WHERE a.IsProduct = 'true' AND a.CategoryID <> ? AND a.PerusahaanNO = ? AND a.DeviceID = ?
            UNION ALL
            SELECT itemID, itemName, b.CategoryID, b.CategoryName, a.DeviceNo FROM masteritem a
                        LEFT JOIN mastercategory b ON a.CategoryID = b.CategoryID AND a.PerusahaanNO = b.PerusahaanNO AND a.DeviceID = b.DeviceID AND a.CategoryDeviceNo = b.DeviceNo
                        WHERE a.IsProduct = 'true' AND a.CategoryID = ? AND a.CategoryDeviceNo <> ? AND a.PerusahaanNO = ? AND a.DeviceID = ?)rs1
                        ORDER BY CategoryName, itemName ";
        $query = $this->db->query($sql, array($categoryid, getPerusahaanNo(), $outlet, $categoryid, $deviceno, getPerusahaanNo(), $outlet));
        return $query->result_array();
    }

    public function getKategoriByOutlet($idoutlet)
    {
        $query = $this->db->get_where('mastercategory', array('DeviceID' => $idoutlet));
        return $query->result_array();
    }
}
