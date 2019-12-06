<?php 

/**
* 
*/
class Purchase extends MY_Model
{
	protected $table = "purchase";
	
	protected $primary_key = ['DeviceID', 'TransactionID'];

    public function get_all($deviceid, $period)
    {
        $sql = "SELECT * FROM {$this->table} WHERE DeviceID = {$deviceid}";
        if ($period) {
            $p_date_start = $period[0];
            $p_date_end = $period[1];

            $sql .= " AND PurchaseDate >= '$p_date_start' AND PurchaseDate <= '$p_date_end'";

        }
        return $this->db->query($sql ."order by TransactionID desc")->result();
    }

    public function get_single_transaction($deviceid, $id, $devno)
    {
        $sql = "SELECT * FROM {$this->table} WHERE DeviceID = {$deviceid} AND TransactionID = {$id} AND DeviceNo = {$devno}";
        return $this->db->query($sql)->result();
    }

    public function get_single_transactionrowarray($deviceid, $id, $devno)
    {
        $sql = "SELECT * FROM {$this->table} WHERE DeviceID = {$deviceid} AND TransactionID = {$id} AND DeviceNo = {$devno}";
        return $this->db->query($sql)->row_array();
    }

    public function get_transaction_id($deviceid)
    {
        $sql = "SELECT COALESCE(MAX(TransactionID),0)+1 AS result 
                                FROM (SELECT TransactionID FROM purchase
                                        WHERE DeviceID = {$deviceid}
                                        UNION ALL 
                                            (
                                                SELECT TransactionID FROM purchasedelete
                                                WHERE DeviceID = {$deviceid}
                                            )) AS TID ";

        return $this->db->query($sql)->first_row()->result;
    }

    public function get_generate_purchase($date, $deviceid)
    {
        $generate_date = date_format(date_create($date), 'ymd');
        
        $sql = "SELECT CONCAT('P/', '{$generate_date}/0/' , COALESCE(MAX(CAST(REPLACE(PurchaseNumber,'P/{$generate_date}/0/','') AS UNSIGNED)), 0) + 1) AS result 
                FROM purchase
                WHERE Purchasedate = '{$date}' AND DeviceID = {$deviceid}";

        return $this->db->query($sql)->first_row()->result;
    }

    public function get_generate_purchase_update($id, $deviceid, $date)
    {
        $generate_date = date_format(date_create($date), 'Ymd');
        
        $sql = "SELECT PurchaseNumber AS result 
                FROM purchase 
                WHERE TransactionID = '{$id}' AND DeviceID = {$deviceid}";

        return $this->db->query($sql)->first_row()->result;
    }

    public function delete($where_attr = [])
    {
        $this->backup($where_attr);
        parent::delete($where_attr);
    }

    public function backup($where_attr)
    {
        $item = $this->db->get_where($this->table, $where_attr);
        $item = $item->first_row();

        return $this->db->insert($this->table.'delete', [
            'TransactionID' => $item->TransactionID,
            'DeviceNo' => $item->DeviceNo,
            'PurchaseNumber' => $item->PurchaseNumber,
            'PurchaseDate' => $item->PurchaseDate,
            'PurchaseTime' => $item->PurchaseTime,
            'SupplierName' => $item->SupplierName,
            'Total' => $item->Total,
            'FinalDiscount' => $item->FinalDiscount,
            'Rounding' => $item->Rounding,
            'Donation' => $item->Donation,
            'PaymentMode' => $item->PaymentMode,
            'CashAccountID' => $item->CashAccountID,
            'BankAccountID' => $item->BankAccountID,
            'CashAccountDeviceNo' => $item->CashAccountDeviceNo,
            'BankAccountDeviceNo' => $item->BankAccountDeviceNo,
            'TotalPayment' => $item->TotalPayment,
            'Change' => $item->Change,
            'ClearingDate' => $item->ClearingDate,
            'CardType' => $item->CardType,
            'CardName' => $item->CardName,
            'CardNumber' => $item->CardNumber,
            'BatchNumberEDC' => $item->BatchNumberEDC,
            'Pending' => $item->Pending,
            'SupplierID' => $item->SupplierID,
            'SupplierDeviceNo' => $item->SupplierDeviceNo,
            'DeviceID' => $item->DeviceID,
            'CashBankAccountName' => $item->CashBankAccountName,
            'IsDetailsSaved' => 1,
            'PerusahaanID' => getLoggedInUserID(),
            'PerusahaanNo' => getPerusahaanNo(),
            'Varian' => 'Nuta',
            'CreatedBy' => getLoggedInUsername(),
            'CreatedDate' => $item->CreatedDate,
            'CreatedTime' => $item->CreatedTime
        ]);
    }

    public function force_delete($where_attr = [])
    {
        return parent::delete($where_attr);
    }


    public function get_bank_account($outlet)
    {
//        echo $outlet . "<br>";
//        echo getLoggedInUserID() . "<br>";
//        echo getPerusahaanNo() . "<br>";
        $query = $this->db->get_where(
            'mastercashbankaccount', 
            array( 
                'DeviceID'      => $outlet,
                'PerusahaanNo'  => getPerusahaanNo(),
                'AccountType'   => 2,
            )
        );
        return $query->result();
    }



}