<?php

trait EditIncomingStock
{
    public function edit($outlet, $id)
    {
        if (!isset($outlet) || !isset($id)) {
            redirect(base_url('stokmasuk'));
        }

        if (!array_key_exists($outlet, $this->data['outlets'])) {
            redirect(base_url('stokmasuk'));
        }
        $realid = explode(".", $id)[0];
        $devno = explode(".", $id)[1];

        $this->load->model('Stockopnamedetail');
        $this->setData('page_part', 'webparts/incoming-stock/edit');
        $this->setData('koreksi_stok', $this->get_list_koreksi_by_id($outlet, $realid, $devno));
        $this->setData('item_stok', $this->Stockopnamedetail->get_item_stok($outlet, $realid, $devno));
        $this->setData('items', $this->getItem($outlet));
        $namaDanAlamatOutlet = str_replace('#$%^', ' - ', $this->data['outlets'][$outlet]);
        $this->setData('nama_alamat_outlet', $namaDanAlamatOutlet);
        $this->addJsPart('webparts/incoming-stock/edit_js');

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