<?php

trait AddCashBankOut
{
    protected $attributes;
    public function add()
    {
        if (in_array($this->data['selected_outlet'], $this->data['outlets']))
        {
            redirect('/uangkeluar');
        }

        $this->setData('page_part', 'webparts/uang-keluar/add');
        $this->addJsPart('webparts/uang-keluar/add_js');
        $this->setData('items', $this->getItem($this->data['selected_outlet']));
        $this->setData('css_bootstrap_select', true);
        $this->setData('js_bootstrap_select', true);

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