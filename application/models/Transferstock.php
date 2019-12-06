<?php 

/**
* 
*/
class Transferstock extends MY_Model
{
	protected $table = "transferstock";
	
	protected $primary_key = ['DeviceID', 'TransactionID'];

    public function get_item_by_id($deviceid, $id)
    {
        return $this->db->get_where($this->table,[
            'DeviceID' => $deviceid,
            'TransactionID' => $id
            ])->first_row();
    }

    public function get_items_in_two_outlets($devid1,$devid2)
    {
        $sql = "SELECT * FROM masteritem WHERE DeviceID IN(".$devid1.",".$devid2.")";

        return $this->db->query($sql)->result();
    }
    
    public function get_all($deviceid,$date_start=null,$date_end=null)
    {

        if ($date_start && $date_end) {
            $date_end_mod = new DateTime($date_end);

            $period = new DatePeriod(
                new DateTime($date_start),
                new DateInterval('P1D'),
                $date_end_mod->modify('+1 day')
            );

            $periode='';
            foreach( $period as $date) { 
                $periode .= "'".$date->format('Y-m-d')."',"; 
            } 

            $periode = rtrim($periode,',');
            $periode = "and a.TransferDate IN($periode)";
        } else {
            $periode=  "";
        }

        return $this->db->query("SELECT 
                                    a.TransactionID, a.TransferDate, a.TransferNumber, 
                                    a.DeviceID, a.TransferToDeviceID, a.TransferTime , b.Note
                                FROM
                                    transferstock a
                                        INNER JOIN
                                    transferstockdetail b ON a.TransactionID = b.TransactionID
                                    AND a.DeviceID = b.DeviceID
                                WHERE a.DeviceID={$deviceid} AND a.TransferNumber LIKE 'TS%' $periode
                                GROUP BY 
                                    a.TransferNumber")->result();
    }

    public function get_transaction_id($deviceid)
    {
        $sql = "SELECT COALESCE(MAX(TransactionID),0)+1 AS result 
                                FROM (
                                        SELECT TransactionID 
                                        FROM transferstock
                                        WHERE 
                                            DeviceID = {$deviceid}
                                        UNION ALL 
                                            SELECT TransactionID 
                                            FROM transferstockdelete
                                            WHERE 
                                                DeviceID = {$deviceid}
                                    ) AS TID";
        return $this->db->query($sql)->first_row()->result;
    }

    public function get_generate_transferstock($date, $deviceid)
    {
        $generate_date = date_format(date_create($date), 'ymd');
        
        $sql = "SELECT CONCAT('TS/', '{$generate_date}/' , COALESCE(MAX(TransactionID), 0) + 1) AS result 
                    FROM transferstock 
                    WHERE TransferDate = '{$date}' AND DeviceID = {$deviceid}";
                    
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
            'DeviceID' => $item->DeviceID,
            'TransactionID' => $item->TransactionID,
            'TransferNumber' => $item->TransferNumber,
            'TransferDate' => $item->TransferDate,
            'TransferTime' => $item->TransferTime,
            'TransferToDeviceID' => $item->TransferToDeviceID,
            'EditedBy' => $item->EditedBy,
            'EditedDate' => $item->EditedDate,
            'EditedTime' => $item->EditedTimea,
            'CreatedVersionCode' => $item->CreatedVersionCode,
            'EditedVersionCode' => $item->EditedVersionCode,
            'RowVersion' => $item->RowVersion,
            'HasBeenDownloaded' => $item->HasBeenDownloaded,
            'HasBeenDownloaded2' => $item->HasBeenDownloaded2,
            'TglJamUpdate' => $item->TglJamUpdate,
            'IsDetailsSaved' => 1,
            'PerusahaanID' => getLoggedInUserID(),
            'PerusahaanNo' => getPerusahaanNo(),
            'Varian' => 'nuta',
            'CreatedBy' => getLoggedInUsername(),
            'CreatedDate' => $item->CreatedDate,
            'CreatedTime' => $item->CreatedTime
        ]);
    }

    public function force_delete($where_attr = [])
    {
        return parent::delete($where_attr);
    }

}