<?php
include_once "CashBankIn/AddCashBankIn.php";
include_once "CashBankIn/ListCashBankIn.php";
include_once "CashBankIn/ViewCashBankIn.php";
include_once "CashBankIn/EditCashBankIn.php";
include_once "CashBankIn/DeleteCashBankIn.php";

class Uangmasuk extends MY_Controller
{
    use AddCashBankIn,ListCashBankIn,ViewCashBankIn,EditCashBankIn,DeleteCashBankIn;
    function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();

        $this->data = [
            'page_part' 					=> 'uang-masuk/index',
            'js_chart' 						=> [],
            'js_part' 						=> array('features/js/js_form'),
            'outlets' 						=> $this->GetOutletTanpaSemua(),
            'selected_outlet'				=> $this->getOutletId(),
            'visibilityMenu' 				=> $this->visibilityMenu,
            'isLaporanStokVisible' 			=> $this->IsLaporanStokVisible(),
            'isLaporanPembelianVisible' 	=> $this->IsLaporanPembelianVisible() ,
            'isLaporanPriceVarianVisible'	=> $this->IsLaporanVarianHargaVisible() ,
            'menu' 							=> "uangmasuk",
        ];
    }

    protected function getOutletId()
    {
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet)
        {
            $selected_outlet = 0;
        }

        return $selected_outlet;
    }

    protected function addJsPart($location)
    {
        if ($location) {
            array_push($this->data['js_part'], $location);
        }
    }

    protected function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    protected function getItem($outlet, $iscloud = 1, $deviceno = -1)
    {
        $accountTypeClause = "";
        if ($iscloud === 0) {
            $accountTypeClause = "AND (AccountType=2 OR (AccountType=1 AND DeviceNo = ?))";
        } else {
            $accountTypeClause = "AND AccountType=2";
        }

        $query_string = "
SELECT 
AccountID,DeviceNo,
    CASE AccountType
        WHEN 1 THEN AccountName
        WHEN 2 THEN CONCAT(BankName , ' ', AccountNumber, ' ', AccountName)
    END AS AccountName
FROM
    nutacloud.mastercashbankaccount
WHERE
    DeviceID = ?
    $accountTypeClause
        ";

        $param = array($outlet);
        if ($iscloud === 0) {
            array_push($param, $deviceno);
        }

        $query_mastercashbank = $this->db->query($query_string, $param);
        $mastercashbank = $query_mastercashbank->result();
        return $mastercashbank;
    }

    protected function get_list_uangmasuk_by_id($outlet, $id, $iscloud = 1, $deviceno = -1)
    {
        $deviceClause = '';
        if ($iscloud === 1) {
            $tbl = 'cloud_cashbankin';
        } else {
            $tbl = 'cashbankin';
            $deviceClause = 'AND DeviceNo = ?';
        }
        
        $query = "
        select * from $tbl
            where DeviceID = ? 
                and PerusahaanNo = ?
                and TransactionID= ?
                $deviceClause

        ";
        $param = array($outlet, getPerusahaanNo(), $id);
        if ($iscloud === 0) {
            array_push($param, $deviceno);
        }

        $query = $this->db->query($query, $param);
        return $query->row();
    }

    protected function get_uangmasuk_by_id_rowarray($outlet, $id, $iscloud = 1)
    {
        $tbl = $iscloud === 1 ? "cloud_cashbankin" : "cashbankin";
        $query = "
        select * from $tbl 
            where DeviceID=?
                and PerusahaanNo=?
                and TransactionID=?

        ";
        $query = $this->db->query($query, array($outlet, getPerusahaanNo(), $id));
        return $query->row_array();
    }

    public function simpan(){
        if ($this->input->post('iscloud', TRUE) === NULL) {
            show_error("Invalid Parameter", 400, "Bad Request");
        }

        $iscloud = (int)$this->input->post('iscloud', TRUE);
//        echo $this->input->post('accountid');
//        echo $this->input->post('paidto');
//        echo $this->input->post('jenis');
//        echo $this->input->post('note');
//        echo $this->input->post('amount');
        //var_dump($this->input->post());
        $this->load->model('Uangmasukmodel');

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($this->input->post('outlet'));
        $cloudDevno = 0;
        if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
            $cloudDevno = 1;
        }

        if ($this->input->post('mode')=="new") {
            $this->attributes = $this->generateAttribute($cloudDevno);
            $storing = $this->Uangmasukmodel->create($this->attributes);
        } else if ($this->input->post('mode')=="edit") {
            $this->attributes = $this->generateAttribute($cloudDevno);
            $clause = array(
                "PerusahaanNo"  => getPerusahaanNo(),
                "DeviceID"		=> $this->input->post('outlet'),
                "TransactionID"	=> $this->input->post('transaction_id')
            );
            if ($iscloud === 0) {
                $clause["DeviceNo"] = $this->input->post('deviceno', TRUE);
            }
            //var_dump($clause); die;
            $storing = $this->Uangmasukmodel->updateUangMasuk($iscloud, $clause, $this->attributes);
        }

        if (!$storing) {
//            var_dump($this->attributes);
//            var_dump($storing);
            return redirect(base_url('uangmasuk/add?outlet=' . $this->input->post('outlet')));
        }

        // push data to firebase
        if ($this->input->post('mode')=="new") {
            $tr_id = $this->attributes['TransactionID'];
        } else if ($this->input->post('mode')=="edit") {
            $tr_id = $this->input->post('transaction_id');
        }
        $insert_query = $this->get_uangmasuk_by_id_rowarray($this->input->post('outlet'), $tr_id, $iscloud);
        $last_insert_data =  array(
            "table"     => $iscloud === 1 ? 'cloudcashbankin' : 'cashbankin',
            "column"    => $insert_query
        );
        $this->load->model('Firebasemodel');
        $this->Firebasemodel->push_firebase($this->input->post('outlet'),$last_insert_data,
            $tr_id, $cloudDevno, getPerusahaanNo(), 0);

        redirect(base_url('uangmasuk/?outlet=' . $this->input->post('outlet').
            '&ds=' . $this->input->post('date_start') .'&de=' . $this->input->post('date_end')));
//        return redirect($this->input->server('HTTP_REFERER'));
    }

    protected function generateAttribute($cloudDevno)
    {
        $account = explode(".",$this->input->post('accountid'));
        if ($this->input->post('mode')=="edit") {
            return [
                'AccountID' => $account[0],
                'AccountDeviceNo' => $account[1],
                'CashBankAccountName' => $this->input->post('cashbankaccountname'),
                'ReceivedFrom' => $this->input->post('receivedfrom'),
                'IncomeType' => $this->input->post('jenis'),
                'Note' => $this->input->post('note'),
                'Amount' => $this->input->post('amount'),
                'EditedBy' => getLoggedInUsername(),
                'EditedDate' => date('Y-m-d'),
                'EditedTime' => date('H:i')
            ];
        }
        $id = $this->db->query($this->Uangmasukmodel->get_transaction_id($this->input->post('outlet')))->first_row();
        $code = $this->db->query($this->Uangmasukmodel->get_generate_nomoruangmasuk(
            $this->getDateTime()['date'], $this->input->post('outlet')))->first_row();
        return [
            'TransactionID' => $id->result,
            'DeviceNo' => $cloudDevno,
            'TransactionNumber' => $code->result,
            'TransactionDate' => $this->getDateTime()['date'],
            'TransactionTime' => $this->getDateTime()['time'],
            'AccountID' => $account[0],
            'AccountDeviceNo' => $account[1],
            'CashBankAccountName' => $this->input->post('cashbankaccountname'),
            'ReceivedFrom' => $this->input->post('receivedfrom'),
            'IncomeType' => $this->input->post('jenis'),
            'Note' => $this->input->post('note'),
            'Amount' => $this->input->post('amount'),
            'DeviceID' => $this->input->post('outlet'),
            'PerusahaanID' => getLoggedInUserID(),
            'PerusahaanNo' => getPerusahaanNo(),
            'CreatedBy' => getLoggedInUsername(),
            'CreatedDate' => date('Y-m-d'),
            'CreatedTime' => date('H:i'),
            'Varian' => 'Nuta',
            'HasBeenDownloaded' => 0,
            'EditedBy' => '',
            'EditedDate' => '',
            'EditedTime' => ''
        ];
    }

    public function tesimporcsvgoogle()
    {
        //$spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vQS0oo2ZQyHTto6_5PxQvmP56dCqm8DGmjIzLVvI-eq2d0utzHPsIpQWeMHYb04XAENbbAoc_S1S65Y/pub?gid=0&single=true&output=csv";
        $spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vQS0oo2ZQyHTto6_5PxQvmP56dCqm8DGmjIzLVvI-eq2d0utzHPsIpQWeMHYb04XAENbbAoc_S1S65Y/pub?output=csv";

        if(!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";

        if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                //$spreadsheet_data[] = $data;
                var_dump($data);
            }
            fclose($handle);
        }
        else
            die("Problem reading csv");
    }

    protected function getDateTime()
    {
        return [
            'date' => date("Y-m-d"),
            'time' => date("H:i"),
        ];
    }
}
