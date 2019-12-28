<?php

trait ViewOutgoingStock
{
    public function view($outlet, $id)
    {
        if (!isset($outlet) || !isset($id)) {
            redirect(base_url('stokkeluar'));
        }

        if (!array_key_exists($outlet, $this->data['outlets'])) {
            redirect(base_url('stokkeluar'));
        }
        $realid = explode(".", $id)[0];
        $devno = explode(".", $id)[1];
        $this->load->model('Stockopnamedetail');
        $this->setData('page_part', 'webparts/outgoing-stock/view');
        $this->setData('koreksi_stok', $this->get_list_koreksi_by_id($outlet, $realid, $devno));
        $this->setData('item_stok', $this->Stockopnamedetail->get_item_stok($outlet, $realid, $devno));
        $namaDanAlamatOutlet = str_replace('#$%^', ' - ', $this->data['outlets'][$outlet]);
        $this->setData('nama_alamat_outlet', $namaDanAlamatOutlet);

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
        $this->load->view('main_part', $this->data);
    }


}