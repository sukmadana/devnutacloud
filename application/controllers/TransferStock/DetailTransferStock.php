<?php 

trait DetailTransferStock
{
	public function detail($deviceid, $id)
	{
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
		
		// Runnning edit guard
		if (!$deviceid && !$id)
			redirect('transferstok');

		if (!array_key_exists($deviceid, $this->data['outlets'])) {
			redirect('/transferstok');
		}

		// Prepare attribute
		$attributes = [
			'DeviceID' => $deviceid,
			'TransactionID' => $id
		];

		// load a library
		$this->load->model('Transferstock');
		$this->load->model('Transferstockdetail');
		$this->load->model('Outlet');
		$this->load->model('Masteritem');
/*
		echo "<pre>";

		print_r($this->Transferstock->get_item_by_id($deviceid, $id)->TransferToDeviceID);

		die();*/

		// Loading data	
		$transfer_data = $this->Transferstock->get_item_by_id($deviceid, $id);
		$transfer_detail = $this->Transferstockdetail->get_item_by_id($deviceid, $id);
		$transfer_detail2 = json_decode(json_encode($transfer_detail), True);

		

		$list_items = $this->Transferstock->get_items_in_two_outlets($deviceid,$transfer_data->TransferToDeviceID);

		foreach ($transfer_detail as $key => $value) {
			$item_data = $this->get_item_name($list_items,$value->ItemID,$value->DeviceID);
			if ($item_data['ItemID']==$value->ItemID and $item_data['DeviceID']==$value->DeviceID) {
				$transfer_detail2[$key]['ItemName'] = $item_data['ItemName'];
				$transfer_detail2[$key]['Unit'] = $item_data['Unit'];
			}
		}

		$this->setData('itemModel', $this->Masteritem);
		$this->setData('outletModel', $this->Outlet);
		$this->setData('page_part', 'webparts/transfer-stock/detail');
		$this->setData('deviceid', $deviceid);
		$this->setData('items', $this->Transferstock->get_all($deviceid));
		$this->setData('transfer', $transfer_data);
		$this->setData('transfer_detail', $transfer_detail2);

		$this->setData('list_items', $list_items);
		

		$this->load->view('main_part', $this->data);
	}

	protected function get_item_name($items,$itemid,$deviceid){
		foreach ($items as $key => $item) {
			if ($item->ItemID==$itemid and $item->DeviceID==$deviceid) {
				return array(
						'ItemID'	=> $item->ItemID,
						'DeviceID'	=> $item->DeviceID,
						'ItemName'	=> $item->ItemName,
						'Unit'		=> $item->Unit,
				);
			}
		}
	}
	
}