<?php 

trait ListTransferStock
{
	public function index()
	{


		// set date start and date end
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

        $this->setData('js_part', array('webparts/transfer-stock/parts/js_index.php', 'webparts/parts/js_form', 'webparts/parts/filter_date_mulai_sampai_js'));

		$this->load->model('Transferstock');

		$items = $this->Transferstock->get_all($this->data['selected_outlet'],$dateStart,$dateEnd);

		$this->load->model('Outlet');
		$this->setData('outletModel', $this->Outlet);
        $this->load->model('Options');
        $options = $this->Options->get_by_devid($this->data['selected_outlet']);
        $this->setData('options', $options);
		$this->setData('items', $items);

        $this->load->view('main_part', $this->data);
	}
}