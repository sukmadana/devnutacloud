<?php

class Masteraccount extends CI_Model
{
    var $_tableName = "masteraccount";
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
    
    // private //

    private function JournalAccountID($perusahaan_no) {
        $row = $this->db->select_max('JournalAccountID', 'ID')
            ->where('PerusahaanNo', $perusahaan_no)
            ->get($this->_tableName)->row();

        $result = 1;
        if (isset($row->ID) && !empty($row->ID)) {
            $result = (int)$row->ID + 1;
        }

        return $result;
    }

    private function GetAccount($perusahaan_no, $options = array(), $include_default_account = false) {
        $this->db->where('PerusahaanNo', $perusahaan_no);
        if ($include_default_account == false) $this->db->where('IsDefault', false);
        if (count($options) > 0) {
            foreach ($options as $key => $val) {
                if (is_array($val)) {
                    $this->db->where_in($key, $val);
                }else{
                    $this->db->where($key, $val);
                }
            }
        }
        $this->db->order_by('AccountType', 'ASC');
        $this->db->order_by('AccountCode', 'ASC');
        $this->db->order_by('AccountName', 'ASC');

        $result = $this->db->get($this->_tableName)->result();
        return $result;
    }

    // public //

    public function accountType() {
        $list[1] = 'Aktiva Lancar';
        $list[2] = 'Aktiva Tidak Lancar';
        $list[3] = 'Hutang';
        $list[4] = 'Ekuitas';
        $list[6] = 'Beban Pokok Penjualan';
        $list[7] = 'Pendapatan Lain';
        $list[8] = 'Biaya Operasional';
        $list[9] = 'Pajak';
        
        return $list;
    }

    public function accountTypeRiil() {
        $list[1] = 'Aktiva Lancar';
        $list[2] = 'Aktiva Tidak Lancar';
        $list[3] = 'Hutang';
        $list[4] = 'Ekuitas';

        return $list;
    }

    public function accountTypeNominal() {
        $list[6] = 'Beban Pokok Penjualan';
        $list[7] = 'Pendapatan Lain';
        $list[8] = 'Biaya Operasional';
        $list[9] = 'Pajak';

        return $list;
    }

    public function accountOtherIncome($perusahaan_no) {
        $result = $this->GetAccount($perusahaan_no, array('AccountType' => 7), true);
        return $result;
    }

    public function accountNonIncome($perusahaan_no) {
        $result = $this->GetAccount($perusahaan_no, array('AccountType' => [1, 2, 3, 4]), true);
        return $result;
    }

    public function accountCost($perusahaan_no) {
        $result = $this->GetAccount($perusahaan_no, array('AccountType' => [6, 8, 9]), true);
        return $result;
    }

    public function accountNonCost($perusahaan_no) {
        $result = $this->GetAccount($perusahaan_no, array('AccountType' => [1, 2, 3, 4]), true);
        return $result;
    }

    public function getAllAccount($perusahaan_no, $include_default_account = false, $options = array()) {
        $result = $this->GetAccount($perusahaan_no, $options, $include_default_account);
        return $result;
    }

    public function getRiilAccount($perusahaan_no, $include_default_account = false, $options = array()){
        $options['AccountType <'] = 5;
        $result = $this->GetAccount($perusahaan_no, $options, $include_default_account);
        return $result;
    }

    public function getNominalAccount($perusahaan_no, $include_default_account = false, $options = array()) {
        $options['AccountType >'] = 5;
        $result = $this->GetAccount($perusahaan_no, $options, $include_default_account);
        return $result;
    }

    public function initializeAccount($perusahaan_no) {
        $result = false;

        $this->db->where('PerusahaanNo', 0);
        $list = $this->db->get($this->_tableName)->result();

        if (!is_null($list) && count($list) > 0) {
            $parameter = [];
            foreach ($list as $key => $val) {
                $journal_account_id = $this->JournalAccountID($perusahaan_no);
                $parameter[] = array(
                    'PerusahaanNo' => $perusahaan_no,
                    'JournalAccountID' => $journal_account_id,
                    'AccountType' => $val->AccountType,
                    'AccountCode' => $val->AccountCode,
                    'AccountName' => $val->AccountName,
                    'IsDefault' => $val->IsDefault,
                    'TglJamUpdate' => date("Y-m-d H:i:s")
                );
            }
            $this->db->trans_start();
            $this->db->insert_batch($this->_tableName, $parameter);
            $this->db->trans_complete();
            $result = $this->db->trans_status();
        }

        return $result;
    }

    public function getById($perusahaan_no, $account_id) {
        $this->db->where('PerusahaanNo', $perusahaan_no);
        $this->db->where('JournalAccountID', $account_id);
        $result = $this->db->get($this->_tableName)->row();
        return $result;
    }

    public function create($parameter) {
        $journal_account_id = $this->JournalAccountID($parameter['PerusahaanNo']);
        $parameter['JournalAccountID'] = $journal_account_id;
        $this->db->insert($this->_tableName, $parameter);
        $result = $this->db->affected_rows() == 0 ? false : true;
        return array('status' => $result, 'data' => $parameter);
    }

    public function update($perusahaan_no, $account_id, $parameter) {
        $this->db->where('PerusahaanNo', $perusahaan_no);
        $this->db->where('JournalAccountID', $account_id);
        $result = $this->db->update($this->_tableName, $parameter);
        $parameter['JournalAccountID'] = $account_id;
        return array('status' => $result, 'data' => $parameter);
    }

    public function delete($perusahaan_no, $account_id) {
        $result = false;

        $this->db->where('PerusahaanNo', $perusahaan_no);
        $this->db->where('JournalAccountID', $account_id);
        $this->db->limit(1);
        $used = $this->db->get('journaldetail')->result();
        
        if (count($used) == 0) {
            $this->db->where('PerusahaanNo', $perusahaan_no);
            $this->db->where('JournalAccountID', $account_id);
            $result = $this->db->delete($this->_tableName);
        }
        
        return $result;
    }

    /**
     * Saldo Akun
     * type : balance(default - ledger), real(laporan neraca saldo), nominal(laporan laba rugi)
     * 
     */
    public function AccountBalance($perusahaan_no, $device_id, $type="balance", $min_date = null) {
        $this->db->select_max('x.LastPeriodDate');
        $this->db->where('x.PerusahaanNo = c.PerusahaanNo');
        $this->db->where('x.DeviceID = c.DeviceID');
        $this->db->where('x.JournalAccountID = c.JournalAccountID');
        if (!is_null($min_date)) $this->db->where('c.LastPeriodDate <=', $min_date);
        $subquery_maxdate = $this->db->get_compiled_select('checkpoint_journal x');
        $this->db->reset_query();

        $this->db->select('c.PerusahaanNo, c.JournalAccountID');
        $this->db->select_sum('c.Saldo');
        $this->db->from('checkpoint_journal c');
        $this->db->where('c.PerusahaanNo', $perusahaan_no);
        if ($device_id !== 'semua') $this->db->where('c.DeviceID', $device_id);
        $this->db->where('c.LastPeriodDate = ('.$subquery_maxdate.')');
        $this->db->group_by(array('c.PerusahaanNo', 'c.JournalAccountID'));
        $subquery_checkpoint = $this->db->get_compiled_select();
        $this->db->reset_query();

        $this->db->select('a.JournalAccountID, a.AccountCode, a.AccountName, a.AccountType, a.IsDefault, cp.Saldo');
        $this->db->from('masteraccount a');
        $this->db->join('('.$subquery_checkpoint.') as cp', 'cp.PerusahaanNo = a.PerusahaanNo AND cp.JournalAccountID = a.JournalAccountID', 'left');
        $this->db->where('a.PerusahaanNo', $perusahaan_no);
        if ($type == 'real') {
            $this->db->where('a.AccountType < 5');
        }else if ($type == 'nominal') {
            $this->db->where('a.AccountType > 5');
        }
        $this->db->order_by('a.AccountCode', 'ASC');
        $this->db->order_by('a.AccountName', 'ASC');
        $result = $this->db->get()->result();
        
        return $result;
    }

}