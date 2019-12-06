<?php
/**
 * Created by PhpStorm.
 * User: ANDROMEDA
 * Date: 14/06/2017
 * Time: 07.22
 */
class Supplier extends CI_Model
{
    var $_tableName = "mastersupplier";
    protected $_dbMaster;

    public function __construct ()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Firebasemodel');
    }

    public function supplier($Outlet)
    {
        return "
        SELECT SupplierID,
        DeviceNo,
        SupplierName AS Nama,
        SupplierAddress AS Alamat,
        SupplierPhone AS Telepon,
        SupplierEmail AS Email,
        Note AS Catatan
        FROM mastersupplier 
        WHERE 
        DeviceID = " . $this->db->escape($Outlet);
    }

    protected function initDbMaster ()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function createNewSupplier ($nama, $alamat, $telepon, $email, $catatan, $idoutlet)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'lower(SupplierName)' => strtolower($nama)));
        $query = $this->_dbMaster->get($this->_tableName);
        $count = $query->num_rows();
        if ($count > 0) {
            $result = $query->result();
            return 'Supplier ini sudah ada, silahkan pakai nama lain.';

        }

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($idoutlet);
        $cloudDevno = 0;
        if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
            $cloudDevno = 1;
        }

        $perusahaanNo = getPerusahaanNo();
        $suppid = $this->getMaxTransactionId($idoutlet);
        $this->_dbMaster->where(array(
            'DeviceID' => $idoutlet
        ));
        $this->_dbMaster->insert($this->_tableName, array(
            'SupplierID' => $suppid,
            'DeviceNo' => $cloudDevno,
            'SupplierName' => $nama,
            'SupplierAddress' => $alamat,
            'SupplierPhone' => $telepon,
            'SupplierEmail' => $email,
            'Note' => $catatan,
            'DeviceID' => $idoutlet,
            'Varian' => 'Nuta',
            'PerusahaanNo' => $perusahaanNo,
        ));

        // push data to firebase
        $query_datainserted = $this->_dbMaster->get_where($this->_tableName, array(
            'SupplierID' => $suppid,
            'DeviceID' => $idoutlet,
            'DeviceNo' => $cloudDevno));
        $last_insert_data = array(
            "table" => $this->_tableName,
            "column" => $query_datainserted->row_array()
        );
        $this->Firebasemodel->push_firebase($idoutlet, $last_insert_data,
            $suppid, $cloudDevno, $perusahaanNo, 0);
        return $suppid . "." . $cloudDevno;
    }

    public function updateSupplier ($nama, $alamat, $telepon, $email, $catatan, $idoutlet, $id_supp)
    {
        $realID = explode(".", $id_supp)[0];
        $devno = explode(".", $id_supp)[1];
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'SupplierID' => $realID, 'DeviceNo' => $devno));
        $this->_dbMaster->update($this->_tableName, array(
            'SupplierName' => $nama,
            'SupplierAddress' => $alamat,
            'SupplierPhone' => $telepon,
            'SupplierEmail' => $email,
            'Note' => $catatan,
        ));

        $query = $this->_dbMaster->get_where($this->_tableName,
            array('DeviceID' => $idoutlet, 'SupplierID' => $realID, 'DeviceNo' => $devno));
        $result = $query->result();
        if (count($result) > 0) {
            $last_update_data = array(
                "table" => $this->_tableName,
                "column" => $query->row_array()
            );
            $this->Firebasemodel->push_firebase($idoutlet, $last_update_data,
                $result[0]->SupplierID, $result[0]->DeviceNo, getPerusahaanNo(), 0);

            return $result[0]->SupplierID . "." . $result[0]->DeviceNo;
        } else {
            return $this->createNewSupplier($nama, $alamat, $telepon, $email, $catatan, $idoutlet);;
        }
    }

    public function getByName($suppid, $devno, $idoutlet)
    {

        $query = $this->db->get_where($this->_tableName,
            array('SupplierID' => $suppid, 'DeviceID' => $idoutlet, 'DeviceNo' => $devno));
        $result = $query->result();
        // $this->output->enable_profiler(TRUE);

        return $result[0];

    }


    public function getMaxTransactionId ($idoutlet)
    {
        $queryid = $this->db->query("SELECT COALESCE (max(SupplierID)+1,1) id  
			FROM (
				SELECT SupplierID
				FROM mastersupplier 
				WHERE DeviceID={$this->db->escape($idoutlet)}
				UNION ALL
					SELECT SupplierID
					FROM mastersupplierdelete
					WHERE DeviceID={$this->db->escape($idoutlet)}
			) AS CID");

        $resultid = $queryid->result();
        $supplierid = $resultid[0]->id;
        return $supplierid;
    }

    public function updateBySupp ($oldsupp, $nama, $alamat, $telepon, $email, $catatan, $idoutlet)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'SupplierName' => $oldsupp));
        $this->_dbMaster->update($this->_tableName, array(
            'SupplierName' => $nama,
            'SupplierAddress' => $alamat,
            'SupplierPhone' => $telepon,
            'SupplierEmail' => $email,
            'Note' => $catatan,
        ));

        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'SupplierName' => $nama));
        $query = $this->_dbMaster->get($this->_tableName);
        $result = $query->result();
        if (count($result) > 0) {
            // push data to firebase
            $last_update_data = array(
                "table" => $this->_tableName,
                "column" => $query->row_array()
            );
            $this->Firebasemodel->push_firebase($idoutlet, $last_update_data,
                $result[0]->SupplierID, $result[0]->DeviceNo, getPerusahaanNo(), 0);

            return $result[0]->SupplierID . "." . $result[0]->DeviceNo;
        } else {
            return $this->createNewSupplier($nama, $alamat, $telepon, $email, $catatan, $idoutlet);
        }
    }

    public function isInMultiOutlet($suppid, $perusahaanID)
    {
        $query = $this->db->query("select OutletID from outlet where outletid in (select DeviceID from mastersupplier where SupplierID="
            . $this->db->escape($suppid) .
            ") AND PerusahaanID=" . $this->db->escape($perusahaanID));
        $result = $query->result();
        $retval = array();
        foreach ($result as $r) {
            array_push($retval, $r->OutletID);
        }
        return $retval;
    }

    public function deleting ($attr = [])
    {
        $this->backuping($attr);

        $deleted_data = array(
            "table" => "delete" . $this->_tableName,
            "column" => array(
                "SupplierID" => $attr['SupplierID'],
                "DeviceNo" => $attr['DeviceNo']
            )
        );
        $this->Firebasemodel->push_firebase($attr['DeviceID'], $deleted_data,
            $attr['SupplierID'], $attr['DeviceNo'], getPerusahaanNo(), 0);
        return $this->db->delete($this->_tableName, $attr);
    }

    public function backuping ($attr)
    {
        $supp = $this->db->get_where($this->_tableName, $attr);
        $supp = $supp->first_row();

        return $this->db->insert($this->_tableName.'delete', [
            'SupplierID' => $supp->SupplierID,
            'DeviceNo' => $supp->DeviceNo,
            'SupplierName' => $supp->SupplierName,
            'SupplierAddress' => $supp->SupplierAddress,
            'SupplierPhone' => $supp->SupplierPhone,
            'SupplierEmail' => $supp->SupplierEmail,
            'Note' => $supp->Note,
            'DeviceID' => $supp->DeviceID, 'Varian' => 'Nuta',
            'PerusahaanID' => $supp->PerusahaanID,
            'PerusahaanNo' => $supp->PerusahaanNo,
        ]);
    }
}