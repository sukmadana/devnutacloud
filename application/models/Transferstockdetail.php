<?php 

/**
* 
*/
class Transferstockdetail extends MY_Model
{
	protected $table = "transferstockdetail";
	
	protected $primary_key = 'DetailID';

    public function get_item_by_id($deviceid, $id)
    {
        return $this->db->get_where($this->table,[
            'DeviceID' => $deviceid,
            'TransactionID' => $id
            ])->result();
    }

	public function get_detail_id($deviceid)
    {
        $sql = "SELECT COALESCE(MAX(DetailID),0)+1 AS result 
                FROM (
                    SELECT DetailID
                    FROM transferstockdetail
                    WHERE DeviceID = {$deviceid}
                    UNION ALL 
                    SELECT DetailID
                    FROM transferstockdetaildelete
                    WHERE DeviceID = {$deviceid} ) AS TID";

        return $this->db->query($sql)->first_row()->result;
    }

    public function force_delete($where_attr = [])
    {
        return parent::delete($where_attr);
    }
}