<?php

trait ViewCashBankIn
{
    public function view($outlet, $id)
    {
        if (!isset($outlet) || !isset($id)) {
            redirect(base_url('uangmasuk'));
        }

        if (!array_key_exists($outlet, $this->data['outlets'])) {
            redirect(base_url('uangmasuk'));
        }

        $iscloud = (int)$this->input->get('iscloud', TRUE);

        $this->load->model('Uangmasukmodel');
        $this->setData('page_part', 'webparts/uang-masuk/view');
        $uk = $this->get_list_uangmasuk_by_id($outlet, $id, $iscloud);
        $this->setData('uang_masuk', $uk);
        $this->setData('items', $this->getItem($uk->DeviceID));
        $namaDanAlamatOutlet = str_replace('#$%^', ' - ', $this->data['outlets'][$outlet]);
        $this->setData('nama_alamat_outlet',$namaDanAlamatOutlet);

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