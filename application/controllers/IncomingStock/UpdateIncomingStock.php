<?php 

trait UpdateIncomingStock
{
	public function edit($deviceid, $id)
	{
		// Runnning edit guard
		if (!$deviceid && !$id)
			redirect('stokmasuk');

		if (!array_key_exists($deviceid, $this->data['outlets'])) {
			redirect('/stokmasuk');
		}

		// Prepare attribute
		$attributes = [
			'DeviceID' => $deviceid,
			'TransactionID' => $id
		];

		// load a library
		$this->load->model('Stockopname');
		$this->load->model('Stockopnamedetail');

		// Loading data
		$this->addJsPart('webparts/incoming-stock/parts/js_add');
		$this->setData('page_part', 'webparts/incoming-stock/edit');
		$this->setData('deviceid', $deviceid);
		$this->setData('items', $this->getitem($deviceid));
		$this->setData('stock', $this->Stockopname->where($attributes)->first_row());
		$this->setData('stock_detail', $this->Stockopnamedetail->where($attributes)->result());

		$this->load->view('main_part', $this->data);
	}

	public function update($deviceid, $id)
	{
		// Method not allowed guard
		if ($this->input->server('REQUEST_METHOD') != 'POST')
			throw new Exception("Method Not Allowed", 405);

		// Runnning edit guard
		if (!$deviceid && !$id)
			redirect('stokmasuk');

		if (!array_key_exists($deviceid, $this->data['outlets'])) {
			redirect('/stokmasuk');
		}

		// Prepare attribute
		$this->attributes = [
			'DeviceID' => $deviceid,
			'TransactionID' => $id
		];

		// load a library
		$this->load->library('session');
		$this->load->model('Stockopname');
		$this->load->model('Stockopnamedetail');

		// First update attributes and update data in table stockopname 
		$this->Stockopname->update($this->attributes, $this->generateAttributeUpdate($id));

		// Execute update detail
		if ($this->Stockopnamedetail->force_delete([
			'TransactionID' => $this->attributes['TransactionID'],
			'DeviceID' => $this->attributes['DeviceID']
		])) {
			if ($this->storeDetail()) {
				$this->savedDetail();
				$this->session->set_flashdata('notif', 'Tersimpan');
			}
		}

		return redirect($this->input->server('HTTP_REFERER'));
	}

	protected function generateAttributeUpdate($id)
	{
		$code = $this->db->query($this->Stockopname->get_generate_stockopname_update(
					$this->attributes['TransactionID'], $this->attributes['DeviceID'] , $this->getDateTime()['date']
				))->first_row();

		return [
			'TransactionID' => $id,
            'StockOpnameNumber' => $code->result,
            'StockOpnameDate' => $this->getDateTime()['date'],
            'StockOpnameTime' => $this->getDateTime()['time'],
            'PerusahaanID' => getLoggedInUserID(),
            'isDetailsSaved' => 0,
            'EditedBy' => getLoggedInUsername(),
            'EditedDate' => date("Y-m-d"),
            'EditedTime' => date("H:i")
        ];
	}
}