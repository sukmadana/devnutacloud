<?php

trait AddPurchase
{

    protected $attributes;
    protected $perusahaanno = 0;

    public function form()
    {
// Running outlet input guard
        if (in_array($this->data['selected_outlet'], $this->data['outlets'])) {
            redirect('/pembelian');
        }

        $this->load->model('Supplier');
        $this->load->model('Purchase');
        $this->cashBankAccount = $this->getMasterCashBankAccount();

        $this->setData('page_part', 'webparts/purchase/add');
        $this->addJsPart('webparts/purchase/parts/js_add');
        $this->addJsPart('webparts/purchase/parts/js_supplier');
        $this->setData('items', $this->getItem($this->data['selected_outlet']));
        $this->setData('suppliers',
            $this->db->query(
                $this->Supplier->supplier($this->input->get('outlet'))
            )->result()
        );
        $this->setData( 'getAccount', $this->Purchase->get_bank_account($this->data['selected_outlet']) );

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

     //   print_r($this->getItem($this->data['selected_outlet']));
    }

    public function store()
    {
        if ($this->input->server('REQUEST_METHOD') != 'POST')
            throw new Exception("Method Not Allowed", 405);
        $this->perusahaanno = getPerusahaanNo();

        $this->load->model('Purchase');

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($this->input->post('outlet'));
        $cloudDevno = 0;
        if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
            $cloudDevno = 1;
        }

        $this->attributes = $this->getAttributesStore($cloudDevno);
	
        $storing = $this->Purchase->create($this->attributes);

//        var_dump($storing);
        if (!$storing)
            return redirect(base_url('pembelian/form?outlet=' . $this->input->post('outlet')));

        // push data to firebase
        $insert_query = $this->Purchase->get_single_transactionrowarray(
            $this->input->post('outlet'), $this->attributes['TransactionID'], $this->attributes['DeviceNo']);
        $last_insert_data =  array(
            "table"     => 'purchase',
            "column"    => $insert_query
        );
        $this->load->model('Firebasemodel');
        $this->Firebasemodel->push_firebase($this->input->post('outlet'),$last_insert_data,
            $this->attributes['TransactionID'], $this->attributes['DeviceNo'], $this->perusahaanno, 0);
        if ($this->storeDetail($cloudDevno)) {
            $this->savedDetail();
            $this->session->set_flashdata('notif', 'Tersimpan');
        }


//        return redirect($this->input->server('HTTP_REFERER'));
    }

    protected function storeDetail($cloudDevno)
    {
        $this->load->model('Purchaseitemdetail');
        $this->load->model('masterItem');
        foreach ($this->input->post('item-name') as $key => $value) {
            $atributdetil = $this->generateAttributeStoreDetail($key, $cloudDevno);
            $storing = $this->Purchaseitemdetail->create($atributdetil);

            // push data to firebase
            $insert_query = $this->Purchaseitemdetail->get_purchaseitemdetail_rowarray($this->input->post('outlet'), $atributdetil['DetailID'], $atributdetil['DeviceNo']);
            $last_insert_data =  array(
                "table"     => 'purchaseitemdetail',
                "column"    => $insert_query
            );
            $this->load->model('Firebasemodel');
            $this->Firebasemodel->push_firebase($this->input->post('outlet'),$last_insert_data,
                $atributdetil['DetailID'], $atributdetil['DeviceNo'], $this->perusahaanno, 0);

//            var_dump($storing);
            if (!$storing)
                return redirect(base_url('pembelian/form?outlet=' . $this->input->post('outlet')));
        }
        return redirect(base_url('pembelian/?outlet=' . $this->input->post('outlet')));
//        return true;
    }

    protected function savedDetail()
    {
        $this->Purchase->update($this->attributes, [
            'IsDetailsSaved' => 1
        ]);
    }

    protected function getItem($outlet)
    {
        $this->load->model('masterItem');
        return $this->masterItem->getMasterItemByOutlet($outlet);
    }

    protected function getMasterCashBankAccount()
    {
        return "
SELECT * FROM mastercashbankaccount
";
    }

    protected function getAttributesStore($cloudDevno)
    {
        $this->load->model('Supplier');
		
        $supplier = $this->input->post('supplier');
        //log_message('error',$supplier);
        if ($supplier == null) {
            $realsuppid = 0;
            $devno = 0;
            $supplierDeviceNo = 0;
            $supplierName = "";
        } else {
            $realsuppid = explode(".", $supplier)[0];
            $devno = explode(".", $supplier)[1];
            $supp = $this->Supplier->getByName($realsuppid, $devno, $this->input->post('outlet'));
            $supplierName = $supp->SupplierName;
            $supplierDeviceNo = $devno;
        }
        $total = $this->input->post('grand-total2');

        // foreach ($this->input->post('sum') as $value)
        //     $total += $value;

        if ($this->input->post('jenis_diskon_final')=="%") {
            $diskon_final = $this->input->post('diskon_final')."".$this->input->post('jenis_diskon_final');
        } else {
            $diskon_final = $this->input->post('diskon_final');
        }

        $TransactionID = $this->Purchase->get_transaction_id($this->input->post('outlet'));
        $attribute = [
            'TransactionID'         => $TransactionID,
            'DeviceNo'              => $cloudDevno,
            'PurchaseNumber'        => $this->Purchase->get_generate_purchase($this->getDateTime()['date'], $this->input->post('outlet')),
            'PurchaseDate'          => $this->getDateTime()['date'],
            'PurchaseTime'          => $this->getDateTime()['time'],
            'SupplierName'          => set_null($supplierName),
            'SupplierID'            => set_null($realsuppid,false),
            'SupplierDeviceNo'      => $supplierDeviceNo,
            'Total'                 => $total,
            'CashPaymentAmount'     => 0,
            'TotalPayment'          => $total,
            'Change'                => 0,
            'BatchNumberEDC'        => 0,
            'DeviceID'              => $this->input->post('outlet'),
            'CashBankAccountName'   => 'Kasir',
            'IsDetailsSaved'        => 0,
            'PerusahaanID'          => getLoggedInUserID(),
            'PerusahaanNo'          => getPerusahaanNo(),
            'Varian'                => 'Nuta',
            'CreatedBy'             => getLoggedInUsername(),
            'CreatedDate'           => date("Y-m-d"),
            'CreatedTime'           => date("H:i"),
            'FinalDiscount'         => $diskon_final,
            'Rounding'              => 0,
            'Donation'              => 0,
            'PaymentMode'           => 2,
            'CashAccountID'         => 0,
            'CashPaymentAmount'     => 0,
            'BankPaymentAmount'     => $total,
            'Change'                => 0,
            'ClearingDate'          => '',
            'CardType'          => '',
            'CardName'          => '',
            'CardNumber'          => '',
            'BatchNumberEDC'          => 0,
            'Pending'          => 0,
            'EditedBy' => '',
            'EditedDate' => '',
            'EditedTime' => ''
        ];
        
        if (!empty($this->input->post('bank_account'))) {
            $bankaccount = explode(".",$this->input->post('bank_account'));
            $attribute['BankAccountID'] = $bankaccount[0];
            $attribute['BankAccountDeviceNo'] = $bankaccount[1];
        }
        // $attribute = array_merge($attribute, $this->getAttributePayment());

        return $attribute;
    }

    protected function getAttributePayment()
    {
        $attribute = [];

        // switch ($this->input->post("pembayaran")) {
        //     case "tunai" :
        //         $attribute = [
        //             'PaymentMode' => 1,
        //             'CashAccountID' => 1,
        //             'BankAccountID' => 0,
        //             'CashPaymentAmount' => $this->input->post("jumlah-bayar"),
        //             'BankPaymentAmount' => 0,
        //             'TotalPayment' => $this->input->post("jumlah-bayar"),
        //             'Change' => $this->input->post("kembalian")
        //         ];
        //         break;

        //     case "kartu" :
        //         $attribute = [
        //             'PaymentMode' => 2,
        //             'CashAccountID' => 0,
        //             'BankAccountID' => $this->input->post("masukKeKartu"),
        //             'CashPaymentAmount' => 0,
        //             'BankPaymentAmount' => $this->input->post("totalKartu"),
        //             'TotalPayment' => $this->input->post("totalKartu"),
        //             'Change' => 0
        //         ];
        //         break;

        //     case "campuran" :
        //         $attribute = [
        //             'PaymentMode' => 3,
        //             'CashAccountID' => 1,
        //             'BankAccountID' => $this->input->post("masukKeCampuran"),
        //             'CashPaymentAmount' => $this->input->post("tunaiCampuran"),
        //             'BankPaymentAmount' => $this->input->post("totalCampuran"),
        //             'TotalPayment' => $this->input->post("totalCampuran"),
        //             'Change' => 0
        //         ];
        //         break;
        // }

        return $attribute;
    }

    public function generateAttributeStoreDetail($key, $cloudDevno)
    {
        $this->load->model('masterItem');
        $item = $this->masterItem->getByID($this->input->post('item-name')[$key], $this->input->post('outlet'));

        if ($this->input->post('jenis_diskon')[$key]=="%") {
            $diskon = $this->input->post('diskon')[$key]."".$this->input->post('jenis_diskon')[$key];
        } else {
            $diskon = $this->input->post('diskon')[$key];
        }
        $isproduct = 0;
        if($item->IsProduct)
            $isproduct = 1;
        $realitemid = explode(".", $this->input->post('item-name')[$key])[0];
        $devno = explode(".", $this->input->post('item-name')[$key])[1];
//        log_message('error', var_export($this->input->post('item-name')[$key], true));
//        log_message('error', var_export($key, true));
        $inputdetailid = $this->input->post('detailid')[$key];
        if($inputdetailid == 0) {
            $detailid = $this->Purchaseitemdetail->get_detail_id($this->input->post('outlet'));
            $detaildevno = $cloudDevno;
        } else {
            $detailid = $inputdetailid;
            $detaildevno = $this->input->post('detaildevno')[$key];
        }
        $attribute = [
            'DetailID'          => $detailid,
            'DeviceNo'          => $detaildevno,
            'TransactionID'     => $this->attributes['TransactionID'],
            'TransactionDeviceNo' => $this->attributes['DeviceNo'],
            'DetailNumber'      => $key + 1,
            'ItemID'            => $realitemid,
            'ItemDeviceNo'            => $devno,
            'IsProduct'         => $isproduct,
            'ItemName'          => $item->ItemName,
            'Quantity'          => $this->input->post('item-total')[$key],
            'Discount'          => $diskon,
            'UnitPrice'         => $this->input->post('price')[$key],
            'SubTotal'          => $this->input->post('sum')[$key],
            'Note'              => '',
            'Varian'            => 'Nuta',
            'DeviceID'          => $item->DeviceID,
            'PerusahaanID'      => getLoggedInUserID(),
            'PerusahaanNo'      => getPerusahaanNo(),
        ];

        return $attribute;
    }
}
