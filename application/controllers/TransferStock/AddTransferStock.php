<?php 

trait AddTransferStock
{

	protected $attributes;

	public function form()
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

		// Running outlet input guard
		if (in_array($this->data['selected_outlet'], $this->data['outlets'])) {
			redirect(base_url('transferstok'));
		}

		$this->load->model('Transferstock');

		$this->setData('page_part', 'webparts/transfer-stock/add');
		$this->addJsPart('webparts/transfer-stock/parts/js_add');
		$this->setData('css_bootstrap_select', true);
		//$this->setData('js_bootstrap_select', true);
		$outlet1 = $this->getOutletId();


		$outlet2 = $this->getOutletTujuan();

		if (empty($outlet2)) {
			$i=0;
			foreach ($this->GetOutletTanpaSemua() as $key => $value) {
				if ($key!=$outlet1) {
					if ($i==0) {
						$outlet2 = $key;
					}
					$i++;
				}
			}
		}

		$this->setData('outlet_tujuan', $outlet2);

		$items = $this->get_item_two_outlet($outlet1,$outlet2);
		$this->setData('items', $items);

		$this->load->view('main_part', $this->data);
	}

	public function store()
	{
		if ($this->input->server('REQUEST_METHOD') != 'POST')
			throw new Exception("Method Not Allowed", 405);

		$this->load->model('Transferstock');

		$this->attributes = $this->getAttributesTransfer();

		if ($this->input->post('mode')=="new") {
			$storing = $this->Transferstock->create($this->attributes);
			if (!$storing) 
				return redirect('/transferstok/form?outlet='.$this->input->post('from_outlet'));

			if ($this->storeDetail()) {
				$this->savedDetail();
				$this->session->set_flashdata('notif', 'Tersimpan');
			}

		} else if ($this->input->post('mode')=="edit") {

			// Prepare attribute
	        $clause = [
	            "PerusahaanNo"  => getPerusahaanNo(),
	            'DeviceID' => $this->input->post('from_outlet'),
	            'TransactionID' => $this->input->post('id_transfer'),
	        ];


			$storing = $this->Transferstock->update($clause, array('TransferToDeviceID' => $this->input->post('to_outlet')) );


			if (!$storing) 
				return redirect('/transferstok/edit/'.$this->input->post('from_outlet').'/'.$this->input->post('id_transfer'));

			$this->load->model('Transferstockdetail');
			if ($this->Transferstockdetail->force_delete($clause)) {
				if ($this->storeDetail()) {
					$this->savedDetail();
					$this->session->set_flashdata('notif', 'Tersimpan');
				}
			}
		}

		
		

		return redirect($this->input->server('HTTP_REFERER'));
	}

	protected function storeDetail()
	{
		$this->load->model('Transferstockdetail');
		foreach ($this->input->post('item-name') as $key => $value) {

			$storing = $this->Transferstockdetail->create($this->getAttributesTransferDetail($key));
				// if (!$storing)
					// return redirect('/transferstok/form?outlet='.$this->attributes['DeviceID']);

			
		}
		return true;
	}

	protected function savedDetail()
	{
		$this->Transferstock->update($this->attributes, [
				'IsDetailsSaved' => 1
			]);
	}

	protected function getAttributesTransfer()
	{
		$this->load->model('Transferstock');

		return [
			'PerusahaanNo' => getPerusahaanNo(),
			'PerusahaanID' => getLoggedInUserID(),
			'DeviceID' => $this->input->post('from_outlet'),
			'Varian' => 'nuta',
			'TransactionID' => $this->Transferstock->get_transaction_id($this->input->post('from_outlet')),
			'TransferNumber' => $this->Transferstock
				->get_generate_transferstock($this->getDateTime()['date'], $this->input->post('from_outlet')),
			'TransferDate' => $this->getDateTime()['date'],
			'TransferTime' => $this->getDateTime()['time'],
			'TransferToDeviceID' => $this->input->post('to_outlet'),
			'CreatedBy' => getLoggedInUsername(),
            'CreatedDate' => date("d-m-Y"),
            'CreatedTime' => date("H:i"),
            'isDetailsSaved' => 0,
            'EditedBy' => '',
            'EditedDate' => '',
            'EditedTime' => '',
            'HasBeenDownloaded' => 0,

		];

	}

	protected function getAttributesTransferDetail($key)
	{
		$this->load->model('Transferstockdetail');

		$item_name = $this->get_item_name_asal($this->input->post('from_outlet'),$this->input->post('item-name')[$key]);

		$attribut_transfer_detail =  [
			'PerusahaanNo' 			=> getPerusahaanNo(),
			'PerusahaanID' 			=> getLoggedInUserID(),
			'DeviceID' 				=> $this->input->post('from_outlet'),
			'Varian' 				=> 'nuta',
			'DetailID' 				=> $this->Transferstockdetail->get_detail_id($this->input->post('from_outlet')),
			'DetailNumber' 			=> $key+1,
			'ItemID' 				=> $this->input->post('item-name')[$key],
			'Quantity' 				=> $this->input->post('item-total')[$key],
			'Note' 					=> $this->input->post('note')[$key],
			'TransferToDeviceID' 	=> $this->input->post('to_outlet'),
			'HasBeenDownloaded' 	=> 0,
			'TransferToItemID'		=> $this->get_item_tujuan( $this->input->post('to_outlet'), $item_name ),
		];

		if ( $this->input->post('mode') == "new" ) {
			$attribut_transfer_detail['TransactionID'] = $this->attributes['TransactionID'];
		} else if ( $this->input->post('mode') == "edit" ) {
			$attribut_transfer_detail['TransactionID'] = $this->input->post('id_transfer');
		}

		return $attribut_transfer_detail;
	}

	protected function get_item_name_asal($outletasal_id,$item_id){
		$this->load->model('masterItem');
		$itemname = null;
		foreach ($this->masterItem->getMasterItemByOutlet($outletasal_id) as $key => $value) {
			if ($value->ItemID == $item_id) {
				$itemname = $value->ItemName; 
			}
		}
		return $itemname;
	}

	protected function get_item_tujuan($outlettujuan_id,$item_name){
		$this->load->model('masterItem');
		$itemid = null;
		foreach ($this->masterItem->getMasterItemByOutlet($outlettujuan_id) as $key => $value) {
			if ($value->ItemName == $item_name) {
				$itemid = $value->ItemID; 
			}
		}
		return $itemid;
	}


}