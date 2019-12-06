<?php

class Datarekeningmodel extends CI_Model
{
    var $_tableName = "mastercashbankaccount";
    protected $_dbMaster;

    public function __construct ()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Firebasemodel');
    }

    public function datarekening($Outlet)
    {
        return "
        SELECT AccountID,
        DeviceNo,
        BankName,
        AccountNumber,
        AccountName
        FROM mastercashbankaccount 
        WHERE 
        DeviceID = " . $this->db->escape($Outlet) . " AND AccountType=2 ORDER BY AccountID ASC";
    }

    protected function initDbMaster ()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function createNewDataRekening ($bankName, $accountNumber, $accountName, $idoutlet)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'lower(BankName)' => strtolower($bankName), 'lower(AccountNumber)' => strtolower($accountNumber)));
        $query = $this->_dbMaster->get($this->_tableName);
        $count = $query->num_rows();
        if ($count > 0) {
            $result = $query->result();
            return 'No. Rekening ini sudah ada, silahkan pakai No. Rekening lain.';
        }

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($idoutlet);
        $cloudDevno = 0;
        if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
            $cloudDevno = 1;
        }

        $accountID = $this->getMaxTransactionId($idoutlet);
        $this->_dbMaster->where(array(
            'DeviceID' => $idoutlet
        ));
        $this->_dbMaster->insert($this->_tableName, array(
            'AccountID' => $accountID,
            'DeviceNo' => $cloudDevno,
            'AccountType' => 2,
            'AccountName' => $accountName,
            'BankName' => $bankName,
            'AccountNumber' => $accountNumber,
            'HasEDC' => false,
            'ClearingDayEDC' => 0,
            'Varian' => 'Nuta',
            'DeviceID' => $idoutlet,
            'PerusahaanNo' => getPerusahaanNo(),
        ));

        // push data to firebase
        $query_datainserted = $this->_dbMaster->get_where($this->_tableName, array(
            'AccountID' => $accountID,
            'DeviceID' => $idoutlet,
            'DeviceNo' => $cloudDevno));
        $last_insert_data = array(
            "table" => $this->_tableName,
            "column" => $query_datainserted->row_array()
        );
        $this->Firebasemodel->push_firebase($idoutlet, $last_insert_data,
            $accountID, $cloudDevno, getPerusahaanNo(), 0);
        return $accountID . "." . $cloudDevno;
    }

    public function updateDataRekening ($bankName, $accountNumber, $accountName, $idoutlet, $accountID, $devno)
    {
        $realID = $accountID;

        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'AccountID' => $realID, 'DeviceNo' => $devno));
        $this->_dbMaster->update($this->_tableName, array(
            'AccountName' => $accountName,
            'BankName' => $bankName,
            'AccountNumber' => $accountNumber
        ));

        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'AccountID' => $realID, 'DeviceNo' => $devno));
        $query = $this->_dbMaster->get($this->_tableName);
        $result = $query->result();

        if(count($result) > 0) {
            $last_update_data = array(
                "table" => $this->_tableName,
                "column" => $query->row_array()
            );
            $this->Firebasemodel->push_firebase($idoutlet, $last_update_data,
                $result[0]->AccountID, $result[0]->DeviceNo, getPerusahaanNo(), 0);
            return $result[0]->AccountID . "." . $result[0]->DeviceNo;
        } else {
            return $this->createNewDatarekening($bankName, $accountNumber, $accountName, $idoutlet);;
        }
    }

    public function getByAccountID($accountID, $devno, $idoutlet)
    {

        $query = $this->db->get_where($this->_tableName,
            array('AccountID' => $accountID, 'DeviceID' => $idoutlet, 'DeviceNo' => $devno));
        $result = $query->result();

        return $result[0];

    }

    public function getMaxTransactionId ($idoutlet)
    {
        $queryid = $this->db->query("SELECT COALESCE (max(AccountID)+1,1) id  
            FROM (
                SELECT AccountID
                FROM mastercashbankaccount 
                WHERE DeviceID={$this->db->escape($idoutlet)}
                UNION ALL
                    SELECT AccountID
                    FROM mastercashbankaccountdelete
                    WHERE DeviceID={$this->db->escape($idoutlet)}
            ) AS CID");

        $resultid = $queryid->result();
        $accountID = $resultid[0]->id;
        return $accountID;
    }

    public function deleting ($attr = [])
    {
        $this->backuping($attr);

        $deleted_data = array(
            "table" => "delete" . $this->_tableName,
            "column" => array(
                "AccountID" => $attr['AccountID'],
                "DeviceNo" => $attr['DeviceNo']
            )
        );
        $this->Firebasemodel->push_firebase($attr['DeviceID'], $deleted_data,
            $attr['AccountID'], $attr['DeviceNo'], getPerusahaanNo(), 0);

        return $this->db->delete($this->_tableName, $attr);
    }

    public function backuping ($attr)
    {
        $datarekening = $this->db->get_where($this->_tableName, $attr);
        $datarekening = $datarekening->first_row();

        return $this->db->insert($this->_tableName.'delete', [
            'AccountID' => $datarekening->AccountID,
            'DeviceNo' => $datarekening->DeviceNo,
            'AccountType' => $datarekening->AccountType,
            'AccountName' => $datarekening->AccountName,
            'BankName' => $datarekening->BankName,
            'AccountNumber' => $datarekening->AccountNumber,
            'HasEDC' => $datarekening->HasEDC,
            'ClearingDayEDC' => $datarekening->ClearingDayEDC,
            'DeviceID' => $datarekening->DeviceID, 'Varian' => 'Nuta',
            'PerusahaanID' => $datarekening->PerusahaanID,
            'PerusahaanNo' => $datarekening->PerusahaanNo,
        ]);
    }
}