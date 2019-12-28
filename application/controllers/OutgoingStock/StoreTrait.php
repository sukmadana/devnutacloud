<?php

/**
 * Created by PhpStorm.
 * User: Yustiko
 * Date: 4/21/2017
 * Time: 11:07 PM
 */
trait StoreTrait
{

    protected $attributes = [];

    public function form()
    {
        $this->setData('page_part', 'webparts/outgoing-stock/create');
        $this->addJsPart('webparts/parts/js_socket');
        $this->addJsPart('webparts/outgoing-stock/parts/js_add');
        $this->setData('items', $this->getitem($this->input->get('outlet')));
        $this->setData('DeviceID', $this->input->get('outlet'));

        $this->load->view('main_part', $this->data);
    }

    public function store()
    {
        if ($this->input->server('REQUEST_METHOD') != 'POST')
            throw new Exception("Method Not Allowed", 405);

        $this->runValidation();

        $this->load->library('session');

        $this->attributes = $this->generateAttribute();
        $storing = $this->Stockopname->create($this->attributes);

        if (!$storing)
            return redirect('/stokkeluar/form?outlet=' . $this->input->post('outlet'));

        if ($this->storeDetail()) {
            $this->savedDetail();
            $this->session->set_flashdata('notif', 'Simpan Berhasil');

            return redirect($this->input->server('HTTP_REFERER'));
        }
    }

    protected function storeDetail()
    {
        foreach ($this->input->post('item-name') as $key => $value) {
            $storing = $this->Stockopnamedetail->create($this->generateAttributeDetail($key));
            if (!$storing)
                return redirect('/stokmasuk/form?outlet=' . $this->attributes['DeviceID']);
        }
        return true;
    }

    protected function savedDetail()
    {
        $this->Stockopname->update($this->attributes, [
            'IsDetailsSaved' => 1
        ]);
    }

    protected function generateAttribute()
    {
        $id = $this->db->query($this->Stockopname->get_sk_transaction_id($this->input->post('outlet')))->first_row();
        $code = $this->db->query($this->Stockopname->get_generate_sk_stockopname(
            $this->getDateTime()['date'], $this->input->post('outlet')))->first_row();
        return [
            'TransactionID' => $id->result,
            'StockOpnameNumber' => $code->result,
            'StockOpnameDate' => $this->getDateTime()['date'],
            'StockOpnameTime' => $this->getDateTime()['time'],
            'DeviceID' => $this->input->post('outlet'),
            'PerusahaanID' => getLoggedInUserID(),
            'PerusahaanNo' => getPerusahaanNo(),
            'CreatedBy' => getLoggedInUsername(),
            'isDetailsSaved' => 0,
            'CreatedDate' => date('Y-m-d'),
            'CreatedTime' => date('H:i'),
            'Varian' => 'Nuta',
            'HasBeenDownloaded' => 0,
            'EditedBy' => '',
            'EditedDate' => '',
            'EditedTime' => ''
        ];
    }

    protected function generateAttributeDetail($key)
    {
        $id = $this->db->query($this->Stockopnamedetail->get_sk_detail_id($this->attributes['DeviceID']));
        return [
            'DetailID' => $id->first_row()->result,
            'TransactionID' => $this->attributes['TransactionID'],
            'detailnumber' => $key + 1,
            'ItemID' => $this->input->post('item-name')[$key],
            'StockByApp' => 0,
            'RealStock' => $this->input->post('item-total')[$key] * -1,
            'Note' => $this->input->post('note'),
            'Varian' => 'Nuta',
            'DeviceID' => $this->attributes['DeviceID'],
            'PerusahaanID' => getLoggedInUserID(),
            'PerusahaanNo' => getPerusahaanNo(),
            'HasBeenDownloaded' => 0
        ];
    }

    protected function runValidation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('datetime', 'Waktu', 'required|max_length[20]');
        $this->form_validation->set_rules('note', 'Catatan', 'required');
        ($this->form_validation->run()) ?: redirect('/stokkeluar');
    }
}