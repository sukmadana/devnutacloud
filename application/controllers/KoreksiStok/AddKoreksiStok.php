<?php

trait AddKoreksiStok
{
    protected $attributes;
    public function add()
	{
		if (in_array($this->data['selected_outlet'], $this->data['outlets']))
		{
			redirect('/koreksistok');
		}

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

		$this->setData('page_part', 'webparts/koreksi-stok/add');
		$this->addJsPart('webparts/koreksi-stok/add_js');
		$this->setData('items', $this->getItem($this->data['selected_outlet']));
		$this->setData('css_bootstrap_select', true);
		$this->setData('js_bootstrap_select', true);

        $this->setData('date_start', $dateStart);
        $this->setData('date_end', $dateEnd);
		$this->load->view('main_part', $this->data);

	}
}