<?php

trait UpdatePurchase
{
    public function edit($deviceid, $id)
    {
        // Runnning edit guard
        if (!$deviceid && !$id)
            redirect('/pembelian');

        if (!array_key_exists($deviceid, $this->data['outlets'])) {
            redirect('/pembelian');
        }

        $realtransID = explode(".", $id)[0];
        $devno = explode(".", $id)[1];
        // Prepare attribute
        $attributes = [
            'DeviceID' => $deviceid,
            'TransactionID' => $realtransID,
            'DeviceNo' => $devno,
            'PerusahaanNo' => getPerusahaanNo()
        ];
        $attributesdetil = [
            'DeviceID' => $deviceid,
            'TransactionID' => $realtransID,
            'TransactionDeviceNo' => $devno,
            'PerusahaanNo' => getPerusahaanNo()
        ];

        // load mastercashbankaccount
        $this->cashBankAccount = $this->getCashBankAccount();

        // load a library
        $this->load->model('Supplier');
        $this->load->model('Purchase');
        $this->load->model('Purchaseitemdetail');

        // Loading data
        $this->addJsPart('webparts/purchase/parts/js_edit');
        $this->addJsPart('webparts/purchase/parts/js_supplier');
        $this->setData('page_part', 'webparts/purchase/edit');
        $this->setData('deviceid', $deviceid);
        $this->setData('items', $this->getitem($deviceid));
        $this->setData('purchase', $this->Purchase->where($attributes)->first_row());
        $this->setData('purchase_detail', $this->Purchaseitemdetail->where($attributesdetil)->result());
        $this->setData('suppliers',
            $this->db->query(
                $this->Supplier->supplier($deviceid)
            )->result()
        );
        $this->setData( 'getAccount', $this->Purchase->get_bank_account($deviceid) );

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

    public function update($deviceid, $id)
    {
        // Method not allowed guard
        if ($this->input->server('REQUEST_METHOD') != 'POST') {
            throw new Exception("Method Not Allowed", 405);
            return;
        }

        // Runnning edit guard
        if (!$deviceid && !$id) {
            redirect('/pembelian');
        }
        $this->perusahaanno = getPerusahaanNo();

        $realtransID = explode(".", $id)[0];
        $devno = explode(".", $id)[1];

        if (!array_key_exists($deviceid, $this->data['outlets'])) {
            redirect('/pembelian');
        }

        // Prepare attribute
        $this->attributes = [
            "PerusahaanNo"  => getPerusahaanNo(),
            'DeviceID' => $deviceid,
            'TransactionID' => $realtransID,
            'DeviceNo' => $devno
        ];

        // load a library
        $this->load->library('session');
        $this->load->model('Purchase');
        $this->load->model('Purchaseitemdetail');

        // First update attributes and update data in table stockopname
        $this->Purchase->update($this->attributes, $this->generateAttributeUpdate($realtransID,$devno));

        // push data to firebase
        $this->load->model('Options');
        $options = $this->Options->get_by_devid($this->input->post('outlet'));
        if($options->CreatedVersionCode<220 && $options->EditedVersionCode<220) {
            $query_dataupdated = $this->Purchase->get_single_transactionrowarray($this->input->post('outlet'), $realtransID, $devno);
            $last_update_data =  array(
                "table"     => 'purchase',
                "column"    => $query_dataupdated
            );
            $this->load->model('Firebasemodel');
            $this->Firebasemodel->push_firebase($this->input->post('outlet'),$last_update_data,
                $this->attributes['TransactionID'], $this->attributes['DeviceNo'], $this->perusahaanno, 0);
        }

        $cloudDevno = 0;
        if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
            $cloudDevno = 1;
        }
        // Execute update detail
        //if ($this->Purchaseitemdetail->force_delete($this->attributes)) {
            if ($this->storeDetailModeEdit($cloudDevno, $options->CreatedVersionCode<220 && $options->EditedVersionCode<220 )) {
                $this->savedDetail();
                $this->session->set_flashdata('notif', 'Tersimpan');
            } else {
                $this->session->set_flashdata('Error', 'Gagal disimpan');
                redirect(base_url('pembelian/form?outlet=' . $this->input->post('outlet') .
                    '&ds=' . $this->input->post('date_start') .'&de=' . $this->input->post('date_end')));
            }
        //}

        redirect(base_url('pembelian/?outlet=' . $this->input->post('outlet').
            '&ds=' . $this->input->post('date_start') .'&de=' . $this->input->post('date_end')));
//        var_dump(base_url('pembelian/?outlet=' . $this->input->post('outlet').
//            '&ds=' . $this->input->post('date_start') .'&de=' . $this->input->post('date_end')));
//        var_dump($this->input->post('bank_account'));
//        var_dump($this->generateAttributeUpdate($id));
    }

    protected function storeDetailModeEdit($cloudDevno, $toFirebase = true)
    {
        $this->load->model('Purchaseitemdetail');
        $this->load->model('masterItem');
        foreach ($this->input->post('item-name') as $key => $value) {
            $detailid = $this->input->post('detailid')[$key];
            $atributdetil = $this->generateAttributeStoreDetail($key, $cloudDevno);
            if($detailid == 0) {
                $storing = $this->Purchaseitemdetail->create($atributdetil);
            } else {
                $storing = $this->Purchaseitemdetail->update
                    (
                        array
                        (
                            "PerusahaanNo"=>$this->attributes['PerusahaanNo'],
                            "DeviceID"=>$this->attributes['DeviceID'],
                            "DetailID"=>$detailid,
                            "DeviceNo"=>$this->input->post('detaildevno')[$key],
                        ),
                        $atributdetil
                    );
            }

            // push data to firebase

            if($toFirebase) {
                $insert_query = $this->Purchaseitemdetail->get_purchaseitemdetail_rowarray($this->input->post('outlet'), $atributdetil['DetailID'], $atributdetil['DeviceNo']);
                $last_insert_data =  array(
                    "table"     => 'purchaseitemdetail',
                    "column"    => $insert_query
                );
                $this->load->model('Firebasemodel');
                $this->Firebasemodel->push_firebase($this->input->post('outlet'),$last_insert_data,
                    $atributdetil['DetailID'], $atributdetil['DeviceNo'], $this->perusahaanno, 0);
            }

//            var_dump($storing);
            if (!$storing)
                return false;
        }
        return true;
    }

    protected function generateAttributeUpdate($id,$tr_devno)
    {
        $this->load->model('Supplier');
        $supplier = $this->input->post('supplier');
        $realsuppid = explode(".", $supplier)[0];
        $devno = explode(".", $supplier)[1];
        $supp = $this->Supplier->getByName($realsuppid, $devno, $this->input->post('outlet'));
        $supplierName = $supp->SupplierName;
        $supplierDeviceNo = $supp->DeviceNo;
        $total = $this->input->post('grand-total2');

        // foreach ($this->input->post('sum') as $value)
        //     $total += $value;

        if ($this->input->post('jenis_diskon_final')=="%") {
            $diskon_final = $this->input->post('diskon_final')."".$this->input->post('jenis_diskon_final');
        } else {
            $diskon_final = $this->input->post('diskon_final');
        }

        $attributeUpdate = [
            'TransactionID'         => $id,
            'DeviceNo' => $tr_devno,
            'SupplierName'          => $supplierName,
            'SupplierID'            => $realsuppid,
            'SupplierDeviceNo'            => $supplierDeviceNo,
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
            'EditedBy'              => getLoggedInUsername(),
            'EditedDate'            => date("Y-m-d"),
            'EditedTime'            => date("H:i"),
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
        ];

        if (!empty($this->input->post('bank_account'))) {
            $bankaccount = explode(".",$this->input->post('bank_account'));
            $attribute['BankAccountID'] = $bankaccount[0];
            $attribute['BankAccountDeviceNo'] = $bankaccount[1];
        }

        // $attributeUpdate = array_merge($attributeUpdate, $this->getAttributeUpdatePayments());

        return $attributeUpdate;
    }

    protected function getAttributeUpdatePayments()
    {
        // $attributeUpdate = [];

        // switch ($this->input->post("pembayaran")) {
        //     case "tunai" :
        //         $attributeUpdate = [
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
        //         $attributeUpdate = [
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
        //         $attributeUpdate = [
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

        // return $attributeUpdate;
    }

    protected function getCashBankAccount()
    {
        // return "
        // SELECT * FROM mastercashbankaccount
        // ";
    }
}
