<?php 

/**
* 
*/
class Purchaseitemdetail extends MY_Model
{
	
	protected $table = "purchaseitemdetail";

	protected $primary_key = "DetailID";

    public function get_detail_id($deviceid)
    {
        $perusahaanno = getPerusahaanNo();
        $sql = "SELECT COALESCE(MAX(DetailID),0)+1 AS result 
                FROM (
                    SELECT DetailID
                    FROM {$this->table}
                    WHERE DeviceID = {$deviceid} AND PerusahaanNo = {$perusahaanno}
                    UNION ALL 
                    SELECT DetailID
                    FROM purchaseitemdetaildelete
                    WHERE DeviceID = {$deviceid} AND PerusahaanNo = {$perusahaanno} 
                    ) AS TID";

        return $this->db->query($sql)->first_row()->result;
    }

    public function get_purchaseitemdetail_rowarray($deviceid, $detailid, $devno, $pNo)
    {
        $query = $this->db->get_where( $this->table, array(
            'PerusahaanNo'      => $pNo,
            'DeviceID'        => $deviceid,
            'DetailID'          => $detailid,
            'DeviceNo'          => $devno,
            /*'Varian'            => 'Nuta',
            'PerusahaanNo'      => getPerusahaanNo()*/
        ) );
//        $query = $this->db->query("SELECT * FROM {$this->table} WHERE DeviceID = {$deviceid} AND DetailID = {$detailid}");
        $row = $query->row_array();
//        $sql = "SELECT * FROM {$this->table} WHERE DeviceID = {$deviceid} AND DetailID = {$detailid}";
//        $query = $this->db->query($sql);
//        return $query->row_array();
        return $row;
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
            $this->db->insert($this->table.'delete', [
                'DetailID' => $item->DetailID,
                'TransactionID' => $item->TransactionID,
                'DetailNumber' => $item->DetailNumber,
                'ItemID' => $item->ItemID,
                'ItemName' => $item->ItemName,
                'Quantity' => $item->Quantity,
                'Discount' => $item->Quantity,
                'UnitPrice' => $item->UnitPrice,
                'SubTotal' => $item->SubTotal,
                'Note' => $item->Note,
                'IsProduct' => $item->IsProduct,
                'Varian' => 'Nuta',
                'DeviceID' => $item->DeviceID,
                'PerusahaanID' => getLoggedInUserID(),
                'PerusahaanNo' => getPerusahaanNo(),
            ]);
        }
    }
}