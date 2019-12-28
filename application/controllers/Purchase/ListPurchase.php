<?php

trait ListPurchase
{
    public function index()
    {
        $this->addJsPart('webparts/purchase/parts/js_index.php');

        $this->load->model('Purchase');
        $period = array( $this->data['date_start'], $this->data['date_end'] );

        $p_date_start = new DateTime($period[0]);
        $p_date_end = new DateTime($period[1]);
        if ( $p_date_start < $p_date_end || $p_date_start == $p_date_end ) {
            $this->setData('items', $this->Purchase->get_all($this->data['selected_outlet'], $period));
            $this->setData('error_mesg', null);
        } else {
            $this->setData('items', null);
            $this->setData('error_mesg', "Tanggal mulai tidak boleh lebih mendahului tanggal sampai");
        }
        $this->load->model('Options');
        $options = $this->Options->get_by_devid($this->data['selected_outlet']);
        $this->setData('options', $options);
        $this->load->view('main_part', $this->data);
    }
}
