<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Journal extends MY_Controller {
    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();
        $this->load->library('NutaQuery');
        $this->load->model('Userperusahaancabang');
        $this->load->model('Perusahaanmodel');
        $devid = getLoggedInUserID();
        $result = $this->Perusahaanmodel->get_perusahaanno_by_devid($devid);
        $noperusahaan = 1;
        if (count($result) >= 0) {
            $noperusahaan = $result[0]->PerusahaanNo;
        }
        $this->nutaquery->setDeviceID(getLoggedInRegisterWithDeviceID());
        $this->DevIDAtauIDPerusahaan = $devid;
        $this->nutaquery->setPerusahaaanID($this->DevIDAtauIDPerusahaan, $noperusahaan);
        $cabangs = $this->Userperusahaancabang->getListCabang(getLoggedInUsername(), $this->DevIDAtauIDPerusahaan);
        $this->nutaquery->setCabangs($cabangs);
        $this->load->library('CurrencyFormatter');
        $this->load->helper('nuta_helper');

        $this->load->model('journalmodel');
        $this->load->model('masteraccount');
        $this->load->model('options');
    }

    public function index() {
        $journal = array();
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet) {
            $selected_outlet = 0;
        }

        if (isNotEmpty($this->input->get('ds') && $this->input->get('de'))) {
            $dateStart = $this->input->get('ds');
            $dateEnd = $this->input->get('de');
        }else{
            $dateStart = $this->input->post('date_start');
            $dateEnd = $this->input->post('date_end');
        }

        $account_default = $this->masteraccount->getAllAccount(getPerusahaanNo(), true, array('IsDefault' => true));
        if (count($account_default) == 0) {
            $init = $this->masteraccount->initializeAccount(getPerusahaanNo());
        }

        if (!isset($dateStart)) $dateStart = date('Y-m-d');
        if (!isset($dateEnd)) $dateEnd = date('Y-m-d');
        
        $options = $this->options->get_by_devid($selected_outlet);

        $journal = $this->journalmodel->getJournal(
            array(
                'PerusahaanNo' => getPerusahaanNo(),
                'DeviceID' => $selected_outlet,
                'DATE(JournalDate) >=' => $dateStart,
                'DATE(JournalDate) <=' => $dateEnd
            ),
            array(
                'JournalDate' => 'ASC',
                'JournalTime' => 'ASC'
            )
        );

        $data['title'] = 'Daftar Jurnal';
        $data['journal'] = $journal['journal'];
        $data['options'] = $options;
        $data['outlets'] = $this->GetOutletTanpaSemua();
        $data['selected_outlet'] = $selected_outlet;
        $data['date_start'] = $dateStart;
        $data['date_end'] = $dateEnd;
        $data['js_chart'] = array();
        $data['page_part'] = 'journal/index';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/filters/filter_date_mulai_sampai_js',
            'journal/index_js'
        );
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part', $data);
    }
    
    public function formdata() {
        $journal_id = $this->input->get('journal_id');
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet) {
            $selected_outlet = 0;
        }

        $dateStart = isNotEmpty($this->input->get('ds')) ? $this->input->get('ds') : date('Y-m-d');
        $dateEnd = isNotEmpty($this->input->get('de')) ? $this->input->get('de') : date('Y-m-d');

        $account = $this->masteraccount->getAllAccount(getPerusahaanNo(), true);
        $journal = array();

        if (isNotEmpty($journal_id)) {
            $journal= $this->journalmodel->getJournal(array(
                'PerusahaanNo' => getPerusahaanNo(),
                'DeviceID' => $selected_outlet,
                'JournalID' => $journal_id
            ), array(), true);
        }

        $data['js_part'] = array(
            'features/js/js_form',
            'journal/add_js'
        );

        $data['title'] = isNotEmpty($journal_id) ? 'Edit Jurnal Nomor '. $journal['journal']->JournalNumber : 'Transaksi Jurnal';
        $data['account'] = $account;
        $data['journal'] = $journal;
        $data['js_chart'] = array();
        $data['outlets'] = $this->GetOutletTanpaSemua();
        $data['selected_outlet'] = $selected_outlet;
        $data['date_start'] = $dateStart;
        $data['date_end'] = $dateEnd;
        $data['page_part'] = 'journal/add';
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part', $data);
    }

    public function saveJournal(){
        $edit_mode = false;

        $perusahaan_no = getPerusahaanNo();
        $journal_id = $this->input->post('journalid');
        $outlet = $this->input->post('outlet');
        $date = $this->input->post('journal_date');
        $time = $this->input->post('journaltime');

        $debet = $this->input->post('debet');
        $account= $this->input->post('journalaccountid');
        $credit = $this->input->post('credit');
        $detailnote = $this->input->post('detailnote');
        $detailid = $this->input->post('journaldetailid');
        $deletedid = $this->input->post('deletedid');

        if (isNotEmpty($journal_id)) $edit_mode = true;

        $param_header = array();
        $param_detail = array();
        $param_delete = array();

        $param_header = array(
            'JournalDate' => $date,
            'JournalTime' => $time,
            'TransactionName' => $this->input->post('transactionname'),
            'TransactionNumber' => $this->input->post('transactioncode'),
            'Note' => $this->input->post('note'),
            'JournalStatus' => 'normal',
            'TglJamUpdate' => date("Y-m-d H:i:s")
        );

        if ($edit_mode == false){
            $param_header['PerusahaanNo'] = $perusahaan_no;
            $param_header['DeviceID'] = $outlet;
            $param_header['JournalNumber'] = '';
            $param_header['TransactionID'] = '';
            $param_header['TransactionDeviceNo'] = '';
            $param_header['CreatedBy'] = getLoggedInUsername();
            $param_header['MonthlyClosingJournal'] = 0;
            $param_header['AnnualClosingJournal'] = 0;
            $param_header['JournalStatus'] = 'normal';
            $param_header['IsAuto'] = 0;
        }

        if (count($debet) == count($account) 
                && count($credit) == count($account) 
                && count($detailnote) == count($account) 
                && count($detailid) == count($account) 
                && count($deletedid) == count($account))
        {
            for ($i = 0; $i < count($account); $i++) {
                $values = array();

                if (isNotEmpty($deletedid[$i])) {
                    $param_delete[] = $deletedid[$i];
                }else{
                    $values = array(
                        'JournalDate' => $date. ' ' .$time,
                        'JournalAccountID' => $account[$i],
                        'Debit' => $debet[$i],
                        'Credit' => $credit[$i],
                        'DetailNote' => $detailnote[$i],
                        'TglJamUpdate' => date("Y-m-d H:i:s")
                    );

                    if ($edit_mode == false) {
                        $values['PerusahaanNo'] = $perusahaan_no;
                        $values['DeviceID']  = $outlet;
                    }else{
                        if (isNotEmpty($detailid[$i])) $values['DetailID'] = $detailid[$i];
                    }
                }

                $param_detail[] = $values;
            }
        }

        if ($edit_mode == true) {
            $exec = $this->journalmodel->updateJournal($perusahaan_no, $outlet, $journal_id, array(
                'journal' => $param_header,
                'journaldetail' => $param_detail,
                'deletedjournal' => $param_delete
            ));
        }else{
            $exec = $this->journalmodel->createJournal(array(
                'journal' => $param_header,
                'journal_detail' => $param_detail
            ));
        }
        
        return redirect (base_url('journal/?outlet=' .$this->input->post('outlet')));
    }

    public function deleteJournal() {
        $perusahaan_no = getPerusahaanNo();
        $selected_outlet = $this->input->post('outlet');
        $journal_id = $this->input->post('id');

        $result = $this->journalmodel->deleteJournal($perusahaan_no, $selected_outlet, $journal_id);
        if ($this->input->is_ajax_request()) {
            echo json_encode($result);
         }else{
             // do something
         }
    }

    public function checkpointJournal() {
        $selected_outlet = $this->input->post('outlet');
        $dateStart = $this->input->post('date_start');
        $dateEnd =  $this->input->post('date_end');

        $this->load->model('journalmodel');
        $this->load->model('masteraccount');

        //$account_type = $this->masteraccount->AccountType(getPerusahaanNo());
        //$account_default = $this->masteraccount->getAll(getPerusahaanNo(), array(), array('IsDefault' => true));
        $account_balance = $this->masteraccount->AccountBalance(getPerusahaanNo(), trim(strtolower($selected_outlet)), 'balance', $dateStart);
        $journal = $this->journalmodel->generateLedger(getPerusahaanNo(), trim(strtolower($selected_outlet)), $dateStart, $dateEnd);
        
        $values = array();
        
        foreach ($journal as $key => $val) {
            if ($val->IsDefault == '1') continue;
            
            $key_bal = array_search($val->JournalAccountID, array_column($account_balance, 'JournalAccountID'));
            
            $debit = empty($val->Debit) ? 0 : $val->Debit;
            $credit = empty($val->Credit) ? 0 : $val->Credit;
            $balance = isNotEmpty($account_balance[$key_bal]->Saldo) ? (double)$account_balance[$key_bal]->Saldo : 0;
            $subtotal = 0;
            $total = 0;

            if ($val->AccountType == 1 || $val->AccountType == 2 || $val->AccountType == 6 || $val->AccountType == 8 || $val->AccountType == 9) {
                $subtotal = $debit - $credit;
            }else{
                $subtotal = $credit - $debit;
            }

            $total = $balance + $subtotal;

            $arr = array(
                'PerusahaanNo' => $perusahaan_no,
                'DeviceID' => $selected_outlet,
                'JournalAccountID' => $val->JournalAccountID,
                'LastPeriodDate' => $dateEnd,
                'JournalType' => 'akun',
                'Saldo' => $saldo,
                'CreatedAt' => date("Y-m-d H:i:s")
            );

            $values[] = $arr;
        }

        $result = $this->journalmodel->generateCheckpoint($values);

        return $result;
    }

}