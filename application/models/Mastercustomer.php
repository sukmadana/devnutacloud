<?php

/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 18/03/2017
 * Time: 12:26
 */
class Mastercustomer extends CI_Model
{
    var $_tableName = "mastercustomer";
    protected $_dbMaster;
    public $NO_ERROR_MESSAGE = "OK";

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

    function create($nama, $alamat, $phone, $email, $note, $birthday, $outlet)
    {
        $customer_id = $this->generateCustomerID($outlet);
        $this->initDbMaster();

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($outlet);
        $cloudDevno = 0;
        if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
            $cloudDevno = 1;
        }

        $is_exist = $this->isExist($nama, $outlet);
        if ($is_exist) {
            return "Nama Pelanggan " . $nama . " sudah ada";
        } else {

            $attrib_data = array(
                'CustomerID'        => $customer_id,
                'DeviceNo'        => $cloudDevno,
                'CustomerName'      => $nama,
                'CustomerAddress'   => $alamat,
                'CustomerPhone'     => $phone,
                'CustomerEmail'     => $email,
                'Note'              => $note,
                'Birthday'          => $birthday,
                'DeviceID'          => $outlet,
                'Varian'            => 'Nuta',
                'PerusahaanNo'      => getPerusahaanNo(),
                'HasBeenDownloaded' => 0
            );

            $this->_dbMaster->insert( $this->_tableName, $attrib_data );

            // push data to firebase
            $perusahaanNo = getPerusahaanNo();
            $insert_query = $this->_dbMaster->get_where( $this->_tableName, array(
                'CustomerID'        => $customer_id,
                'DeviceID'          => $outlet,
                'DeviceNo'        => $cloudDevno,
                'Varian'            => 'Nuta',
                'PerusahaanNo'      => $perusahaanNo
            ) );
            $last_insert_data =  array(
                "table"     => $this->_tableName,
                "column"    => $insert_query->row_array()
            );
            $this->Firebasemodel->push_firebase($outlet,$last_insert_data,
                $customer_id, $cloudDevno, $perusahaanNo, 0);

            return $this->NO_ERROR_MESSAGE;
        }
    }

    function update($old_nama, $nama, $alamat, $phone, $email, $note, $birthday, $outlet)
    {

        $is_name_equals = ($old_nama === $nama);
        if (!$is_name_equals) {
            $is_exist = $this->isExist($nama, $outlet);
            if ($is_exist) {
                return "Nama Pelanggan " . $nama . " sudah ada";
            }
        }
        $this->initDbMaster();
        $this->_dbMaster->where('CustomerName', $old_nama);
        $this->_dbMaster->where('DeviceID', $outlet);
        $this->_dbMaster->update($this->_tableName,
            array(
                'CustomerName' => $nama,
                'CustomerAddress' => $alamat,
                'CustomerPhone' => $phone,
                'CustomerEmail' => $email,
                'Note' => $note,
                'Birthday' => $birthday,
                'HasBeenDownloaded' => 0
            )
        );

        // push data to firebase
        $perusahaanNo = getPerusahaanNo();
        $query_tobe_pushed = $this->_dbMaster->get_where( $this->_tableName, array(
            'CustomerName'        => $nama,
            'DeviceID'          => $outlet,
            'Varian'            => 'Nuta',
            'PerusahaanNo'      => $perusahaanNo
        ) );
        $last_insert_data =  array(
            "table"     => $this->_tableName,
            "column"    => $query_tobe_pushed->row_array()
        );
        $this->Firebasemodel->push_firebase($outlet,$last_insert_data,
            $last_insert_data["column"]['CustomerID'],
            $last_insert_data["column"]['DeviceNo'], $perusahaanNo, 0);
        return $this->NO_ERROR_MESSAGE;

    }

    function deleteByName($nama, $outlet)
    {
        $this->db->where('CustomerName', $nama);
        $this->db->where('DeviceID', $outlet);
        $query_pelanggan = $this->db->get($this->_tableName);
        $pelanggan = $query_pelanggan->row();
        if (isset($pelanggan)) {
            $idpelanggan = $pelanggan->CustomerID;
            $devno = $pelanggan->DeviceNo;
            $this->initDbMaster();
            $this->_dbMaster->insert($this->_tableName . 'delete',
                array(
                    'CustomerID' => $pelanggan->CustomerID,
                    'DeviceNo' => $pelanggan->DeviceNo,
                    'CustomerName' => $pelanggan->CustomerName,
                    'CustomerAddress' => $pelanggan->CustomerAddress,
                    'CustomerPhone' => $pelanggan->CustomerPhone,
                    'CustomerEmail' => $pelanggan->CustomerEmail,
                    'Note' => $pelanggan->Note,
                    'Birthday' => $pelanggan->Birthday,
                    'DeviceID' => $pelanggan->DeviceID,
                    'Varian' => $pelanggan->Varian,
                    'PerusahaanNo' => $pelanggan->PerusahaanNo,
                    'HasBeenDownloaded' => 0
                )
            );
            $this->_dbMaster->where('CustomerName', $pelanggan->CustomerName);
            $this->_dbMaster->where('DeviceID', $pelanggan->DeviceID);
            $this->_dbMaster->delete($this->_tableName);

            $deleted_data = array(
                "table" => "delete" . $this->_tableName,
                "column" => array(
                    "CustomerID" => $idpelanggan,
                    "DeviceNo" => $devno
                )
            );
            $this->Firebasemodel->push_firebase($outlet, $deleted_data,
                $idpelanggan, $devno, getPerusahaanNo(), 0);
            return $this->NO_ERROR_MESSAGE;
        } else {
            return "Pelanggan " . $nama . " tidak ditemukan";
        }

    }

    private function generateCustomerID($deviceid)
    {
        $sqlid = "
        SELECT
          COALESCE (MAX(CustomerID),0) +1 as id
        FROM
        (SELECT
            CustomerID
        FROM
            mastercustomer
        WHERE
            DeviceID = " . $this->db->escape($deviceid) . "
            UNION ALL
        SELECT
            CustomerID
        FROM
           mastercustomerdelete
        WHERE
            DeviceID = " . $this->db->escape($deviceid) . ") a";
        $query_id = $this->db->query($sqlid);
        $row = $query_id->row();
        $customer_id = $row->id;
        return $customer_id;

    }

    private function isExist($nama, $deviceid)
    {
        $this->db->where('CustomerName', $nama);
        $this->db->where('DeviceID', $deviceid);
        $query = $this->db->get($this->_tableName);
        $num = $query->num_rows();
        return $num > 0;
    }

    public function getByID($custid, $devno, $idoutlet)
    {

        $query = $this->db->get_where($this->_tableName,
            array('CustomerID' => $custid, 'DeviceID' => $idoutlet, 'DeviceNo' => $devno));
        $result = $query->result();

        return $result[0];

    }

    public function findAll($idoutlet) {
        $query = $this->db->get_where($this->_tableName,
            array('DeviceID' => $idoutlet));
        return $query->result();
    }
}