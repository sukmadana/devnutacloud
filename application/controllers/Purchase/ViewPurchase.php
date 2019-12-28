<?php

trait ViewPurchase
{
    public function view($deviceid, $id)
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
        $this->load->model('Suppliermodel');
        $this->load->model('Purchase');
        $this->load->model('Purchaseitemdetail');

        // Loading data

        $data_transaksi = $this->Purchase->get_single_transaction($deviceid, $realtransID, $devno);

        $this->addJsPart('webparts/purchase/parts/js_add');
        $this->setData('page_part', 'webparts/purchase/view');
        $this->setData('deviceid', $deviceid);
        $this->setData('transaction_data', $data_transaksi);
        $this->setData('purchase', $this->Purchase->where($attributes)->first_row());
        $this->setData('purchase_detail', $this->Purchaseitemdetail->where($attributesdetil)->result());
        $this->setData('suppliers',
            $this->db->query(
                $this->Supplier->supplier($deviceid)
            )->result()
        );
        $this->setData('supplier_single', $this->Suppliermodel->getByName($data_transaksi[0]->SupplierID, $data_transaksi[0]->SupplierDeviceNo, $deviceid) );
        $this->setData('getAccount',
            $this->db->get_where(
                'mastercashbankaccount',
                array(
                    'DeviceID'      => $deviceid,
                    'PerusahaanNo'  => getPerusahaanNo(),
                    'AccountID'   => $data_transaksi[0]->BankAccountID,
                    'DeviceNo'   => $data_transaksi[0]->BankAccountDeviceNo,
                )
            )->result()
        );

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
}