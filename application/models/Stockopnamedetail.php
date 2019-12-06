<?php

/**
 *
 */
class Stockopnamedetail extends MY_Model
{

    protected $table = "stockopnamedetail";

    protected $primary_key = "DetailID";

    public function get_sk_detail_id($DeviceID)
    {
        return "SELECT COALESCE(MAX(DetailID),0)+1 AS result 
                FROM (
                    SELECT DetailID
                    FROM stockopnamedetail
                    WHERE DeviceID = {$DeviceID}
                    UNION ALL 
                    SELECT DetailID
                    FROM stockopnamedetaildelete
                    WHERE DeviceID = {$DeviceID} ) AS TID";
    }

    public function get_detail_id($deviceid)
    {
        return "SELECT COALESCE(MAX(DetailID),0)+1 AS result 
                FROM (
                    SELECT DetailID
                    FROM stockopnamedetail
                    WHERE DeviceID = {$deviceid}
                    UNION ALL 
                    SELECT DetailID
                    FROM stockopnamedetaildelete
                    WHERE DeviceID = {$deviceid} ) AS TID";
    }

    public function force_delete($where_attr = [])
    {
        return parent::delete($where_attr);
    }

    public function delete($where_attr = [])
    {
        $this->backup($where_attr);
        parent::delete($where_attr);
    }

    public function backup($where_attr)
    {
        $items = $this->db->get_where($this->table, $where_attr);
        $items = $items->result();
        foreach ($items as $key => $item) {
            $this->db->insert($this->table . 'delete', [
                'DetailID' => $item->DetailID,
                'TransactionID' => $item->TransactionID,
                'DetailNumber' => $item->DetailNumber,
                'ItemID' => $item->ItemID,
                'StockByApp' => $item->StockByApp,
                'Varian' => 'Nuta',
                'Note' => $item->Note,
                'RealStock' => $item->RealStock,
                'DeviceID' => $item->DeviceID,
                'PerusahaanID' => getLoggedInUserID(),
                'PerusahaanNo' => getPerusahaanNo(),
                'HasBeenDownloaded' => 0
            ]);
        }
    }

    public function get_item_stok($outlet, $id, $devno)
    {
        $query = "
        SELECT 
            s.DetailID,
            s.DeviceNo DetailDeviceNo,
            s.StockByApp,
            s.RealStock,
            s.Note, 
            s.ItemID,
            s.ItemDeviceNo,
            i.ItemName,
            i.Unit
        FROM stockopnamedetail s
        INNER JOIN masteritem i on i.ItemID=s.ItemID AND i.DeviceNo=s.ItemDeviceNo
            where 
                s.DeviceID='$outlet' 
                and s.PerusahaanNo='".getPerusahaanNo()."' 
                and s.TransactionID='$id'
                and s.DeviceNo='$devno'
                and i.DeviceID='$outlet' 
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function get_stockopnamedetail_rowarray($outlet, $id, $devno)
    {
        $query = "
        SELECT *
        FROM stockopnamedetail s
            where 
                s.DeviceID='$outlet' 
                and s.PerusahaanNo='".getPerusahaanNo()."' 
                and s.DetailID='$id'
                and s.DeviceNo='$devno'
        ";
        $query = $this->db->query($query);
        return $query->row_array();
    }

    public function update_item($attr, $clause){
        $this->db->where($clause);
        return $this->db->update("stockopnamedetail", $attr);
    }
}