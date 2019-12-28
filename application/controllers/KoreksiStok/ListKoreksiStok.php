<?php

trait ListKoreksiStok
{
    public function index()
    {
        // if (in_array($this->data['selected_outlet'], $this->data['outlets']))
        // {
        //  redirect('/koreksistok');
        // }

        if (isset($_GET['ds']) && isset($_GET['de'])) {
            $dateStart = $_GET['ds'];
            $dateEnd = $_GET['de'];
        } else {
            $dateStart = $this->input->post('date_start');
            $dateEnd = $this->input->post('date_end');
        }


        if (!isset($dateStart)) {
            $dateStart = date('Y-m-d');
        }

        if (!isset($dateEnd)) {
            $dateEnd = date('Y-m-d');
        }

        $this->setData('date_start', $dateStart);
        $this->setData('date_end', $dateEnd);

        $period = array($this->data['date_start'], $this->data['date_end']);
        $p_date_start = new DateTime($period[0]);
        $p_date_end = new DateTime($period[1]);

        if ($p_date_start < $p_date_end || $p_date_start == $p_date_end) {
            $this->setData('error_mesg', null);
        } else {
            $this->setData('error_mesg', "Tanggal mulai tidak boleh lebih mendahului tanggal sampai");
        }

        $this->setData('js_part', array('webparts/parts/js_form', 'webparts/parts/filter_date_mulai_sampai_js'));
        $this->setData('page_part', 'webparts/koreksi-stok/list');
        $this->addJsPart('webparts/koreksi-stok/list_js');
        $this->load->model('Options');
        $options = $this->Options->get_by_devid($this->data['selected_outlet']);
        $this->setData('options', $options);
        $this->setData('list_stok', $this->get_list_koreksi_stok($this->data['selected_outlet'], $dateStart, $dateEnd));

        $this->load->view('main_part', $this->data);
    }

    protected function get_list_koreksi_stok($outlet, $date_start, $date_end)
    {

        // start - auto method
        // $date_start =  new DateTime($date_start);
        // $date_start = $date_start->format('Y-m-d H:i:s');

        // $date_end =  new DateTime($date_end." 23:59:59");
        // $date_end = $date_end->format('Y-m-d H:i:s');

        // $query = "
        // select * from stockopname
        // 	where DeviceID='$outlet'
        // 		and PerusahaanNo='".getPerusahaanNo()."'
        // 		and PerusahaanID='".getLoggedInUserID()."'
        // 		and TglJamUpdate between '$date_start' and '$date_end'
        // 	order by TglJamUpdate desc
        // ";
        // end - auto method


        // start manual method
        $query = "
		SELECT * FROM stockopname 
			WHERE DeviceID='$outlet' 
				AND PerusahaanNo='" . getPerusahaanNo() . "'
				 AND StockOpnameNumber LIKE 'KS%' 
		";

        $query .= " AND StockOpnameDate >= '$date_start' AND StockOpnameDate <= '$date_end'";
        $query .= " order by StockOpnameDate desc, StockOpnameTime DESC, TransactionID DESC";
        // start manual method

        //execute query
        $query = $this->db->query($query);
        return $query->result();
    }

}
