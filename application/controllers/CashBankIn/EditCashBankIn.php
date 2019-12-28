<?php

trait EditCashBankIn
{
    public function edit($outlet,$id)
    {
        if (!isset($outlet) || !isset($id)) {
            redirect(base_url('uangmasuk'));
        }

        if (!array_key_exists($outlet, $this->data['outlets'])) {
            redirect(base_url('uangmasuk'));
        }

        if ($this->input->get('iscloud', TRUE) === NULL) {
            show_error("Invalid Parameter", 400, "Bad Request");
        }

        $iscloud = (int)$this->input->get('iscloud', TRUE);
        $this->setData('iscloud', $iscloud);

        $deviceno = -1;
        if ($iscloud === 0) {
            $tmp = explode(".", $id, 2);
            $id = $tmp[0];
            $deviceno =  $tmp[1];
        }

        $this->load->model('masteraccount');
        $this->load->model('Uangmasukmodel');
        $this->setData('page_part', 'webparts/uang-masuk/edit');
        $uk = $this->get_list_uangmasuk_by_id($outlet,$id, $iscloud, $deviceno);
        $this->setData('uang_masuk', $uk);
        if ($uk->IncomeType == 1) {
            $chart_of_account = $this->masteraccount->accountOtherIncome(getPerusahaanNo());
        }else{
            $chart_of_account = $this->masteraccount->accountNonIncome(getPerusahaanNo());
        }
        $this->setData('items', $this->getItem($uk->DeviceID, $iscloud, $deviceno));
        $this->setData('accounts', $chart_of_account);
        $namaDanAlamatOutlet = str_replace('#$%^', ' - ', $this->data['outlets'][$outlet]);
        $this->setData('nama_alamat_outlet',$namaDanAlamatOutlet);
        $this->addJsPart('webparts/uang-masuk/edit_js');

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