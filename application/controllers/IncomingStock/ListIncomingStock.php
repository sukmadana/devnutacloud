<?php


trait ListIncomingStock
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
        $this->setData('page_part', 'webparts/incoming-stock/list');
        $this->addJsPart('webparts/incoming-stock/list_js');
        $this->load->model('Options');
        $options = $this->Options->get_by_devid($this->getOutletId());
        $this->setData('options', $options);
        $this->setData('list_stok', $this->generateItems($this->getOutletId(), $dateStart, $dateEnd));
//        $this->addJsPart('webparts/parts/js_socket.php');
//        $this->addJsPart('webparts/parts/js_datatable.php');
//        $this->addJsPart('webparts/incoming-stock/parts/js_index.php');
//        $this->setData('items', $this->generateItems($this->getOutletId()));
//        $notify = $this->input->get('notify');
//        $this->setData('notify', isset($notify));
//        if ($notify) {
//            $src = $this->input->get('src');
//            $this->setData('src', $src);
//        }

        $this->load->view('main_part', $this->data);
    }

    protected function getOutletId()
    {
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet) {
            $selected_outlet = 0;
        }
        return $selected_outlet;
    }

    protected function generateItems($outlet_id, $dt1,$dt2)
    {
        $this->load->model('Stockopname');
        return $this->Stockopname->get_all_incoming_stock($outlet_id, $dt1,$dt2);
    }

}
