<?php

/**
 *
 */
class Stockopname extends MY_Model
{
    protected $table = "stockopname";

    protected $primary_key = ['DeviceID', 'TransactionID'];

    public function get_transaction_id($deviceid)
    {
        return "SELECT COALESCE(MAX(TransactionID),0)+1 AS result 
FROM (
    SELECT TransactionID FROM stockopname 
    WHERE PerusahaanNo = " . getPerusahaanNo() . " AND DeviceID = {$deviceid}
    UNION ALL 
    SELECT TransactionID FROM stockopnamedelete 
    WHERE PerusahaanNo = " . getPerusahaanNo() . " AND DeviceID = {$deviceid}
) AS TID";
    }

    public function get_generate_nomorstokmasuk($date, $deviceid)
    {
        $format = date_format(date_create($date), 'ymd');

        return "SELECT CONCAT('SM/', '{$format}/' , COALESCE(MAX(CAST(REPLACE(StockOpnameNumber,'SM/{$format}/','') AS UNSIGNED)), 0) + 1) AS result 
                FROM stockopname 
                WHERE StockOpnameDate = '{$date}' AND DeviceID = {$deviceid} AND StockOpnameNumber LIKE 'SM%'";
    }

    public function get_sk_transaction_id($DeviceID)
    {
        return $this->get_transaction_id($DeviceID);
    }

    public function get_generate_nomorstokkeluar($date, $device)
    {
        $format = date_format(date_create($date), 'ymd');

        return "SELECT CONCAT('SK/', '{$format}/' , COALESCE(MAX(CAST(REPLACE(StockOpnameNumber,'SK/{$format}/','') AS UNSIGNED)), 0) + 1) AS result 
                FROM stockopname 
                WHERE StockOpnameDate = '{$date}' AND DeviceID = {$device} AND StockOpnameNumber LIKE 'SK%'";
    }

    public function get_generate_nomorkoreksistok($date, $device)
    {
        $format = date_format(date_create($date), 'ymd');

        return "SELECT CONCAT('KS/', '{$format}/' , COALESCE(MAX(CAST(REPLACE(StockOpnameNumber,'KS/{$format}/','') AS UNSIGNED)), 0) + 1) AS result 
                FROM stockopname 
                WHERE StockOpnameDate = '{$date}' AND DeviceID = {$device} AND StockOpnameNumber LIKE 'KS%'";
    }

    public function get_all_incoming_stock($deviceid, $dt1, $dt2)
    {
        $strDt1 =  $this->db->escape($dt1);
        $strDt2 =  $this->db->escape($dt2);
        return $this->db->query("SELECT a.TransactionID,a.DeviceNo, a.StockOpnameDate, a.StockOpnameNumber, a.DeviceID, a.StockOpnameTime
                                FROM stockopname a
                                WHERE a.DeviceID={$deviceid} 
                                AND a.StockOpnameNumber LIKE 'SM%'
                                AND a.StockOpnameDate>={$strDt1} AND a.StockOpnameDate<={$strDt2}
                                order by StockOpnameDate desc, StockOpnameTime DESC, TransactionID DESC
                                ")->result();
    }

    public function get_all_outgoing_stock($deviceid, $dt1, $dt2)
    {
        $strDt1 =  $this->db->escape($dt1);
        $strDt2 =  $this->db->escape($dt2);
        return $this->db->query("SELECT a.TransactionID,a.DeviceNo, a.StockOpnameDate, a.StockOpnameNumber, a.DeviceID, a.StockOpnameTime
                                FROM stockopname a
                                WHERE a.DeviceID={$deviceid} 
                                AND a.StockOpnameNumber LIKE 'SK%'
                                AND a.StockOpnameDate>={$strDt1} AND a.StockOpnameDate<={$strDt2}
                                order by StockOpnameDate desc, StockOpnameTime DESC, TransactionID DESC
                                ")->result();
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

        return $this->db->insert($this->table . 'delete', [
            'TransactionID' => $item->TransactionID,
            'StockOpnameNumber' => $item->StockOpnameNumber,
            'StockOpnameDate' => $item->StockOpnameDate,
            'StockOpnameTime' => $item->StockOpnameTime,
            'DeviceID' => $item->DeviceID,
            'IsDetailsSaved' => 1,
            'PerusahaanID' => $item->PerusahaanID,
            'PerusahaanNo' => $item->PerusahaanNo,
            'Varian' => 'Nuta',
            'CreatedBy' => $item->CreatedBy,
            'CreatedDate' => $item->CreatedDate,
            'CreatedTime' => $item->CreatedTime,
            'EditedBy' => $item->EditedBy,
            'EditedDate' => $item->EditedDate,
            'EditedTime' => $item->EditedTime,
            'HasBeenDownloaded' => 0
        ]);
    }
}