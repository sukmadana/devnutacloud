<?php 

trait EditTransferStock
{

	public function edit($deviceid,$id)
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

		$outlet2 = $this->getOutletTujuan();
		if (empty($outlet2)) {
			$outlet2 = $transfer_data->TransferToDeviceID;
		}

		// $list_items = $this->Transferstock->get_items_in_two_outlets($deviceid,$transfer_data->TransferToDeviceID);
		$list_items = $this->get_item_two_outlet($deviceid,$outlet2);

		foreach ($transfer_detail as $key => $value) {
			$item_data = $this->get_item_unit($value->DeviceID,$value->ItemID);
			if ($item_data['ItemID']==$value->ItemID) {
				$transfer_detail2[$key]['Unit'] = $item_data['Unit'];
			}
		}

		$this->setData('itemModel', $this->Masteritem);
		$this->setData('outletModel', $this->Outlet);
		$this->setData('page_part', 'webparts/transfer-stock/edit');
		$this->addJsPart('webparts/transfer-stock/parts/js_edit');
		$this->setData('deviceid', $deviceid);
		$this->setData('outlet_tujuan', $outlet2);
		
		// $items = $this->get_item_two_outlet($deviceid,$outlet2);

		$this->setData('items', $list_items);
		
		$this->setData('transfer', $transfer_data);
		$this->setData('transfer_detail', $transfer_detail2);

		$this->load->view('main_part', $this->data);
	}


	protected function get_item_unit($outletasal_id,$item_id){
		$this->load->model('masterItem');
		
		foreach ($this->masterItem->getMasterItemByOutlet($outletasal_id) as $key => $value) {
			if ($value->ItemID == $item_id) {
				return array(
					'ItemID'	=> $value->ItemID,
					'Unit'		=> $value->Unit,
				);
			}
		}
	}
	

}