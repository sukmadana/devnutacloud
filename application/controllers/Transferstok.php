<?php

include_once "TransferStock/ListTransferStock.php";
include_once "TransferStock/AddTransferStock.php";
include_once "TransferStock/EditTransferStock.php";
include_once "TransferStock/DetailTransferStock.php";

/**
*
*/
class Transferstok extends MY_Controller
{
	use ListTransferStock, AddTransferStock, EditTransferStock, DetailTransferStock;

	public function __construct()
	{
		parent::__construct();
		ifNotAuthenticatedRedirectToLogin();

		$this->data = [
			'page_part' => 'transfer-stock/index',
			'js_part' => array('features/js/js_form'), 'js_chart' => [],
			'outlets' => $this->getOutletByVersion($version = 200, $type = '>='),
			// 'outlets' => $this->GetOutletTanpaSemua(),
			'selected_outlet' => $this->getOutletId(),
			'default_to_outlet'=> $this->getOutletIdDefaultTo(),
			'visibilityMenu' => $this->visibilityMenu,
			'isLaporanStokVisible' => $this->IsLaporanStokVisible(),
			'isLaporanPembelianVisible' => $this->IsLaporanPembelianVisible(),
			'isLaporanPriceVarianVisible' => $this->IsLaporanVarianHargaVisible(),
			'menu'	=> 'stok',
		];
	}

	protected function getDateTime()
    {
        return [
            // 'date' => explode(' ', $this->input->post('datetime'))[0],
            // 'time' => explode(' ', $this->input->post('datetime'))[1]
            'date' => date("Y-m-d"),
            'time' => date("H:i"),
        ];
    }

	protected function getOutletId()
	{
		$selected_outlet = $this->input->get('outlet');
		if (!$selected_outlet) {
			$selected_outlet = 0 ;
		}
		return $selected_outlet;
	}

	protected function getOutletTujuan()
	{
		$selected_outlet = $this->input->get('outlet_tujuan');
		return $selected_outlet;

	}

	protected function addJsPart($location)
	{
		array_push($this->data['js_part'], $location);
	}

	protected function setData($key, $value){
		$this->data[$key] = $value;
	}

	protected function getOutletIdDefaultTo(){
		$outlets = $this->GetOutletTanpaSemua();
		$selected_outlet = $this->getOutletId();
		$default=0;
		foreach ($outlets as $devid => $label) {
			if($devid == $selected_outlet){
				continue;
			}else{
				$default  = $devid;
				break;
			}
		}
		return $default;
	}

	public function ajax_getItems() {
		$dev1 = $this->input->get('dev1');
		$dev2 = $this->input->get('dev2');

		$this->load->model('Transferstock');
		$items = $this->Transferstock->get_items_in_two_outlets($dev1,$dev2);
		echo json_encode($items);
	}

	public function delete($deviceid,$id){
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

		// Push Firebase
		$this->load->model('Transferstock');
		$transferstockData = $this->Transferstock->getDetailTransferstock(array('DeviceID' => $this->attributes['DeviceID'], 'TransactionID' => $this->attributes['TransactionID']));

		$this->Transferstock->delete($attributes);
		if ($transferstockData) {
			$this->load->model('Options');
			$this->load->model('Firebasemodel');
			$options = $this->Options->get_by_devid($this->attributes['DeviceID']);
			if ($options->CreatedVersionCode < 200 && $options->EditedVersionCode < 200) {
				$last_insert_data = array(
					"table" => 'deletetransferstock',
					"column" => array(
						'DeviceNo' => $transferstockData['DeviceNo'],
						'TransactionID' => $transferstockData['TransactionID']
					)
				);
				$this->Firebasemodel->push_firebase(
					$transferstockData['DeviceID'],
					$last_insert_data,
					$transferstockData['TransactionID'],
					$transferstockData['DeviceNo'],
					$transferstockData['PerusahaanNo'],
					0
				);
			}
		}
		return redirect('/transferstok/?outlet='.$deviceid.'&ds='.$dateStart.'&de='.$dateEnd);
	}

	public function get_item_two_outlet($outlet1,$outlet2){
		$this->load->model('masterItem');

        $item_outlet1 = $this->masterItem->getMasterItemByOutlet($outlet1);


        $items = array();

        foreach ($item_outlet1 as $key => $value) {
        	if ( $value->ItemName == $this->cek_item_name_tujuan($outlet2,$value->ItemName) ) {
        		$items[] = array(
        			'ItemID' 	=> $value->ItemID,
        			'ItemName' 	=> $value->ItemName,
        		);
        	}
        }

        return $items;
	}

	protected function cek_item_name_tujuan($outlet_tujuan,$item_name){
		$this->load->model('masterItem');
        $items = $this->masterItem->getMasterItemByOutlet($outlet_tujuan);
        foreach ($items as $key => $value) {
        	if ($value->ItemName == $item_name) {
        		return $value->ItemName;
        	}
        }
	}



}
