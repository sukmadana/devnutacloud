<?php 
include_once "Purchase/ListPurchase.php";
include_once "Purchase/AddPurchase.php";
include_once "Purchase/UpdatePurchase.php";
include_once "Purchase/DeletePurchase.php";
include_once "Purchase/ViewPurchase.php";
/**
* 
*/
class Pembelian extends MY_Controller
{
	use ListPurchase,AddPurchase,UpdatePurchase,DeletePurchase,ViewPurchase;
	
	public function __construct()
	{
		parent::__construct();
		ifNotAuthenticatedRedirectToLogin();

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

		$this->data = [
			'page_part' 					=> 'purchase/index',
			'js_chart' 						=> [],
			'js_part' 						=> array('features/js/js_form','features/filters/filter_date_mulai_sampai_js'),
			'outlets' 						=> $this->GetOutletTanpaSemua(),
			'selected_outlet' 				=> $this->getOutletId(),
			'visibilityMenu' 				=> $this->visibilityMenu,
			'isLaporanStokVisible' 			=> $this->IsLaporanStokVisible(),
			'isLaporanPembelianVisible' 	=> $this->IsLaporanPembelianVisible(),
			'isLaporanPriceVarianVisible' 	=> $this->IsLaporanVarianHargaVisible(),
			'menu'							=> "stok",
			'date_start'					=> $dateStart,
			'date_end'						=> $dateEnd,
		];
	}

	protected function getDateTime()
    {
        return [
            //'date' => explode(' ', $this->input->post('datetime'))[0],
            //'time' => explode(' ', $this->input->post('datetime'))[1]
        	'date' => date('Y-m-d'),
            'time' => date('h:i')
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

	protected function addJsPart($location)
	{
		array_push($this->data['js_part'], $location);
	}

	protected function setData($key, $value){
		$this->data[$key] = $value;
	}
}
