<?php

class JournalModel extends CI_Model
{
    var $_tableName = array("journal", "journaldetail");
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

    private function journalQueries($parameter) {
        $last_journal_id = "SELECT COALESCE (MAX(a.JournalID), 0) + 1 as ID FROM journal a WHERE a.PerusahaanNo = ".$parameter['PerusahaanNo']." AND a.DeviceID = ". $parameter['DeviceID'];
        $last_journal_detail_id = "SELECT COALESCE (MAX(a.DetailID), 0) + 1 as ID FROM journaldetail a WHERE a.PerusahaanNo = ".$parameter['PerusahaanNo']." AND a.DeviceID = ". $parameter['DeviceID'];

        return array(
            0 => $last_journal_id,
            1 => $last_journal_detail_id
        );
    }

    public function getJournal($parameter, $order, $by_id = false) {
        if (!is_null($parameter)) {
            $this->db->where($parameter);
        }

        if (!empty($order)) {
            if (is_array($order)) {
                foreach ($order as $key => $val) {
                    $this->db->order_by($key, $val);
                }
            }else{
                $this->db->order_by($order);
            }
        }

        $value = $this->db->get($this->_tableName[0]);
        $datadetail = array();
        if ($by_id == false) {
            $data = $value->result();
        }else{
            $data = $value->row();

            $this->db->where($parameter);
            $datadetail = $this->db->get($this->_tableName[1])->result();
        }
        return array('journal' => $data, 'journaldetail' => $datadetail);
    }

    public function createJournal($parameter) {
        $param_header = $parameter['journal'];
        $param_detail = $parameter['journal_detail'];

        $query = $this->journalQueries(
            array(
                'PerusahaanNo' => $param_header['PerusahaanNo'],
                'DeviceID' => $param_header['DeviceID']
            )
        );

        $journal_id = (int)$this->db->query($query[0])->row()->ID;
        $journal_detail_id = (int)$this->db->query($query[1])->row()->ID;
        
        $param_header['JournalID'] = $journal_id;
        
        for ($i = 0; $i < count($param_detail); $i++) {
            $param_detail[$i]['JournalID'] = $journal_id;
            $param_detail[$i]['DetailID'] = $journal_detail_id;
            $journal_detail_id += 1;
        }
        
        $this->db->trans_start();
        $exec_header = $this->db->insert('journal', $param_header);
        $exec_detail = $this->db->insert_batch('journaldetail', $param_detail);
        $this->db->trans_complete();
        $status = $this->db->trans_status();
        
        return array('status' => $status);
    }

    public function updateJournal($perusahaan_no, $device_id, $journal_id, $parameter) {
        $this->db->trans_start();
        $this->db->where('PerusahaanNo', $perusahaan_no);
        $this->db->where('DeviceID', $device_id);
        $this->db->where('JournalID', $journal_id);
        $this->db->update($this->_tableName[0], $parameter['journal']);

        if (count($parameter['deletedjournal']) > 0) {
            foreach ($parameter['deletedjournal'] as $row) {
                $this->db->where('PerusahaanNo', $perusahaan_no);
                $this->db->where('DeviceID', $device_id);
                $this->db->where('JournalID', $journal_id);
                $this->db->where('DetailID', $row);
                $this->db->delete($this->_tableName[1]);
            }
        }

        foreach ($parameter['journaldetail'] as $key => $row) {
            $this->db->where('PerusahaanNo', $perusahaan_no);
            $this->db->where('DeviceID', $device_id);
            $this->db->where('DetailID', $row['DetailID']);
            $this->db->where('JournalID', $journal_id);
            $this->db->update($this->_tableName[1], $row);
        }
        $this->db->trans_complete();
        $status = $this->db->trans_status();
        return array('status' => $status);
    }

    public function deleteJournal($perusahaan_no, $device_id, $journal_id) {
        $this->db->trans_start();
        $this->db->where('PerusahaanNo', $perusahaan_no);
        $this->db->where('DeviceID', $device_id);
        $this->db->where('JournalID', $journal_id);
        $exec_delete = $this->db->delete($this->_tableName);
        $this->db->trans_complete();
        $status = $this->db->trans_status();
        return array('status' => $status);
    }
    
    public function generateGeneralLedger($perusahaan_no, $device_id, $date_start, $date_end) {
        $this->db->select('a.AccountCode, a.AccountName, j.JournalNumber, DATE(j.JournalDate) AS JournalDate, j.JournalTime, j.TransactionName, j.TransactionNumber, Debit, Credit, 0 AS Saldo');
        $this->db->from('journal j');
        $this->db->join('journaldetail d', 'd.PerusahaanNo = j.PerusahaanNo AND d.DeviceID = j.DeviceID AND d.JournalID = j.JournalID');
        $this->db->join('masteraccount a', 'a.PerusahaanNo = d.PerusahaanNo AND a.JournalAccountID = d.JournalAccountID');
        $this->db->where('j.PerusahaanNo', $perusahaan_no);
        if ($device_id !== 'semua') $this->db->where('j.DeviceID', $device_id);
        $this->db->where('DATE(j.JournalDate) >=', $date_start);
        $this->db->where('DATE(j.JournalDate) <=', $date_end);
        $this->db->order_by('a.AccountCode', 'ASC');
        $this->db->order_by('DATE(j.JournalDate)', 'ASC');
        $this->db->order_by('a.AccountName', 'ASC');
        $result = $this->db->get()->result();
        return $result;
    }

    public function generateLedger($perusahaan_no, $device_id, $date_start, $date_end) {
        $this->db->select('jd.JournalAccountID, jd.PerusahaanNo, SUM(jd.Debit) AS Debit, SUM(jd.Credit) AS Credit');
        $this->db->from('journal j');
        $this->db->join('journaldetail jd', 'jd.PerusahaanNo = j.PerusahaanNo AND jd.DeviceID = j.DeviceID AND jd.JournalID = j.JournalID');
        if ($device_id !== 'semua') $this->db->where('jd.DeviceID', $device_id);
        $this->db->where('DATE(j.JournalDate) >=', $date_start);
        $this->db->where('DATE(j.JournalDate) <=', $date_end);
        $this->db->group_by(array('jd.JournalAccountID', 'jd.PerusahaanNo'));

        $subquery_journal = $this->db->get_compiled_select();
        $this->db->reset_query();

        $this->db->select('a.JournalAccountID, a.AccountType, a.AccountCode, a.AccountName, a.IsDefault, jnl.Debit, jnl.Credit');
        $this->db->from('masteraccount a');
        $this->db->join('('.$subquery_journal.') AS jnl', 'jnl.JournalAccountID = a.JournalAccountID AND jnl.PerusahaanNo = a.PerusahaanNo', 'left');
        $this->db->where('a.PerusahaanNo', $perusahaan_no);
        $this->db->order_by('a.AccountCode', 'ASC');
        $result = $this->db->get()->result();
        
        return $result;
    }

    public function generateCheckpoint($data) {
        $result = $this->db->insert_batch('checkpoint_journal', $data);
        return $result;
    }

}